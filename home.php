<section class="hero-section d-flex align-items-center justify-content-center">
    <div class="text-center">
    <h1 class="fw-bold display-5">Tanauan School of Craftsmanship and Home Industries<br>Document Management System</h1>
    <p class="lead">Effortlessly Store, Manage, and Retrieve School Documents.</p>
    <div class="mt-3">
        <a class="btn login-btn btn-outline-light fw-bold me-2" data-bs-toggle="modal" data-bs-target="#login-pop-up">Login now</a>
        <a class="btn sign-up-btn fw-bold" data-bs-toggle="modal" data-bs-target="#sign-up-pop-up">Sign up now</a>
    </div>
    </div>
</section>

<!-- Feature Section -->
<section class="features-section py-5" style="background-color: #E3F09B;">
    <div class="container">
        <h2 class="text-center fw-bold mb-4">Why Use Our DMS?</h2>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-4 rounded shadow-sm bg-white h-100">
                    <img class="mb-4" src="elems/uploads.png" alt="Easy Uploads" style="width: 250px;">
                    <i class="bi bi-cloud-arrow-up display-4 text-primary mb-3"></i>
                    <h5 class="fw-semibold">Easy Uploads</h5>
                    <p class="text-muted">Upload PDF files with just a few clicks. No complex forms, no clutter.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded shadow-sm bg-white h-100">
                    <img class="mb-4" src="elems/secure.png" alt="Secure Access" style="width: 250px;">
                    <i class="bi bi-shield-lock display-4 text-success mb-3"></i>
                    <h5 class="fw-semibold">Secure Access</h5>
                    <p class="text-muted">Role-based access control ensures your data is protected and only viewable to authorized users.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded shadow-sm bg-white h-100">
                    <img class="mb-4" src="elems/retrieve.png" alt="Fast Retrieval" style="width: 250px;">
                    <i class="bi bi-speedometer2 display-4 text-danger mb-3"></i>
                    <h5 class="fw-semibold">Fast Retrieval</h5>
                    <p class="text-muted">Find what you need quickly with our real-time search and filter features.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Developer Credits Section -->
<section class="dev-section text-white py-5" style="background: linear-gradient(135deg, #2a2a72, #009ffd);">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Meet the Developers</h2>
        <p class="mb-4">This system was proudly crafted by passionate BSIT students from ACLC College of Tacloban.</p>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="p-3 bg-white rounded text-dark shadow-sm">
                    <h5 class="fw-semibold mb-1">Cesar Janell Medina</h5>
                    <p class="mb-0 text-muted">Team Leader · Developer</p>
                </div>
            </div>
            <div class="col-md-4 mt-3 mt-md-0">
                <div class="p-3 bg-white rounded text-dark shadow-sm">
                    <h5 class="fw-semibold mb-1">Edmund Sealtiel De Veyra</h5>
                    <p class="mb-0 text-muted">Core Developer · UI Designer</p>
                </div>
            </div>
            <div class="col-md-4 mt-3 mt-md-0">
                <div class="p-3 bg-white rounded text-dark shadow-sm">
                    <h5 class="fw-semibold mb-1">Sheila Mae Comandao</h5>
                    <p class="mb-0 text-muted">Research · Quality Assurance</p>
                </div>
            </div>
        </div>
        <p class="mt-4 small fst-italic">Document Management System for TSCHI · 2025</p>
    </div>
</section>

<?php
    include('login.php');
    include('sign-up.php');
?>
<?php if (isset($_SESSION['login_error'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginModal = new bootstrap.Modal(document.getElementById('login-pop-up'));
        loginModal.show();
    });
    </script>
<?php unset($_SESSION['login_error']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['register_error']) || isset($_SESSION['register_success'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const signUpModal = new bootstrap.Modal(document.getElementById('sign-up-pop-up'));
        signUpModal.show();
    });
    </script>
<?php unset($_SESSION['register_error']); 
unset($_SESSION['register_success']);?>
<?php endif; ?>
