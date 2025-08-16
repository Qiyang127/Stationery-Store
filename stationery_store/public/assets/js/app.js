'use strict';
(function() {
  // Enable Bootstrap validation
  var forms = document.querySelectorAll('.needs-validation');
  Array.prototype.slice.call(forms).forEach(function(form) {
    form.addEventListener('submit', function(event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });

  // Toast via ?msg=...
  var params = new URLSearchParams(window.location.search);
  var msg = params.get('msg');
  if (msg) {
    var toastEl = document.getElementById('liveToast');
    var body = document.getElementById('toast-body');
    if (toastEl && body) {
      body.textContent = msg;
      var toast = new bootstrap.Toast(toastEl);
      toast.show();
    }
  }
})();