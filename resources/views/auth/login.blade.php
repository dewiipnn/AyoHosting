<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AyoHost</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .auth-container { min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .auth-box { background: rgba(15, 15, 30, 0.95); padding: 2.5rem; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.1); width: 100%; max-width: 400px; box-shadow: 0 0 50px rgba(108, 92, 231, 0.2); text-align: center; }
        .auth-box h2 { margin-bottom: 2rem; font-size: 2rem; color: white; }
        .auth-switch { margin-bottom: 2rem; display: flex; justify-content: space-around; border-bottom: 2px solid rgba(255, 255, 255, 0.1); }
        .auth-switch button { background: none; border: none; color: var(--text-secondary); padding: 10px 20px; font-size: 1rem; cursor: pointer; font-weight: 600; }
        .auth-switch button.active { color: var(--accent); border-bottom: 2px solid var(--accent); margin-bottom: -2px; }
        .error-msg { color: #ff7675; font-size: 0.9rem; margin-bottom: 1rem; display: block; }
    </style>
</head>

<body>
    <div class="background-globes">
        <div class="globe globe-1"></div>
        <div class="globe globe-2"></div>
    </div>

    <div class="auth-container">
        <div class="auth-box">
            <div class="logo" style="justify-content: center; margin-bottom: 20px;">
                <i class="fa-solid fa-cloud-bolt"></i> AyoHost
            </div>

            <div class="auth-switch">
                <button id="tab-login" class="active" onclick="switchTab('login')">Masuk</button>
                <button id="tab-register" onclick="switchTab('register')">Daftar</button>
            </div>

            @if($errors->any())
                <div class="error-msg">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Login Form -->
            <form id="login-form" action="{{ url('/login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-purchase">Masuk</button>
            </form>

            <!-- Register Form -->
            <form id="register-form" action="{{ url('/register') }}" method="POST" style="display: none;">
                @csrf
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-purchase">Daftar Sekarang</button>
            </form>

            <p style="margin-top: 1.5rem; font-size: 0.9rem; color: #b2bec3;">
                <a href="{{ url('/') }}" style="color: white; text-decoration: none;">&larr; Kembali ke Beranda</a>
            </p>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            if (tab === 'login') {
                document.getElementById('login-form').style.display = 'block';
                document.getElementById('register-form').style.display = 'none';
                document.getElementById('tab-login').classList.add('active');
                document.getElementById('tab-register').classList.remove('active');
            } else {
                document.getElementById('login-form').style.display = 'none';
                document.getElementById('register-form').style.display = 'block';
                document.getElementById('tab-login').classList.remove('active');
                document.getElementById('tab-register').classList.add('active');
            }
        }
    </script>
</body>
</html>