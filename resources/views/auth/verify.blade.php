<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - AyoHost</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .auth-container { min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .auth-box { background: rgba(15, 15, 30, 0.95); padding: 2.5rem; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.1); width: 100%; max-width: 400px; text-align: center; }
        .otp-inputs { display: flex; justify-content: center; gap: 10px; margin: 20px 0; }
        .otp-inputs input { width: 50px; height: 50px; text-align: center; font-size: 1.5rem; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.05); color: white; font-weight: bold; }
        .otp-inputs input:focus { border-color: var(--primary); outline: none; }
    </style>
</head>

<body>
    <div class="background-globes">
        <div class="globe globe-1"></div>
        <div class="globe globe-2"></div>
    </div>

    <div class="auth-container">
        <div class="auth-box">
            <div style="font-size: 3rem; color: var(--accent); margin-bottom: 1rem;">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>
            <h2>Verifikasi Email</h2>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Kami telah mengirimkan kode OTP ke <b>{{ $email ?? 'email Anda' }}</b>
            </p>

            <form id="verify-form" action="{{ url('/verify') }}" method="POST">
                @csrf
                <div class="otp-inputs">
                    <input type="text" name="otp[]" maxlength="1" oninput="this.nextElementSibling?.focus()" required>
                    <input type="text" name="otp[]" maxlength="1" oninput="this.nextElementSibling?.focus()" required>
                    <input type="text" name="otp[]" maxlength="1" oninput="this.nextElementSibling?.focus()" required>
                    <input type="text" name="otp[]" maxlength="1" oninput="this.nextElementSibling?.focus()" required>
                    <input type="text" name="otp[]" maxlength="1" oninput="this.nextElementSibling?.focus()" required>
                    <input type="text" name="otp[]" maxlength="1" required>
                </div>

                <button type="button" onclick="submitVerify()" class="btn-purchase">Verifikasi Sekarang</button>
            </form>

            <p id="error-msg" style="color: #ff7675; display: none; margin-top: 10px;"></p>
        </div>
    </div>

    <script>
        function submitVerify() {
            const form = document.getElementById('verify-form');
            const btn = document.querySelector('.btn-purchase');
            const errorMsg = document.getElementById('error-msg');
            
            errorMsg.style.display = 'none';
            btn.innerText = "Memverifikasi...";
            
            const formData = new FormData(form);

            fetch("{{ url('/verify') }}", {
                method: "POST",
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert("Verifikasi Berhasil!");
                    window.location.href = data.redirect;
                } else {
                    errorMsg.innerText = data.message || "Kode OTP Salah";
                    errorMsg.style.display = 'block';
                    btn.innerText = "Verifikasi Sekarang";
                }
            })
            .catch(err => {
                console.error(err);
                errorMsg.innerText = "Terjadi kesalahan sistem.";
                errorMsg.style.display = 'block';
                btn.innerText = "Verifikasi Sekarang";
            });
        }
    </script>
</body>
</html>