    </div>
    <footer class="border-top py-4 mt-5">
      <div class="container d-flex justify-content-between align-items-center">
        <span class="text-muted">© <?= date('Y') ?> Paper & Pen. All rights reserved.</span>
        <div>
          <a class="text-muted me-3" href="https://facebook.com" target="_blank" rel="noopener">Facebook</a>
          <a class="text-muted me-3" href="https://instagram.com" target="_blank" rel="noopener">Instagram</a>
          <a class="text-muted" href="https://twitter.com" target="_blank" rel="noopener">Twitter</a>
        </div>
      </div>
    </footer>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
      <div id="liveToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body" id="toast-body">Action completed</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.7.2/dist/axios.min.js"></script>
    <script src="/assets/js/app.js" defer></script>
  </body>
</html>