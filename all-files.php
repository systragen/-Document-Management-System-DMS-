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

// Fetch filtered files
$result = mysqli_query($conn, "
    SELECT file.*, file.stored_name, category.name AS category_name, status.name AS status_name 
    FROM file 
    JOIN category ON file.category_id = category.id
    JOIN status ON file.status_id = status.id
    $filter_sql
    ORDER BY file.upload_date DESC
");

// Layout selector
$layout = $_SESSION['layout'] ?? 'grid';
if (isset($_GET['layout']) && in_array($_GET['layout'], ['grid', 'table'])) {
    $_SESSION['layout'] = $_GET['layout'];
    $layout = $_GET['layout'];
}
?>

<div class="container my-4">
    <h3 class="mb-2">All Files</h3>
    <?php include('search-filter.php'); ?>
    <hr class="mb-2">
    <?php include('file-explore.php'); ?>
</div>

<?php include('file-view-edit.php'); ?>

<script>
function setLayout(type) {
    const params = new URLSearchParams(window.location.search);
    params.set('layout', type);
    window.location.search = params.toString();
}
</script>
<?php
include('file-view-edit.php');;
?>