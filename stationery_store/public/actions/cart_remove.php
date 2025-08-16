<?php
require_once __DIR__ . '/../../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
  http_response_code(400);
  exit('Bad request');
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$cart = get_cart();
unset($cart[$productId]);
save_cart($cart);

redirect('/cart.php');