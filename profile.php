<?php
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php?nav=home");
  exit();
}
include('config.php');
$login_id = $_SESSION['user_id']; // Make sure this is set
$query = mysqli_query($conn, "SELECT * FROM profile WHERE login_id = $login_id");
$data = mysqli_fetch_assoc($query);

$firstname = $data['first_name'] ?? '';
$middlename = $data['middle_name'] ?? '';
$lastname = $data['last_name'] ?? '';
?>
<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <!-- Profile Card -->
      <div class="card shadow-sm border-0">
        <div class="card-body text-center py-4">
          <!-- Profile Picture -->
          <label for="pic-file" class="profile-pic-hover">
            <?php 
            $profile_pic_path = "elems/profile-picture/{$login_id}.png";
            if (!file_exists($profile_pic_path)) {
              $profile_pic_path = "elems/profile-picture/default.png";
            }
            ?>
            <img src="<?= $profile_pic_path ?>" alt="Profile Picture" id="img-display"
              class="rounded-circle mb-3 border border-3 border-secondary-subtle"
              width="130" height="130" style="object-fit: cover; cursor: pointer;">
          </label>
          <input type="file" id="pic-file" onchange="return showPic()" style="display: none;">
          <h5 class="fw-bold mb-4"><?= $firstname . ' ' . $lastname ?></h5>

          <!-- Profile Info Form -->
          <div class="text-start">
            <div class="mb-3">
              <label class="form-label">First Name</label>
              <input class="form-control" id="first-name" type="text" value="<?= $firstname ?>" placeholder="First Name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Middle Name</label>
              <input class="form-control" id="middle-name" type="text" value="<?= $middlename ?>" placeholder="Middle Name">
            </div>
            <div class="mb-3">
              <label class="form-label">Last Name</label>
              <input class="form-control" id="last-name" type="text" value="<?= $lastname ?>" placeholder="Last Name" required>
            </div>
          </div>
          <button id="save-profile-button" class="btn btn-primary w-100 mt-2" onclick="return updateProfileBtn()">Update Profile</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
function showPic() {
  const file = document.getElementById("pic-file").files[0];
  if (file && file.type.startsWith("image/")) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById("img-display").src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}
</script>
<script>
function updateProfileBtn() {
  const formData = new FormData();
  formData.append('update_profile', 1);
  formData.append('first_name', document.getElementById('first-name').value.trim());
  formData.append('middle_name', document.getElementById('middle-name').value.trim());
  formData.append('last_name', document.getElementById('last-name').value.trim());

  const file = document.getElementById('pic-file').files[0];
  if (file) {
    formData.append('profile_picture', file);
  }

  fetch('config.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(response => {
    showToast('Profile updated successfully!');
  })
  .catch(error => {
    console.error(error);
    showToast('Failed to update profile.', 'danger');
  });

  return false;
}
</script>
<!-- Toast Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
  <div id="profile-toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toast-message">Profile updated successfully!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script>
function showToast(message, type = 'success') {
  const toastEl = document.getElementById('profile-toast');
  const toastBody = document.getElementById('toast-message');

  toastBody.innerText = message;
  toastEl.className = `toast align-items-center text-bg-${type} border-0`;

  const toast = new bootstrap.Toast(toastEl);
  toast.show();
}
</script>