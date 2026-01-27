$(document).ready(function() {
  // Load sản phẩm giảm giá
  $.ajax({
    url: "inDSSPGiamGia.php",
    method: "GET",
    success: function(data) {
      $(".loai-san-pham-giam-gia").html(data);
    },
    error: function() {
      alert("Lỗi load sản phẩm giảm giá!");
    }
  });

  // Load sản phẩm mới
  $.ajax({
    url: "inDSSPMoi.php",
    method: "GET",
    success: function(data) {
      $(".loai-san-pham-moi").html(data);
    },
    error: function() {
      alert("Lỗi load sản phẩm mới!");
    }
  });

  // Load danh mục
  $.ajax({
    url: "inDanhMuc.php",
    method: "GET",
    success: function(data) {
      $(".nut-danh-muc").html(data);
    },
    error: function() {
      alert("Lỗi load danh mục!");
    }
  });
});