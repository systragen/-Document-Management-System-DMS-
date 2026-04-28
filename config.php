<?php
    error_reporting(1);
    session_start();
    $conn = mysqli_connect('localhost', 'root', '', 'tschi_dms');
    
    // LOGIN ==========
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $stmt = $conn->prepare("SELECT * FROM login WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($login = $result->fetch_assoc()) {
            if (password_verify($password, $login['password'])) {
                // Store session data
                $_SESSION['user_id'] = $login['id'];
                $_SESSION['role'] = $login['usertype_id'];
                // Fetch first name from profile
                $profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT first_name FROM profile WHERE login_id = {$login['id']}"));
                $_SESSION['first_name'] = $profile['first_name'] ?? 'User';
                // Role-based redirect
                if ($login['usertype_id'] == 1) {
                    header("Location:index.php?nav=admin-dashboard");
                } elseif ($login['usertype_id'] == 2) {
                    header("Location:index.php?nav=my-dashboard");
                } else {
                    header("Location:index.php?nav=my-dashboard");
                } exit();
            } else {
                $_SESSION['login_error'] = "Incorrect password.";
            }                       
        } else {
            $_SESSION['login_error'] = "Email not found.";
        } 
        header("Location:index.php?nav=home");
        exit();
    }

    // REGISTER ==========
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        $first_name = trim($_POST['first_name']);
        $middle_name = trim($_POST['middle_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
    
        // Validation
        if ($password !== $confirm_password) {
            $_SESSION['register_error'] = "Passwords do not match.";
            header("Location: index.php?nav=home");
            exit();
        }
        // Check if email exists
        $check = $conn->prepare("SELECT * FROM login WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();
        if ($res->num_rows > 0) {
            $_SESSION['register_error'] = "Email already registered.";
            header("Location: index.php?nav=home");
            exit();
        }
        // Password hash
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Insert into login
        $insert_login = $conn->prepare("INSERT INTO login (email, password, usertype_id) VALUES (?, ?, ?)");
        $usertype_id = 3; // default user
        $insert_login->bind_param("ssi", $email, $hashed_password, $usertype_id);
        $insert_login->execute();
        $login_id = $insert_login->insert_id;
        // Insert into profile
        $insert_profile = $conn->prepare("INSERT INTO profile (first_name, middle_name, last_name, login_id) VALUES (?, ?, ?, ?)");
        $insert_profile->bind_param("sssi", $first_name, $middle_name, $last_name, $login_id);
        $insert_profile->execute();
        $_SESSION['register_success'] = "Account created successfully. You can now log in.";
        header("Location: index.php?nav=home");
        exit();
    }

    // ADD CATEGORY ==========
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
        $name = trim($_POST['category_name']);
        // Check for duplicate
        $check = $conn->prepare("SELECT id FROM category WHERE name = ?");
        $check->bind_param("s", $name);
        $check->execute();
        $check_result = $check->get_result();
        if ($check_result->num_rows > 0) {
            $_SESSION['category_error'] = "Category already exists.";
            header("Location: index.php?nav=manage-categories");
            exit();
        }
        // Insert if not duplicate
        $insert = $conn->prepare("INSERT INTO category (name) VALUES (?)");
        $insert->bind_param("s", $name);
        $insert->execute();
        $_SESSION['category_success'] = "Category added successfully.";
        header("Location: index.php?nav=manage-categories");
        exit();
    }

    if (isset($_POST['edit_category_id']) && isset($_POST['new_name'])) {
        $id = $_POST['edit_category_id'];
        $new_name = trim($_POST['new_name']);
        $stmt = $conn->prepare("UPDATE category SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $new_name, $id);
        $stmt->execute();
        $_SESSION['category_success'] = "Category updated successfully.";
        header("Location: index.php?nav=manage-categories");
        exit();
    }
    
    if (isset($_POST['delete_category_id'])) {
        $id = $_POST['delete_category_id'];
        // Check for existing files
        $check = $conn->prepare("SELECT COUNT(*) FROM file WHERE category_id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $check->bind_result($file_count);
        $check->fetch();
        $check->close();
        if ($file_count > 0) {
            $_SESSION['category_error'] = "Cannot delete a category with files.";
        } else {
            $del = $conn->prepare("DELETE FROM category WHERE id = ?");
            $del->bind_param("i", $id);
            $del->execute();
            $_SESSION['category_success'] = "Category deleted.";
        }
        header("Location: index.php?nav=manage-categories");
        exit();
    }

    // FILE UPLOAD ========== 
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $category_id = intval($_POST['category_id']);
        $user_id = $_SESSION['user_id'];
        $upload_dir = "uploads/";
        // Basic validation
        if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['upload_error'] = "Error uploading the file.";
            header("Location: index.php?nav=upload");
            exit();
        }
        // File type validation
        $file_type = mime_content_type($_FILES['pdf_file']['tmp_name']);
        if ($file_type !== 'application/pdf') {
            $_SESSION['upload_error'] = "Only PDF files are allowed.";
            header("Location: index.php?nav=upload");
            exit();
        }
        // Ensure uploads directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        // Generate unique file name
        $filename = time() . "_" . basename($_FILES['pdf_file']['name']);
        $target_path = $upload_dir . $filename;
        if (!move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target_path)) {
            $_SESSION['upload_error'] = "Failed to save the uploaded file.";
            header("Location: index.php?nav=upload");
            exit();
        }
        // Insert file into database
        $stmt = $conn->prepare("INSERT INTO file (name, stored_name, description, upload_date, category_id, status_id, login_id) VALUES (?, ?, ?, NOW(), ?, ?, ?)");
        $status_id = 1; // default status
        $stmt->bind_param("sssiii", $name, $filename, $description, $category_id, $status_id, $user_id);

        if ($stmt->execute()) {
            $_SESSION['upload_success'] = "File uploaded successfully!";
        } else {
            $_SESSION['upload_error'] = "Database error. Please try again.";
        }
        header("Location: index.php?nav=file-status");
        exit();
    }

    if (isset($_POST['delete_file_id'])) {
        $file_id = intval($_POST['delete_file_id']);
        $user_id = $_SESSION['user_id'];
        $is_admin = $_SESSION['role'] == 1;
    
        if ($is_admin) {
            // Admin: No need to match login_id
            $query = $conn->prepare("SELECT stored_name FROM file WHERE id = ?");
            $query->bind_param("i", $file_id);
        } else {
            // Regular user: Must own the file
            $query = $conn->prepare("SELECT stored_name FROM file WHERE id = ? AND login_id = ?");
            $query->bind_param("ii", $file_id, $user_id);
        }
    
        $query->execute();
        $result = $query->get_result();
    
        if ($file = $result->fetch_assoc()) {
            $file_path = "uploads/" . $file['stored_name'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
    
            // Delete from DB
            if ($is_admin) {
                $conn->query("DELETE FROM file WHERE id = $file_id");
            } else {
                $conn->query("DELETE FROM file WHERE id = $file_id AND login_id = $user_id");
            }
    
            $_SESSION['delete_temp_success'] = "File deleted successfully.";
        } else {
            $_SESSION['delete_temp_success'] = "File not found or permission denied.";
        }
    
        // Redirect
        $redirect = $is_admin ? "all-files" : "file-status";
        header("Location: index.php?nav=$redirect");
        exit();
    }
    
    
    // REVIEW FILES =============
    if (isset($_POST['review_file_id']) && isset($_POST['approve_file'])) {
        $file_id = $_POST['review_file_id'];
        $stmt = $conn->prepare("UPDATE file SET status_id = 2 WHERE id = ?");
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $_SESSION['review_success'] = "File approved successfully.";
        header("Location: index.php?nav=review-uploads");
        exit();
    }

    if (isset($_POST['review_file_id']) && isset($_POST['reject_file']) && isset($_POST['rejection_remark'])) {
        $file_id = $_POST['review_file_id'];
        $remark = trim($_POST['rejection_remark']);
    
        $stmt = $conn->prepare("UPDATE file SET status_id = 3, remarks = ? WHERE id = ?");
        $stmt->bind_param("si", $remark, $file_id);
        $stmt->execute();
    
        $_SESSION['review_success'] = "File rejected with remarks.";
        header("Location: index.php?nav=review-uploads");
        exit();
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_remarks_file_id'])) {
        $file_id = intval($_POST['clear_remarks_file_id']);
    
        // Ensure only admin or moderator can do this
        if (in_array($_SESSION['role'], [1, 2])) {
            $stmt = $conn->prepare("UPDATE file SET remarks = NULL, status_id = 1 WHERE id = ?");
            $stmt->bind_param("i", $file_id);
            $stmt->execute();
            header("Location: index.php?nav=review-uploads");
            exit();
        } else {
            header("Location: index.php?nav=review-uploads");
            exit();
        }
    }    

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_file'])) {
        $file_id = intval($_POST['file_id']);
        $new_name = trim($_POST['new_name']);
        $new_desc = trim($_POST['new_description']);
        $new_cat = intval($_POST['new_category_id']);
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['role'];
    
        // Fetch current file data
        $query = $user_role == 1 
            ? $conn->prepare("SELECT name, description, category_id FROM file WHERE id = ?") 
            : $conn->prepare("SELECT name, description, category_id FROM file WHERE id = ? AND login_id = ?");
        
        if ($user_role == 1) {
            $query->bind_param("i", $file_id);
        } else {
            $query->bind_param("ii", $file_id, $user_id);
        }
    
        $query->execute();
        $result = $query->get_result();
    
        if ($row = $result->fetch_assoc()) {
            // Check if there are changes
            if (
                $row['name'] === $new_name &&
                $row['description'] === $new_desc &&
                (int)$row['category_id'] === $new_cat
            ) {
                $_SESSION['delete_temp_success'] = "No changes detected. File not updated.";
            } else {
                // Proceed with update
                $stmt = $user_role == 1
                    ? $conn->prepare("UPDATE file SET name = ?, description = ?, category_id = ?, status_id = 1 WHERE id = ?")
                    : $conn->prepare("UPDATE file SET name = ?, description = ?, category_id = ?, status_id = 1 WHERE id = ? AND login_id = ?");
                
                if ($user_role == 1) {
                    $stmt->bind_param("ssii", $new_name, $new_desc, $new_cat, $file_id);
                } else {
                    $stmt->bind_param("ssiii", $new_name, $new_desc, $new_cat, $file_id, $user_id);
                }
    
                $stmt->execute();
                $_SESSION['delete_temp_success'] = "File updated successfully. Status reset to pending.";
            }
        } else {
            $_SESSION['delete_temp_success'] = "File not found or permission denied.";
        }
    
        // Redirect based on role
        $redirect_page = $user_role == 1 ? "all-files" : "file-status";
        header("Location: index.php?nav=$redirect_page");
        exit();
    }    
       

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $middle_name = trim($_POST['middle_name']);
        $last_name = trim($_POST['last_name']);
        $user_id = $_SESSION['user_id'];
    
        // Update name fields
        $stmt = $conn->prepare("UPDATE profile SET first_name = ?, middle_name = ?, last_name = ? WHERE login_id = ?");
        $stmt->bind_param("sssi", $first_name, $middle_name, $last_name, $user_id);
        $stmt->execute();
    
        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "elems/profile-picture/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
    
            $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $filename = $user_id . '.png'; // Standardize filename (overwrite allowed)
            $upload_path = $target_dir . $filename;
    
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path);
        }
    
        echo "OK";
        exit();
    }
    
    // Update user role
    if (isset($_POST['update_role_id'], $_POST['new_role'])) {
        $id = intval($_POST['update_role_id']);
        $role = intval($_POST['new_role']);
        $stmt = $conn->prepare("UPDATE login SET usertype_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $role, $id);
        $stmt->execute();

        $_SESSION['delete_temp_success'] = "User role updated.";
        header("Location: index.php?nav=manage-users");
        exit();
    }

    // Delete user
    if (isset($_POST['delete_user_id'])) {
        $id = intval($_POST['delete_user_id']);
        // Delete from profile first due to FK constraints
        mysqli_query($conn, "DELETE FROM profile WHERE login_id = $id");
        mysqli_query($conn, "DELETE FROM login WHERE id = $id");

        $_SESSION['delete_temp_success'] = "User deleted successfully.";
        header("Location: index.php?nav=manage-users");
        exit();
    }

    if ($_FILES['pdf_file']['size'] > 5 * 1024 * 1024) { // 5MB in bytes
        $_SESSION['upload_error'] = "File size exceeds the 5MB limit.";
        header("Location: index.php?nav=upload");
        exit();
    }
    
?>