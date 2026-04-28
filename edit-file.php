<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    echo "<div class='modal-body'><p class='text-danger'>Unauthorized access.</p></div>";
    exit();
}

$file_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch file data
if (in_array($_SESSION['role'], [1])) {
    // Admin can edit any file
    $query = $conn->prepare("
        SELECT file.*, category.name AS category_name 
        FROM file 
        JOIN category ON file.category_id = category.id 
        WHERE file.id = ?
    ");
    $query->bind_param("i", $file_id);
} else {
    // Regular user can only edit their own file
    $query = $conn->prepare("
        SELECT file.*, category.name AS category_name 
        FROM file 
        JOIN category ON file.category_id = category.id 
        WHERE file.id = ? AND file.login_id = ?
    ");
    $query->bind_param("ii", $file_id, $user_id);
}
$query->execute();
$result = $query->get_result();
$file = $result->fetch_assoc();

// Fetch all categories
$categories = mysqli_query($conn, "SELECT * FROM category ORDER BY name ASC");
?>

<?php if ($file): ?>
<div class="modal-header bg-secondary text-white">
    <h5 class="modal-title">Edit File</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <form method="POST" action="config.php">
        <input type="hidden" name="edit_file" value="1">
        <input type="hidden" name="file_id" value="<?= $file['id'] ?>">

        <div class="mb-3">
            <label class="form-label">File Name</label>
            <input type="text" class="form-control" name="new_name" 
                   value="<?= htmlspecialchars($file['name']) ?>" 
                   placeholder="Enter new file name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="new_description" rows="4" placeholder="Describe your file" required><?= htmlspecialchars($file['description']) ?></textarea>
        </div>

        <div class="mb-4">
            <label class="form-label">Category</label>
            <select name="new_category_id" class="form-select" required>
                <option value="">-- Select Category --</option>
                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $file['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-success">
                Save Changes
            </button>
        </div>
    </form>
</div>
<?php else: ?>
<div class="modal-body">
  <p class="text-danger fw-semibold">File not found or permission denied.</p>
</div>
<?php endif; ?>
