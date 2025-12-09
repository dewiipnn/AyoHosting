// --- INITIALIZATION ---
document.addEventListener('DOMContentLoaded', () => {
    checkAuth();
    loadPackages();
});

// --- AUTH CHECK ---
function checkAuth() {
    const user = db.getCurrentUser();
    const loginBtn = document.querySelector('.btn-login');

    if (!loginBtn) return; // Guard clause if button not found

    if (user) {
        if (user.role === 'admin') {
            loginBtn.innerHTML = '<i class="fa-solid fa-user-shield"></i> Dashboard Admin';
            loginBtn.onclick = () => window.location.href = '/admin';
        } else {
            loginBtn.innerHTML = '<i class="fa-solid fa-user"></i> Dashboard Client';
            loginBtn.onclick = () => window.location.href = '/dashboard';
        }
    } else {
        loginBtn.innerText = 'Login';
        loginBtn.onclick = () => window.location.href = '/login';
    }
}

// --- LOAD PACKAGES DYNAMICALLY ---
function loadPackages() {
    const pkgs = db.getPackages();
    const container = document.querySelector('.pricing-cards');
    if (!container) return;

    container.innerHTML = pkgs.map(p => {
        const isPop = p.name === 'Pro'; // Just for styling preference
        return `
            <div class="card ${isPop ? 'popular' : ''}" onclick="initPurchase(${p.id}, '${p.name}', ${p.price})">
                ${isPop ? '<div class="popular-tag">Paling Laris</div>' : ''}
                <div class="card-header">
                    <h3>${p.name}</h3>
                    <div class="price">${formatRupiah(p.price)}<span>/bulan</span></div>
                </div>
                <ul class="features">
                    <li><i class="fa-solid fa-check"></i> ${p.websites} Website</li>
                    <li><i class="fa-solid fa-check"></i> ${p.storage}</li>
                    <li><i class="fa-solid fa-check"></i> Gratis SSL</li>
                    <li><i class="fa-solid fa-check"></i> 24/7 Support</li>
                </ul>
                <button class="btn-select">Pilih Paket</button>
            </div>
        `;
    }).join('');
}

function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
}

// --- DOMAIN CHECKER ---
function checkDomain() {
    const input = document.getElementById('domain-input');
    const resultDiv = document.getElementById('domain-result');
    const domain = input.value.trim();

    if (!domain) {
        resultDiv.innerHTML = '<span style="color: #ff7675;">Silakan masukkan nama domain.</span>';
        return;
    }

    resultDiv.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengecek ketersediaan...';

    setTimeout(() => {
        const isAvailable = Math.random() > 0.3;

        if (isAvailable && domain.includes('.')) {
            resultDiv.innerHTML = `<span style="color: var(--accent);"><i class="fa-solid fa-check"></i> Selamat! <b>${domain}</b> tersedia!</span>`;
            // Keep domain for checkout
            localStorage.setItem('selected_domain', domain);
        } else if (!domain.includes('.')) {
            resultDiv.innerHTML = '<span style="color: #ff7675;">Format domain salah (contoh: mywebsite.com)</span>';
        } else {
            resultDiv.innerHTML = `<span style="color: #ff7675;"><i class="fa-solid fa-xmark"></i> Maaf, <b>${domain}</b> sudah terpakai.</span>`;
        }
    }, 1000);
}

// --- CHECKOUT LOGIC ---
let selectedPackage = null;

function initPurchase(id, name, price) {
    const user = db.getCurrentUser();
    if (!user) {
        alert('Silakan login terlebih dahulu untuk membeli paket.');
        window.location.href = '/login';
        return;
    }

    selectedPackage = { id, name, price };
    openModal();
}

// Modal Elements
const modal = document.getElementById('checkout-modal');
const modalPlanName = document.getElementById('modal-plan-name');
const modalPlanPrice = document.getElementById('modal-plan-price');

function openModal() {
    modal.style.display = "flex";
    modalPlanName.innerText = selectedPackage.name;
    modalPlanPrice.innerText = formatRupiah(selectedPackage.price) + " / bulan";

    // Auto fill user
    const user = db.getCurrentUser();
    if (user) {
        // Assuming first input is name, second is email (from original HTML structure)
        const inputs = document.querySelectorAll('.checkout-details input');
        if (inputs[0]) inputs[0].value = user.name;
        if (inputs[1]) inputs[1].value = user.email;
    }
}

function closeModal() {
    modal.style.display = "none";
}

window.onclick = function (event) {
    if (event.target == modal) {
        closeModal();
    }
}

// --- PAYMENT SIMULATION (QRIS/E-WALLET) ---
function processPurchase() {
    const btn = document.querySelector('.btn-purchase');

    // CLOSE Checkout Form
    closeModal();

    // OPEN Payment Gateway Modal (Created dynamically)
    showPaymentGateway();
}

function showPaymentGateway() {
    // Check if gateway exists, if not create
    if (!document.getElementById('gateway-modal')) {
        createGatewayModal();
    }
    document.getElementById('gateway-modal').style.display = 'flex';

    // Simulate auto-check status
    startPaymentTimer();
}

function createGatewayModal() {
    const modalHtml = `
    <div id="gateway-modal" class="modal" style="z-index: 9999;">
        <div class="modal-content" style="background: white; color: #333; text-align: center; max-width: 400px;">
            <div style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/QRIS_logo.svg/1200px-QRIS_logo.svg.png" style="height: 30px;">
            </div>
            
            <h3>Scan untuk Membayar</h3>
            <p style="font-size: 0.9rem; color: #666;">Total: <b style="color: var(--primary); font-size: 1.2rem;">${modalPlanPrice.innerText}</b></p>
            
            <div style="margin: 20px 0; position: relative;">
                <!-- QR CODE MOCK -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=SimulasiPembayaranAyoHost" style="border: 2px solid #eee; padding: 10px; border-radius: 10px;">
                <div id="scan-overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.9); display:none; flex-direction:column; justify-content:center; align-items:center;">
                    <i class="fa-solid fa-circle-check" style="font-size: 3rem; color: #2ecc71; margin-bottom: 10px;"></i>
                    <h3 style="color: #2ecc71;">Pembayaran Berhasil!</h3>
                </div>
            </div>

            <p style="font-size: 0.8rem; color: #888;">ShopeePay / GoPay / OVO / Dana</p>
            <p style="font-size: 0.8rem; color: #888;">Menunggu pembayaran... <span id="timer">02:00</span></p>

            <button onclick="simulateScan()" class="btn-purchase" style="margin-top: 15px;">Simulasikan Bayar (Klik Ini)</button>
        </div>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function simulateScan() {
    const overlay = document.getElementById('scan-overlay');
    overlay.style.display = 'flex';

    document.querySelector('#gateway-modal button').style.display = 'none';

    setTimeout(() => {
        // ACTUAL DB SAVE (Moved from processPurchase)
        const user = db.getCurrentUser();
        const domain = localStorage.getItem('selected_domain') || 'domain-pending.com';

        db.createOrder(user.id, selectedPackage.id, domain, 'monthly', selectedPackage.price);

        document.getElementById('gateway-modal').style.display = 'none';

        // Show Invoice Notification
        showInvoiceToast();

        // Redirect
        setTimeout(() => window.location.href = '/dashboard', 3000);
    }, 2000);
}

function startPaymentTimer() {
    // Just visual timer
}

function showInvoiceToast() {
    const toastHtml = `
    <div id="invoice-toast" class="toast show" style="background: #2d3436; border-left: 5px solid #00b894;">
        <i class="fa-solid fa-envelope-circle-check" style="font-size: 2rem; color: #00b894;"></i>
        <div>
            <h4 style="color: white;">Invoice Terkirim!</h4>
            <p style="color: #dfe6e9; font-size: 0.9rem;">Cek email Anda untuk bukti pembayaran.</p>
        </div>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', toastHtml);
}

function showToast() {
    const toast = document.getElementById("success-toast");
    toast.className = "toast show";
    setTimeout(function () { toast.className = toast.className.replace("show", ""); }, 3000);
}
