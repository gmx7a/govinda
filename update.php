<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["member_id"])) {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION["member_id"];
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$sql = "SELECT * FROM borrowings WHERE id = ? AND member_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id, $member_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    header("Location: home.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_title = htmlspecialchars(trim($_POST["book_title"]));
    $book_category = htmlspecialchars(trim($_POST["book_category"]));
    $quantity = intval($_POST["quantity"]);
    $status = htmlspecialchars(trim($_POST["status"]));

    if (empty($book_title) || empty($book_category) || $quantity <= 0 || empty($status)) {
        $message = "Please fill in all fields correctly.";
    } else {
        $sql = "UPDATE borrowings SET book_title = ?, book_category = ?, quantity = ?, status = ? WHERE id = ? AND member_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssisii", $book_title, $book_category, $quantity, $status, $id, $member_id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: home.php");
            exit();
        } else {
            $message = "Failed to update record.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Borrowing Record</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning"><h4>Update Borrowing Record</h4></div>
                <div class="card-body">
                    <?php if ($message != "") { ?><div class="alert alert-danger"><?php echo $message; ?></div><?php } ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Book Title</label>
                            <input type="text" name="book_title" class="form-control" value="<?php echo $row['book_title']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Book Category</label>
                            <select name="book_category" class="form-select">
                                <option value="Fiction" <?php if ($row['book_category'] == 'Fiction') echo 'selected'; ?>>Fiction</option>
                                <option value="Science" <?php if ($row['book_category'] == 'Science') echo 'selected'; ?>>Science</option>
                                <option value="Technology" <?php if ($row['book_category'] == 'Technology') echo 'selected'; ?>>Technology</option>
                                <option value="Education" <?php if ($row['book_category'] == 'Education') echo 'selected'; ?>>Education</option>
                                <option value="History" <?php if ($row['book_category'] == 'History') echo 'selected'; ?>>History</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="<?php echo $row['quantity']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Borrowed" <?php if ($row['status'] == 'Borrowed') echo 'selected'; ?>>Borrowed</option>
                                <option value="Returned" <?php if ($row['status'] == 'Returned') echo 'selected'; ?>>Returned</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning">Update</button>
                        <a href="home.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
