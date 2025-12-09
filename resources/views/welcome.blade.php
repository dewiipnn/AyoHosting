<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AyoHosting - Hosting Web Premium</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="background-globes">
        <div class="globe globe-1"></div>
        <div class="globe globe-2"></div>
    </div>

    <nav class="navbar">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="AyoHosting Logo" style="height: 45px;">
        </div>
        <div class="nav-links">
            <a href="#home">Beranda</a>
            <a href="#pricing">Paket</a>
            <a href="#features">Fitur</a>
            <a href="#support">Bantuan</a>
        </div>
        @auth
            @if(Auth::user()->role === 'admin')
            <button class="btn-login" onclick="window.location.href='{{ url('/admin') }}'"><i class="fa-solid fa-user-shield"></i> Admin Panel</button>
            @else
            <button class="btn-login" onclick="window.location.href='{{ url('/dashboard') }}'"><i class="fa-solid fa-user"></i> Dashboard</button>
            @endif
        @else
        <button class="btn-login" onclick="window.location.href='{{ url('/login') }}'">Login</button>
        @endauth
    </nav>

    <header id="home" class="hero">
        <div class="hero-content">
            <h1>Hosting Tercepat<br>untuk <span class="gradient-text">Masa Depan</span></h1>
            <p>Bangun keberadaan digital Anda dengan infrastruktur cloud premium kami. Cepat, aman, dan dapat
                diandalkan.</p>

            <div class="domain-search">
                <input type="text" placeholder="Cari nama domain impianmu..." id="domain-input">
                <button onclick="checkDomain()">Cari Domain</button>
            </div>
            <div id="domain-result" class="domain-result"></div>
        </div>
        <div class="hero-image" style="display: flex; justify-content: center; align-items: center;">
            <img src="{{ asset('images/hero.png') }}" alt="Cloud Hosting Hero" style="max-width: 120%; width: 500px; height: auto; animation: floating 3s ease-in-out infinite;">
        </div>
        <style>
            @keyframes floating {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
                100% { transform: translateY(0px); }
            }
        </style>
    </header>

    <section id="pricing" class="pricing-section">
        <h2>Pilih Paket Hosting Anda</h2>
        <p class="subtitle">Solusi untuk setiap skala bisnis</p>

        <div class="pricing-cards">
            <!-- Dynamic Loop with Custom Middle Card -->
            @foreach($packages as $pkg)
                @if($loop->iteration == 2)
                    <!-- Kartu Tengah (Promo/Info) -->
                    <div class="card popular" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); cursor: default; transform: scale(1.05); z-index: 10;">
                        <div class="popular-tag">Rekomendasi</div>
                        <div class="card-header">
                            <h3 style="font-size: 1.8rem; margin-bottom: 1rem;">Bingung Memilih?</h3>
                            <div class="price" style="font-size: 1.2rem; margin-bottom: 2rem; color: rgba(255,255,255,0.9);">
                                Kami siap membantu Anda menemukan solusi terbaik.
                            </div>
                        </div>
                        <ul class="features" style="text-align: center; margin-bottom: 2rem;">
                            <li style="justify-content: center;">Konsultasi Gratis</li>
                            <li style="justify-content: center;">Optimasi Khusus</li>
                            <li style="justify-content: center;">Diskon Spesial</li>
                        </ul>
                        <a href="https://wa.me/6283814720164?text=Halo%20Admin,%20saya%20butuh%20konsultasi%20hosting." target="_blank" class="btn-select" style="background: white; color: #764ba2; text-decoration: none; display: inline-block; width: 100%; text-align: center;">Hubungi Kami</a>
                    </div>
                @else
                    <!-- Kartu Paket Normal -->
                    <div class="card" onclick="selectPlan({{ $pkg->id }}, '{{ $pkg->name }}', {{ $pkg->price }})">
                        <div class="card-header">
                            <h3>{{ $pkg->name }}</h3>
                            <div class="price">Rp {{ number_format($pkg->price, 0, ',', '.') }}<span>/bulan</span></div>
                        </div>
                        <ul class="features">
                            <li><i class="fa-solid fa-check"></i> {{ $pkg->websites }} Website</li>
                            <li><i class="fa-solid fa-check"></i> {{ $pkg->storage }}</li>
                            @if($pkg->description)
                                @foreach(explode(',', $pkg->description) as $feature)
                                <li><i class="fa-solid fa-check"></i> {{ trim($feature) }}</li>
                                @endforeach
                            @else
                                <li><i class="fa-solid fa-check"></i> Gratis SSL</li>
                                <li><i class="fa-solid fa-check"></i> 24/7 Support</li>
                            @endif
                        </ul>
                        <button class="btn-select">Pilih Paket</button>
                    </div>
                @endif
            @endforeach
        </div>
    </section>

    <!-- Purchase Modal Simulation -->
    <div id="checkout-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2>Konfirmasi Pembelian</h2>
            <div class="checkout-details">
                <input type="hidden" id="selected-pkg-id">
                <p>Paket: <span id="modal-plan-name" class="highlight"></span></p>
                <p>Harga: <span id="modal-plan-price" class="highlight"></span></p>

                <div class="form-group" style="display:none;" id="login-alert">
                    <p style="color: red; margin-bottom:10px;">Silakan Login dahulu untuk melanjutkan checkout.</p>
                    <a href="{{ url('/login') }}" class="btn-purchase" style="display:inline-block; margin-top:0;">Login Sekarang</a>
                </div>

                @auth
                <div id="checkout-form-area">
                    <p style="margin-bottom:15px; color:#aaa;">Checkout sebagai: <b style="color:white;">{{ Auth::user()->name }}</b></p>
                    <div class="form-group">
                        <label>Domain</label>
                        <input type="text" id="domain-order" required placeholder="example.com">
                    </div>
                    <button class="btn-purchase" onclick="processPurchase()">Lanjut ke WhatsApp <i class="fa-brands fa-whatsapp"></i></button>
                </div>
                @endauth
                
                @guest
                <script>document.getElementById('login-alert').style.display='block';</script>
                @endguest
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="features-grid-section">
        <h2>Mengapa AyoHosting?</h2>
        <div class="features-grid">
            <div class="feature-item">
                <i class="fa-solid fa-rocket"></i>
                <h4>Super Cepat</h4>
                <p>Server menggunakan teknologi LiteSpeed dan NVMe SSD terbaru.</p>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-shield-halved"></i>
                <h4>Aman Terjamin</h4>
                <p>Proteksi DDoS, Malware Scanner, dan gratis SSL untuk setiap domain.</p>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-headset"></i>
                <h4>Support 24/7</h4>
                <p>Tim teknis kami siap membantu Anda kapanpun dibutuhkan.</p>
            </div>
        </div>
    </section>

    <footer id="support">
        <p>&copy; 2025 AyoHosting. Dibuat untuk tugas Komputasi Awan.</p>
    </footer>

    <!-- Meta CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Script JS -->
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        // Override script.js checkout functions for Laravel
        let selectedPkg = {};
        
        function selectPlan(id, name, price) {
            selectedPkg = {id, name, price};
            document.getElementById('selected-pkg-id').value = id;
            document.getElementById('modal-plan-name').innerText = name;
            document.getElementById('modal-plan-price').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(price);
            document.getElementById('checkout-modal').style.display = 'flex';

            // Auto fill domain if searched
            const savedDomain = localStorage.getItem('selected_domain');
            const domainInput = document.getElementById('domain-order');
            if(savedDomain && domainInput) {
                domainInput.value = savedDomain;
            }
        }

        function closeModal() {
            document.getElementById('checkout-modal').style.display = 'none';
        }

        function processPurchase() {
            const domainVal = document.getElementById('domain-order').value;
            if(!domainVal) return alert("Masukkan nama domain!");

            const btn = document.querySelector('#checkout-form-area .btn-purchase');
            // btn.innerText = "Memproses..."; // Matikan kosmetik dulu biar ga bingung
            // btn.disabled = true;

            fetch('{{ url("/checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    package_id: selectedPkg.id,
                    price: selectedPkg.price,
                    domain: domainVal
                })
            }).then(res => res.json()).then(serverData => {
                console.log("SERVER DATA:", serverData); // Cek console

            if(serverData.success) {
                // 1. Ambil Data
                var myOrderId = serverData.order_id || "PENDING";
                var myAdmin   = "6283814720164"; // Nomor Admin

                // 2. Susun Pesan (Langsung emoji di string agar encoding otomatis benar)
                var rawText = "Halo Admin, order baru masuk! 🚀\n\n";
                rawText += "✅ *Order ID:* " + myOrderId + "\n";
                rawText += "📦 *Paket:* " + selectedPkg.name + "\n";
                rawText += "🌐 *Domain:* " + domainVal + "\n";
                rawText += "💸 *Total:* Rp " + new Intl.NumberFormat('id-ID').format(selectedPkg.price) + "\n\n";
                rawText += "Mohon info pembayaran. Terima kasih!";

                // 3. Generate URL (Gunakan api.whatsapp.com yang lebih stabil untuk teks panjang)
                var finalUrl = "https://api.whatsapp.com/send?phone=" + myAdmin + "&text=" + encodeURIComponent(rawText);

                // 4. Eksekusi
                window.open(finalUrl, '_blank');

                // Redirect ke Dashboard
                setTimeout(function() {
                    window.location.href = "{{ url('/dashboard') }}";
                }, 2000); 
            }
            }).catch(err => {
                console.error(err);
                alert("Error: " + err.message);
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>