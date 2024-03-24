<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thông báo: Có khách hàng vừa cần liên hệ</title>
</head>
<body>
    <h3>Chào bạn</h3>
    <p><i>Hệ Wingsocean vừa ghi nhận được thông tin khách hàng từ form đăng ký trên trang web.</i></p>
    <div>
        <h4>Tông tin</h4>
        <p>Tên CTY: {{ $companyNameInp }}</p>
        <p>Địa chỉ Email: {{ $emailInp }}</p>
        <p>Số điện thoại: {{ $phoneInp }}</p>
        <p>Nội dung: {{ $requestInp }}</p>
    </div>
</body>
</html>
