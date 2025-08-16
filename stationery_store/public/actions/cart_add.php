<?php
require_once __DIR__ . '/../../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
  http_response_code(400);
  exit('Bad request');
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

if ($productId <= 0) {
  redirect('/cart.php');
}

$cart = get_cart();
$cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
save_cart($cart);

redirect('/cart.php');