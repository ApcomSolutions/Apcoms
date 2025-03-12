{{-- resources/views/emails/reset-password-otp.blade.php --}}
    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eaeaea;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .content {
            padding: 20px 0;
        }
        .otp-container {
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #3b82f6;
            padding: 15px 20px;
            background-color: #f0f5ff;
            border-radius: 8px;
            border: 1px dashed #3b82f6;
            display: inline-block;
        }
        .footer {
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }
        .warning {
            background-color: #fff7ed;
            border-left: 4px solid #f97316;
            padding: 10px;
            margin: 20px 0;
            font-size: 14px;
            color: #9a3412;
        }
        h1 {
            color: #1f2937;
            font-size: 24px;
            margin-top: 0;
        }
        p {
            margin: 10px 0;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ asset('images/icon.png') }}" alt="Logo" class="logo">
        <h1>Verifikasi Akun Anda</h1>
    </div>

    <div class="content">
        <p>Halo,</p>

        <p>Kami menerima permintaan untuk mengatur ulang password akun Anda. Gunakan kode OTP berikut untuk melanjutkan proses reset password:</p>

        <div class="otp-container">
            <div class="otp-code">{{ $otp }}</div>
        </div>

        <p>Kode OTP ini akan kadaluarsa dalam 10 menit.</p>

        <div class="warning">
            Jika Anda tidak melakukan permintaan ini, abaikan email ini atau hubungi administrator.
        </div>

        <p>Terima kasih,<br>Tim Admin</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} APCOM Solutions. All rights reserved.</p>
        <p>Email ini dikirim otomatis, harap jangan membalas email ini.</p>
    </div>
</div>
</body>
</html>
