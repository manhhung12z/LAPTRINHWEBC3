<?php
include('db_connect.php');

// --- LẤY ID PHÒNG TỪ URL ---
$maphong = $_GET['id'] ?? null;
$checkin = $_GET['checkin'] ?? null;
$checkout = $_GET['checkout'] ?? null;
$songuoi = $_GET['songuoi'] ?? null;

if (!$maphong) {
    echo "<h3>Không tìm thấy phòng!</h3>";
    exit;
}

// --- TRUY VẤN THÔNG TIN PHÒNG ---
$sql = "SELECT p.MaPhong, lp.TenLoai, p.Gia, lp.SoNguoiToiDa, lp.MoTa, p.TrangThai,image
        FROM phong p
        JOIN loaiphong lp ON p.MaLoai = lp.MaLoai
        WHERE p.MaPhong = '$maphong'";
$result = $conn->query($sql);
$room = $result->fetch_assoc();

if (!$room) {
    echo "<h3>Không tìm thấy thông tin phòng.</h3>";
    exit;
}
if ($checkin && $checkout) {
    $sql1 ="select * from Datphong where MaPhong ='$maphong' and '$checkin' < NgayTra AND '$checkout' > NgayNhan";
    $result1=$conn->query($sql1);
    if($result1->num_rows>0)
    {
        $room['TrangThai'] = 'Đã đặt';
    }     
    else 
    {
        $room['TrangThai'] = 'Trống';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi tiết phòng - <?= htmlspecialchars($room['TenLoai']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background-color: #f9f9f9; font-family: 'Segoe UI', sans-serif; }
.container { max-width: 1000px; margin-top: 40px; }
.room-img { width: 100%; border-radius: 10px; object-fit: cover; height: 350px; }
.detail-box { background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
.btn-book { background: #0071c2; color: white; font-weight: 600; }
.btn-book:hover { background: #005fa3; }
footer { background: #003580; color: white; text-align: center; padding: 15px; margin-top: 40px; }
</style>
</head>
<body>

<div class="container">
  <a href="rooms.php?checkin=<?Php echo $checkin?>&checkout=<?php echo $checkout?> " class="text-decoration-none"><i class="bi bi-arrow-left"></i> Quay lại danh sách phòng</a>
  <div class="row mt-4">
    <div class="col-md-6">
      <img src="<?php echo $room['image']?>" alt="Phòng" class="room-img">
    </div>
    <div class="col-md-6">
      <div class="detail-box">
        <h3><?= htmlspecialchars($room['TenLoai']) ?></h3>
        <p><b>Mã phòng:</b> <?= $room['MaPhong'] ?></p>
        <p><b>Giá:</b> <?= number_format($room['Gia']) ?> VNĐ / đêm</p>
        <p><b>Sức chứa tối đa:</b> <?= $room['SoNguoiToiDa'] ?> người</p>
        <p><b>Trạng thái:</b> 
          <?php if ($room['TrangThai'] == 'Trống'): ?>
            <span class="text-success fw-bold">Trống</span>
          <?php else: ?>
            <span class="text-danger fw-bold">Đã đặt</span>
          <?php endif; ?>
        </p>
        <p><b>Mô tả:</b> <?= nl2br($room['MoTa']) ?></p>

        <?php if ($room['TrangThai'] == 'Trống'): ?>
        <hr>
        <h5>Đặt phòng ngay</h5>
        <!-- DÙNG GET ĐỂ TRUYỀN THAM SỐ -->
        <form method="GET" action="booking.php" class="mt-3">
          <input type="hidden" name="room" value="<?= $room['MaPhong'] ?>">
          <input type="hidden" name="checkin" value="<?= $checkin ?>">
          <input type="hidden" name="checkout" value="<?= $checkout ?>">
          <input type="hidden" name="songuoi" value="<?= $songuoi ?>">

          <button type="submit" class="btn btn-book w-100">Tiếp tục đặt phòng</button>
        </form>
        <?php else: ?>
          <div class="alert alert-warning mt-3">Phòng này hiện đã được đặt. Vui lòng chọn phòng khác.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<footer>© 2025 Hotel Booking — Xây dựng bởi nhóm của bạn</footer>

</body>
</html>
