<div class="modal fade" id="login-pop-up" tabindex="-1" aria-labelledby="loginLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content login-modal overflow-hidden">
      <div class="row g-0">
        <!-- Left Image Panel -->
        <div class="col-md-6 login-left d-flex align-items-end text-white p-4">
          <div class="login-left-content">
            <img src="elems/logo.png" width="40" class="mb-2">
            <h2 class="fw-bold">Login to <br>TSCHI - DMS</h2>
            <p class="small">Access your institutional documents securely.</p>
          </div>
        </div>

        <!-- Right Form Panel -->
        <div class="col-md-6 bg-light p-4 d-flex align-items-center">
          <div class="w-100">
            <h3 class="text-center fw-bold mb-3">LOGIN</h3>
            <p class="text-center mb-4 small text-muted">Enter your details to log in to your account</p>

            <form method="POST" action="config.php">
              <input type="hidden" name="login" value="1">
              <?php if (isset($_SESSION['login_error'])): ?>
                <div class="alert alert-danger text-center py-1">
                  <?= $_SESSION['login_error']; ?>
                </div>
              <?php endif; ?>
              <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email Address" required>
              </div>
              <div class="mb-4">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
              </div>
              <button type="submit" class="btn w-100 login-submit-btn">Login</button>
            </form>
            <p class="mt-3 text-center small">Don't have an account yet? <a href="#" class="text-decoration-none text-primary" data-bs-toggle="modal" data-bs-target="#sign-up-pop-up" data-bs-dismiss="modal">Sign up here</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>