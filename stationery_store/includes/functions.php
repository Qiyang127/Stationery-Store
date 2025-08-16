<?php
require_once __DIR__ . '/database.php';

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function format_price(float $amount): string {
  return '$' . number_format($amount, 2);
}

function redirect(string $path): void {
  $base = BASE_URL !== '' ? BASE_URL : '';
  header('Location: ' . ($base . $path));
  exit;
}

function csrf_token(): string {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool {
  return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function current_user_id(): ?int {
  return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function is_logged_in(): bool {
  return current_user_id() !== null;
}

function ensure_logged_in(): void {
  if (!is_logged_in()) {
    redirect('/login.php');
  }
}

function get_cart(): array {
  if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }
  return $_SESSION['cart'];
}

function save_cart(array $cart): void {
  $_SESSION['cart'] = $cart;
}

function get_cart_count(): int {
  $cart = get_cart();
  $count = 0;
  foreach ($cart as $productId => $qty) {
    $count += max(0, (int)$qty);
  }
  return $count;
}

function get_cart_product_ids(): array {
  return array_map('intval', array_keys(get_cart()));
}

function get_cart_total(PDO $pdo): float {
  $cart = get_cart();
  if (!$cart) {
    return 0.0;
  }
  $ids = array_map('intval', array_keys($cart));
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
  $stmt->execute($ids);
  $total = 0.0;
  while ($row = $stmt->fetch()) {
    $pid = (int)$row['id'];
    $qty = isset($cart[$pid]) ? (int)$cart[$pid] : 0;
    $total += ((float)$row['price']) * $qty;
  }
  return $total;
}

function get_wishlist_count(PDO $pdo): int {
  $userId = current_user_id();
  if (!$userId) {
    return 0;
  }
  $stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM wishlists WHERE user_id = ?');
  $stmt->execute([$userId]);
  $row = $stmt->fetch();
  return $row ? (int)$row['c'] : 0;
}

function get_categories(PDO $pdo): array {
  $stmt = $pdo->query('SELECT id, name, slug FROM categories ORDER BY name');
  return $stmt->fetchAll();
}

function get_products(PDO $pdo, array $options = []): array {
  $where = [];
  $params = [];
  if (!empty($options['category_slug'])) {
    $where[] = 'c.slug = ?';
    $params[] = $options['category_slug'];
  }
  if (!empty($options['search'])) {
    $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
    $params[] = '%' . $options['search'] . '%';
    $params[] = '%' . $options['search'] . '%';
  }
  $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
  $limit = isset($options['limit']) ? (int)$options['limit'] : 24;
  $offset = isset($options['offset']) ? (int)$options['offset'] : 0;

  $sql = "SELECT p.id, p.name, p.slug, p.description, p.price, p.image_url, p.stock,
                 c.name AS category_name, c.slug AS category_slug
          FROM products p
          JOIN categories c ON c.id = p.category_id
          $whereSql
          ORDER BY p.created_at DESC
          LIMIT $limit OFFSET $offset";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  return $stmt->fetchAll();
}

function get_product(PDO $pdo, int $id): ?array {
  $stmt = $pdo->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = ?');
  $stmt->execute([$id]);
  $row = $stmt->fetch();
  return $row ?: null;
}

function get_related_products(PDO $pdo, int $categoryId, int $excludeProductId, int $limit = 4): array {
  $stmt = $pdo->prepare('SELECT id, name, price, image_url FROM products WHERE category_id = ? AND id <> ? ORDER BY created_at DESC LIMIT ?');
  $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
  $stmt->bindValue(2, $excludeProductId, PDO::PARAM_INT);
  $stmt->bindValue(3, $limit, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll();
}

function wishlist_contains(PDO $pdo, int $userId, int $productId): bool {
  $stmt = $pdo->prepare('SELECT 1 FROM wishlists WHERE user_id = ? AND product_id = ?');
  $stmt->execute([$userId, $productId]);
  return (bool)$stmt->fetchColumn();
}