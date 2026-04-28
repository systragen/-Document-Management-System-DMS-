<?php
session_start();
include('config.php');

$id = intval($_GET['id']);
$login_id = $_SESSION['user_id'];

if ($_SESSION['role'] == 1 || $_SESSION['role'] == 2) {
    // Admin or moderator: can view any file
    $query = $conn->prepare("
        SELECT file.*, 
               category.name AS category_name, 
               status.name AS status_name, 
               profile.first_name, 
               profile.last_name
        FROM file
        JOIN category ON file.category_id = category.id
        JOIN status ON file.status_id = status.id
        JOIN profile ON file.login_id = profile.login_id
        WHERE file.id = ?
    ");
    $query->bind_param("i", $id);
} else {
    // Regular user: can only view their own file
    $query = $conn->prepare("
        SELECT file.*, 
               category.name AS category_name, 
               status.name AS status_name, 
               profile.first_name, 
               profile.last_name
        FROM file
        JOIN category ON file.category_id = category.id
        JOIN status ON file.status_id = status.id
        JOIN profile ON file.login_id = profile.login_id
        WHERE file.id = ? AND file.login_id = ?
    ");
    $query->bind_param("ii", $id, $login_id);
}

$query->execute();
$result = $query->get_result();

if ($file = $result->fetch_assoc()):
?>

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">File Information</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <ul class="list-group list-group-flush mb-3">
        <li class="list-group-item">
            <strong>File Name:</strong><br>
            <span class="text-muted"><?= htmlspecialchars($file['name']) ?></span>
        </li>

        <li class="list-group-item">
            <strong>Description:</strong>
            <div class="mt-1 p-2 border bg-light rounded overflow-auto" style="max-height: 200px;">
                <?= nl2br(htmlspecialchars($file['description'])) ?>
            </div>
        </li>

        <li class="list-group-item">
            <strong>Category:</strong><br>
            <span class="badge bg-secondary"><?= htmlspecialchars($file['category_name']) ?></span>
        </li>

        <li class="list-group-item">
            <strong>Status:</strong><br>
            <span class="badge bg-<?= 
                $file['status_id'] == 1 ? 'warning text-dark' :
                ($file['status_id'] == 2 ? 'success' : 'danger') ?>">
                <?= htmlspecialchars($file['status_name']) ?>
            </span>
        </li>

        <?php if (!empty($file['remarks'])): ?>
        <li class="list-group-item">
            <strong>Remarks:</strong>
            <div class="text-muted fst-italic mt-1 p-2 border bg-warning-subtle rounded">
                <?= nl2br(htmlspecialchars($file['remarks'])) ?>
            </div>
        </li>
        <?php endif; ?>

        <li class="list-group-item">
            <strong>Uploader:</strong><br>
            <span class="text-muted"><?= htmlspecialchars($file['first_name'] . ' ' . $file['last_name']) ?></span>
        </li>

        <li class="list-group-item">
            <strong>Upload Date:</strong><br>
            <span class="text-muted"><?= date('M d, Y h:i A', strtotime($file['upload_date'])) ?></span>
        </li>
    </ul>

    <div class="text-end">
        <a href="uploads/<?= htmlspecialchars($file['stored_name']) ?>" target="_blank" class="btn btn-outline-primary">
            <i class="bi bi-box-arrow-up-right"></i> Open File
        </a>
    </div>
</div>

<?php else: ?>
<div class="modal-body">
    <p class="text-danger fw-bold">File not found or access denied.</p>
</div>
<?php endif; ?>
