<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php
$cart = get_cart();
$ids = array_map('intval', array_keys($cart));
$items = [];
if ($ids) {
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $stmt = $pdo->prepare("SELECT id, name, price, image_url FROM products WHERE id IN ($placeholders)");
  $stmt->execute($ids);
  while ($row = $stmt->fetch()) {
    $items[(int)$row['id']] = $row;
  }
}
$total = get_cart_total($pdo);
?>
<h1 class="h4 mb-3">Your Cart</h1>
<?php if (!$cart): ?>
  <div class="alert alert-info">Your cart is empty. <a href="/products.php">Browse products</a>.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>Item</th><th style="width: 130px">Price</th><th style="width: 140px">Quantity</th><th style="width: 130px">Subtotal</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($cart as $pid => $qty): $pid=(int)$pid; $qty=(int)$qty; $p=$items[$pid] ?? null; if(!$p) continue; ?>
        <tr>
          <td>
            <div class="d-flex align-items-center">
              <img src="<?= e($p['image_url'] ?: 'https://via.placeholder.com/80?text=Pen') ?>" class="rounded me-2" style="width: 56px; height: 56px; object-fit: cover" alt="<?= e($p['name']) ?>">
              <div>
                <a href="/product.php?id=<?= $pid ?>" class="text-decoration-none fw-semibold"><?= e($p['name']) ?></a>
              </div>
            </div>
          </td>
          <td><?= format_price((float)$p['price']) ?></td>
          <td>
            <form action="/actions/cart_update.php" method="post" class="d-flex align-items-center gap-2">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="product_id" value="<?= $pid ?>">
              <input type="number" name="quantity" class="form-control" value="<?= $qty ?>" min="0" style="width: 90px">
              <button class="btn btn-sm btn-outline-secondary" type="submit">Update</button>
            </form>
          </td>
          <td><?= format_price(((float)$p['price']) * $qty) ?></td>
          <td>
            <form action="/actions/cart_remove.php" method="post">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="product_id" value="<?= $pid ?>">
              <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="d-flex justify-content-between align-items-center">
    <a class="btn btn-outline-secondary" href="/products.php">Continue Shopping</a>
    <div class="h5 mb-0">Total: <span class="text-primary"><?= format_price($total) ?></span></div>
  </div>
<?php endif; ?>
<?php require_once __DIR__ . '/partials/footer.php'; ?>