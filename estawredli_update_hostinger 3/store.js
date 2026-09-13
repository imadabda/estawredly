'use strict';

const Store = (() => {

  const KEYS = {
    PRODUCTS : 'estawrdly_products',
    ORDERS   : 'estawrdly_orders',
    USERS    : 'estawrdly_users',
    SESSION  : 'estawrdly_session',
    SETTINGS : 'estawrdly_settings',
    CART     : 'store_cart',
    WISH     : 'store_wish',
    DELIVERY : 'estawrdly_delivery',
  };

  let currencyLoaded = false;
  let currencySettings = {
    enabled: false,
    base_rate: 0.50,
    current_rate: 0.50,
    auto_fetch: true
  };

  const currencyPromise = fetch('api/get_currency.php?t=' + Date.now())
    .then(r => r.json())
    .then(data => {
      currencySettings = data;
      currencyLoaded = true;
    })
    .catch(e => console.error('Error loading currency settings:', e));

  function ensureReady() {
    return currencyPromise;
  }

  function _get(key, fallback = null) {
    try {
      const raw = localStorage.getItem(key);
      return raw ? JSON.parse(raw) : fallback;
    } catch { return fallback; }
  }

  function _set(key, value) {
    try { localStorage.setItem(key, JSON.stringify(value)); return true; }
    catch { return false; }
  }

  // PRODUCTS
  // PRODUCTS
  
  function getProducts() {
    let list = [];
    if (typeof PRODUCTS_DB !== 'undefined' && PRODUCTS_DB.length > 0) {
      list = PRODUCTS_DB;
    } else {
      const stored = _get(KEYS.PRODUCTS);
      if (stored && stored.length > 0) list = stored;
    }

    const isAdminPage = window.location.pathname.endsWith('admin.php') || (typeof adminProducts !== 'undefined');
    if (!isAdminPage && currencySettings.enabled && currencySettings.base_rate > 0) {
      const multiplier = currencySettings.current_rate / currencySettings.base_rate;
      if (multiplier !== 1.0) {
        return list.map(p => {
          const item = { ...p };
          if (item.price) {
            const pr = parseFloat(item.price);
            if (!isNaN(pr)) item.price = Math.round((pr * multiplier) * 100) / 100;
          }
          if (item.oldPrice) {
            const opr = parseFloat(item.oldPrice);
            if (!isNaN(opr)) item.oldPrice = Math.round((opr * multiplier) * 100) / 100;
          }
          return item;
        });
      }
    }
    return list;
  }

  function saveProducts(products) {
    if (typeof PRODUCTS_DB !== 'undefined' && PRODUCTS_DB !== products) {
        // تحديث النسخة في الذاكرة لتجنب الحاجة لعمل ريفريش فوري
        PRODUCTS_DB.length = 0;
        PRODUCTS_DB.push(...products);
    }
    return _set(KEYS.PRODUCTS, products);
  }

  function getProduct(id) {
    return getProducts().find(p => String(p.id) === String(id)) || null;
  }

  function addProduct(product) {
    const products = getProducts();
    const maxId = products.reduce((m, p) => Math.max(m, Number(p.id)), 0);
    product.id = maxId + 1;
    product.stars = product.stars || 4.7;
    product.reviews = product.reviews || 0;
    product.inStock = product.inStock !== false;
    products.push(product);
    saveProducts(products);
    return product;
  }

  function updateProduct(id, updates) {
    const products = getProducts();
    const idx = products.findIndex(p => String(p.id) === String(id));
    if (idx === -1) return null;
    products[idx] = { ...products[idx], ...updates };
    saveProducts(products);
    return products[idx];
  }

  function deleteProduct(id) {
    const products = getProducts();
    const filtered = products.filter(p => String(p.id) !== String(id));
    saveProducts(filtered);
    return filtered.length < products.length;
  }

  function searchProducts(query, cat = '') {
    const all = getProducts();
    const q = query.trim().toLowerCase();
    return all.filter(p => {
      const matchQuery = !q || 
        (p.name||'').toLowerCase().includes(q) ||
        (p.code||'').toLowerCase().includes(q) ||
        (p.desc||'').toLowerCase().includes(q);
      const matchCat = !cat || p.cat === cat;
      return matchQuery && matchCat;
    });
  }

  function getCategories() {
    const cats = [...new Set(getProducts().map(p => p.cat).filter(Boolean))];
    return cats.sort();
  }

  // ORDERS
  function getOrders() {
    return _get(KEYS.ORDERS, []);
  }

  function saveOrders(orders) {
    return _set(KEYS.ORDERS, orders);
  }

  function addOrder(orderData) {
    const orders = getOrders();
    // Ensure costPrice is saved for each item at the time of purchase
    if (orderData.items) {
      orderData.items = orderData.items.map(item => {
        const prod = getProduct(item.id);
        return {
          ...item,
          costPrice: prod ? (prod.costPrice || 0) : 0
        };
      });
    }

    const order = {
      id: 'ORD-' + Date.now(),
      date: new Date().toISOString(),
      status: 'pending',
      ...orderData,
    };
    orders.unshift(order);
    saveOrders(orders);
    return order;
  }

  function updateOrderStatus(orderId, status) {
    const orders = getOrders();
    const idx = orders.findIndex(o => o.id === orderId);
    if (idx === -1) return null;
    orders[idx].status = status;
    orders[idx].updatedAt = new Date().toISOString();
    saveOrders(orders);
    return orders[idx];
  }

  function getOrderById(orderId) {
    return getOrders().find(o => String(o.id) === String(orderId)) || null;
  }

  function deleteOrder(orderId) {
    let orders = getOrders();
    orders = orders.filter(o => String(o.id) !== String(orderId));
    saveOrders(orders);
    return true;
  }

  function getDeliveryZones() {
    const stored = _get(KEYS.DELIVERY);
    if (stored && stored.length > 0) return stored;
    // Default zones
    const defaultZones = [
      { id: 1, name: 'الضفة', price: 20 },
      { id: 2, name: 'القدس', price: 30 },
      { id: 3, name: 'الداخل', price: 70 }
    ];
    _set(KEYS.DELIVERY, defaultZones);
    return defaultZones;
  }

  function saveDeliveryZones(zones) {
    return _set(KEYS.DELIVERY, zones);
  }

  // USERS / AUTH
  function getUsers() {
    return _get(KEYS.USERS, []);
  }

  function saveUsers(users) {
    return _set(KEYS.USERS, users);
  }

  function registerUser(userData) {
    const users = getUsers();
    const exists = users.find(u => u.email.toLowerCase() === userData.email.toLowerCase());
    if (exists) return { error: 'البريد الإلكتروني مسجل مسبقاً' };
    const user = {
      id: 'USR-' + Date.now(),
      name: userData.name,
      email: userData.email.toLowerCase(),
      password: btoa(userData.password), // تشفير بسيط للتخزين المحلي
      phone: userData.phone || '',
      role: 'customer',
      createdAt: new Date().toISOString(),
      avatar: userData.avatar || null,
      provider: userData.provider || 'email',
    };
    users.push(user);
    saveUsers(users);
    return { user };
  }

  function loginUser(email, password) {
    const users = getUsers();
    const user = users.find(u => 
      u.email.toLowerCase() === email.toLowerCase() &&
      u.password === btoa(password) &&
      u.provider === 'email'
    );
    if (!user) return { error: 'البريد الإلكتروني أو كلمة المرور غير صحيحة' };
    const session = { ...user };
    delete session.password;
    _set(KEYS.SESSION, session);
    return { user: session };
  }

  function loginWithGoogle(googleUser) {
    const users = getUsers();
    let user = users.find(u => u.email.toLowerCase() === googleUser.email.toLowerCase());
    if (!user) {
      // تسجيل تلقائي عبر جوجل
      const newUser = {
        id: 'USR-' + Date.now(),
        name: googleUser.name,
        email: googleUser.email.toLowerCase(),
        password: null,
        phone: '',
        role: 'customer',
        createdAt: new Date().toISOString(),
        avatar: googleUser.picture || null,
        provider: 'google',
      };
      users.push(newUser);
      saveUsers(users);
      user = newUser;
    }
    const session = { ...user };
    delete session.password;
    _set(KEYS.SESSION, session);
    return { user: session };
  }

  function logout() {
    localStorage.removeItem(KEYS.SESSION);
  }

  function getCurrentUser() {
    return _get(KEYS.SESSION);
  }

  function isLoggedIn() {
    return !!getCurrentUser();
  }

  function isAdmin() {
    const u = getCurrentUser();
    return u && u.role === 'admin';
  }

  function getUserById(id) {
    return getUsers().find(u => u.id === id) || null;
  }

  // SETTINGS
  function getSettings() {
    return _get(KEYS.SETTINGS, {
      storeName: 'إستوردلي',
      currency: 'ILS',
      currencySymbol: '₪',
      taxRate: 17,
      freeShippingThreshold: 200,
      shippingCost: 25,
      phone: '',
      email: '',
      whatsapp: '',
    });
  }

  function saveSettings(settings) {
    return _set(KEYS.SETTINGS, { ...getSettings(), ...settings });
  }

  // STATS
  function getStats() {
    const orders = getOrders();
    const products = getProducts();
    const users = getUsers();
    const revenue = orders
      .filter(o => o.status !== 'cancelled')
      .reduce((s, o) => s + (o.total || 0), 0);
    const pending = orders.filter(o => o.status === 'pending').length;
    const today = new Date().toDateString();
    const todayOrders = orders.filter(o => new Date(o.date).toDateString() === today).length;
    return {
      totalProducts: products.length,
      totalOrders: orders.length,
      totalUsers: users.length,
      totalRevenue: revenue,
      pendingOrders: pending,
      todayOrders,
      inStockProducts: products.filter(p => p.inStock !== false).length,
    };
  }

  // SEED ADMIN (أول مرة فقط)
  function seedAdmin() {
    const users = getUsers();
    const adminExists = users.find(u => u.role === 'admin');
    if (!adminExists) {
      const admin = {
        id: 'USR-ADMIN',
        name: 'مدير المتجر',
        email: 'admin@estawrdly.com',
        password: btoa('admin123'),
        phone: '',
        role: 'admin',
        createdAt: new Date().toISOString(),
        avatar: null,
        provider: 'email',
      };
      users.push(admin);
      saveUsers(users);
      console.log('✅ Admin account created: admin@estawrdly.com / admin123');
    }
  }

  seedAdmin();

  return {
    ensureReady,
    // Products
    getProducts, saveProducts, getProduct,
    addProduct, updateProduct, deleteProduct,
    searchProducts, getCategories,
    // Orders
    getOrders, addOrder, updateOrderStatus, getOrderById, deleteOrder,
    // Delivery
    getDeliveryZones, saveDeliveryZones,
    // Users / Auth
    registerUser, loginUser, loginWithGoogle,
    logout, getCurrentUser, isLoggedIn, isAdmin, getUserById, getUsers,
    // Settings
    getSettings, saveSettings,
    // Stats
    getStats,
    // Keys (for direct access if needed)
    KEYS,
  };
})();
