<?php
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Проверка подключения
if (!$conn) {
	// Логируем ошибку при подключении с временем и IP
	error_log("DB connection failed: " . mysqli_connect_error() . " | IP: " . $_SERVER['REMOTE_ADDR'] . " | Time: " . date('Y-m-d H:i:s'));

	// Устанавливаем статус ошибки
	http_response_code(500);

	// Показываем общее сообщение для пользователя
	exit("Сайт тимчасово недоступний. Спробуйте пізніше.");
}

// Установка кодировки
mysqli_set_charset($conn, "utf8mb4");


// Устанавливаем часовой пояс сессии БД (Киев, UTC+3)
mysqli_query($conn, "SET time_zone = '+03:00'");
?>