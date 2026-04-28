<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], [2, 3])) {
    header("Location: index.php?nav=home");
    exit();
}
include('config.php');
$login_id = $_SESSION['user_id'];

$total = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM file"))[0];
$pending = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM file WHERE status_id = 1"))[0];
$approved = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM file WHERE status_id = 2"))[0];
$rejected = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM file WHERE status_id = 3"))[0];
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <?php if ($_SESSION['role'] == 3):?>
            <h3 class="mb-0">My Dashboard</h3>
        <?php elseif ($_SESSION['role'] == 2):?>
            <h3 class="mb-0">Moderator Dashboard</h3>
        <?php endif ?>
    </div>
    <?php if ($_SESSION['role'] == 2): ?>
        <div class="row mb-4 text-center">
            <div class="col-md-3 mb-2">
                <div class="border rounded p-2 bg-light">
                    <strong>Total Files</strong><br><?= $total ?>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="border rounded p-2 bg-warning text-dark">
                    <strong>Pending</strong><br><?= $pending ?>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="border rounded p-2 bg-success text-white">
                    <strong>Approved</strong><br><?= $approved ?>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="border rounded p-2 bg-danger text-white">
                    <strong>Rejected</strong><br><?= $rejected ?>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <hr>
    <?php if ($_SESSION['role'] == 2):?>
        <div class="d-flex align-items-center mb-3 gap-3">
            <h3 class="mb-2">Your Files</h3>
            <a href="index.php?nav=upload" class="btn btn-success">+ Upload</a>
        </div>
    <?php endif ?>
    <?php include('search-filter.php'); ?>
    <?php if (mysqli_num_rows($result) === 0): ?>
        <p class="text-muted">No files found.</p>
    <?php endif ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php while ($file = mysqli_fetch_assoc($result)): ?>
        <div class="col">
            <div class="card h-100 border shadow-sm">
                <div class="card-body file-card-body position-relative" style="padding-bottom: 3rem; cursor:pointer;" onclick="window.open('uploads/<?= htmlspecialchars($file['stored_name']) ?>', '_blank')">
                    <h5 class="card-title" title="<?= htmlspecialchars($file['name']) ?>">
                        <?= strlen($file['name']) > 30 ? htmlspecialchars(substr($file['name'], 0, 30)) . '...' : htmlspecialchars($file['name']) ?>
                    </h5>
                    <p class="card-text small mb-1">Category: <?= htmlspecialchars($file['category_name']) ?></p>
                    <p class="card-text small">Uploaded: <?= date('M d, Y', strtotime($file['upload_date'])) ?></p>
                    <span class="badge position-absolute start-0 bottom-0 m-3 bg-<?= 
                        $file['status_id'] == 1 ? 'warning text-dark' : 
                        ($file['status_id'] == 2 ? 'success' : 'danger') ?>">
                        <?= htmlspecialchars($file['status_name']) ?>
                    </span>
                </div>
                <div class="card-footer d-flex">
                    <button class="btn btn-sm btn-outline-secondary" onclick="loadViewFile(<?= $file['id'] ?>)">info</button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include('file-view-edit.php'); ?>