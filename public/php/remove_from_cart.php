<?php
session_start();

header('Content-Type: application/json');

// Подключение к базе данных (однократно)
require_once '../includes/db_connect.php';

// Проверяем, есть ли данные о товаре
if (isset($_POST['offer_id'])) {
	$offer_id = intval($_POST['offer_id']); // Преобразуем в целое число для безопасности
	$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

	if ($user_id) {
		// Для авторизованных пользователей — удаляем товар из БД
		$query = "DELETE FROM Cart WHERE user_id = $user_id AND offer_id = $offer_id";
		mysqli_query($conn, $query);

		// Теперь считаем сколько товаров осталось у юзера
		$count_sql = "SELECT COUNT(*) as total_items FROM Cart WHERE user_id = $user_id";
		$count_result = mysqli_query($conn, $count_sql);
		$count_row = mysqli_fetch_assoc($count_result);
		$cart_count = $count_row['total_items'];

		echo json_encode([
			'status' => 'success',
			'message' => 'Товар видалено з кошика',
			'cart_count' => $cart_count
		]);
	} else {
		// Для неавторизованных пользователей — удаляем товар из куки
		$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

		if (($key = array_search($offer_id, $cart)) !== false) {
			unset($cart[$key]);  // Удаляем товар из корзины
			setcookie('cart', json_encode(array_values($cart)), time() + (86400 * 30), "/"); // Обновляем куки
			
			// Считаем сколько товаров осталось в куки
			$cart_count = count($cart);
			
			echo json_encode([
				'status' => 'success',
				'message' => 'Товар видалено з кошика',
				'cart_count' => $cart_count
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Товар не знайдено в кошику',
				'cart_count' => isset($cart) ? count($cart) : 0
			]);
		}
	}
} else {
   echo json_encode([
		'status' => 'error',
		'message' => 'Помилка: offer_id не передано',
		'cart_count' => 0
	]);
}
?>