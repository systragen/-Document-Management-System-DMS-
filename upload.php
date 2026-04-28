<?php
session_start();
include('config.php');

// Block access for admins
if ($_SESSION['role'] != 2 && $_SESSION['role'] != 3) {
    header("Location: index.php?nav=home");
    exit();
}

// Fetch available categories
$categories = mysqli_query($conn, "SELECT * FROM category");

?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-light">
                    <h5 class="mb-0">Upload New File</h5>
                </div>
                <div class="card-body">

                    <?php if (isset($_SESSION['upload_success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $_SESSION['upload_success']; unset($_SESSION['upload_success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['upload_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['upload_error']; unset($_SESSION['upload_error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="config.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="upload" value="1">

                        <div class="mb-3">
                            <label for="file_name" class="form-label">File Name</label>
                            <input type="text" name="name" class="form-control" id="file_name" placeholder="Enter descriptive name" required>
                        </div>

                        <div class="mb-3">
                            <label for="desc" class="form-label">File Description</label>
                            <textarea name="description" class="form-control" rows="3" id="desc" placeholder="Brief details about this file..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Select Category</label>
                            <select name="category_id" class="form-select" id="category" required>
                                <option value="">-- Choose category --</option>
                                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="pdf_file" class="form-label">Upload PDF File</label>
                            <input type="file" name="pdf_file" id="pdf_file" class="form-control" accept="application/pdf" required>
                            <small class="form-text text-muted">Only PDF files are allowed. Max size: 10MB.</small>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Submit File</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('pdf_file').addEventListener('change', function() {
    const file = this.files[0];
    const maxSizeMB = 10; // Max file size in MB

    if (file && file.size > maxSizeMB * 1024 * 1024) {
        alert(`File exceeds ${maxSizeMB}MB. Please select a smaller file.`);
        this.value = ''; // Clear the input
    }
});
</script>