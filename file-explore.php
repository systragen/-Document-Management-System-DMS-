<?php
$layout = $_SESSION['layout'] ?? 'grid';
if (isset($_GET['layout']) && in_array($_GET['layout'], ['grid', 'table'])) {
    $_SESSION['layout'] = $_GET['layout'];
    $layout = $_GET['layout'];
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <!-- Layout Toggle Buttons -->
    <div class="btn-group" role="group">
        <button class="btn <?= $layout === 'grid' ? 'btn-secondary' : 'btn-outline-secondary' ?>" onclick="setLayout('grid')">
            <i class="bi bi-grid-3x3-gap-fill me-1"></i> Grid
        </button>
        <button class="btn <?= $layout === 'table' ? 'btn-secondary' : 'btn-outline-secondary' ?>" onclick="setLayout('table')">
            <i class="bi bi-table me-1"></i> Table
        </button>
    </div>

    <!-- Alert Box -->
    <?php if (isset($_SESSION['delete_temp_success'])): ?>
        <div class="alert alert-success alert-dismissible py-1" role="alert">
            <div class="row align-items-center">
                <div class="col">
                    <i class="bi bi-check-circle-fill"></i>
                    <span class="text-break"><?= $_SESSION['delete_temp_success']; ?></span>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['delete_temp_success']); ?>
    <?php endif; ?>
</div>

<?php if (mysqli_num_rows($result) === 0): ?>
    <p class="text-muted fst-italic">No files found.</p>

<?php elseif ($layout === 'table'): ?>
    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Upload Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($file = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($file['name']) ?></td>
                    <td><?= htmlspecialchars($file['category_name']) ?></td>
                    <td>
                        <span class="badge d-flex justify-content-center bg-<?= 
                            $file['status_id'] == 1 ? 'warning text-dark' :
                            ($file['status_id'] == 2 ? 'success' : 'danger') ?>">
                            <?= htmlspecialchars($file['status_name']) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($file['upload_date'])) ?></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-primary" onclick="loadViewFile(<?= $file['id'] ?>)">View</button>
                        <button class="btn btn-sm btn-success" onclick="loadEditFile(<?= $file['id'] ?>)">Edit</button>
                        <form method="POST" action="config.php" class="d-inline" onsubmit="return confirm('Delete this file?');">
                            <input type="hidden" name="delete_file_id" value="<?= $file['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php mysqli_data_seek($result, 0); while ($file = mysqli_fetch_assoc($result)): ?>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div 
                    class="card-body file-card-body position-relative bg-light-hover" 
                    style="cursor:pointer; padding-bottom: 3rem;"
                    onclick="window.open('uploads/<?= htmlspecialchars($file['stored_name']) ?>', '_blank')"
                    title="Click to open file"
                >
                    <h6 class="card-title fw-semibold text-truncate" title="<?= htmlspecialchars($file['name']) ?>">
                        <?= strlen($file['name']) > 40 ? htmlspecialchars(substr($file['name'], 0, 40)) . '...' : htmlspecialchars($file['name']) ?>
                    </h6>
                    <p class="card-text small mb-1 text-muted">Category: <?= htmlspecialchars($file['category_name']) ?></p>
                    <p class="card-text small text-muted">Uploaded: <?= date('M d, Y', strtotime($file['upload_date'])) ?></p>
                    <span class="badge position-absolute start-0 bottom-0 m-3 bg-<?= 
                        $file['status_id'] == 1 ? 'warning text-dark' : 
                        ($file['status_id'] == 2 ? 'success' : 'danger') ?>">
                        <?= htmlspecialchars($file['status_name']) ?>
                    </span>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center gap-1 bg-white border-top">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="loadViewFile(<?= $file['id'] ?>)">Info</button>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-primary" onclick="loadEditFile(<?= $file['id'] ?>)">Edit</button>
                        <form method="POST" action="config.php" onsubmit="return confirm('Are you sure you want to delete this file?');">
                            <input type="hidden" name="delete_file_id" value="<?= $file['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<script>
function setLayout(type) {
    const params = new URLSearchParams(window.location.search);
    params.set('layout', type);
    window.location.search = params.toString();
}
</script>
