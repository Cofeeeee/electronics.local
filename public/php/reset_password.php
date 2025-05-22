<?php
session_start();

// Проверяем, вошел ли пользователь
if (!isset($_SESSION['user_id'])) {
    header("Location: php/login.php");
    exit;
}

// Подключение к базе данных (однократно)
require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$message = "";

// Если форма отправлена
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Проверяем, что пароли совпадают
    if ($new_password !== $confirm_password) {
        $message = "Паролі не співпадають!";
    } else {
        // Хешируем новый пароль
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Обновляем пароль в БД
        $sql = "UPDATE Users SET password = '$hashed_password' WHERE user_id = $user_id";
        if (mysqli_query($conn, $sql)) {
            $message = "Пароль успішно змінено!";
            header("refresh:2;url=../personal_cabinet.php"); // Перенаправление через 2 сек
        } else {
            $message = "Помилка: " . mysqli_error($conn);
        }
    }
}

// Закрываем соединение
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Скидання паролю</title>
    <link rel="stylesheet" href="../css/reset_password.css">
</head>
<body>
    <div class="reset-container">
        <h2>Скидання паролю</h2>
        <?php if ($message) : ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form action="" method="post">
            <label for="new_password">Новий пароль:</label>
            <input type="password" name="new_password" required>

            <label for="confirm_password">Підтвердіть пароль:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit" class="btn">Оновити пароль</button>
        </form>

        <a href="../personal_cabinet.php">Назад</a>
    </div>
</body>
</html>