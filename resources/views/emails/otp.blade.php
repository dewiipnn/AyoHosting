<!DOCTYPE html>
<html>
<head>
    <title>Kode Verifikasi</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;">
        <h2 style="color: #6c5ce7;">AyoHost Verification</h2>
        <p>Halo,</p>
        <p>Terima kasih telah mendaftar di AyoHost. Berikut adalah kode verifikasi (OTP) Anda:</p>
        
        <div style="background: #f0f2f5; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0; border-radius: 5px;">
            {{ $otp }}
        </div>
        
        <p>Kode ini berlaku selama 10 menit. Jangan berikan kode ini kepada siapapun.</p>
        <p>Terima kasih,<br>Tim AyoHost</p>
    </div>
</body>
</html>
