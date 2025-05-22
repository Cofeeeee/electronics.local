<?php
// Подключение к базе данных (однократно)
require_once 'includes/db_connect.php';

// Ініціалізуємо змінну для кількості товарів у кошику
$cart_count = 0;
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

if ($user_id) {
	// Якщо користувач авторизований, отримуємо кількість товарів у кошику з БД
	$sql = "SELECT COUNT(*) AS count FROM Cart WHERE user_id = $user_id";
	$result = mysqli_query($conn, $sql);
	if ($result) {
		$row = mysqli_fetch_assoc($result);
		$cart_count = $row['count'];
	}
} else {
	// Якщо користувач гість, перевіряємо cookie з кошиком
	if (isset($_COOKIE['cart'])) {
		$cart_items = json_decode($_COOKIE['cart'], true);
		if (is_array($cart_items)) {
			$cart_count = count($cart_items); // Рахуємо кількість товарів у cookie
		}
	}
}
?>

<header class="intro">
	<div class="intro__row">
		<!-- Логотип -->
		<div class="intro__logo">
			<a href="index.php"><img src="img/2022-04-18_10-19-11_1.jpg" alt=""></a>
		</div>

		<!-- Заголовок -->
		<div class="intro__title">
			<h1>Агрегатор ноутбуків по всій Україні</h1>
		</div>

		<!-- Панель входу та кошик -->
		<div class="intro__enter">

			<!-- Кошик -->
			<div class="intro__enter__basket">
				<a href="cart.php">
					<img src="img/premium-icon-add-to-cart-5412585.png" alt="">
					<span class="cart-count <?= ($cart_count > 0) ? '' : 'hidden' ?>">
						<?= $cart_count ?>
					</span>
				</a>
			</div>

			<!-- Вхід/особистий кабінет -->
			<div class="intro__enter__body">
				<?php if ($user_id): ?>
					<?php
					// Получаем имя пользователя и роль по user_id
					$sql = "SELECT name, role FROM Users WHERE user_id = $user_id";
					$result = mysqli_query($conn, $sql);

					// Проверяем, что пользователь найден
					if ($result && mysqli_num_rows($result) > 0) {
						$user = mysqli_fetch_assoc($result);
						$user_name = $user['name']; // Имя пользователя
						$user_role = $user['role']; // Роль пользователя
					} else {
						$user_name = "Неизвестный пользователь";
						$user_role = ""; // Если не найдено, то роль пустая
					}

					// Закрываем соединение
					mysqli_close($conn);
					?>

					<!-- Если пользователь авторизован -->
					<a href="<?php echo ($user_role === 'admin') ? 'admin_page.php' : 'personal_cabinet.php'; ?>" class="login-user"><?= htmlspecialchars($user_name) ?></a> <!-- Показать имя пользователя и перенаправление в зависимости от роли -->
					<a href="php/logout.php" class="logout-user"><button class="open-popup">Вийти</button></a> <!-- Кнопка выхода -->
				<?php else: ?>
					<!-- Если пользователь не авторизован -->
					<button onclick="openPopup(); showLoginForm()" class="open-popup">Вхід</button>
					<button onclick="openPopup(); showRegisterForm()" class="open-popup">Реєстрація</button>
				<?php endif; ?>
			</div>				
		</div>
	</div>
	<div class="intro__menu">
		<div class="intro__list">
			<ul>
				<li><a href="index.php">Головна</a></li>
				<li><a href="stock.php">Акції</a></li>
				<li><a href="about.php">Про нас</a></li>
			</ul>
		</div>
	</div>
</header>