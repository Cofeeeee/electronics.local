<?php
session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: ../index.php");
	exit();
}

$user_id = $_SESSION['user_id'];

// Подключение к базе данных (однократно)
require_once '../includes/db_connect.php';

// Удаляем пользователя (Небезопасно!)
$sql = "DELETE FROM Users WHERE user_id = $user_id";
if (mysqli_query($conn, $sql)) {
	// Закрываем сессию и перенаправляем
	require_once 'logout.php';
} else {
   echo "Помилка видалення акаунта.";
}

mysqli_close($conn);
?>