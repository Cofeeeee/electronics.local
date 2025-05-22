<?php
session_start();

header('Content-Type: application/json');

// Подключение к базе данных (однократно)
require_once '../includes/db_connect.php';

// Проверяем, есть ли данные о товаре
if (isset($_POST['offer_id'])) {
	$offer_id = intval($_POST['offer_id']);
	$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

	if ($user_id) {
		// Перевірка, чи товар вже в кошику
		$check_sql = "SELECT cart_id FROM Cart WHERE user_id = $user_id AND offer_id = $offer_id";
		$check_result = mysqli_query($conn, $check_sql);
	
		if (mysqli_num_rows($check_result) > 0) {
			echo json_encode([
				'status' => 'warning',
				'message' => 'Товар вже у кошику'
			]);
			exit;
		}

		// Авторизованный пользователь — пишем в БД
		$insert_sql = "INSERT INTO Cart (user_id, offer_id) VALUES ($user_id, $offer_id)";
		$insert_result = mysqli_query($conn, $insert_sql);

		if ($insert_result) {
			// Отримуємо кількість товарів в кошику
			$count_sql = "SELECT COUNT(*) as total_items FROM Cart WHERE user_id = $user_id";
			$count_result = mysqli_query($conn, $count_sql);
			$count_row = mysqli_fetch_assoc($count_result);
			$cart_count = $count_row['total_items'];

			// Повертаємо кількість товарів в кошику для авторизованих
			echo json_encode([
				'status' => 'success',
				'message' => 'Товар доданий до кошика (сесія)',
				'cart_count' => $cart_count // Повертаємо кількість товарів
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Помилка при додаванні'
			]);
		}
	} else {
		// Для неавторизованных пользователей — сохраняем в cookie
		$cookie_name = "cart";
		$cart = [];

		if (isset($_COOKIE[$cookie_name])) {
			$cart = json_decode($_COOKIE[$cookie_name], true);
			if (json_last_error() !== JSON_ERROR_NONE || !is_array($cart)) {
				$cart = [];
		  	}
		}

		if (in_array($offer_id, $cart)) {
			echo json_encode([
				'status' => 'warning',
				'message' => 'Товар уже в кошику'
			]);
			exit;
		}

   	// Додаємо товар в кошик
   	$cart[] = $offer_id;
   	setcookie($cookie_name, json_encode($cart), time() + (86400 * 30), "/"); // 30 днів

		// Считаем сколько товаров осталось в куки
		$cart_count = count($cart);

		// Повертаємо кількість товарів в кошику для гостей
		echo json_encode([
			'status' => 'success',
			'message' => 'Товар доданий до кошика (куки)',
			'cart_count' => $cart_count
		]);
   }
} else {
   echo json_encode([
		'status' => 'error',
		'message' => 'Некоректний ID пропозиції',
		'cart_count' => 0
	]);
}
?>