<?php
session_start(); // Ініціалізація сесії

// Отримуємо дані з форми POST
// trim() видаляє пробіли, табуляції з початку та кінця рядка
$name = trim($_POST['name']);
$tel = trim($_POST['tel']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

/*Захист від XSS, перетворює спеціальні символи в HTML-код:
"<" → &lt;
">" → &gt;
"&" → &amp;
'"' → &quot;
"'" → &#039;*/
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$tel = htmlspecialchars($tel, ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

// Хешуємо пароль (БЕЗ `htmlspecialchars()`)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Подключение к базе данных (однократно)
require_once '../includes/db_connect.php';

// SQL запит для перевірки, чи існує користувач з таким email чи телефоном
$checkUser = "SELECT * FROM Users WHERE email = '$email' OR phone = '$tel'";
$resultUser = mysqli_query($conn, $checkUser);

// Якщо знайдений хоча б один запис з таким email або з таким телефоном, вивести повідомлення про помилку
if (mysqli_num_rows($resultUser) > 0) {
   echo json_encode([
		'status' => 'error',
		'message' => 'Користувач з таким email або телефоном вже існує!'
	]);
} else {
   // Якщо email і телефон унікальний, формуємо SQL запит для вставки нового користувача в БД
   $sql = "INSERT INTO Users (name, phone, email, password) VALUES ('$name', '$tel', '$email', '$hashedPassword')";

   // Виконання запиту на вставку
   if (mysqli_query($conn, $sql)) {
		// Отримуємо ID нового користувача
		$userId = mysqli_insert_id($conn);

		// Після успішної реєстрації зберігаємо лише user_id в сесії
		$_SESSION['user_id'] = $userId;
		
      // Успіх
      echo json_encode([
			'status' => 'success',
			'message' => 'Реєстрація успішна!'
		]);
   } else {
      // Якщо сталася помилка при вставці, вивести SQL запит та повідомлення про помилку
      echo json_encode([
			'status' => 'error',
			'message' => 'Помилка: ' . mysqli_error($conn)
		]);
   }
}

// Закриття з'єднання з базою даних
mysqli_close($conn);
?>