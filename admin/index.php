<?php
session_start();
require_once '../includes/db-conn.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['admin_id'];
$sql = "SELECT username, email, nic,mobile,profile_picture FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch users from the database
// SQL query to get data
$sql = "SELECT * FROM admins";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Home - EduWide</title>

    <?php include_once("../includes/css-links-inc.php"); ?>
</head>

<body>

    <?php include_once("../includes/header.php") ?>

    <?php include_once("../includes/sadmin-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Home</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            

                            

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include_once("../includes/footer.php") ?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include_once("../includes/js-links-inc.php") ?>
    <script type="text/javascript">
      document.addEventListener('DOMContentLoaded', function () {
        const approveButtons = document.querySelectorAll('.approve-btn');
        const disableButtons = document.querySelectorAll('.disable-btn');
        const deleteButtons = document.querySelectorAll('.delete-btn');

        approveButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');
                window.location.href = `process-admins.php?approve_id=${userId}`;
            });
        });

        disableButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');
                window.location.href = `process-admins.php?disable_id=${userId}`;
            });
        });

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');
                if (confirm("Are you sure you want to delete this user?")) {
                    window.location.href = `process-admins.php?delete_id=${userId}`;
                }
            });
        });
      });
    </script>

</body>

</html>

<?php
// Close database connection
$conn->close();
?>
