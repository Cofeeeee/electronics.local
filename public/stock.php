<?php session_start(); ?>

<!DOCTYPE html>
<html lang="uk">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Агрегатор ноутбуків по всій Україні – порівняй ціни, характеристики та знайди ідеальний ноутбук.">
		<meta name="keywords" content="ноутбуки, агрегатор, ціни, купити ноутбук, Україна">
		<title>Акції</title>
		<link rel="stylesheet" href="css/stock.css">
		<link rel="icon" href="img/icon.svg" type="image/x-icon">
	</head>
	<body>
		<div id="up" class="wrapper">
			<!-- header -->
			<?php require "includes/header.php"; ?>
			<div class="content">
				<section class="promotion">
					<div class="promotion__action">
						<div class="container">
							<div class="promotion__row">
								<div class="promotion__title">
									<h2>Акція!</h2>
								</div>
								<div class="promotion__text">
									<p>Тільки до 30 квітня 2024 року!</p>
									<p>Встигни отримати -10% на будь-який товар</p>
								</div>
								<div class="promotion__mail">
									<input type="text" placeholder="E-mail">
									<button>Отримати знижку</button>
								</div>
							</div>
						</div>
					</div>
				</section>
				<section class="condition">
					<div class="container">
						<div class="condition__column">
							<div class="condition__title">
								<h3>Умова акції:</h3>
							</div>
							<div class="condition__text">
								<p>Купуючи товар від 20000.00 грн. Ви отримаєте знижку на будь-який товар із нашого магазину.</p>
								<a href="index.html"><button>Цікаво!</button></a>
							</div>
						</div>
					</div>
				</section>
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