<?php
require 'vendor/autoload.php';
include('db_connect.php');

\Stripe\Stripe::setApiKey('sk_test_51SQ6iL0HaSJZxzXQoW2VW6li9krsjj81JazYpVc7MPAIsZu2nZjFysNSpTeDawe46pwZrlEO6NWaxmnlXiPWeUiR00UhLhLKWP');

$maphong = $_POST['maphong'];
$checkin = $_POST['checkin'];
$checkout = $_POST['checkout'];
$hoten = $_POST['hoten'];
$email = $_POST['email'];

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
                     '&hoten=' . urlencode($hoten) .
                     '&email=' . urlencode($email) .
                     '&checkin=' . urlencode($checkin) .
                     '&checkout=' . urlencode($checkout),
    'cancel_url'  => 'http://localhost/LAPTRINHWEBC3/payment_cancel.php',
]);

header("Location: " . $session->url);
exit;
?>
