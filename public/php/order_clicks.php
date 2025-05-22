<?php
session_start();

// Подключение к базе данных (однократно)
require_once '../includes/db_connect.php';

// Проверяем, что передан offer_id
if (!isset($_GET['offer_id']) || !is_numeric($_GET['offer_id'])) {
    die("Некорректний ID пропозиції");
}

$offer_id = (int) $_GET['offer_id'];

// Определяем product_id по offer_id
$sql_product = "SELECT product_id FROM Offers WHERE offer_id = $offer_id";
$result = mysqli_query($conn, $sql_product);
$row = mysqli_fetch_assoc($result);

if (!$row) {
   die("Пропозицію не знайдено");
}

$product_id = $row['product_id'];

// Проверяем, есть ли авторизованный пользователь
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

// Для неавторизованного пользователя создаем cookie
$cookie_name = "clicked_product_" . $product_id;

if ($user_id === NULL) {
	// Если уже есть запись в cookie, перенаправляем без записи в БД
	if (isset($_COOKIE[$cookie_name])) {
		header("Location: {$_GET['url']}");
		exit;
	} else {
		// Устанавливаем cookie на 7 дней
		setcookie($cookie_name, "clicked", time() + (7 * 24 * 60 * 60), "/");
	}
}

// Проверяем, есть ли уже запись в БД для этого пользователя и товара
$sql_check = "SELECT 1 FROM Orders_Clicks WHERE user_id " . ($user_id === NULL ? "IS NULL" : "= $user_id") . " 
              AND offer_id IN (SELECT offer_id FROM Offers WHERE product_id = $product_id) LIMIT 1";
$result = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result) == 0) {
	// Записываем клик в БД
	$sql_insert_click = "INSERT INTO Orders_Clicks (user_id, offer_id) VALUES (" . ($user_id === NULL ? "NULL" : $user_id) . ", '$offer_id')";
	mysqli_query($conn, $sql_insert_click);
}

// Перенаправляем пользователя в магазин
header("Location: {$_GET['url']}");
exit;
?>