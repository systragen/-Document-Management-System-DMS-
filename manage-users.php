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

$search = trim($_GET['search'] ?? '');
$role_filter = $_GET['role'] ?? '';

$conditions = ["login.usertype_id != 1"]; // Exclude admin

if ($search !== '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $conditions[] = "(login.email LIKE '%$safe_search%' OR profile.first_name LIKE '%$safe_search%' OR profile.last_name LIKE '%$safe_search%')";
}

if (in_array($role_filter, ['2', '3'])) {
    $conditions[] = "login.usertype_id = $role_filter";
}

$where_sql = count($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';


// Fetch all users (excluding admin itself)
$result = mysqli_query($conn, "
    SELECT login.id AS login_id, login.email, login.usertype_id,
           profile.first_name, profile.middle_name, profile.last_name,
           COUNT(file.id) AS file_count
    FROM login
    LEFT JOIN profile ON login.id = profile.login_id
    LEFT JOIN file ON login.id = file.login_id
    $where_sql
    GROUP BY login.id
    ORDER BY login.usertype_id ASC
");

?>
<div class="container my-4">
    <h3 class="mb-2">Manage Users</h3>
    <form method="GET" action="index.php" class="mb-4">
        <input type="hidden" name="nav" value="manage-users">

        <div class="row g-3 align-items-center">
            <!-- Search Input + Search Button -->
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control shadow-sm" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-success" type="submit">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                </div>
            </div>

            <!-- Role Filter -->
            <div class="col-md-4">
                <select name="role" class="form-select shadow-sm">
                    <option value="">Filter by Role</option>
                    <option value="2" <?= $role_filter == '2' ? 'selected' : '' ?>>Moderator</option>
                    <option value="3" <?= $role_filter == '3' ? 'selected' : '' ?>>User</option>
                </select>
            </div>

            <!-- Apply Button -->
            <div class="col-md-2">
                <button class="btn btn-outline-primary w-100 shadow-sm" type="submit">
                    <i class="bi bi-funnel me-1"></i>Apply
                </button>
            </div>
        </div>
    </form>



    <div class="d-flex justify-content-between">
        <?php if (isset($_SESSION['delete_temp_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show py-1 mb-0 d-flex align-items-center" role="alert" style="height: 38px;">
                <?= $_SESSION['delete_temp_success']; ?>
                <button type="button" class="btn-close btn-sm ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['delete_temp_success']); ?>
        <?php endif; ?>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Contributions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php
                                echo $user['usertype_id'] == 2 ? 'Moderator' : 'User';
                            ?>
                        </td>
                        <td>
                          <span class="badge d-flex justify-content-center bg-info text-dark"><?= (int) $user['file_count'] ?> file<?= $user['file_count'] != 1 ? 's' : '' ?></span>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-inline-flex gap-1">
                                <!-- Promote/Demote Role -->
                                <form method="POST" action="config.php" style="width: 180px;">
                                    <input type="hidden" name="update_role_id" value="<?= $user['login_id'] ?>">
                                    <input type="hidden" name="new_role" value="<?= $user['usertype_id'] == 3 ? 2 : 3 ?>">
                                    <button class="btn btn-sm btn-outline-warning text-dark w-100">
                                        <?= $user['usertype_id'] == 3 ? 'Promote to Moderator' : 'Demote to User' ?>
                                    </button>
                                </form>

                                <!-- Delete User -->
                                <form method="POST" action="config.php" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="delete_user_id" value="<?= $user['login_id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
