/**
 * SIMULATED DATABASE & BACKEND (NebulaHost / AyoHost)
 * Uses LocalStorage to persist data like a real database.
 */

const DB_KEYS = {
    USERS: 'ayohost_users',
    PACKAGES: 'ayohost_packages',
    ORDERS: 'ayohost_orders',
    INVOICES: 'ayohost_invoices',
    TICKETS: 'ayohost_tickets',
    CURRENT_USER: 'ayohost_current_user'
};

// --- INITIAL SEED DATA ---
const SEED_PACKAGES = [
    { id: 1, name: 'Starter', price: 15000, storage: '10 GB SSD', websites: 1, type: 'shared' },
    { id: 2, name: 'Pro', price: 45000, storage: '50 GB NVMe', websites: 5, type: 'shared' },
    { id: 3, name: 'Business', price: 90000, storage: 'Unlimited NVMe', websites: 'Unlimited', type: 'cloud' }
];

const SEED_ADMIN = {
    id: 'admin_001',
    name: 'Administrator',
    email: 'admin@ayohost.com',
    password: 'admin',
    role: 'admin'
};

// --- DATABASE MANAGER ---
const db = {
    // Generic Helper
    get: (key) => JSON.parse(localStorage.getItem(key) || '[]'),
    save: (key, data) => localStorage.setItem(key, JSON.stringify(data)),

    // Initialization
    init: () => {
        // Packages Check
        const currentPkgs = db.get(DB_KEYS.PACKAGES);
        if (!currentPkgs || currentPkgs.length === 0) {
            console.log("Restoring Default Packages...");
            db.save(DB_KEYS.PACKAGES, SEED_PACKAGES);
        }

        // Users Check
        let users = db.get(DB_KEYS.USERS);
        let changed = false;

        // Ensure Admin Exists (by email)
        if (!users.find(u => u.email === SEED_ADMIN.email)) {
            users.push(SEED_ADMIN);
            changed = true;
        }

        if (changed || users.length === 0) {
            db.save(DB_KEYS.USERS, users);
        }
    },

    // UTILITY: Call this from console `db.reset()` to wipe everything and start fresh
    reset: () => {
        localStorage.clear();
        window.location.reload();
    },

    // User Methods
    findUser: (email, password) => {
        const users = db.get(DB_KEYS.USERS);
        return users.find(u => u.email === email && u.password === password);
    },
    registerUser: (name, email, password) => {
        const users = db.get(DB_KEYS.USERS);
        if (users.find(u => u.email === email)) return { success: false, message: 'Email already registered' };

        const newUser = {
            id: 'user_' + Date.now(),
            name, email, password, role: 'customer',
            joined_at: new Date().toISOString()
        };
        users.push(newUser);
        db.save(DB_KEYS.USERS, users);
        return { success: true, user: newUser };
    },

    // Session Methods
    login: (user) => localStorage.setItem(DB_KEYS.CURRENT_USER, JSON.stringify(user)),
    logout: () => localStorage.removeItem(DB_KEYS.CURRENT_USER),
    getCurrentUser: () => JSON.parse(localStorage.getItem(DB_KEYS.CURRENT_USER)),

    // Package Methods
    getPackages: () => db.get(DB_KEYS.PACKAGES),
    savePackage: (pkg) => {
        const packages = db.get(DB_KEYS.PACKAGES);
        if (pkg.id) {
            // Update - Use loose comparison for ID to catch string/number mismatches
            const index = packages.findIndex(p => p.id == pkg.id);
            if (index !== -1) {
                // Merge with existing to keep other properties (like type)
                packages[index] = { ...packages[index], ...pkg };
            }
        } else {
            // Create
            pkg.id = Date.now();
            // Default type if missing
            if (!pkg.type) pkg.type = 'shared';
            packages.push(pkg);
        }
        db.save(DB_KEYS.PACKAGES, packages);
    },
    deletePackage: (id) => {
        const packages = db.get(DB_KEYS.PACKAGES).filter(p => p.id !== id);
        db.save(DB_KEYS.PACKAGES, packages);
    },

    // Order & Invoice Methods
    createOrder: (userId, packageId, domain, period, total) => {
        const orders = db.get(DB_KEYS.ORDERS);
        const invoices = db.get(DB_KEYS.INVOICES);

        const orderId = 'ORD-' + Date.now();
        const invoiceId = 'INV-' + Date.now();

        const newOrder = {
            id: orderId,
            userId, packageId, domain, period, total,
            status: 'active',
            created_at: new Date().toISOString()
        };

        const newInvoice = {
            id: invoiceId,
            orderId, userId, total,
            status: 'paid', // Simulating instant payment
            date: new Date().toISOString()
        };

        orders.push(newOrder);
        invoices.push(newInvoice);

        db.save(DB_KEYS.ORDERS, orders);
        db.save(DB_KEYS.INVOICES, invoices);

        return newOrder;
    },
    getUserOrders: (userId) => {
        const orders = db.get(DB_KEYS.ORDERS);
        const pkgs = db.get(DB_KEYS.PACKAGES);
        return orders.filter(o => o.userId === userId).map(o => {
            const p = pkgs.find(pkg => pkg.id == o.packageId) || { name: 'Unknown Package' };
            return { ...o, packageName: p.name };
        });
    },
    getAllOrders: () => {
        const orders = db.get(DB_KEYS.ORDERS);
        const users = db.get(DB_KEYS.USERS);
        return orders.map(o => {
            const u = users.find(user => user.id === o.userId) || { name: 'Unknown', email: 'Unknown' };
            return { ...o, userEmail: u.email };
        });
    },

    // Support Tickets
    createTicket: (userId, subject, message) => {
        const tickets = db.get(DB_KEYS.TICKETS);
        const newTicket = {
            id: 'TKT-' + Date.now(),
            userId, subject, message,
            status: 'open',
            replies: [],
            created_at: new Date().toISOString()
        };
        tickets.push(newTicket);
        db.save(DB_KEYS.TICKETS, tickets);
        return newTicket;
    },
    getUserTickets: (userId) => db.get(DB_KEYS.TICKETS).filter(t => t.userId === userId),
    getAllTickets: () => db.get(DB_KEYS.TICKETS)
};

// Initialize DB on load
db.init();
