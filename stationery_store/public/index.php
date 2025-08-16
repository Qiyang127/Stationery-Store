<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php
$featured = get_products($pdo, ['limit' => 8]);
?>
<div class="p-5 mb-4 bg-light rounded-3">
  <div class="container py-5">
    <h1 class="display-5 fw-bold">Everything for Your Desk</h1>
    <p class="col-md-8 fs-4">Premium pens, notebooks, office supplies, art materials, and more — curated for productivity and creativity.</p>
    <a class="btn btn-primary btn-lg" href="/products.php" role="button">Shop Products</a>
  </div>
</div>

<h2 class="h4 mb-3">Featured Products</h2>
<div class="row row-cols-2 row-cols-md-4 g-3">
  <?php foreach ($featured as $p): ?>
  <div class="col">
    <div class="card h-100">
      <img src="<?= e($p['image_url'] ?: 'https://via.placeholder.com/600x400?text=Stationery') ?>" class="card-img-top" alt="<?= e($p['name']) ?>">
      <div class="card-body d-flex flex-column">
        <h3 class="h6 card-title mb-1"><a class="text-decoration-none" href="/product.php?id=<?= (int)$p['id'] ?>"><?= e($p['name']) ?></a></h3>
        <div class="text-muted small mb-2"><?= e($p['category_name']) ?></div>
        <div class="mt-auto d-flex align-items-center justify-content-between">
          <span class="fw-semibold text-primary"><?= format_price((float)$p['price']) ?></span>
          <form action="/actions/cart_add.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
            <button class="btn btn-sm btn-outline-primary" type="submit">Add to Cart</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>