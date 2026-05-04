<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["member_id"])) {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION["member_id"];
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$sql = "DELETE FROM borrowings WHERE id = ? AND member_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id, $member_id);
mysqli_stmt_execute($stmt);

header("Location: home.php");
exit();
?>
