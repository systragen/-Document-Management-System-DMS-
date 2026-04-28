<?php
session_start();
if (!isset($login_id)) $login_id = $_SESSION['user_id'] ?? 0;

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$category = $_GET['category'] ?? '';

// Admins will view all files; users only their own
$is_admin_or_mod = in_array($_SESSION['role'], [1]);

// Initialize WHERE clause
$filter_sql = $is_admin_or_mod ? "WHERE 1=1" : "WHERE file.login_id = $login_id";

// Apply filters
if ($search !== '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $filter_sql .= " AND file.name LIKE '%$safe_search%'";
}

if (in_array($status, ['1', '2', '3'])) {
    $filter_sql .= " AND file.status_id = $status";
}

if (is_numeric($category) && $category > 0) {
    $filter_sql .= " AND file.category_id = $category";
}

// Execute search result if needed
$result = mysqli_query($conn, "
    SELECT file.*, category.name AS category_name, status.name AS status_name
    FROM file
    JOIN category ON file.category_id = category.id
    JOIN status ON file.status_id = status.id
    $filter_sql
    ORDER BY file.upload_date DESC
");
?>

<form method="GET" action="index.php" class="mb-4">
    <input type="hidden" name="nav" value="<?= htmlspecialchars($_GET['nav'] ?? 'file-status') ?>">

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-3 align-items-center mb-2">
                <!-- Search Input -->
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="Search file name..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-success w-100">
                        <i class="bi bi-arrow-right-circle me-1"></i> Search
                    </button>
                </div>
            </div>

            <div class="row g-3">
                <!-- Category Filter -->
                <div class="col-md-6">
                    <label class="form-label small mb-1">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php
                        $cat_query = mysqli_query($conn, "SELECT * FROM category");
                        while ($cat = mysqli_fetch_assoc($cat_query)):
                        ?>
                            <option value="<?= $cat['id'] ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-4">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="1" <?= $status == '1' ? 'selected' : '' ?>>Pending</option>
                        <option value="2" <?= $status == '2' ? 'selected' : '' ?>>Approved</option>
                        <option value="3" <?= $status == '3' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>

                <!-- Apply Button -->
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-outline-primary w-100" type="submit">
                        <i class="bi bi-funnel-fill me-1"></i> Apply
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>