'use strict';
(function () {
  var PLACEHOLDER = 'https://via.placeholder.com/600x400?text=Stationery';

  var db = {
    categories: [
      { id: 1, name: 'Pens & Writing', slug: 'pens-writing' },
      { id: 2, name: 'Notebooks', slug: 'notebooks' },
      { id: 3, name: 'Office Supplies', slug: 'office-supplies' },
      { id: 4, name: 'Art & Craft', slug: 'art-craft' }
    ],
    products: [
      { id: 1, category_id: 1, name: 'Premium Gel Pen', price: 2.99, image_url: 'https://images.unsplash.com/photo-1515876300841-1f3a5d1aa36e?w=600', description: 'Smooth writing gel pen with 0.5mm tip.' },
      { id: 2, category_id: 1, name: 'Fountain Pen', price: 24.99, image_url: 'https://images.unsplash.com/photo-1519681393784-d120267933ba?w=600', description: 'Elegant fountain pen with refillable converter.' },
      { id: 3, category_id: 2, name: 'A5 Dotted Notebook', price: 9.99, image_url: 'https://images.unsplash.com/photo-1520975922284-9e0ce82764b8?w=600', description: '160 pages, 100gsm paper, perfect for bullet journaling.' },
      { id: 4, category_id: 3, name: 'Desk Organizer', price: 14.5, image_url: 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?w=600', description: 'Keep your desk tidy with multiple compartments.' },
      { id: 5, category_id: 4, name: 'Watercolor Set', price: 19.0, image_url: 'https://images.unsplash.com/photo-1501876725168-00c445821c9e?w=600', description: '24-color watercolor set with brush.' }
    ]
  };

  function load(key, fallback) {
    try { return JSON.parse(localStorage.getItem(key) || 'null') ?? fallback; } catch (e) { return fallback; }
  }
  function save(key, value) { localStorage.setItem(key, JSON.stringify(value)); }

  var store = {
    getUser: function () { return load('pnp_user', null); },
    setUser: function (u) { save('pnp_user', u); },
    logout: function () { localStorage.removeItem('pnp_user'); },
    getCart: function () { return load('pnp_cart', {}); },
    setCart: function (c) { save('pnp_cart', c); },
    getWishlist: function () { return load('pnp_wishlist', []); },
    setWishlist: function (w) { save('pnp_wishlist', w); },
    getMessages: function () { return load('pnp_messages', []); },
    setMessages: function (m) { save('pnp_messages', m); }
  };

  function byId(id) { return document.getElementById(id); }
  function formatPrice(p) { return '$' + p.toFixed(2); }
  function findCategory(categoryId) { return db.categories.find(function (c) { return c.id === categoryId; }); }
  function getProduct(id) { return db.products.find(function (p) { return p.id === id; }); }

  function updateNavCounts() {
    var cart = store.getCart();
    var cartCount = Object.values(cart).reduce(function (s, q) { return s + (q|0); }, 0);
    var wishlistCount = store.getWishlist().length;
    var cartEl = byId('cart-count'); if (cartEl) cartEl.textContent = String(cartCount);
    var wishEl = byId('wishlist-count'); if (wishEl) wishEl.textContent = String(wishlistCount);
    var user = store.getUser();
    var navLogin = byId('nav-login');
    var navAccount = byId('nav-account');
    if (navLogin && navAccount) {
      if (user) { navLogin.classList.add('d-none'); navAccount.classList.remove('d-none'); }
      else { navLogin.classList.remove('d-none'); navAccount.classList.add('d-none'); }
    }
    var yearEl = byId('year'); if (yearEl) yearEl.textContent = String(new Date().getFullYear());
  }

  function renderProductCard(p) {
    var category = findCategory(p.category_id);
    return '\n<div class="col">\n  <div class="card h-100">\n    <img src="' + (p.image_url || PLACEHOLDER) + '" class="card-img-top" alt="' + p.name + '">\n    <div class="card-body d-flex flex-column">\n      <h3 class="h6 card-title mb-1"><a class="text-decoration-none" href="product.html?id=' + p.id + '">' + p.name + '</a></h3>\n      <div class="text-muted small mb-2">' + (category ? category.name : '') + '</div>\n      <div class="mt-auto d-flex align-items-center justify-content-between">\n        <span class="fw-semibold text-primary">' + formatPrice(p.price) + '</span>\n        <button class="btn btn-sm btn-outline-primary" data-add-cart="' + p.id + '">Add to Cart</button>\n      </div>\n    </div>\n  </div>\n</div>\n';
  }

  function attachAddToCartHandlers(root) {
    root.querySelectorAll('[data-add-cart]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = parseInt(btn.getAttribute('data-add-cart'), 10);
        var cart = store.getCart();
        cart[id] = (cart[id] || 0) + 1;
        store.setCart(cart);
        updateNavCounts();
      });
    });
  }

  function attachWishlistHandlers(root) {
    root.querySelectorAll('[data-toggle-wish]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = parseInt(btn.getAttribute('data-toggle-wish'), 10);
        var list = store.getWishlist();
        var idx = list.indexOf(id);
        if (idx >= 0) list.splice(idx, 1); else list.push(id);
        store.setWishlist(list);
        updateNavCounts();
        window.location.reload();
      });
    });
  }

  function searchProducts(query, categorySlug) {
    var q = (query || '').toLowerCase();
    return db.products.filter(function (p) {
      var matchQ = q ? (p.name.toLowerCase().includes(q) || (p.description||'').toLowerCase().includes(q)) : true;
      var matchC = categorySlug ? (findCategory(p.category_id).slug === categorySlug) : true;
      return matchQ && matchC;
    });
  }

  function populateCategories(select) {
    if (!select) return;
    select.innerHTML = '<option value="">All Categories</option>' + db.categories.map(function (c) {
      return '<option value="' + c.slug + '">' + c.name + '</option>';
    }).join('');
  }

  function renderHome() {
    updateNavCounts();
    var grid = byId('featured-grid');
    var featured = db.products.slice(0, 8);
    grid.innerHTML = featured.map(renderProductCard).join('');
    attachAddToCartHandlers(grid);
  }

  function renderProducts() {
    updateNavCounts();
    var params = new URLSearchParams(window.location.search);
    var q = params.get('q') || '';
    var cat = params.get('category') || '';
    var grid = byId('products-grid');
    var select = byId('category-select');
    populateCategories(select);
    if (select && cat) select.value = cat;
    var products = searchProducts(q, cat);
    grid.innerHTML = products.map(renderProductCard).join('') || '<div class="alert alert-info">No products found.</div>';
    attachAddToCartHandlers(grid);

    var searchInput = byId('search-input');
    var searchBtn = byId('search-btn');
    if (searchInput) searchInput.value = q;
    if (searchBtn) searchBtn.addEventListener('click', function () {
      var params = new URLSearchParams();
      if (searchInput.value) params.set('q', searchInput.value);
      if (select.value) params.set('category', select.value);
      window.location.search = params.toString();
    });
    var clearBtn = byId('clear-filters');
    if (clearBtn) clearBtn.addEventListener('click', function () { window.location.href = 'products.html'; });
    if (select) select.addEventListener('change', function () { searchBtn.click(); });
  }

  function renderProduct() {
    updateNavCounts();
    var params = new URLSearchParams(window.location.search);
    var id = parseInt(params.get('id') || '0', 10);
    var p = getProduct(id);
    var container = byId('product-container');
    if (!p) { container.innerHTML = '<div class="alert alert-danger">Product not found.</div>'; return; }
    var category = findCategory(p.category_id);
    var wishList = store.getWishlist();
    var inWish = wishList.indexOf(p.id) >= 0;
    container.innerHTML = '\n<div class="row g-4">\n  <div class="col-md-6">\n    <img class="img-fluid rounded" src="' + (p.image_url||PLACEHOLDER) + '" alt="' + p.name + '">\n  </div>\n  <div class="col-md-6">\n    <h1 class="h3 mb-1">' + p.name + '</h1>\n    <div class="text-muted mb-2">Category: <a href="products.html?category=' + (category?category.slug:'') + '">' + (category?category.name:'') + '</a></div>\n    <div class="h4 text-primary mb-3">' + formatPrice(p.price) + '</div>\n    <p>' + (p.description||'') + '</p>\n    <div class="d-flex align-items-center gap-2 mt-4">\n      <div class="d-flex align-items-center gap-2">\n        <input type="number" id="qty" class="form-control" style="width: 100px" min="1" value="1">\n        <button class="btn btn-primary" id="add-to-cart">Add to Cart</button>\n      </div>\n      <button class="btn btn-outline-danger" data-toggle-wish="' + p.id + '">' + (inWish ? 'Remove from Wishlist' : 'Add to Wishlist') + '</button>\n    </div>\n  </div>\n</div>\n';
    attachWishlistHandlers(container);
    var qtyEl = byId('qty');
    var addBtn = byId('add-to-cart');
    addBtn.addEventListener('click', function () {
      var qty = Math.max(1, parseInt(qtyEl.value||'1', 10));
      var cart = store.getCart();
      cart[p.id] = (cart[p.id] || 0) + qty;
      store.setCart(cart);
      updateNavCounts();
    });
  }

  function renderCart() {
    updateNavCounts();
    var container = byId('cart-container');
    var cart = store.getCart();
    var ids = Object.keys(cart).map(function (s) { return parseInt(s, 10); });
    if (!ids.length) { container.innerHTML = '<div class="alert alert-info">Your cart is empty. <a href="products.html">Browse products</a>.</div>'; return; }
    var rows = ids.map(function (id) {
      var p = getProduct(id); if (!p) return '';
      var qty = cart[id] || 0;
      var subtotal = p.price * qty;
      return '\n<tr>\n  <td>\n    <div class="d-flex align-items-center">\n      <img src="' + (p.image_url||PLACEHOLDER) + '" class="rounded me-2" style="width: 56px; height: 56px; object-fit: cover" alt="' + p.name + '">\n      <div><a href="product.html?id=' + p.id + '" class="text-decoration-none fw-semibold">' + p.name + '</a></div>\n    </div>\n  </td>\n  <td>' + formatPrice(p.price) + '</td>\n  <td>\n    <div class="d-flex align-items-center gap-2">\n      <input data-qty="' + p.id + '" type="number" class="form-control" value="' + qty + '" min="0" style="width: 90px">\n      <button class="btn btn-sm btn-outline-secondary" data-update="' + p.id + '">Update</button>\n    </div>\n  </td>\n  <td>' + formatPrice(subtotal) + '</td>\n  <td><button class="btn btn-sm btn-outline-danger" data-remove="' + p.id + '">Remove</button></td>\n</tr>\n';
    }).join('');
    var total = ids.reduce(function (sum, id) { var p=getProduct(id); return p? sum + p.price*(cart[id]||0) : sum; }, 0);
    container.innerHTML = '\n<div class="table-responsive">\n  <table class="table align-middle">\n    <thead><tr><th>Item</th><th style="width: 130px">Price</th><th style="width: 140px">Quantity</th><th style="width: 130px">Subtotal</th><th></th></tr></thead>\n    <tbody>' + rows + '</tbody>\n  </table>\n</div>\n<div class="d-flex justify-content-between align-items-center">\n  <a class="btn btn-outline-secondary" href="products.html">Continue Shopping</a>\n  <div class="h5 mb-0">Total: <span class="text-primary">' + formatPrice(total) + '</span></div>\n</div>\n';
    container.querySelectorAll('[data-update]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = parseInt(btn.getAttribute('data-update'), 10);
        var input = container.querySelector('[data-qty="' + id + '"]');
        var qty = Math.max(0, parseInt(input.value||'0', 10));
        var cart = store.getCart();
        if (qty <= 0) delete cart[id]; else cart[id] = qty;
        store.setCart(cart);
        renderCart();
      });
    });
    container.querySelectorAll('[data-remove]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = parseInt(btn.getAttribute('data-remove'), 10);
        var cart = store.getCart(); delete cart[id]; store.setCart(cart); renderCart();
      });
    });
  }

  function renderWishlist() {
    updateNavCounts();
    var grid = byId('wishlist-grid');
    var list = store.getWishlist();
    if (!list.length) { grid.innerHTML = '<div class="alert alert-info">Your wishlist is empty. <a href="products.html">Browse products</a>.</div>'; return; }
    var html = list.map(function (id) {
      var p = getProduct(id); if (!p) return '';
      return '\n<div class="col">\n  <div class="card h-100">\n    <img src="' + (p.image_url||PLACEHOLDER) + '" class="card-img-top" alt="' + p.name + '">\n    <div class="card-body d-flex flex-column">\n      <h3 class="h6 card-title mb-1"><a class="text-decoration-none" href="product.html?id=' + p.id + '">' + p.name + '</a></h3>\n      <div class="mt-auto d-flex align-items-center justify-content-between">\n        <span class="fw-semibold text-primary">' + formatPrice(p.price) + '</span>\n        <button class="btn btn-sm btn-outline-danger" data-toggle-wish="' + p.id + '">Remove</button>\n      </div>\n    </div>\n  </div>\n</div>\n';
    }).join('');
    grid.innerHTML = html;
    attachWishlistHandlers(grid);
  }

  function renderContact() {
    updateNavCounts();
    var form = byId('contact-form');
    var success = byId('contact-success');
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
      var data = Object.fromEntries(new FormData(form).entries());
      var list = store.getMessages();
      list.push({
        id: Date.now(),
        name: data.name,
        email: data.email,
        subject: data.subject,
        message: data.message
      });
      store.setMessages(list);
      form.reset();
      form.classList.remove('was-validated');
      success.classList.remove('d-none');
      setTimeout(function () { success.classList.add('d-none'); }, 2500);
    });
  }

  function renderLogin() {
    var user = store.getUser(); if (user) { window.location.href = 'account.html'; return; }
    var form = byId('login-form');
    var alertEl = byId('login-error');
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
      var data = Object.fromEntries(new FormData(form).entries());
      var users = load('pnp_users', []);
      var found = users.find(function (u) { return u.email === data.email && u.password === data.password; });
      if (found) { store.setUser({ name: found.name, email: found.email }); window.location.href = 'account.html'; }
      else { alertEl.classList.remove('d-none'); }
    });
  }

  function renderRegister() {
    var user = store.getUser(); if (user) { window.location.href = 'account.html'; return; }
    var form = byId('register-form');
    var alertEl = byId('register-error');
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
      var data = Object.fromEntries(new FormData(form).entries());
      if (data.password !== data.confirm_password) { alertEl.textContent = 'Passwords do not match'; alertEl.classList.remove('d-none'); return; }
      var users = load('pnp_users', []);
      if (users.some(function (u) { return u.email === data.email; })) { alertEl.textContent = 'Email already registered'; alertEl.classList.remove('d-none'); return; }
      users.push({ name: data.name, email: data.email, password: data.password });
      save('pnp_users', users);
      store.setUser({ name: data.name, email: data.email });
      window.location.href = 'account.html';
    });
  }

  function renderAccount() {
    var user = store.getUser(); if (!user) { window.location.href = 'login.html'; return; }
    var el = byId('account-container');
    var cartCount = Object.values(store.getCart()).reduce(function (s, q) { return s + (q|0); }, 0);
    var wishCount = store.getWishlist().length;
    el.innerHTML = '\n<div class="card">\n  <div class="card-header">Profile</div>\n  <div class="card-body">\n    <div class="mb-2"><strong>Name:</strong> ' + user.name + '</div>\n    <div class="mb-2"><strong>Email:</strong> ' + user.email + '</div>\n    <div class="mb-2">Cart items: <strong>' + cartCount + '</strong></div>\n    <div class="mb-2">Wishlist items: <strong>' + wishCount + '</strong></div>\n    <div class="d-flex gap-2 mt-3">\n      <a class="btn btn-outline-primary" href="cart.html">Go to Cart</a>\n      <a class="btn btn-outline-secondary" href="wishlist.html">Go to Wishlist</a>\n      <button class="btn btn-outline-danger" id="logout">Logout</button>\n    </div>\n  </div>\n</div>\n';
    byId('logout').addEventListener('click', function () { store.logout(); window.location.href = 'index.html'; });
  }

  window.PnP = {
    renderHome: renderHome,
    renderProducts: renderProducts,
    renderProduct: renderProduct,
    renderCart: renderCart,
    renderWishlist: renderWishlist,
    renderContact: renderContact,
    renderLogin: renderLogin,
    renderRegister: renderRegister,
    renderAccount: renderAccount
  };
})();