<?php
session_start();
session_unset(); // Очищає всі сесійні змінні
session_destroy(); // Знищує сесію на сервері
setcookie(session_name(), '', time() - 3600, '/'); // Видаляє cookie сесії

// Ожидаем редирект с проверкой истории
echo '<script>
if (window.history.length > 1) {
   window.history.back();  // Возвращаемся на предыдущую страницу
} else {
   window.location.href = "../index.php";  // Если истории нет, перенаправляем на главную
}
</script>';
?>