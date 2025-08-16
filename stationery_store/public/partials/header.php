<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
$pdo = get_pdo();
$cartCount = get_cart_count();
$wishlistCount = get_wishlist_count($pdo);
$categories = get_categories($pdo);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>Paper & Pen - Stationery Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/styles.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container">
        <a class="navbar-brand fw-bold" href="/index.php">Paper & Pen</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="/index.php">Home</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Products</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/products.php">All Products</a></li>
                <?php foreach ($categories as $cat): ?>
                <li><a class="dropdown-item" href="/products.php?category=<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></a></li>
                <?php endforeach; ?>
              </ul>
            </li>
            <li class="nav-item"><a class="nav-link" href="/contact.php">Contact</a></li>
          </ul>
          <form class="d-flex me-3" role="search" action="/products.php" method="get">
            <input class="form-control me-2" type="search" name="q" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-primary" type="submit">Search</button>
          </form>
          <ul class="navbar-nav mb-2 mb-lg-0">
            <li class="nav-item me-2">
              <a class="btn position-relative" href="/wishlist.php" title="Wishlist">
                <span class="bi bi-heart"></span>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="wishlist-count"><?= (int)$wishlistCount ?></span>
              </a>
            </li>
            <li class="nav-item me-3">
              <a class="btn position-relative" href="/cart.php" title="Cart">
                <span class="bi bi-cart"></span>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" id="cart-count"><?= (int)$cartCount ?></span>
              </a>
            </li>
            <?php if (is_logged_in()): ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Account</a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="/account.php">My Account</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
                </ul>
              </li>
            <?php else: ?>
              <li class="nav-item"><a class="nav-link" href="/login.php">Login</a></li>
              <li class="nav-item"><a class="nav-link" href="/register.php">Register</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container py-4">