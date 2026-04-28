<div class="modal fade" id="sign-up-pop-up" tabindex="-1" aria-labelledby="signupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content login-modal overflow-hidden">
            <div class="row g-0">
                <!-- Left Panel with Image and Text -->
                <div class="col-md-6 login-left d-flex align-items-end text-white p-4">
                    <div class="login-left-content">
                        <img src="elems/logo.png" width="40" class="mb-2">
                        <h2 class="fw-bold">Sign up to <br>TSCHI - DMS</h2>
                        <p class="small">Access your documents securely.</p>
                    </div>
                </div>

                <!-- Right Form Panel -->
                <div class="col-md-6 bg-light p-4 d-flex align-items-center">
                    <div class="w-100">
                        <h3 class="text-center fw-bold mb-3">SIGN UP</h3>
                        <p class="text-center mb-4 small text-muted">Create an account</p>

                        <form method="POST" action="config.php">
                            <input type="hidden" name="register" value="1">
                            <?php if (isset($_SESSION['register_error'])): ?>
                                <div class="alert alert-danger text-center small py-1 mb-2">
                                    <?= $_SESSION['register_error']; ?>
                                </div>
                            <?php endif;
                            if (isset($_SESSION['register_success'])): ?>
                                <div class="alert alert-success text-center small py-1 mb-2">
                                    <?= $_SESSION['register_success']; ?>
                                </div>
                            <?php endif; ?>
                            <div class="mb-2"><input type="text" name="first_name" class="form-control" placeholder="First Name" required></div>
                            <div class="mb-2"><input type="text" name="middle_name" class="form-control" placeholder="Middle Name (Optional)"></div>
                            <div class="mb-2"><input type="text" name="last_name" class="form-control" placeholder="Last Name" required></div>
                            <div class="mb-2"><input type="email" name="email" class="form-control" placeholder="Email Address" required></div>
                            <div class="mb-2"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                            <div class="mb-4"><input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required></div>
                            <button type="submit" class="btn w-100 login-submit-btn">Sign up</button>
                        </form>
                        <p class="mt-3 text-center small">Already have an account? 
                            <a href="#" class="text-decoration-none text-primary" data-bs-toggle="modal" data-bs-target="#login-pop-up" data-bs-dismiss="modal">Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
