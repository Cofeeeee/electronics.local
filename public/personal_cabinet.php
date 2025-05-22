<?php
session_start(); // Ініціалізація сесії

// Перевіряємо, чи є в сесії user_id
if (isset($_SESSION['user_id'])) {
   // Отримуємо user_id з сесії
   $user_id = $_SESSION['user_id'];

	// Подключение к базе данных (однократно)
	require_once 'includes/db_connect.php';

   // Отримуємо дані користувача за user_id
   $sql = "SELECT user_id, name, email, phone, password FROM Users WHERE user_id = '$user_id'";
   $result = mysqli_query($conn, $sql);

   if ($result && mysqli_num_rows($result) > 0) {
		// Отримуємо дані користувача
		$user = mysqli_fetch_assoc($result);
   } else {
		echo "Користувача не знайдено.";
		exit; // Якщо користувача немає в базі
   }

   // Закриття з'єднання з базою даних
   mysqli_close($conn);
} else {
   echo "Будь ласка, увійдіть в систему.";
   exit; // Вихід, якщо немає даних користувача в сесії
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Особистий кабінет</title>
	<link rel="stylesheet" href="css/personal_cabinet.css">
	<link rel="icon" href="img/icon.svg" type="image/x-icon">
</head>
<body>
	<div class="profile-container">
		<h1>Кабінет користувача №<?php echo htmlspecialchars($user['user_id'], ENT_QUOTES, 'UTF-8'); ?></h1>
		<p><strong>Ім'я:</strong> <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></p>
		<p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
		<p><strong>Телефон:</strong> <?php echo htmlspecialchars($user['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
		<a href="php/reset_password.php" class="btn">Скинути пароль</a>
		<a href="php/logout.php" class="btn logout-btn">Вийти</a>
		<a href="php/delete_account.php" class="btn delete-btn" onclick="return confirm('Ви впевнені, що хочете видалити свій акаунт? Це дію неможливо скасувати!');">
    		Видалити акаунт
		</a>
		<a href="index.php" class="btn">Назад на головну</a>
   </div>
</body>
</html>