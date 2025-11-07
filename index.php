<?php
include('db_connect.php');

// Lấy loại phòng để đổ dropdown (nếu cần dùng sau)
$loaiphong_sql = "SELECT * FROM loaiphong";
$loaiphong_rs = $conn->query($loaiphong_sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hotel Booking | Trang chủ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

<style>
body {
  background-color: #f8f9fa;
  font-family: 'Segoe UI', sans-serif;
  margin: 0;
}

/* NAVBAR */
.navbar {
  background-color: #003580;
  padding: 15px 0;
}
.navbar-brand {
  color: white !important;
  font-weight: 700;
  font-size: 22px;
}
.btn-login {
  border: 1px solid white;
  color: #003580;
  background: #fff;
  border-radius: 6px;
  padding: 6px 15px;
  font-weight: 500;
}

/* HERO */
.hero {
  background-color: #003580;
  color: white;
  text-align: center;
  padding: 80px 20px 130px;
}
.hero h1 { font-size: 42px; font-weight: 700; }
.hero p { font-size: 18px; color: #cfd9ff; }

/* SEARCH BAR */
.search-bar {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  background: white;
  border: 3px solid #feba02;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.15);
  width: 90%;
  margin: -70px auto 60px;
  padding: 10px;
  position: relative;
}
.search-item {
  flex: 1;
  min-width: 250px;
  border-right: 1px solid #ddd;
  padding: 10px 15px;
  display: flex;
  align-items: center;
  cursor: pointer;
  position: relative;
}
.search-item:last-child { border-right: none; }
.search-item i { color: #003580; font-size: 20px; margin-right: 10px; }
.search-item input {
  border: none;
  outline: none;
  width: 100%;
  font-size: 15px;
}
.search-item input::placeholder { color: #777; }

.search-btn {
  background: #0071c2;
  color: white;
  border: none;
  padding: 14px 28px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 16px;
  margin-left: 10px;
  transition: background 0.3s;
}
.search-btn:hover { background: #005fa3; }

/* GUEST POPUP */
.guest-box {
  position: absolute;
  top: 110%;
  left: 0;
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  padding: 15px;
  z-index: 999;
  display: none;
  width: 260px;
}
.guest-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 10px 0;
}
.guest-controls button {
  background: #f0f0f0;
  border: none;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  font-weight: bold;
}
.guest-controls span { margin: 0 8px; }

/* PROMO */
.promo-section {
  text-align: center;
  padding: 40px 0 80px;
}
.promo-section h2 { color: #222; font-weight: 600; }
.promo-section p { color: #666; margin-bottom: 30px; }
.promo-card {
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  overflow: hidden;
  transition: transform 0.2s;
  background: white;
}
.promo-card:hover { transform: translateY(-6px); }
.promo-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}
.promo-info { padding: 15px; text-align: left; }
.promo-info h5 { color: #003580; font-weight: 600; }
.promo-info p { font-size: 14px; color: #555; margin: 5px 0; }

footer {
  background: #003580;
  color: white;
  text-align: center;
  padding: 20px;
}
.card {
  display: flex;
  align-items: center;
  gap: 15px;
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  height: 100%;
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
  text-align: left;
}
.card img {
  width: 60px;
  height: 60px;
  flex-shrink: 0;
}
.card h5 {
  font-weight: 700;
  font-size: 22px;
  margin-bottom: 5px;
  color: #222;
}
.card p {
  color: #555;
  font-size: 15px;
  margin: 0;
}



</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container d-flex justify-content-between">
    <a class="navbar-brand" href="index.php">🏨 HOTEL BOOKING</a>
    <div>
      <a href="#" class="btn-login me-2">Đăng ký</a>
      <a href="#" class="btn-login">Đăng nhập</a>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <h1>Tìm chỗ nghỉ tiếp theo</h1>
  <p>Tìm ưu đãi khách sạn, chỗ nghỉ dạng nhà và nhiều hơn nữa...</p>
</section>

<!-- SEARCH BAR -->
<form action="rooms.php" method="GET" class="search-bar">


  <!-- Ngày nhận & trả -->
  <div class="search-item">
    <i class="bi bi-calendar-event"></i>
    <input type="text" id="dateRange" placeholder="Chọn ngày nhận và trả phòng" readonly required>
    <input type="hidden" name="checkin" id="checkin">
    <input type="hidden" name="checkout" id="checkout">
  </div>

  <!-- Người lớnphòng -->
  <div class="search-item" id="guestSelector">
  <i class="bi bi-person"></i>
  <input type="text" id="guestDisplay" value="2 người · 1 phòng" readonly>

  <div class="guest-box" id="guestBox">
    <div class="guest-row">
      <span>Người</span>
      <div class="guest-controls">
        <button type="button" onclick="updateGuest('adults', -1)">−</button>
        <span id="adultsCount">2</span>
        <button type="button" onclick="updateGuest('adults', 1)">+</button>
      </div>
    </div>
    
    <div class="guest-row">
      <span>Phòng</span>
      <div class="guest-controls">
        <button type="button" onclick="updateGuest('rooms', -1)">−</button>
        <span id="roomsCount">1</span>
        <button type="button" onclick="updateGuest('rooms', 1)">+</button>
      </div>
    </div>
    <div class="text-end mt-2">
      <button type="button" class="btn btn-primary btn-sm" onclick="closeGuestBox()">Xong</button>
    </div>
  </div>
</div>

<!-- CHUYỂN 4 INPUT ẨN RA NGOÀI -->
<input type="hidden" name="songuoi" id="songuoi" value="2">
<input type="hidden" name="nguoi" id="nguoi" value="2">
<input type="hidden" name="sophong" id="sophong" value="1">

  <button type="submit" class="search-btn">Tìm</button>
</form>
<!-- LÝ DO NÊN CHỌN CHÚNG TÔI -->
<section class="container my-5">
  <h2 class="fw-bold mb-4 text-center">Vì sao nên đặt trên Hotel Booking?</h2>
  <div class="row g-4">

    <div class="col-md-3">
      <div class="card h-100 shadow-sm border-0 text-center p-3">
        <img src="https://img.icons8.com/color/96/000000/calendar--v1.png" alt="Đặt phòng linh hoạt" class="mb-3" width="60">
        <h5 class="fw-bold">Đặt phòng linh hoạt</h5>
        <p class="text-muted">Thanh toán tại nơi, đặt phòng và hủy phòng một cách tiện lợi</p>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card h-100 shadow-sm border-0 text-center p-3">
        <img src="https://img.icons8.com/color/96/000000/facebook-like.png" alt="Đánh giá thực tế" class="mb-3" width="60">
        <h5 class="fw-bold">Đánh giá thực tế tốt</h5>
        <p class="text-muted">Hơn 2 triệu đánh giá từ các khách du lịch, được nhiều người tin tưởng</p>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card h-100 shadow-sm border-0 text-center p-3">
        <img src="https://img.icons8.com/color/96/000000/globe-earth.png" alt="Mạng lưới toàn cầu" class="mb-3" width="60">
        <h5 class="fw-bold">Mạng lưới toàn quốc</h5>
        <p class="text-muted">Đa dạng chỗ nghỉ tại khắp nơi, tha hồ lựa chọn</p>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card h-100 shadow-sm border-0 text-center p-3">
        <img src="https://img.icons8.com/color/96/000000/customer-support.png" alt="Hỗ trợ 24/7" class="mb-3" width="60">
        <h5 class="fw-bold">Hỗ trợ khách hàng 24/7</h5>
        <p class="text-muted">Luôn sẵn sàng trợ giúp và giải đáp thắc mắc của bạn mọi lúc, mọi nơi</p>
      </div>
    </div>

  </div>
</section>
<!-- PROMO SECTION -->
<section class="promo-section container">
  <h2>Ưu đãi cho cuối tuần</h2>
  <p>Khám phá những phòng nghỉ có giá tốt nhất cho cuối tuần này!</p>
  <div class="row g-4">
    <?php
    $promo_sql = "SELECT p.MaPhong, lp.TenLoai, p.Gia, lp.SoNguoiToiDa 
              FROM phong p 
              JOIN loaiphong lp ON p.MaLoai = lp.MaLoai 
              WHERE p.TrangThai = 'Trống'
              ORDER BY p.Gia ASC LIMIT 4";

    $promo_rs = $conn->query($promo_sql);

    if ($promo_rs && $promo_rs->num_rows > 0):
      while ($promo = $promo_rs->fetch_assoc()):
    ?>
    <div class="col-md-3">
      <div class="promo-card">
<img src="assets/images/default.jpg" 
     alt="Phòng" 
     class="w-100" 
     style="height:200px;object-fit:cover;border-radius:10px 10px 0 0;">
        <div class="promo-info">
          <h5><?= htmlspecialchars($promo['TenLoai']) ?></h5>
          <p>Sức chứa: <?= $promo['SoNguoiToiDa'] ?> người</p>
          <p class="text-danger fw-bold"><?= number_format($promo['Gia']) ?> VNĐ / đêm</p>
          <a href="room_detail.php?id=<?= $promo['MaPhong'] ?>" class="btn btn-primary w-100 mt-2">Xem chi tiết</a>
        </div>
      </div>
    </div>
    <?php
      endwhile;
    else:
    ?>
    <p class="text-muted text-center">Hiện chưa có phòng nào trong danh sách ưu đãi.</p>
    <?php endif; ?>
  </div>
</section>


<footer>
  © 2025 Hotel Booking System — Đặt phòng nhanh chóng & an toàn
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// =================== CHỌN NGÀY ===================
flatpickr("#dateRange", {
  mode: "range",
  minDate: "today",
  dateFormat: "Y-m-d",
  onChange: function(selectedDates) {
    if (selectedDates.length === 2) {
      document.getElementById('checkin').value = selectedDates[0].toISOString().split('T')[0];
      document.getElementById('checkout').value = selectedDates[1].toISOString().split('T')[0];
    }
  }
});

// =================== CHỌN SỐ KHÁCH ===================
const guestSelector = document.getElementById('guestSelector');
const guestBox = document.getElementById('guestBox');
let guestData = { adults: 2, rooms: 1 };

// ✅ Thêm đoạn này
guestSelector.addEventListener('click', (e) => {
  e.stopPropagation();
  guestBox.style.display = (guestBox.style.display === 'block') ? 'none' : 'block';
});

function updateGuest(type, delta) {
  guestData[type] = Math.max(0, guestData[type] + delta);

  // Cập nhật hiển thị
  document.getElementById(`${type}Count`).innerText = guestData[type];
  document.getElementById('guestDisplay').value =
    `${guestData.adults} người · ${guestData.rooms} phòng`;

  // Gửi tổng số người + từng giá trị cụ thể vào input hidden
  document.getElementById('songuoi').value = guestData.adults;
  document.getElementById('nguoi').value = guestData.adults;
  document.getElementById('sophong').value = guestData.rooms;
}

function closeGuestBox() {
  guestBox.style.display = 'none';
}

// Ẩn popup khi click ra ngoài
document.addEventListener('click', (e) => {
  if (!guestSelector.contains(e.target)) guestBox.style.display = 'none';
});

</script>
</body>
</html>
