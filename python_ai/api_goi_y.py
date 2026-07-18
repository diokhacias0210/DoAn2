import pandas as pd
import numpy as np
from sklearn.decomposition import TruncatedSVD
import mysql.connector
from flask import Flask, request, jsonify
from flask_cors import CORS
from scipy.sparse import csr_matrix
import threading
import warnings

warnings.filterwarnings('ignore', category=UserWarning)

app = Flask(__name__)
CORS(app)

user_factors = None
item_factors = None
all_users = []
all_items = []
user_history = {}
top_trending_items = []
is_training = False 

def get_db_connection():
    return mysql.connector.connect(host="localhost", user="root", password="", database="doan2")

def train_model():
    global user_factors, item_factors, all_users, all_items, user_history, top_trending_items, is_training
    if is_training: return
    is_training = True
    
    print("\n[AI] Đang cập nhật lại dữ liệu học tập (SVD)...")
    try:
        conn = get_db_connection()
        query = "SELECT IdTaiKhoan, MaHH, Diem AS SoSao FROM HanhVi_AI"
        df = pd.read_sql(query, conn)
        conn.close()

        if df.empty:
            is_training = False
            return

        item_popularity = df['MaHH'].value_counts()
        top_trending_items = item_popularity.index.tolist()

        all_users = df['IdTaiKhoan'].unique().tolist()
        all_items = df['MaHH'].unique().tolist()
        user_to_idx = {user: i for i, user in enumerate(all_users)}
        item_to_idx = {item: i for i, item in enumerate(all_items)}

        row = df['IdTaiKhoan'].map(user_to_idx).values
        col = df['MaHH'].map(item_to_idx).values
        data = df['SoSao'].values

        sparse_matrix = csr_matrix((data, (row, col)), shape=(len(all_users), len(all_items)))
        user_history = df.groupby('IdTaiKhoan')['MaHH'].apply(set).to_dict()

        n_components = min(20, len(all_items) - 1, len(all_users) - 1)
        svd = TruncatedSVD(n_components=n_components, random_state=42)
        
        user_factors = svd.fit_transform(sparse_matrix)
        item_factors = svd.components_

        print("[AI] Đã học xong dữ liệu mới nhất!")
    except Exception as e:
        print("[Lỗi Train Model]:", e)
    finally:
        is_training = False

@app.route('/recommend', methods=['GET'])
def recommend():
    try:
        user_id = int(request.args.get('user_id', 0))
        top_n = int(request.args.get('top_n', 8))
        exclude_ids_str = request.args.get('exclude', '')
        exclude_ids = [int(x) for x in exclude_ids_str.split(',')] if exclude_ids_str else []

        conn = get_db_connection()
        cursor = conn.cursor()

        # Tìm Top 2 danh mục (MaDM) yêu thích nhất của User này (Content-Based)
        fav_categories = []
        if user_id > 0:
            cursor.execute(f"""
                SELECT h.MaDM 
                FROM HanhVi_AI v 
                JOIN HangHoa h ON v.MaHH = h.MaHH 
                WHERE v.IdTaiKhoan = {user_id} 
                GROUP BY h.MaDM 
                ORDER BY SUM(v.Diem) DESC LIMIT 2
            """)
            fav_categories = [row[0] for row in cursor.fetchall()]

        #  Lấy danh sách TẤT CẢ hàng hợp lệ (kèm theo MaDM để lát nữa xét thưởng)
        cursor.execute(f"SELECT MaHH, MaDM FROM HangHoa WHERE SoLuongHH > 0 AND TrangThaiDuyet = 'DaDuyet' AND IdNguoiBan != {user_id}")
        valid_items_data = cursor.fetchall()
        valid_items = [row[0] for row in valid_items_data]
        item_to_madm = {row[0]: row[1] for row in valid_items_data}
        conn.close()

        results = []
        if user_id not in all_users:
            # USER MỚI TINH (Chưa có lịch sử) -> Đẩy đồ Trending
            for item_id in top_trending_items:
                if item_id in valid_items and item_id not in exclude_ids:
                    results.append({"id": int(item_id), "match": 0, "reason": "Trending"})
                if len(results) >= top_n: break
        else:
            # USER CŨ -> SVD (Collaborative) KẾT HỢP DANH MỤC (Content-Based)
            user_idx = all_users.index(user_id)
            user_predictions = np.dot(user_factors[user_idx], item_factors)
            pred_series = pd.Series(user_predictions, index=all_items)

            # Lọc bỏ đồ đã xem & đồ exclude (để tải thêm)
            already_rated = list(user_history.get(user_id, set()))
            items_to_drop = list(set(exclude_ids + already_rated))
            pred_series = pred_series.drop(items_to_drop, errors='ignore')

            #  ĐƯA SẢN PHẨM MỚI VÀO CUỘC CHƠI (Gán điểm mặc định là 0)
            new_items = [item for item in valid_items if item not in pred_series.index and item not in items_to_drop]
            if new_items:
                new_items_series = pd.Series(0.0, index=new_items)
                pred_series = pd.concat([pred_series, new_items_series])

            # Chỉ giữ lại hàng còn tồn kho
            pred_series = pred_series[pred_series.index.isin(valid_items)]

            #  THUẬT TOÁN BƠM ĐIỂM (BOOST SCORE) THEO DANH MỤC YÊU THÍCH
            if fav_categories and not pred_series.empty:
                max_svd_score = pred_series.max() if pred_series.max() > 0 else 1.0
                bonus_1 = max_svd_score * 0.8  # Thưởng 80% sức mạnh SVD cho danh mục Top 1
                bonus_2 = max_svd_score * 0.4  # Thưởng 40% cho danh mục Top 2

                def apply_bonus(item_id, score):
                    madm = item_to_madm.get(item_id)
                    if madm == fav_categories[0]:
                        return score + bonus_1
                    elif len(fav_categories) > 1 and madm == fav_categories[1]:
                        return score + bonus_2
                    return score
                
                # Cập nhật lại điểm số
                pred_series = pd.Series({i: apply_bonus(i, score) for i, score in pred_series.items()})

            # Lấy Top N
            top_items = pred_series.sort_values(ascending=False).head(top_n)

            # Quy đổi điểm ra % mượt mà
            if not top_items.empty:
                max_score = top_items.max()
                min_score = top_items.min()
                for item_id, score in top_items.items():
                    if max_score > min_score:
                        match_percent = int(60 + ((score - min_score) / (max_score - min_score)) * 39)
                    else:
                        match_percent = 85
                    
                    # Phân loại nhãn để Web hiển thị
                    reason = "AI_Match" if item_id not in new_items else "Gợi ý mới"
                    results.append({"id": int(item_id), "match": match_percent, "reason": reason})

        # IN TERMINAL (DEBUG)
        if results:
            print(f"\n---> [AI] Đang gợi ý cho User {user_id} ({len(results)} món):")
            
            id_list = [str(r['id']) for r in results]
            id_str = ",".join(id_list)
            
            conn2 = get_db_connection()
            cursor2 = conn2.cursor()
            cursor2.execute(f"SELECT MaHH, TenHH FROM HangHoa WHERE MaHH IN ({id_str})")
            name_dict = {row[0]: row[1] for row in cursor2.fetchall()}
            conn2.close()

            for r in results:
                item_id = r['id']
                full_name = name_dict.get(item_id, "Sản phẩm không tên")
                words = full_name.split()
                short_name = " ".join(words[:4]) + " ..." if len(words) > 4 else full_name
                print(f"id {item_id} - {short_name} ({r['match']}%)")
        else:
            print(f"\n---> [AI] Không có gợi ý nào cho User {user_id}.")

        return jsonify(results)
    except Exception as e:
        print("[Lỗi Recommend]:", e)
        return jsonify([])

@app.route('/retrain', methods=['GET'])
def retrain_api():
    thread = threading.Thread(target=train_model)
    thread.start()
    return jsonify({"status": "success", "message": "Đang học lại ngầm..."})

if __name__ == '__main__':
    train_model()
    print("==================================================")
    print("Cổng API AI đang mở tại: http://localhost:5000/recommend")
    app.run(host='0.0.0.0', port=5000, debug=False)