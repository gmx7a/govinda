<?php
session_start();
include "db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $message = "Please enter email and password.";
    } else {
        $sql = "SELECT * FROM members WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row["password"])) {
                $_SESSION["member_id"] = $row["id"];
                $_SESSION["member_name"] = $row["name"];
                header("Location: home.php");
                exit();
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "Email not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Library Borrowing System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4>Member Login</h4>
                </div>
                <div class="card-body">
                    <?php if ($message != "") { ?>
                        <div class="alert alert-danger"><?php echo $message; ?></div>
                    <?php } ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter password">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                    <p class="mt-3 text-center">Do not have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
