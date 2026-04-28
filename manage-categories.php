<?php
    session_start();
    include('config.php');
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

    $categories = $conn->query("SELECT * FROM category ORDER BY id DESC");
    $sort = $_GET['sort'] ?? 'id';
    $allowed = ['id', 'name', 'count'];
    if (!in_array($sort, $allowed)) $sort = 'id';
    $result = mysqli_query($conn, "
        SELECT category.id, category.name, COUNT(file.id) as count
        FROM category
        LEFT JOIN file ON file.category_id = category.id
        GROUP BY category.id, category.name
        ORDER BY $sort ASC
    ");

?>

<div class="container mt-4">
    <h3 class="mb-3">Manage Categories</h3>

    <?php if (isset($_SESSION['category_success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['category_success']; 
        unset($_SESSION['category_success']); ?></div>
    <?php elseif (isset($_SESSION['category_error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['category_error']; 
        unset($_SESSION['category_error']); ?></div>
    <?php endif; ?>


    <form method="POST" action="config.php" class="mb-4 mt-2 d-flex">
    <input type="hidden" name="add_category" value="1">
        <div class="input-group">
            <input type="text" name="category_name" class="form-control" placeholder="Enter new category" required>
            <button type="submit" class="btn btn-success">Add Category</button>
        </div>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><a href="?nav=manage-categories&sort=id">ID</a></th>
                <th><a href="?nav=manage-categories&sort=name">Category Name</a></th>
                <th><a href="?nav=manage-categories&sort=count"># of Files</a></th>
                <th style="width: 180px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($cat = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $cat['id'] ?></td>
                <td><?= htmlspecialchars($cat['name']) ?></td>
                <td><?= $cat['count'] ?></td>
                <td class="text-nowrap">
                    <div class="d-flex justify-content-around align-items-center w-100">
                        <button type="button" class="btn btn-sm btn-warning" onclick="showEdit(<?= $cat['id'] ?>)">Update</button>

                        <?php if ($cat['count'] == 0): ?>
                        <form method="POST" action="config.php" class="d-inline">
                            <input type="hidden" name="delete_category_id" value="<?= $cat['id'] ?>">
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                        <?php else: ?>
                            <span class="text-muted small">Can't delete</span>
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="config.php" class="mt-2 d-none" id="edit-form-<?= $cat['id'] ?>">
                        <input type="hidden" name="edit_category_id" value="<?= $cat['id'] ?>">
                        <div class="d-flex">
                            <input type="text" name="new_name" class="form-control form-control-sm" placeholder="New name" required>
                            <button class="btn btn-sm btn-primary">Save</button>
                        </div>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script>
function showEdit(id) {
  const form = document.getElementById('edit-form-' + id);
  form.classList.toggle('d-none');
}
</script>
