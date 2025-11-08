<?php
require 'vendor/autoload.php';
include('db_connect.php');

\Stripe\Stripe::setApiKey('sk_test_51SQ6iL0HaSJZxzXQoW2VW6li9krsjj81JazYpVc7MPAIsZu2nZjFysNSpTeDawe46pwZrlEO6NWaxmnlXiPWeUiR00UhLhLKWP');

$maphong = $_POST['maphong'];
$checkin = $_POST['checkin'];
$checkout = $_POST['checkout'];
$hoten = $_POST['hoten'];
$email = $_POST['email'];
$songuoi = $_POST['songuoi'];
$sdt=$_POST['sdt'];
//inport sau khi ấn nút thanh toán se lưu vào datphong
$result = $conn->query("SELECT COUNT(*) AS total FROM khachhang");
$row = $result->fetch_assoc();
$makh = 'KH' . str_pad($row['total'] + 1, 3, '0', STR_PAD_LEFT);

//insert vào khachhang
$stmt1 = $conn->prepare("INSERT INTO khachhang (MaKH, HoTen, Email, SDT) VALUES (?, ?, ?, ?)");
$stmt1->bind_param("ssss", $makh, $hoten, $email, $sdt);
$stmt1->execute();

$result2 = $conn->query("SELECT COUNT(*) AS total FROM datphong");
$row2 = $result2->fetch_assoc();
$madatphong = 'DP' . str_pad($row2['total'] + 1, 3, '0', STR_PAD_LEFT);

$stmt2 = $conn->prepare("insert into datphong(MaDatPhong,NgayNhan,NgayTra,SoNguoi,MaKH,MaPhong) values(?,?,?,?,?,?)");
$stmt2->bind_param("ssssss",$madatphong,$checkin,$checkout,$songuoi,$makh,$maphong);
$stmt2->execute();
// Lấy thông tin phòng
$room = $conn->query("SELECT * FROM phong WHERE MaPhong='$maphong'")->fetch_assoc();

$gia = isset($room['Gia']) ? (int)preg_replace('/[^0-9]/', '', $room['Gia']) : 0;
if ($gia <= 0) {
    die("Lỗi: Giá phòng không hợp lệ hoặc bằng 0.");
}

// Tạo phiên thanh toán Stripe
$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'vnd',
            'product_data' => [
                'name' => "Đặt phòng {$maphong}",
            ],
            'unit_amount' => $gia, 
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'customer_email' => $email,
    'success_url' => 'http://localhost/LAPTRINHWEBC3/payment_success.php?room=' . $maphong .
                     '&session_id={CHECKOUT_SESSION_ID}' .
                     '&hoten=' . urlencode($hoten) .
                     '&madatphong=' . urlencode($madatphong) .
                     '&email=' . urlencode($email) .
                     '&checkin=' . urlencode($checkin) .
                     '&checkout=' . urlencode($checkout),
    'cancel_url'  => 'http://localhost/LAPTRINHWEBC3/payment_cancel.php',
]);

header("Location: " . $session->url);
exit;
?>
