<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php ensure_logged_in(); ?>
<?php
$userId = current_user_id();
$stmt = $pdo->prepare('SELECT id, name, email, created_at FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>
<h1 class="h4 mb-3">My Account</h1>
<div class="row g-4">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">Profile</div>
      <div class="card-body">
        <div class="mb-2"><strong>Name:</strong> <?= e($user['name']) ?></div>
        <div class="mb-2"><strong>Email:</strong> <?= e($user['email']) ?></div>
        <div class="mb-2"><strong>Member since:</strong> <?= e(date('M j, Y', strtotime($user['created_at']))) ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">Overview</div>
      <div class="card-body">
        <div class="mb-2">Cart items: <strong id="cart-count-inline"><?= (int)get_cart_count() ?></strong></div>
        <div class="mb-2">Wishlist items: <strong id="wishlist-count-inline"><?= (int)get_wishlist_count($pdo) ?></strong></div>
        <div class="d-flex gap-2 mt-3">
          <a class="btn btn-outline-primary" href="/cart.php">Go to Cart</a>
          <a class="btn btn-outline-secondary" href="/wishlist.php">Go to Wishlist</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>