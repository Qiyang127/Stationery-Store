<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php
$categorySlug = isset($_GET['category']) ? trim((string)$_GET['category']) : '';
$search = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

$products = get_products($pdo, [
  'category_slug' => $categorySlug ?: null,
  'search' => $search ?: null,
  'limit' => $perPage,
  'offset' => $offset,
]);

// Count total for pagination
$where = [];
$params = [];
if ($categorySlug) {
  $where[] = 'c.slug = ?';
  $params[] = $categorySlug;
}
if ($search !== '') {
  $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
  $params[] = '%' . $search . '%';
  $params[] = '%' . $search . '%';
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM products p JOIN categories c ON c.id = p.category_id $whereSql");
$stmt->execute($params);
$total = (int)($stmt->fetch()['c'] ?? 0);
$totalPages = max(1, (int)ceil($total / $perPage));
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h4 mb-0">Products</h1>
  <form class="d-flex" method="get">
    <input type="text" name="q" value="<?= e($search) ?>" class="form-control me-2" placeholder="Search products...">
    <select name="category" class="form-select me-2" style="max-width: 220px">
      <option value="">All Categories</option>
      <?php foreach ($categories as $cat): ?>
      <option value="<?= e($cat['slug']) ?>" <?= $categorySlug === $cat['slug'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-outline-secondary" type="submit">Filter</button>
  </form>
</div>

<?php if (!$products): ?>
  <div class="alert alert-info">No products found.</div>
<?php endif; ?>

<div class="row row-cols-2 row-cols-md-4 g-3">
  <?php foreach ($products as $p): ?>
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

<?php if ($totalPages > 1): ?>
<nav class="mt-4" aria-label="Page navigation">
  <ul class="pagination">
    <?php
    $params = $_GET;
    for ($i = 1; $i <= $totalPages; $i++):
      $params['page'] = $i;
      $url = '/products.php?' . http_build_query($params);
    ?>
      <li class="page-item <?= $i === $page ? 'active' : '' ?>">
        <a class="page-link" href="<?= e($url) ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>
<?php require_once __DIR__ . '/partials/footer.php'; ?>