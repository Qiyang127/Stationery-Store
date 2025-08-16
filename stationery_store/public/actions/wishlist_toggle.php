<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
ensure_logged_in();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
  http_response_code(400);
  exit('Bad request');
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$userId = current_user_id();

if ($productId <= 0) {
  redirect('/wishlist.php');
}

$stmt = $pdo->prepare('SELECT 1 FROM products WHERE id = ?');
$stmt->execute([$productId]);
if (!$stmt->fetchColumn()) {
  redirect('/wishlist.php');
}

if (wishlist_contains($pdo, $userId, $productId)) {
  $del = $pdo->prepare('DELETE FROM wishlists WHERE user_id = ? AND product_id = ?');
  $del->execute([$userId, $productId]);
} else {
  $ins = $pdo->prepare('INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)');
  $ins->execute([$userId, $productId]);
}

redirect('/wishlist.php');