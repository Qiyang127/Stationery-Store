<?php require_once __DIR__ . '/partials/header.php'; ?>
<div class="row g-4">
  <div class="col-md-6">
    <h1 class="h4">Contact Us</h1>
    <p>Have a question about our stationery products or your order? Reach out and we'll get back to you.</p>
    <ul class="list-unstyled">
      <li><strong>Email:</strong> support@paperandpen.test</li>
      <li><strong>Phone:</strong> +1 (555) 123-4567</li>
      <li><strong>Address:</strong> 123 Paper Street, Pen City, PC 12345</li>
    </ul>
    <div class="ratio ratio-16x9 rounded overflow-hidden border">
      <iframe src="https://www.google.com/maps?output=embed&q=Times%20Square%2C%20New%20York" style="border:0;" allowfullscreen loading="lazy"></iframe>
    </div>
  </div>
  <div class="col-md-6">
    <h2 class="h5">Send a Message</h2>
    <form method="post" action="/actions/contact_submit.php" class="needs-validation" novalidate>
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <div class="mb-3">
        <label class="form-label">Your Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Subject</label>
        <input type="text" name="subject" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Message</label>
        <textarea name="message" class="form-control" rows="5" required></textarea>
      </div>
      <button class="btn btn-primary" type="submit">Send</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>