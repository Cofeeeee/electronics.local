<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/db_connect.php';

$cart_count = 0;

if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
	$sql = "SELECT COUNT(*) as total_items FROM Cart WHERE user_id = $user_id";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_assoc($result);
	$cart_count = (int)$row['total_items'];
} else {
	$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
	$cart_count = is_array($cart) ? (int)count($cart) : 0;
}

echo json_encode(['cart_count' => $cart_count]);
?>