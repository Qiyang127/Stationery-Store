<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php ensure_logged_in(); ?>
<?php
$userId = current_user_id();
$stmt = $pdo->prepare('SELECT p.id, p.name, p.price, p.image_url FROM wishlists w JOIN products p ON p.id = w.product_id WHERE w.user_id = ? ORDER BY w.created_at DESC');
$stmt->execute([$userId]);
$items = $stmt->fetchAll();
?>
<h1 class="h4 mb-3">Your Wishlist</h1>
<?php if (!$items): ?>
  <div class="alert alert-info">Your wishlist is empty. <a href="/products.php">Browse products</a>.</div>
<?php else: ?>
<div class="row row-cols-2 row-cols-md-4 g-3">
  <?php foreach ($items as $p): ?>
  <div class="col">
    <div class="card h-100">
      <img src="<?= e($p['image_url'] ?: 'https://via.placeholder.com/600x400?text=Stationery') ?>" class="card-img-top" alt="<?= e($p['name']) ?>">
      <div class="card-body d-flex flex-column">
        <h3 class="h6 card-title mb-1"><a class="text-decoration-none" href="/product.php?id=<?= (int)$p['id'] ?>"><?= e($p['name']) ?></a></h3>
        <div class="mt-auto d-flex align-items-center justify-content-between">
          <span class="fw-semibold text-primary"><?= format_price((float)$p['price']) ?></span>
          <form action="/actions/wishlist_toggle.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/partials/footer.php'; ?>