<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Агрегатор ноутбуків по всій Україні – порівняй ціни, характеристики та знайди ідеальний ноутбук.">
		<meta name="keywords" content="ноутбуки, агрегатор, ціни, купити ноутбук, Україна">
		<title>Про нас</title>
		<link rel="stylesheet" href="css/about.css">
		<link rel="icon" href="img/icon.svg" type="image/x-icon">
	</head>
	<body>
		<div id="up" class="wrapper">
			<!-- header -->
			<?php require "includes/header.php"; ?>
			<div class="content">
				<section class="map">
					<div class="container">
						<div class="map__title">
							<h2>КПЗКС, Факультет інформаційних технологій</h2>
						</div>
						<div class="map__row">
							<div class="map__body">
								<div class="info">
									<div class="info__row">
										<div class="info__title">
											<h3>Наші контакти:</h3>
										</div>
										<div class="info__contacts">
											<div class="contacts__tell">
												<p><a href="tel:+380663453372"><img src="img/phone-call.png" alt=""></a><a href="tel:+380663453372">+38 (066) 345-33-72</a></p>
											</div>
											<div class="contacts__mail">
												<p><a href="mailto:dimakurilekh@gmail.com"><img src="img/email.png" alt=""></a><a href="mailto:dimakurilekh@gmail.com">dimakurilekh@gmail.com</a></p>
											</div>
										</div>
									</div>
									<div class="info__schedule">
										<h3>Графік роботи:</h3>
										<div class="schedule__body">
											<p>ПН-ПТ: 9:00 - 18:00</p>
											<p>СБ-НД: 9:00 - 11:00</p>
										</div>
									</div>
								</div>
								<div class="map__text">
									<p><b>Адреса:</b> проспект Дмитра Яворницького, 19, Дніпро,<br> Дніпропетровська область, 49005
									</p>
									<p><b>Автор:</b> ст. гр. 122-24ск-1 НТУ "Дніпровська Політехніка", Курілех Дмитро Віталійович
									</p>
									<p><b>Відповідальна особа:</b> ст. гр. 122-24ск-1 НТУ "Дніпровська Політехніка", Курілех Дмитро
										Віталійович</p>
									<div class="about">
										<p><b>Про факультет:</b></p>
										<p class="about__text">Факультет інформаційних технологій пропонує сучасні освітні
											програми для підготовки висококваліфікованих фахівців у галузі програмування,
											кібербезпеки, управління даними та інших напрямків ІТ. Студенти факультету
											отримують практичні знання та навички, які необхідні для успішної кар'єри в
											ІТ-сфері, співпрацюють з провідними компаніями та беруть участь у міжнародних
											проектах.</p>
									</div>
								</div>
							</div>
							<div class="map__frame">
								<iframe
									src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2646.084315589167!2d35.05983108839931!3d48.454911761271404!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40dbe2d6b66f138d%3A0x98b973a9561df40f!2z0J3QsNGG0LjQvtC90LDQu9GM0L3Ri9C5INGC0LXRhdC90LjRh9C10YHQutC40Lkg0YPQvdC40LLQtdGA0YHQuNGC0LXRgiAi0JTQvdC10L_RgNC-0LLRgdC60LDRjyDQn9C-0LvQuNGC0LXRhdC90LjQutCwIg!5e0!3m2!1sru!2sua!4v1727622430118!5m2!1sru!2sua"
									width="600" height="500" style="border:0;" loading="lazy"
									referrerpolicy="no-referrer-when-downgrade"></iframe>
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