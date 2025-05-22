<?php
session_start();

// Подключение к базе данных (однократно)
require_once 'includes/db_connect.php';

// Проверка, что ID передан
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
   die("Некорректний ID товару");
}

$product_id = (int) $_GET['id'];

// Достаем данные о товаре
$sql_product  = "SELECT name, specifications, image_url FROM Products WHERE product_id = $product_id";
$result = mysqli_query($conn, $sql_product );
$product = mysqli_fetch_assoc($result);

if (!$product) {
   die("Товар не знайдено");
}

// Достаем предложения магазинов
$sql_offers = "
   SELECT o.offer_id, o.price, o.offer_url, s.name AS store_name, s.url AS store_url
   FROM Offers o
   JOIN Stores s ON o.store_id = s.store_id
   WHERE o.product_id = $product_id AND o.availability = 1
   ORDER BY o.price ASC
";
$offers = mysqli_query($conn, $sql_offers);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
   <meta charset="UTF-8">
   <title><?= htmlspecialchars($product['name']) ?></title>
	<link rel="stylesheet" href="css/product.css">
	<link rel="icon" href="img/icon.svg" type="image/x-icon">
</head>
<body>
	<div class="wrapper">
		<!-- header -->
		<?php require "includes/header.php"; ?>
		<div class="content">
			<div class="container">
				<div class="product-page">
					<?php if (!empty($product['image_url'])) : ?>
							<img class="product-page__image" src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
					<?php endif; ?>

					<h2 class="product-page__title"><?= htmlspecialchars($product['name']) ?></h2>
					<p class="product-page__description"><?= nl2br(htmlspecialchars($product['specifications'])) ?></p>

					<h3>Де купити:</h3>
					<ul class="product-page__offers">
						<?php while ($offer = mysqli_fetch_assoc($offers)) : ?>
							<a class="product-page__offer-link" href="php/order_clicks.php?offer_id=<?= $offer['offer_id'] ?>&url=<?= urlencode($offer['offer_url']) ?>" target="_blank">
								<li class="product-page__offer">
									<?= htmlspecialchars($offer['store_name']) ?>
									<span class="product-page__price"><?= number_format($offer['price'], 2, ',', ' ') ?> грн</span>
								</li>
							</a>
						<?php endwhile; ?>
					</ul>

					<a class="product-page__back-link" href="index.php">← Повернутися до каталогу</a>
				</div>
			</div>
		</div>
		<!-- footer -->
		<?php require "includes/footer.php"; ?>
	</div>
	<!-- form -->
	<?php require "includes/form.php"; ?>
	<!-- Подключаем файлы скриптов -->
	<script src="js/toast.js"></script>
	<script src="js/cart.js"></script>
	<script src="js/popup.js"></script>
	<script src="js/form.js"></script>
</body>
</html>