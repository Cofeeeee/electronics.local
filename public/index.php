<?php
session_start();

// Подключение к базе данных (однократно)
require_once 'includes/db_connect.php';

// Получаем список ноутбуков с самой низкой ценой и фотографией
$sql_lowest_price = "SELECT * FROM view_lowest_price_offer"; // Берется только один товар с самой низкой ценой

$result_lowest_price = mysqli_query($conn, $sql_lowest_price);
if (!$result_lowest_price) {
	die('Ошибка запроса: ' . mysqli_error($conn));
}

// Получаем список популярных товаров за месяц
$sql_popular = "SELECT * FROM view_popular_products LIMIT 10";

$popular_result = mysqli_query($conn, $sql_popular);
if (!$popular_result) {
	die('Ошибка запроса: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Агрегатор ноутбуків по всій Україні – порівняй ціни, характеристики та знайди ідеальний ноутбук.">
	<meta name="keywords" content="ноутбуки, агрегатор, ціни, купити ноутбук, Україна">
	<meta name="author" content="Курілех Дмитро Віталійович.">
	<title>Головна</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="icon" href="img/icon.svg" type="image/x-icon">
</head>
<body>
	<div class="wrapper">
		<!-- header -->
		<?php require "includes/header.php"; ?>
		<div class="content">
			<section class="products">
				<div class="container">
					<div class="products__title">
						<h2>Ноутбуки всі</h2>
					</div>
					<div class="products__row">
						<?php while ($row = mysqli_fetch_assoc($result_lowest_price)): ?>
							<div class="product__card <?= $row['product_overall_availability'] === '1' ? '' : 'unavailable' ?>">
								<a href="product.php?id=<?= $row['product_id'] ?>">
									<!-- Изображение товара -->
									<?php if (!empty($row['image_url'])) : ?>
										<div class="image-container"><img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>"></div>
									<?php endif; ?>
									<!-- Название товара -->
									<h3><?= htmlspecialchars($row['name']) ?></h3>
								</a>
								<div class="product__body">
									<!-- Цена -->
									
									<?php if ($row['product_overall_availability'] === '1'): ?>
										<p class="product__price"><?= number_format($row['lowest_price'], 0, '', ' ') ?> <span class="product__price-currency">₴</span></p>
										<button class="add-btn" data-offer-id="<?= $row['offer_id'] ?>">До кошику</button>
									<?php else: ?>
										<p>Немає в наявності</p>
									<?php endif; ?>
								</div>
							</div>
						<?php endwhile; ?>
					</div>
				</div>
			</section>
			<section class="category">
				<div class="container">
					<div class="category__title">
						<h2>Популярні товари</h2>
					</div>
					<?php if (mysqli_num_rows($popular_result) > 0): ?>
					<div class="slider__category">
						<?php while ($row = mysqli_fetch_assoc($popular_result)) : ?>
							<div class="slider__item">
								<a href="product.php?id=<?= $row['product_id'] ?>">
									<!-- Изображение товара -->
									<?php if (!empty($row['image_url'])) : ?>
										<div class="image-container"><img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>"></div>
									<?php endif; ?>
									<!-- Название товара -->
									<h3><?= htmlspecialchars($row['name']) ?></h3>
								</a>
								<!-- Цена товара -->
								<p class="product__price"><?= number_format($row['lowest_price'], 0, '', ' ') ?> <span class="product__price-currency">₴</span></p>
							</div>
						<?php endwhile; ?>
					</div>
					<?php else: ?>
						<p>Популярних товарів немає за останній місяць.</p>
					<?php endif; ?>
				</div>
			</section>
			<section class="info">
				<div class="container">
					<p>Існує дві основні системи класифікації ноутбуків, які доповнюють один одного.</p>
					<p>Класифікація на основі розміру діагоналі дисплея:</p>
					<ul class="info__list">
						<li>17 дюймів та більше – «заміна настільного ПК» (англ. Desktop Replacement);</li>
						<li>14 - 16 дюймів - масові ноутбуки (спеціальної назви для даної категорії ноутбуків не передбачено);</li>
						<li>11 - 13,3 дюймів - субноутбуки або ультрабуки;</li>
						<li>9 - 11 дюймів - ультрапортативні ноутбуки;</li>
						<li>7 - 12,1 дюймів (що не мають DVD-приводу) - нетбуки та смартбуки;</li>
						<li>Пристрої з діагоналлю екрана менше 7 дюймів виділяють у спеціальну категорію "наладонних комп'ютерів" (Handheld PC).</li>
					</ul>
				</div>
			</section>
			<section class="subscribe">
				<div class="container">
					<h2 class="subscribe__title">Підпишіться на знижки та акції</h2>
					<p class="subscribe__text">Отримуйте першими сповіщення про розпродажі, новинки та вигідні пропозиції!</p>
					<form id="subscribe-form" class="subscribe__form">
						<input type="email" name="email" placeholder="Ваша електронна пошта" required>
						<button type="submit">Підписатися</button>
					</form>
					<div id="subscribe-message"></div>
				</div>
			</section>
		</div>
		<!-- footer -->
		<?php require "includes/footer.php"; ?>
	</div>
	<!-- form -->
	<?php require "includes/form.php"; ?>
	<!-- Подключаем jQuery -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<!-- Подключаем слайдер Slick -->
	<script src="js/slick.min.js"></script>
	<!-- Вызываем слайдер Slick -->
	<script src="js/popular.js"></script>
	<!-- Подключаем файлы скриптов -->
	<script src="js/toast.js"></script>
	<script src="js/cart.js"></script>
	<script src="js/popup.js"></script>
	<script src="js/form.js"></script>
	<script src="js/subscribe.js"></script>
</body>
</html>