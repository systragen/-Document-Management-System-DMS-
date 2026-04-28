<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?nav=home");
    exit();
}
?>
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" id="modalContent">
        <script>
        function loadViewFile(id) {
        fetch('view-file.php?id=' + id)
            .then(res => res.text())
            .then(html => {
            document.getElementById('modalContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('viewModal')).show();
            });
        }

        function loadEditFile(id) {
        fetch('edit-file.php?id=' + id)
            .then(res => res.text())
            .then(html => {
            document.getElementById('modalContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('viewModal')).show();
            });
        }
        </script>
    </div>
  </div>
</div>