<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AyoHost</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .dashboard-container { display: flex; min-height: 100vh; padding-top: 80px; }
        .sidebar { width: 250px; background: rgba(15, 15, 30, 0.9); border-right: 1px solid rgba(255, 255, 255, 0.05); padding: 2rem; position: fixed; height: 100vh; top: 80px; }
        .main-content { flex: 1; margin-left: 250px; padding: 2rem; }
        .sidebar-menu a { display: block; color: var(--text-secondary); text-decoration: none; padding: 1rem; border-radius: 10px; margin-bottom: 0.5rem; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255, 255, 255, 0.05); color: white; }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: var(--card-bg); padding: 1.5rem; border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.05); }
        .stat-card h3 { font-size: 2rem; color: var(--accent); }
        .data-table { width: 100%; border-collapse: collapse; background: var(--card-bg); border-radius: 15px; overflow: hidden; margin-top: 1rem; }
        .data-table th, .data-table td { padding: 1rem; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .data-table th { background: rgba(255, 255, 255, 0.05); }
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; }
        .badge-active { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .badge-paid { background: rgba(52, 152, 219, 0.2); color: #3498db; }
        .section { display: none; }
        .section.active { display: block; }
        
        .btn-cancel {
            background: none; border: none; color: #e74c3c; cursor: pointer; 
            margin-left: 5px; font-size: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><i class="fa-solid fa-cloud-bolt"></i> AyoHost</div>
        <div class="nav-links">
            <span style="color: white; font-weight: 600;">{{ $user->name }}</span>
            <a href="{{ url('/') }}" style="margin-right: 15px;">Home</a>
            <a href="{{ url('/logout') }}">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-menu">
                <a href="javascript:void(0)" onclick="showSection('overview')" id="nav-overview" class="active"><i class="fa-solid fa-chart-pie"></i> Overview</a>
                <a href="javascript:void(0)" onclick="showSection('services')" id="nav-services"><i class="fa-solid fa-server"></i> Layanan Saya</a>
                <a href="javascript:void(0)" onclick="showSection('invoices')" id="nav-invoices"><i class="fa-solid fa-file-invoice-dollar"></i> Tagihan</a>
                <a href="javascript:void(0)" onclick="showSection('tickets')" id="nav-tickets"><i class="fa-solid fa-headset"></i> Support Ticket</a>
            </div>
            <div style="margin-top: 2rem;">
                <button onclick="window.location.href='{{ url('/') }}#pricing'" class="btn-purchase" style="font-size: 0.9rem;">+ Beli Layanan Baru</button>
            </div>
        </div>

        <div class="main-content">
            
            <!-- OVERVIEW (Dikembalikan) -->
            <div id="overview" class="section active">
                <h2 style="margin-bottom: 1.5rem;">Selamat Datang!</h2>
                <div class="stat-grid">
                    <div class="stat-card">
                        <p>Total Layanan</p>
                        <h3>{{ $orders->count() }}</h3>
                    </div>
                    <div class="stat-card">
                        <p>Tagihan Aktif</p>
                        <h3>Rp {{ number_format($orders->where('status', 'active')->sum('total'), 0, ',', '.') }}</h3>
                    </div>
                    <div class="stat-card">
                        <p>Tiket Terbuka</p>
                        <h3>{{ $tickets->where('status', 'open')->count() }}</h3>
                    </div>
                </div>
            </div>

            <!-- SERVICES -->
            <div id="services" class="section">
                <h2>Layanan Hosting Saya</h2>

                <!-- PESAN ALERT LAYANAN (Hanya muncul jika tab=services) -->
                @if(request('tab') == 'services')
                    @if(session('success'))
                    <div style="background: rgba(46, 204, 113, 0.2); color: #2ecc71; padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                    </div>
                    @endif
                    @if(session('error'))
                    <div style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
                        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                    @endif
                @endif


                <table class="data-table">
                    <thead><tr><th>Order ID</th><th>Paket</th><th>Domain</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->package->name }}</td>
                            <td>{{ $order->domain }}</td>
                            <td>
                                @if($order->status == 'active')
                                    <span class="badge badge-active">Aktif</span>
                                @else
                                    <span class="badge" style="background: #f1c40f; color: #000;">{{ $order->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($order->status != 'active')
                                <form action="{{ route('order.cancel', $order->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Batalkan pesanan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-cancel" title="Batalkan Pesanan">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5">Belum ada layanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- INVOICES -->
            <div id="invoices" class="section">
                <h2>Riwayat Tagihan</h2>

                <!-- PESAN ALERT TAGIHAN -->
                @if(session('success'))
                <div style="background: rgba(46, 204, 113, 0.2); color: #2ecc71; padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                </div>
                @endif

                <table class="data-table">
                    <thead><tr><th>No Invoice</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>INV-{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td>
                                @if($order->status == 'active')
                                    <span class="badge badge-paid">Lunas</span>
                                @else
                                    <span class="badge" style="background: rgba(241, 196, 15, 0.2); color: #f1c40f;">Menunggu Pembayaran</span>
                                    <br>
                                    <a href="https://wa.me/6283814720164?text=Halo%20Admin,%20saya%20ingin%20konfirmasi%20pembayaran%20untuk%20Order%20ID:%20%23{{ $order->id }}%0A(Domain:%20{{ $order->domain }})" target="_blank" style="color: #3498db; font-size: 0.8rem; text-decoration: underline; margin-top: 5px; display: inline-block;">
                                        <i class="fa-brands fa-whatsapp"></i> Konfirmasi
                                    </a>
                                    <form action="{{ route('order.cancel', $order->id) }}" method="POST" style="display:inline; margin-left: 10px;" onsubmit="return confirm('Batalkan pesanan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="source_tab" value="invoices">
                                        <button type="submit" style="background: none; border: none; color: #e74c3c; cursor: pointer; text-decoration: underline; font-size: 0.8rem;">
                                            <i class="fa fa-trash"></i> Batal
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4">Belum ada tagihan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- TICKETS -->
            <div id="tickets" class="section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2>Support Ticket</h2>
                    <button class="btn-purchase" style="width: auto;" onclick="openTicketModal()">Buat Tiket Baru</button>
                </div>
                <table class="data-table">
                    <thead><tr><th>ID</th><th>Subjek</th><th>Status</th><th>Tanggal</th></tr></thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td>#{{ $ticket->id }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td><span class="badge">{{ $ticket->status }}</span></td>
                            <td>{{ $ticket->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4">Belum ada tiket.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Ticket Modal -->
    <div id="ticket-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeTicketModal()">&times;</span>
            <h2>Buat Tiket Bantuan</h2>
            <div class="form-group">
                <label>Judul Masalah</label>
                <input type="text" id="ticket-subject" placeholder="Cth: Website tidak bisa diakses">
            </div>
            <div class="form-group">
                <label>Pesan</label>
                <textarea id="ticket-msg" rows="5" style="width: 100%; padding: 0.8rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: white;"></textarea>
            </div>
            <button class="btn-purchase" onclick="submitTicket()">Kirim Tiket</button>
        .badge-active { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .badge-paid { background: rgba(52, 152, 219, 0.2); color: #3498db; }
        .section { display: none; }
        .section.active { display: block; }
        
        .btn-cancel {
            background: none; border: none; color: #e74c3c; cursor: pointer; 
            margin-left: 5px; font-size: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><i class="fa-solid fa-cloud-bolt"></i> AyoHost</div>
        <div class="nav-links">
            <span style="color: white; font-weight: 600;">{{ $user->name }}</span>
            <a href="{{ url('/') }}" style="margin-right: 15px;">Home</a>
            <a href="{{ url('/logout') }}">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-menu">
                <a href="javascript:void(0)" onclick="showSection('overview')" id="nav-overview" class="active"><i class="fa-solid fa-chart-pie"></i> Overview</a>
                <a href="javascript:void(0)" onclick="showSection('services')" id="nav-services"><i class="fa-solid fa-server"></i> Layanan Saya</a>
                <a href="javascript:void(0)" onclick="showSection('invoices')" id="nav-invoices"><i class="fa-solid fa-file-invoice-dollar"></i> Tagihan</a>
                <a href="javascript:void(0)" onclick="showSection('tickets')" id="nav-tickets"><i class="fa-solid fa-headset"></i> Support Ticket</a>
            </div>
            <div style="margin-top: 2rem;">
                <button onclick="window.location.href='{{ url('/') }}#pricing'" class="btn-purchase" style="font-size: 0.9rem;">+ Beli Layanan Baru</button>
            </div>
        </div>

        <div class="main-content">
            
            <!-- OVERVIEW (Dikembalikan) -->
            <div id="overview" class="section active">
                <h2 style="margin-bottom: 1.5rem;">Selamat Datang!</h2>
                <div class="stat-grid">
                    <div class="stat-card">
                        <p>Total Layanan</p>
                        <h3>{{ $orders->count() }}</h3>
                    </div>
                    <div class="stat-card">
                        <p>Tagihan Aktif</p>
                        <h3>Rp {{ number_format($orders->where('status', 'active')->sum('total'), 0, ',', '.') }}</h3>
                    </div>
                    <div class="stat-card">
                        <p>Tiket Terbuka</p>
                        <h3>{{ $tickets->where('status', 'open')->count() }}</h3>
                    </div>
                </div>
            </div>

            <!-- SERVICES -->
            <div id="services" class="section">
                <h2>Layanan Hosting Saya</h2>

                <!-- PESAN ALERT LAYANAN (Hanya muncul jika tab=services) -->
                @if(request('tab') == 'services')
                    @if(session('success'))
                    <div style="background: rgba(46, 204, 113, 0.2); color: #2ecc71; padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                    </div>
                    @endif
                    @if(session('error'))
                    <div style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
                        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                    @endif
                @endif
                @if(session('error'))
                <div style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                </div>
                @endif


                <table class="data-table">
                    <thead><tr><th>Order ID</th><th>Paket</th><th>Domain</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->package->name }}</td>
                            <td>{{ $order->domain }}</td>
                            <td>
                                @if($order->status == 'active')
                                    <span class="badge badge-active">Aktif</span>
                                @else
                                    <span class="badge" style="background: #f1c40f; color: #000;">{{ $order->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($order->status != 'active')
                                <form action="{{ route('order.cancel', $order->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Batalkan pesanan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-cancel" title="Batalkan Pesanan">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5">Belum ada layanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- INVOICES -->
            <div id="invoices" class="section">
                <h2>Riwayat Tagihan</h2>

                <!-- PESAN ALERT TAGIHAN (Hanya muncul jika tab=invoices) -->
                @if(request('tab') == 'invoices')
                    @if(session('success'))
                    <div style="background: rgba(46, 204, 113, 0.2); color: #2ecc71; padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                    </div>
                    @endif
                    @if(session('error'))
                    <div style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
                        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                    @endif
                @endif

                <table class="data-table">
                    <thead><tr><th>No Invoice</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>INV-{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td>
                                @if($order->status == 'active')
                                    <span class="badge badge-paid">Lunas</span>
                                @else
                                    <span class="badge" style="background: rgba(241, 196, 15, 0.2); color: #f1c40f;">Menunggu Pembayaran</span>
                                    <br>
                                    <a href="https://wa.me/6283814720164?text=Halo%20Admin,%20saya%20ingin%20konfirmasi%20pembayaran%20untuk%20Order%20ID:%20%23{{ $order->id }}%0A(Domain:%20{{ $order->domain }})" target="_blank" style="color: #3498db; font-size: 0.8rem; text-decoration: underline; margin-top: 5px; display: inline-block;">
                                        <i class="fa-brands fa-whatsapp"></i> Konfirmasi
                                    </a>
                                    <form action="{{ route('order.cancel', $order->id) }}" method="POST" style="display:inline; margin-left: 10px;" onsubmit="return confirm('Batalkan pesanan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: none; border: none; color: #e74c3c; cursor: pointer; text-decoration: underline; font-size: 0.8rem;">
                                            <i class="fa fa-trash"></i> Batal
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4">Belum ada tagihan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- TICKETS -->
            <div id="tickets" class="section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2>Support Ticket</h2>
                    <button class="btn-purchase" style="width: auto;" onclick="openTicketModal()">Buat Tiket Baru</button>
                </div>
                <table class="data-table">
                    <thead><tr><th>ID</th><th>Subjek</th><th>Status</th><th>Tanggal</th></tr></thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td>#{{ $ticket->id }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td><span class="badge">{{ $ticket->status }}</span></td>
                            <td>{{ $ticket->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4">Belum ada tiket.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Ticket Modal -->
    <div id="ticket-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeTicketModal()">&times;</span>
            <h2>Buat Tiket Bantuan</h2>
            <div class="form-group">
                <label>Judul Masalah</label>
                <input type="text" id="ticket-subject" placeholder="Cth: Website tidak bisa diakses">
            </div>
            <div class="form-group">
                <label>Pesan</label>
                <textarea id="ticket-msg" rows="5" style="width: 100%; padding: 0.8rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: white;"></textarea>
            </div>
            <button class="btn-purchase" onclick="submitTicket()">Kirim Tiket</button>
        </div>
    </div>

    <script>
        function showSection(id) {
            // Sembunyikan semua section
            document.querySelectorAll('.section').forEach(el => el.classList.remove('active'));
            // Tampilkan section yang dipilih
            const target = document.getElementById(id);
            if(target) target.classList.add('active');

            // Reset menu active state
            document.querySelectorAll('.sidebar-menu a').forEach(el => el.classList.remove('active'));
            // Set menu yang diklik jadi active
            const nav = document.getElementById('nav-' + id);
            if(nav) nav.classList.add('active');
        }

        const tModal = document.getElementById('ticket-modal');
        function openTicketModal() { tModal.style.display = 'flex'; }
        function closeTicketModal() { tModal.style.display = 'none'; }
        
        function submitTicket() {
            const subj = document.getElementById('ticket-subject').value;
            const msg = document.getElementById('ticket-msg').value;

            if(!subj || !msg) return alert("Mohon lengkapi form.");

            fetch('{{ url("/tickets") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ subject: subj, message: msg })
            }).then(res => res.json()).then(data => {
                if(data.success) { alert("Tiket Terkirim!"); location.reload(); }
            });
        }

        // AUTO-SWITCH TAB BERDASARKAN URL PARAMETER (?tab=services)
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if(tabParam) {
                showSection(tabParam);
            }
        });
    </script>
</body>
</html>