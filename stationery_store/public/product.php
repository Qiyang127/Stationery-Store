<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $id ? get_product($pdo, $id) : null;
if (!$product) {
  http_response_code(404);
  echo '<div class="alert alert-danger">Product not found.</div>';
  require_once __DIR__ . '/partials/footer.php';
  exit;
}
$related = get_related_products($pdo, (int)$product['category_id'], (int)$product['id']);
$inWishlist = is_logged_in() ? wishlist_contains($pdo, current_user_id(), (int)$product['id']) : false;
?>
<div class="row g-4">
  <div class="col-md-6">
    <img class="img-fluid rounded" src="<?= e($product['image_url'] ?: 'https://via.placeholder.com/800x600?text=Stationery') ?>" alt="<?= e($product['name']) ?>">
  </div>
  <div class="col-md-6">
    <h1 class="h3 mb-1"><?= e($product['name']) ?></h1>
    <div class="text-muted mb-2">Category: <a href="/products.php?category=<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a></div>
    <div class="h4 text-primary mb-3"><?= format_price((float)$product['price']) ?></div>
    <p><?= nl2br(e($product['description'])) ?></p>
    <div class="d-flex align-items-center gap-2 mt-4">
      <form action="/actions/cart_add.php" method="post" class="d-flex align-items-center gap-2">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
        <input type="number" name="quantity" class="form-control" style="width: 100px" min="1" value="1">
        <button class="btn btn-primary" type="submit">Add to Cart</button>
      </form>
      <?php if (is_logged_in()): ?>
      <form action="/actions/wishlist_toggle.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
        <button class="btn btn-outline-danger" type="submit"><?= $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' ?></button>
      </form>
      <?php else: ?>
      <a class="btn btn-outline-danger" href="/login.php">Login to wishlist</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if ($related): ?>
<hr class="my-5">
<h2 class="h5 mb-3">You may also like</h2>
<div class="row row-cols-2 row-cols-md-4 g-3">
  <?php foreach ($related as $p): ?>
  <div class="col">
    <div class="card h-100">
      <img src="<?= e($p['image_url'] ?: 'https://via.placeholder.com/600x400?text=Stationery') ?>" class="card-img-top" alt="<?= e($p['name']) ?>">
      <div class="card-body d-flex flex-column">
        <h3 class="h6 card-title mb-1"><a class="text-decoration-none" href="/product.php?id=<?= (int)$p['id'] ?>"><?= e($p['name']) ?></a></h3>
        <div class="mt-auto d-flex align-items-center justify-content-between">
          <span class="fw-semibold text-primary"><?= format_price((float)$p['price']) ?></span>
          <form action="/actions/cart_add.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
            <button class="btn btn-sm btn-outline-primary" type="submit">Add</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/partials/footer.php'; ?>