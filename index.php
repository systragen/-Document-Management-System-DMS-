<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TSCHI DMS</title>
    <link rel="icon" type="image/x-icon" href="elems/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles.css">
  </head>
  <body>
    <?php
      error_reporting(1);
      session_start();
      include('nav.php');
      nav();
      switch ($_REQUEST['nav']) {
        case 'home':
          include('home.php');
          break;
        case 'profile':
          include('profile.php');
          break;
        case 'logout':
          include('logout.php');
          break;
        case 'admin-dashboard':
          include('admin-dashboard.php');
          break;
        case 'manage-users':
          include('manage-users.php');
          break;
        case 'manage-categories':
          include('manage-categories.php');
          break;
        case 'all-files':
          include('all-files.php');
          break;
        case 'my-dashboard':
          include("my-dashboard.php");
          break;
        case 'upload':
          include("upload.php");
          break;
        case 'file-status':
          include("file-status.php");
          break;
        case 'review-uploads':
          include("review-uploads.php");
          break;
        default:
          include('home.php');
          break;
      }
	  ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  </body>
</html>