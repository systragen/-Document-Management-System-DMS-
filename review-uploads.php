<?php
session_start();
include('config.php');

if (!in_array($_SESSION['role'], [1, 2])) {
    header("Location: index.php?nav=home");
    exit();
}

// Search and filter
$search = trim($_GET['search'] ?? '');
$filter = $_GET['status_filter'] ?? '';

// Fetch categories
$categories_result = mysqli_query($conn, "SELECT id, name FROM category");
$selected_category = $_GET['category_filter'] ?? '';

$search_sql = "";
if ($search !== '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $search_sql = " AND (file.name LIKE '%$safe_search%' OR profile.first_name LIKE '%$safe_search%' OR profile.last_name LIKE '%$safe_search%')";
}

$status_filter_sql = "";
if (in_array($filter, ['1', '2', '3'])) {
    $status_filter_sql = " AND file.status_id = $filter";
}

$category_filter_sql = '';
if (is_numeric($selected_category)) {
    $category_filter_sql = " AND file.category_id = $selected_category";
}

$query = "
    SELECT file.*, profile.first_name, profile.last_name, category.name AS category_name, status.name AS status_name
    FROM file
    JOIN profile ON file.login_id = profile.login_id
    JOIN category ON file.category_id = category.id
    JOIN status ON file.status_id = status.id
    WHERE 1=1 $search_sql $status_filter_sql $category_filter_sql
    ORDER BY file.upload_date DESC
";


$result = mysqli_query($conn, $query);
?>

<div class="container my-4">
    <h3 class="mb-4">Review Uploaded Files</h3>

    <!-- Search bar (top full width) -->
    <div class="mb-3">
        <form method="GET" action="index.php">
            <input type="hidden" name="nav" value="review-uploads">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by file name or uploader" value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-success" type="submit">Search</button>
            </div>
        </form>
    </div>

    <!-- Filter row: Category + Status + Apply -->
    <div class="d-flex justify-content-end mb-3">
        <form method="GET" action="index.php" class="d-flex align-items-center flex-wrap gap-2">
            <input type="hidden" name="nav" value="review-uploads">

            <select name="category_filter" class="form-select form-select-sm" style="width: auto;">
                <option value="">All Categories</option>
                <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                    <option value="<?= $cat['id'] ?>" <?= $selected_category == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="status_filter" class="form-select form-select-sm" style="width: auto;">
                <option value="">All Status</option>
                <option value="1" <?= $filter == '1' ? 'selected' : '' ?>>Pending</option>
                <option value="2" <?= $filter == '2' ? 'selected' : '' ?>>Approved</option>
                <option value="3" <?= $filter == '3' ? 'selected' : '' ?>>Rejected</option>
            </select>

            <button class="btn btn-outline-primary btn-sm">Apply</button>
        </form>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle" style="table-layout: fixed; width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 23%;">File Name</th>
                        <th style="width: 16%;">Uploader</th>
                        <th style="width: 15%;">Category</th>
                        <th style="width: 8%;">Status</th>
                        <th style="width: 16%;">Uploaded</th>
                        <th style="width: 22%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($file = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($file['name']) ?></td>
                            <td><?= htmlspecialchars($file['first_name'] . ' ' . $file['last_name']) ?></td>
                            <td><?= htmlspecialchars($file['category_name']) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $file['status_id'] == 1 ? 'warning text-dark' :
                                    ($file['status_id'] == 2 ? 'success' : 'danger')
                                ?>">
                                    <?= htmlspecialchars($file['status_name']) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($file['upload_date'])) ?></td>
                            <td>
                                <a href="uploads/<?= $file['stored_name'] ?>" class="btn btn-sm btn-outline-primary" target="_blank">View</a>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="loadViewFile(<?= $file['id'] ?>)">Info</button>
                                <?php if ($file['status_id'] == 1): ?>
                                    <form method="POST" action="config.php" class="d-inline-block">
                                        <input type="hidden" name="review_file_id" value="<?= $file['id'] ?>">
                                        <button class="btn btn-sm btn-success" name="approve_file" value="1">Approve</button>
                                    </form>

                                    <button class="btn btn-sm btn-danger" onclick="showRejectForm(<?= $file['id'] ?>)">Reject</button>

                                    <form method="POST" action="config.php" class="d-none mt-2" id="reject-form-<?= $file['id'] ?>">
                                        <input type="hidden" name="review_file_id" value="<?= $file['id'] ?>">
                                        <textarea name="rejection_remark" class="form-control form-control-sm my-1" placeholder="Rejection remarks" required></textarea>
                                        <button class="btn btn-sm btn-danger" name="reject_file" value="1">Submit Rejection</button>
                                    </form>

                                <?php elseif ($file['status_id'] == 3 || $file['status_id'] == 2 ): ?>
                                    <form method="POST" action="config.php" class="d-inline">
                                        <input type="hidden" name="clear_remarks_file_id" value="<?= $file['id'] ?>">
                                        <button class="btn btn-sm btn-warning" onclick="return confirm('Undo remarks for this file?')">Undo Remarks</button>
                                    </form>
                                    <?php if ($file['remarks']):?>
                                        <div class="text-muted small mt-1">Reason: <?= htmlspecialchars($file['remarks']) ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">No files found.</p>
    <?php endif; ?>
</div>
<script>
function showRejectForm(id) {
    const form = document.getElementById('reject-form-' + id);
    if (form.classList.contains('d-none')) {
        form.classList.remove('d-none');
    } else {
        form.classList.add('d-none');
    }
}
</script>
<!-- Modal placeholder -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" id="modalContent"></div>
  </div>
</div>

<script>
function loadViewFile(id) {
  fetch('view-file.php?id=' + id)
    .then(res => res.text())
    .then(html => {
      document.getElementById('modalContent').innerHTML = html;
      new bootstrap.Modal(document.getElementById('viewModal')).show();
    });
}
</script>