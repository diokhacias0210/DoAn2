import pandas as pd
import numpy as np
from sklearn.decomposition import TruncatedSVD
import mysql.connector
from flask import Flask, request, jsonify
from flask_cors import CORS
from scipy.sparse import csr_matrix
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

def train_model():
    global user_factors, item_factors, all_users, all_items, user_history, top_trending_items
    print("--------------------------------------------------")
    print("1. Đang kết nối CSDL doan2 và lấy dữ liệu...")
    try:
        conn = mysql.connector.connect(host="localhost", user="root", password="", database="doan2")
        # Đã cập nhật bảng HanhVi_AI
        query = "SELECT IdTaiKhoan, MaHH, Diem AS SoSao FROM HanhVi_AI"
        df = pd.read_sql(query, conn)
        conn.close()

        if df.empty:
            print("Lỗi: Không có dữ liệu trong bảng HanhVi_AI.")
            return False

        print(f"-> Lấy thành công {len(df)} dòng tương tác.")
        print("2. Đang huấn luyện AI & Giải quyết Khởi động lạnh...")
        
        # GIẢI QUYẾT COLD START (TÌM TOP THỊNH HÀNH)
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
        
        global user_factors, item_factors
        user_factors = svd.fit_transform(sparse_matrix)
        item_factors = svd.components_

        print("-> Huấn luyện xong! Não bộ AI đã sẵn sàng.")
        return True
    except Exception as e:
        print("Lỗi Train Model:", e)
        return False

@app.route('/recommend', methods=['GET'])
def recommend():
    try:
        user_id = int(request.args.get('user_id', 0))
        top_n = int(request.args.get('top_n', 8))

        # USER MỚI -> TRẢ VỀ ĐỒ THỊNH HÀNH (COLD START)
        if user_id not in all_users:
            results = []
            for item_id in top_trending_items[:top_n]:
                results.append({"id": int(item_id), "match": 0, "reason": "Trending"})
            return jsonify(results)

        # USER CŨ -> TÍNH TOÁN SVD THÔNG MINH
        user_idx = all_users.index(user_id)
        user_predictions = np.dot(user_factors[user_idx], item_factors)
        pred_series = pd.Series(user_predictions, index=all_items)

        already_rated = user_history.get(user_id, set())
        pred_series = pred_series.drop(list(already_rated), errors='ignore')
        top_items = pred_series.sort_values(ascending=False).head(top_n)

        results = []
        for item_id, score in top_items.items():
            match_percent = min(99, max(65, int((score / 5.0) * 100)))
            results.append({"id": int(item_id), "match": match_percent, "reason": "AI_Match"})

        return jsonify(results)
    except Exception as e:
        return jsonify({"error": str(e)})

# CỔNG RETRAIN CHO PHÉP WEB GỌI ĐỂ CẬP NHẬT DỮ LIỆU TỰ ĐỘNG
@app.route('/retrain', methods=['GET'])
def retrain_api():
    print(">>> CÓ LỆNH YÊU CẦU CẬP NHẬT DỮ LIỆU TỪ WEB...")
    success = train_model()
    if success:
        return jsonify({"status": "success", "message": "Não bộ AI đã được cập nhật dữ liệu mới nhất!"})
    else:
        return jsonify({"status": "error", "message": "Có lỗi xảy ra khi cập nhật AI."})

if __name__ == '__main__':
    if train_model():
        print("--------------------------------------------------")
        print("3. TẤT CẢ ĐÃ SẴN SÀNG!")
        print("Cổng API đang mở tại: http://localhost:5000/recommend")
        app.run(host='0.0.0.0', port=5000, debug=False)