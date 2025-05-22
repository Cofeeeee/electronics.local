<?php
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['email'])) {
	$email = trim($_POST['email']);
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$email_safe = mysqli_real_escape_string($conn, $email);

		// Записуємо в таблицю, якщо ще не підписаний
		$check_sql = "SELECT id FROM Subscribers WHERE email = '$email_safe'";
		$check_result = mysqli_query($conn, $check_sql);

		if (mysqli_num_rows($check_result) === 0) {
			$insert_sql = "INSERT INTO Subscribers (email, subscribed_at) VALUES ('$email_safe', NOW())";
			if (mysqli_query($conn, $insert_sql)) {
				echo "Дякуємо за підписку!";
			} else {
				echo "Помилка збереження. Спробуйте ще раз.";
			}
		} else {
			echo "Ви вже підписані!";
		}
	} else {
		echo "Невірний формат email.";
	}
} else {
	echo "Введіть email.";
}
?>