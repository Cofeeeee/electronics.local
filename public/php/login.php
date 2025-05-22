<?php
session_start(); // Ініціалізація сесії

// Отримуємо дані з форми POST
$login = trim($_POST['login']); // Це може бути email або телефон
$password = trim($_POST['password']);

// Подключение к базе данных (однократно)
require_once '../includes/db_connect.php';

// Створюємо початкову відповідь як помилку
$response = [
	'status' => 'error',
	'message' => 'Невірний логін або пароль.'
];

// Отримуємо користувача за email або телефоном
$sql = "SELECT user_id, password, role FROM Users WHERE email = '$login' OR phone = '$login'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
   // Отримуємо дані користувача
   $user = mysqli_fetch_assoc($result);
    
   // Перевіряємо пароль
   if (password_verify($password, $user['password'])) {
		$_SESSION['user_id'] = $user['user_id'];  // Зберігаємо ID користувача в сесії
		$_SESSION['role'] = $user['role']; // Зберігаємо роль користувача в сесії

      // Оновлюємо відповідь для успіху
		$response = [
			'status' => 'success',
			'role' => $user['role'], // Роль користувача
			'message' => 'Вхід успішний!'
		];
   }
}

// Відправляємо відповідь
echo json_encode($response);

// Закриття з'єднання з базою даних
mysqli_close($conn);
?>