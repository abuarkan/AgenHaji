<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi 2FA Anda</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #093566;
            padding: 25px;
            text-align: center;
            border-bottom: 4px solid #f59e0b;
        }
        .header-title {
            color: #ffffff;
            font-weight: 800;
            font-size: 16px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .content {
            padding: 40px 30px;
            color: #334155;
            line-height: 1.6;
        }
        .content h1 {
            font-size: 18px;
            color: #093566;
            margin-top: 0;
            margin-bottom: 15px;
            font-weight: 750;
        }
        .content p {
            font-size: 13.5px;
            margin-bottom: 25px;
            color: #475569;
        }
        .otp-container {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: 850;
            letter-spacing: 8px;
            color: #093566;
            margin: 0;
        }
        .meta-info {
            font-size: 11.5px;
            color: #64748b;
            text-align: center;
            margin-top: 15px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-title">Badan Pengelola Keuangan Haji</div>
        </div>
        <div class="content">
            <h1>Halo {{ $userName }},</h1>
            <p>Kami menerima permintaan masuk atau pendaftaran ke akun Anda di <strong>Aplikasi Manajemen Agen Haji BPKH</strong>. Silakan gunakan kode verifikasi sekali pakai (OTP) berikut untuk menyelesaikan proses verifikasi email:</p>
            
            <div class="otp-container">
                <h2 class="otp-code">{{ $otpCode }}</h2>
                <div class="meta-info">Kode verifikasi ini berlaku selama <strong>10 menit</strong>. Harap jaga kerahasiaan kode ini dan jangan membagikannya kepada siapa pun.</div>
            </div>

            <p style="margin-bottom: 0;">Jika Anda tidak merasa melakukan tindakan ini, abaikan saja email ini atau hubungi BPKH Customer Support untuk bantuan.</p>
        </div>
        <div class="footer">
            <p>© 2026 Badan Pengelola Keuangan Haji (BPKH). All Rights Reserved.</p>
            <p>Jakarta, Indonesia</p>
        </div>
    </div>
</body>
</html>
