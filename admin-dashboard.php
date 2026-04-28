<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    if($_SESSION['role'] == 3){
        header("location: index.php?nav=my-dashboard");
    } elseif($_SESSION['role'] == 2){
        header("location: index.php?nav=my-dashboard");
    } else{
        header("Location: index.php?nav=home");
    }
    exit();
}
include('config.php');

// File summary stats
$total = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM file"))[0];
$pending = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM file WHERE status_id = 1"))[0];
$approved = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM file WHERE status_id = 2"))[0];
$rejected = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM file WHERE status_id = 3"))[0];

// Activity logs (example: last 5 uploads)
$logs = mysqli_query($conn, "
    SELECT file.name, profile.first_name, profile.last_name, file.upload_date 
    FROM file 
    JOIN profile ON file.login_id = profile.login_id 
    ORDER BY file.upload_date DESC 
    LIMIT 5
");
?>

<div class="container my-4">
    <h3 class="mb-4">Admin Dashboard</h3>

    <!-- Summary Cards -->
    <div class="row text-center mb-4">
        <div class="col-md-3 mb-3">
            <div class="p-3 border rounded bg-light shadow-sm">
                <strong>Total Files</strong><br><span><?= $total ?></span>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="p-3 border rounded bg-warning text-dark shadow-sm">
                <strong>Pending</strong><br><span><?= $pending ?></span>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="p-3 border rounded bg-success text-white shadow-sm">
                <strong>Approved</strong><br><span><?= $approved ?></span>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="p-3 border rounded bg-danger text-white shadow-sm">
                <strong>Rejected</strong><br><span><?= $rejected ?></span>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="mb-4 d-flex">
        <canvas id="statusChart" height="100"></canvas>
    </div>

    <h5 class="mt-4 mb-3">Quick Actions</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="index.php?nav=manage-users" class="btn btn-outline-primary w-100 p-3 shadow-sm">
                <i class="bi bi-people-fill me-2"></i>Manage Users
            </a>
        </div>
        <div class="col-md-4">
            <a href="index.php?nav=review-uploads" class="btn btn-outline-success w-100 p-3 shadow-sm">
                <i class="bi bi-folder-check me-2"></i>Review Files
            </a>
        </div>
        <div class="col-md-4">
            <a href="index.php?nav=manage-categories" class="btn btn-outline-secondary w-100 p-3 shadow-sm">
                <i class="bi bi-tags me-2"></i>Manage Categories
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <h5>Recent Uploads</h5>
    <ul class="list-group">
        <?php while ($log = mysqli_fetch_assoc($logs)): ?>
        <li class="list-group-item d-flex justify-content-between">
            <div>
                <?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?> uploaded 
                <strong><?= htmlspecialchars($log['name']) ?></strong>
            </div>
            <span class="text-muted small"><?= date("M d, Y h:i A", strtotime($log['upload_date'])) ?></span>
        </li>
        <?php endwhile; ?>
    </ul>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('statusChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Pending', 'Approved', 'Rejected'],
        datasets: [{
            label: 'Files',
            data: [<?= $pending ?>, <?= $approved ?>, <?= $rejected ?>],
            backgroundColor: ['#ffc107', '#198754', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
