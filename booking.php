<?php
include('db_connect.php');

// Lấy thông tin từ URL
$maphong  = $_GET['room'] ?? null;
$checkin  = $_GET['checkin'] ?? null;
$checkout = $_GET['checkout'] ?? null;
$songuoi = $_GET['songuoi'] ?? null;

if (!$maphong) {
    die("<h3 style='color:red; text-align:center; margin-top:50px;'>Không tìm thấy phòng!</h3>");
}

// Truy vấn thông tin phòng
$sql = "SELECT p.MaPhong, lp.TenLoai, lp.SoNguoiToiDa, p.Gia 
        FROM phong p 
        JOIN loaiphong lp ON p.MaLoai = lp.MaLoai 
        WHERE p.MaPhong = '$maphong'";
$room = $conn->query($sql)->fetch_assoc();

if (!$room) {
    die("<h3 style='color:red; text-align:center; margin-top:50px;'>Không tìm thấy thông tin phòng!</h3>");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt phòng - <?= htmlspecialchars($room['TenLoai']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f8f9fa;">
<div class="container mt-5">
  <div class="card shadow p-4">
    <h3 class="mb-3 text-primary">Đặt phòng: <?= htmlspecialchars($room['TenLoai']) ?> (<?= $room['MaPhong'] ?>)</h3>
    <p><b>Sức chứa tối đa:</b> <?= $room['SoNguoiToiDa'] ?> người</p>
    <p><b>Giá phòng:</b> <?= number_format($room['Gia']) ?> VNĐ / đêm</p>
    <hr>

    <form action="create_payment.php" method="POST">
      <input type="hidden" name="maphong" value="<?= $maphong ?>">
      <input type="hidden" name="checkin" value="<?= $checkin ?>">
      <input type="hidden" name="checkout" value="<?= $checkout ?>">
      <input type="hidden" name="songuoi" value="<?= $songuoi ?>">
      <input type="hidden" name="gia" value="<?= $room['Gia'] ?>">
      
      <div class="mb-3">
        <label for="hoten" class="form-label">Họ và tên</label>
        <input type="text" name="hoten" id="hoten" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="sodienthoai" class="form-label">Số Điện Thoại</label>
        <input type="number" name="sdt" id="sdt" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-success w-100">Thanh toán ngay</button>
    </form>

  </div>
</div>
</body>
</html>
