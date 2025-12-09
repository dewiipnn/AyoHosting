<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - AyoHost</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .dashboard-container { display: flex; min-height: 100vh; padding-top: 80px; }
        .sidebar { width: 250px; background: #2d3436; padding: 2rem; position: fixed; height: 100vh; top: 0; left: 0; z-index: 100; }
        .main-content { flex: 1; margin-left: 250px; padding: 2rem; }
        .sidebar h3 { color: white; margin-bottom: 2rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 1rem; }
        .sidebar a { display: block; color: #dfe6e9; text-decoration: none; padding: 1rem; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: var(--primary); color: white; }
        .data-table { width: 100%; border-collapse: collapse; background: var(--card-bg); border-radius: 10px; overflow: hidden; }
        .data-table th, .data-table td { padding: 1rem; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .data-table th { background: rgba(0, 0, 0, 0.2); }
        input, select { padding: 0.5rem; border-radius: 5px; border: 1px solid #ccc; background: white; color: black; }
        .modal-content .form-group input { color: #333 !important; background: #fff !important; border: 1px solid #ccc !important; }
        .section { display: none; }
        .section.active { display: block; }
    </style>
</head>
<body style="background: #f0f2f5; color: #333;">

    <div class="sidebar">
        <h3><i class="fa-solid fa-cloud-bolt"></i> Admin Panel</h3>
        <a href="#" onclick="showSection('packages')" id="nav-packages" class="active">Manajemen Paket</a>
        <a href="#" onclick="showSection('orders')" id="nav-orders">Order Masuk</a>
        <a href="{{ url('/logout') }}" style="margin-top: 50px; color: #ff7675;">Logout</a>
    </div>

    <div class="main-content">
        <!-- PACKAGES -->
        <div id="packages" class="section active">
            <div style="display: flex; justify-content: space-between; margin-bottom: 2rem;">
                <h2>Hosting Packages</h2>
                <button class="btn-purchase" style="width: auto; background: var(--primary);" onclick="openPkgModal()">+ Tambah Paket</button>
            </div>
            <table class="data-table" style="background: white; color: #333;">
                <thead><tr><th>Nama Paket</th><th>Harga</th><th>Storage</th><th>Website</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($packages as $pkg)
                    <tr>
                        <td><b>{{ $pkg->name }}</b></td>
                        <td>Rp {{ number_format($pkg->price, 0, ',', '.') }}</td>
                        <td>{{ $pkg->storage }}</td>
                        <td>{{ $pkg->websites }}</td>
                        <td>
                            <button onclick='editPackage(@json($pkg))' style="cursor:pointer; color: blue; border:none; background:none;"><i class="fa fa-edit"></i></button>
                            <button onclick="deletePackage({{ $pkg->id }})" style="cursor:pointer; color: red; border:none; background:none;"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ORDERS -->
        <div id="orders" class="section">
            <h2>Order Masuk</h2>
            <table class="data-table" style="background: white; color: #333;">
                <thead><tr><th>Order ID</th><th>User Email</th><th>Paket</th><th>Domain</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user->email }}</td>
                        <td>{{ $order->package->name }}</td>
                        <td>{{ $order->domain }}</td>
                        <td><span style="color:green; font-weight:bold;">{{ $order->status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Package Modal -->
    <div id="pkg-modal" class="modal">
        <div class="modal-content" style="background: white; color: #333;">
            <span class="close-modal" onclick="closePkgModal()" style="color: #333;">&times;</span>
            <h2 id="modal-title">Edit Paket</h2>
            <form onsubmit="savePackage(event)">
                <input type="hidden" id="pkg-id">
                <div class="form-group"><label style="color:#333">Nama Paket</label><input type="text" id="pkg-name" required></div>
                <div class="form-group"><label style="color:#333">Harga</label><input type="number" id="pkg-price" required></div>
                <div class="form-group"><label style="color:#333">Storage</label><input type="text" id="pkg-storage" required></div>
                <div class="form-group"><label style="color:#333">Website Slot</label><input type="text" id="pkg-websites" required></div>
                <div class="form-group"><label style="color:#333">Spesifikasi (Pisahkan dengan koma)</label>
                    <textarea id="pkg-description" rows="4" style="width:100%; border:1px solid #ccc; padding:0.5rem; background:white; color:black;" placeholder="Contoh: Gratis SSL, 24/7 Support, Unlimited Bandwidth"></textarea>
                </div>
                <button type="submit" class="btn-purchase">Simpan</button>
            </form>
        </div>
    </div>

    <script>
        function showSection(id) {
            document.querySelectorAll('.section').forEach(el => el.classList.remove('active'));
            document.getElementById(id).classList.add('active');
            document.querySelectorAll('.sidebar a').forEach(el => el.classList.remove('active'));
            document.getElementById('nav-' + id).classList.add('active');
        }

        const modal = document.getElementById('pkg-modal');
        function openPkgModal() {
            modal.style.display = 'flex';
            document.getElementById('pkg-id').value = '';
            document.getElementById('pkg-name').value = '';
            document.getElementById('pkg-price').value = '';
            document.getElementById('pkg-storage').value = '';
            document.getElementById('pkg-websites').value = '';
            document.getElementById('pkg-description').value = '';
            document.getElementById('modal-title').innerText = 'Tambah Paket';
        }
        function closePkgModal() { modal.style.display = 'none'; }

        function editPackage(pkg) {
            openPkgModal();
            document.getElementById('pkg-id').value = pkg.id;
            document.getElementById('pkg-name').value = pkg.name;
            document.getElementById('pkg-price').value = pkg.price;
            document.getElementById('pkg-storage').value = pkg.storage;
            document.getElementById('pkg-websites').value = pkg.websites;
            document.getElementById('pkg-description').value = pkg.description || '';
            document.getElementById('modal-title').innerText = 'Edit Paket';
        }

        function savePackage(e) {
            e.preventDefault();
            const id = document.getElementById('pkg-id').value;
            const data = {
                name: document.getElementById('pkg-name').value,
                price: document.getElementById('pkg-price').value,
                storage: document.getElementById('pkg-storage').value,
                websites: document.getElementById('pkg-websites').value,
                description: document.getElementById('pkg-description').value
            };

            const url = id ? '/admin/packages/' + id : '/admin/packages';
            const method = id ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify(data)
            }).then(res => res.json()).then(data => {
                if(data.success) { location.reload(); }
            });
        }

        function deletePackage(id) {
            if(!confirm('Yakin hapus?')) return;
            fetch('/admin/packages/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(res => res.json()).then(data => { if(data.success) location.reload(); });
        }
    </script>
</body>
</html>