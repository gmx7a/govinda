<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["member_id"])) {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION["member_id"];
$member_name = $_SESSION["member_name"];
$message = "";
$alert = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_title = htmlspecialchars(trim($_POST["book_title"]));
    $book_category = htmlspecialchars(trim($_POST["book_category"]));
    $quantity = intval($_POST["quantity"]);
    $status = "Borrowed";

    if (empty($book_title) || empty($book_category) || $quantity <= 0) {
        $message = "Please fill in all book borrowing details correctly.";
        $alert = "danger";
    } else {
        $sql = "INSERT INTO borrowings (member_id, book_title, book_category, quantity, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issis", $member_id, $book_title, $book_category, $quantity, $status);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Book borrowing record added successfully.";
            $alert = "success";
        } else {
            $message = "Failed to add borrowing record.";
            $alert = "danger";
        }
    }
}

$sql = "SELECT * FROM borrowings WHERE member_id = ? ORDER BY id DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $member_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home - Library Borrowing System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">Library Borrowing System</span>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</nav>
<div class="container mt-4">
    <div class="alert alert-info">Welcome, <strong><?php echo $member_name; ?></strong></div>
    <?php if ($message != "") { ?>
        <div class="alert alert-<?php echo $alert; ?>"><?php echo $message; ?></div>
    <?php } ?>
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white"><h5>Borrow Book</h5></div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Book Title</label>
                    <input type="text" name="book_title" class="form-control" placeholder="Enter book title">
                </div>
                <div class="mb-3">
                    <label class="form-label">Book Category</label>
                    <select name="book_category" class="form-select">
                        <option value="">-- Select Category --</option>
                        <option value="Fiction">Fiction</option>
                        <option value="Science">Science</option>
                        <option value="Technology">Technology</option>
                        <option value="Education">Education</option>
                        <option value="History">History</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" placeholder="Enter quantity">
                </div>
                <button type="submit" class="btn btn-primary">Borrow Book</button>
            </form>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-header bg-secondary text-white"><h5>My Borrowed Books</h5></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Book Title</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row["book_title"]; ?></td>
                        <td><?php echo $row["book_category"]; ?></td>
                        <td><?php echo $row["quantity"]; ?></td>
                        <td><span class="badge bg-info text-dark"><?php echo $row["status"]; ?></span></td>
                        <td>
                            <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </td>
                    </tr>
                    <?php }} else { ?>
                    <tr><td colspan="6" class="text-center">No borrowing records found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
