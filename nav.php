<?php
    error_reporting(1);
    session_start();

    function nav() {
    $current_nav = $_GET['nav'] ?? 'home';
?>
<!-- Main Navbar -->
<nav class="navbar shadow-sm" style="background-color: #1F0318;">
    <div class="container d-flex justify-content-between align-items-center">
        <?php if ($_SESSION['role'] == 1) { ?>
            <a class="navbar-brand text-white " href="index.php?nav=admin-dashboard">
        <?php } elseif ($_SESSION['role'] == 2 || $_SESSION['role'] == 3) { ?>
            <a class="navbar-brand text-white" href="index.php?nav=my-dashboard">
        <?php }  else { ?>
            <a class="navbar-brand text-white" href="index.php?nav=home">
        <?php } ?>            
            <img src="elems/logo.png" alt="TSCHI" width="40" height="40">
            <span class="ms-2 fw-bold">TSCHI - DMS</span>
        </a>
        <div>
            <?php 
                if (!isset($_SESSION['user_id'])): 
            ?>
                    <button class="btn login-btn btn-outline-light fw-bold me-2" type="button" data-bs-toggle="modal" data-bs-target="#login-pop-up">Login</button>
                    <button class="btn sign-up-btn fw-bold me-2" type="button" data-bs-toggle="modal" data-bs-target="#sign-up-pop-up">Sign-Up</button>
            <?php 
                else: 
            ?>
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle fw-bold d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php
                        include('config.php');
                        $login_id = $_SESSION['user_id'];

                        $profile_sql = mysqli_query($conn, "SELECT * FROM profile WHERE login_id = $login_id");
                        $profile_data = mysqli_fetch_assoc($profile_sql);

                        $first_name = $profile_data['first_name'] ?? 'User';
                        $profile_pic_path = "elems/profile-picture/{$login_id}.png";
                        if (!file_exists($profile_pic_path)) {
                            $profile_pic_path = "elems/profile-picture/default.png";
                        } 
                    ?>
                    <img src="<?= $profile_pic_path ?>" alt="Profile" class="rounded-circle me-2" width="32" height="32">
                    <?= 
                        $first_name 
                    ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="?nav=profile">Profile Settings</a></li>
                        <li><a class="dropdown-item text-danger" href="?nav=logout">Logout</a></li>
                    </ul>
                </div>
            <?php 
                endif; 
            ?>
        </div>
    </div>
</nav>

<!-- Role-Based Sub Navbar -->
<?php 
    if (isset($_SESSION['role'])): 
?>
<nav class="navbar navbar-expand-sm" style="background-color: #2E0F26; height: 45px;">
    <div class="container">
        <ul class="navbar-nav small fw-bold">
            <?php 
                if ($_SESSION['role'] == 1) { 
            ?>
                    <li class="nav-item"><a class="nav-link text-white <?= $current_nav === 'admin-dashboard' ? 'active fw-bold text-warning' : '' ?>" href="?nav=admin-dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?= $current_nav === 'all-files' ? 'active fw-bold text-warning' : '' ?>" href="?nav=all-files">All Files</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?= $current_nav === 'manage-users' ? 'active fw-bold text-warning' : '' ?>" href="?nav=manage-users">Manage Users</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?= $current_nav === 'review-uploads' ? 'active fw-bold text-warning' : '' ?>" href="?nav=review-uploads">Review Uploads</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?= $current_nav === 'manage-categories' ? 'active fw-bold text-warning' : '' ?>" href="?nav=manage-categories">Manage Categories</a></li>
            <?php 
                } elseif ($_SESSION['role'] == 2) {
            ?>
                    <li class="nav-item"><a class="nav-link text-white <?= $current_nav === 'my-dashboard' ? 'active fw-bold text-warning' : '' ?>" href="?nav=my-dashboard">My Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?= $current_nav === 'upload' ? 'active fw-bold text-warning' : '' ?>" href="?nav=upload">Upload New File</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?= $current_nav === 'file-status' ? 'active fw-bold text-warning' : '' ?>" href="?nav=file-status">My Files</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?= $current_nav === 'review-uploads' ? 'active fw-bold text-warning' : '' ?>" href="?nav=review-uploads">Review Uploads</a></li>
            <?php 
                } else{
            ?>
                    <li class="nav-item"><a class="nav-link text-white <?= $current_nav === 'my-dashboard' ? 'active fw-bold text-warning' : '' ?>" href="?nav=my-dashboard">My Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?= $current_nav === 'upload' ? 'active fw-bold text-warning' : '' ?>" href="?nav=upload">Upload New File</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?= $current_nav === 'file-status' ? 'active fw-bold text-warning' : '' ?>" href="?nav=file-status">My Files</a></li>
            <?php 
                } endif; 
            ?>
        </ul>
    </div>
</nav>
<?php
}
?>