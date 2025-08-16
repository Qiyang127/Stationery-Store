<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php if (is_logged_in()) { redirect('/account.php'); } ?>
<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <h1 class="h4 mb-3">Create Account</h1>
    <form method="post" action="/actions/register.php">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>
      <button class="btn btn-primary" type="submit">Register</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>