<?php
include 'db_connect.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
\Stripe\Stripe::setApiKey('sk_test_51SQ6iL0HaSJZxzXQoW2VW6li9krsjj81JazYpVc7MPAIsZu2nZjFysNSpTeDawe46pwZrlEO6NWaxmnlXiPWeUiR00UhLhLKWP');


// Lấy thông tin từ URL
$maphong = $_GET['room'] ?? '';
$hoten = $_GET['hoten'] ?? '';
$email = $_GET['email'] ?? '';
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$madatphong = $_GET['madatphong'] ?? '';

//truy xuất thông tin giao dịch
$session_id =$_GET['session_id'];//truy suất thông tin giao dịch
$session = \Stripe\Checkout\Session::retrieve($session_id);
$payment_intent =\stripe\PaymentIntent::retrieve($session->payment_intent);
//truy xuấtvà tự sinh mã cho thanh toán
$lastId = $conn->insert_id;
$maThanhToan = 'TT' . str_pad($lastId, 3, '0', STR_PAD_LEFT);
$trangthai =$payment_intent->status;
$sotien =$payment_intent->amount_received /100;
$phuongthuc =$payment_intent->payment_method_types[0];
$ngaythanhtoans = date("Y-m-d");
//import csdl
$sql ="insert into thanhtoan(MaThanhToan,PhuongThuc,SoTien,NgayThanhToan,TrangThai,MaDatPhong)
 values(?,?,?,?,?,?)";
 $stmt =$conn->prepare($sql);
 $stmt->bind_param("ssdsss",$maThanhToan,$phuongthuc,$sotien,$ngaythanhtoans,$trangthai,$madatphong);
if($stmt->execute())
{
 error_log("thanh toán thành công");
}


// Sinh QR Code
$qrData = "Phòng: $maphong\nKhách: $hoten\nCheck-in: $checkin\nCheck-out: $checkout";
$qrCode = new QrCode($qrData);
$writer = new PngWriter();
$qrImage = $writer->write($qrCode);
$qrFile = __DIR__ . "/qr_booking_$maphong.png";
$qrImage->saveToFile($qrFile);

// Gửi email xác nhận
$mail = new PHPMailer(true);
try {
    // Cấu hình SMTP Gmail
$mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'hthu19082004@gmail.com'; 
    $mail->Password = 'ojdn qltn mvwu mcfh';   
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
 $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->setFrom('hthu19082004@gmail.com', 'Hotel Booking System');
    $mail->addAddress($email, $hoten);
    $mail->addAttachment($qrFile);

    $mail->isHTML(true);
    $mail->Subject = 'Xác nhận đặt phòng thành công';
   $mail->Body = "
<div style='font-family:Arial,sans-serif; background:#f9f9f9; padding:20px;'>
  <div style='max-width:600px;margin:auto;background:#fff;border-radius:10px;padding:30px;'>
    <h2 style='color:#2f855a;text-align:center;'>🎉 Xác nhận đặt phòng thành công!</h2>
    <p>Xin chào <b>$hoten</b>,</p>
    <p>Bạn đã đặt thành công <b>phòng $maphong</b>.</p>
    <p>Thời gian lưu trú: <b>$checkin</b> → <b>$checkout</b>.</p>
    <p>Đính kèm là mã QR để xác nhận đặt phòng khi đến khách sạn.</p>
    <div style='text-align:center;margin:30px 0;'>
        <img src='cid:qr_image' alt='QR Code' style='width:150px;height:150px;'>
    </div>
    <hr style='border:none;border-top:1px solid #eee;margin:20px 0'>
    <p style='font-size:13px;color:#666;'>Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi.</p>
    <p style='font-size:13px;color:#666;'><i>Trân trọng,<br>Hotel Booking System</i></p>
  </div>
</div>
";
$mail->addEmbeddedImage($qrFile, 'qr_image', 'qr.png');

    $mail->send();
    $emailStatus = "Email xác nhận đã được gửi đến $email.";
} catch (Exception $e) {
    $emailStatus = "Gửi email thất bại: {$mail->ErrorInfo}";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán thành công</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #f8f8f8; padding: 50px; }
        .card { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: inline-block; }
        h2 { color: #2f855a; }
        p { line-height: 1.5; }
    </style>
</head>
<body>
    <div class="card">
        <h2>✅ Thanh toán thành công!</h2>
        <p><b>Khách hàng:</b> <?= htmlspecialchars($hoten) ?></p>
        <p><b>Email:</b> <?= htmlspecialchars($email) ?></p>
        <p><b>Phòng:</b> <?= htmlspecialchars($maphong) ?></p>
        <p><b>Thời gian:</b> <?= htmlspecialchars($checkin) ?> → <?= htmlspecialchars($checkout) ?></p>
        <hr>
        <p><?= $emailStatus ?></p>
    </div>
</body>
</html>
