<?php
include('db_connect.php');

// Lấy dữ liệu từ form tìm kiếm
$destination = $_GET['destination'] ?? '';
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$songuoi = $_GET['songuoi'] ?? '';
$loaiphong = $_GET['loaiphong'] ?? '';
$giatoida = $_GET['giatoida'] ?? '';

// Câu SQL cơ bản: chỉ lấy phòng chưa bị đặt trong khoảng thời gian đó
$sql = "SELECT p.MaPhong, p.Gia, lp.TenLoai, lp.SoNguoiToiDa, lp.MoTa,image,
        Case
         when p.MaPhong in (SELECT MaPhong FROM datphong
            WHERE ('$checkin' < NgayTra AND '$checkout' > NgayNhan))
        then 'Đã đặt'
        else 'Trống'
        end as TrangThai
        FROM phong p
        JOIN loaiphong lp ON p.MaLoai = lp.MaLoai
        WHERE 1=1 " ;

// Thêm điều kiện lọc động
if (!empty($loaiphong)) {
    $sql .= " AND lp.TenLoai = '" . $conn->real_escape_string($loaiphong) . "'";
}
if (!empty($songuoi)) {
    $sql .= " AND lp.SoNguoiToiDa = " . intval($songuoi);
}
if (!empty($giatoida)) {
    $sql .= " AND p.Gia <= " . intval($giatoida);
}
// Thực thi truy vấn
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Danh sách phòng - Hệ thống đặt phòng</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background-color: #f8f9fa;
  font-family: 'Segoe UI', sans-serif;
}
.header-info {
  background-color: #fff;
  padding: 15px 25px;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  margin-top: 20px;
}
.filter-box {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  padding: 20px;
}
.room-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  margin-bottom: 20px;
  padding: 15px;
  display: flex;
  align-items: center;
}
.room-card img {
  width: 230px;
  height: 160px;
  object-fit: cover;
  border-radius: 8px;
  margin-right: 20px;
}
.room-card .price {
  font-size: 1.1rem;
  color: #c00;
  font-weight: bold;
}
.btn-primary {
  background-color: #0071c2;
  border: none;
}
.btn-primary:hover {
  background-color: #005fa3;
}
</style>
</head>
<body>

<div class="container">
  <div class="header-info mt-4">
    <h5>
      🏨 Kết quả tìm kiếm:
    </h5>
    <p class="mb-0">
      Địa điểm: <b><?= htmlspecialchars($destination ?: 'Không xác định') ?></b> |
      Nhận phòng: <b><?= htmlspecialchars($checkin) ?></b> |
      Trả phòng: <b><?= htmlspecialchars($checkout) ?></b> |
      Khách: <b><?= htmlspecialchars($songuoi) ?></b> người
    </p>
  </div>

  <div class="row mt-4">
    <!-- Cột bộ lọc -->
    <div class="col-md-3">
      <div class="filter-box">
        <h5 class="text-primary">Bộ lọc</h5>
        <form method="get" action="rooms.php">
          <input type="hidden" name="destination" value="<?= htmlspecialchars($destination) ?>">
          <input type="hidden" name="checkin" value="<?= htmlspecialchars($checkin) ?>">
          <input type="hidden" name="checkout" value="<?= htmlspecialchars($checkout) ?>">

          <div class="mb-3">
            <label class="form-label">Khoảng giá (VNĐ)</label>
            <input type="range" class="form-range" name="giatoida" min="200000" max="5000000" step="50000">
          </div>

          <div class="mb-3">
            <label class="form-label">Số người tối đa</label>
            <select class="form-select" name="songuoi">
              <option value="">Tất cả</option>
              <option value="1" <?= $songuoi == 1 ? 'selected' : '' ?>>1 người</option>
              <option value="2" <?= $songuoi == 2 ? 'selected' : '' ?>>2 người</option>
              <option value="3" <?= $songuoi == 3 ? 'selected' : '' ?>>3 người</option>
              <option value="4" <?= $songuoi == 4 ? 'selected' : '' ?>>4 người</option>
            </select>
            </div>
             <div class="mb-3">
            <label class="form-label">Phòng</label>
            <select class="form-select" name="loaiphong">
              <option value="">Tất cả</option>
              <option value="Phòng Đơn" <?= $songuoi == 'Phòng Đơn' ? 'selected' : '' ?>>Phòng Đơn</option>
              <option value="Phòng Đôi" <?= $songuoi == 'Phòng Đôi' ? 'selected' : '' ?>>Phòng Đôi</option>
              <option value="Phòng Gia Đình" <?= $songuoi == 'Phòng Gia Đình' ? 'selected' : '' ?>>Phòng Gia Đình</option>
            </select>
          </div>

          <div class="d-grid">
            <button class="btn btn-primary">Áp dụng</button>
          </div>
        </form>
      </div>
      </div>

    <!-- Danh sách phòng -->
    <div class="col-md-9">
      <h4 class="text-primary mb-3">Danh sách phòng phù hợp</h4>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="room-card">
          <img src="<?php echo $row['image'] ?>" alt="Phòng">
          <div class="flex-grow-1">
            <h5 class="text-primary"><?= htmlspecialchars($row['TenLoai']) ?></h5>
            <p class="mb-1">Mã phòng: <b><?= htmlspecialchars($row['MaPhong']) ?></b></p>
            <p class="mb-1">Sức chứa: <?= htmlspecialchars($row['SoNguoiToiDa']) ?> người</p>
            <p class="mb-1">Tình trạng: 
              <?= ($row['TrangThai'] == 'Trống') ? '<span class="text-success">Trống</span>' : '<span class="text-danger">Đã đặt</span>' ?>
            </p>
            <p class="mb-2"><?= htmlspecialchars($row['MoTa'] ?: 'Phòng tiện nghi, đầy đủ nội thất.') ?></p>
          </div>
          <div class="text-end">
            <div class="price mb-2"><?= number_format($row['Gia']) ?> VNĐ/đêm</div>
<a href="room_detail.php?id=<?= $row['MaPhong'] ?>&checkin=<?php echo $checkin ?>&checkout=<?php echo $checkout ?>" class="btn btn-primary">Xem chi tiết</a>
          </div>
        </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="alert alert-warning">Không tìm thấy phòng nào phù hợp với yêu cầu của bạn.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>
