<?php session_start(); ?>

<!DOCTYPE html>
<html lang="uk">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>CSS/FIGMA/PHP</title>
		<link rel="stylesheet" href="css/hlam.css">
		<link rel="icon" href="img/icon.svg" type="image/x-icon">
	</head>
	<body>
		<div id="up" class="wrapper">
			<!-- header -->
			<?php require "includes/header.php"; ?>
			<div class="content">
				<section class="CSS6">
					<h2>Розділ 1. Індивідуальні роботи CSS з дисципліни "Вебтехнології та вебдизайн"</h2>
					<div class="text">
						<p class="element">Визволяти йшли, запорізькі козаки вогнем і мечем, крізь степи безкраї, щоб повернути
							вільні землі під знамено свободи. Вітер свистів у їхніх оселедцях, а шаблі співали пісні долі, коли
							вони виступили проти сили, що тягнула</p>
						<p class="element">їхній народ у рабство. Їхні очі палали рішучістю, а серця були незламні, як скелі, на
							яких стояли їхні козацькі чайки. Вони знали, що кожен крок вперед — це крок до волі, і що кожен бій —
							це боротьба не лише за сьогодення,</p>
						<p class="element">а й за майбутні покоління, щоб ті жили вільними на своїй землі.</p>
						<p class="element">Козаки несли за собою не лише визволення, а й смерть для тих, хто віками катував і
							знущався над їхнім народом. Вони були, мов хмара, що нависала над ворогом — тиха, грізна, але
							неминуча. Під їхніми ногами здригалася</p>
						<p class="element">земля, а ворог чув цей тривожний гул ще здалеку. Здавалося, сам степ дихав разом із
							козаками, ніби злився з ними в єдине ціле. Кожен їхній крок був символом того, що немає сили, здатної
							приборкати волю, котра вирує в їхніх</p>
						<p class="element">жилах, мов дикі ріки.</p>
						<p class="element">Ночами, коли ворог спав у страху, козаки дивилися на зорі й знали: ці небеса бачили
							все, і на цих степах їхнє ім’я ніколи не зникне. Вони не лише йшли у бій — вони несли з собою давню
							клятву, дану дідами й прадідами: завжди</p>
						<p class="element">стояти за волю й землю свого народу. Вони розуміли, що їхній бій — це не просто війна
							за території, а за саму душу України.</p>
					</div>
				</section>
				<section class="CSS7">
					<div class="container">
						<div class="CSS7__row">
							<div class="song">
								<div class="song__bg">
									<p>Ой у лузі червона калина похилилася,<br>
										Чогось наша славна Україна зажурилася.<br>
										А ми тую червону калину підіймемо,<br>
										А ми нашу славну Україну, гей, гей, розвеселимо!</p>

									<p>Марширують наші добровольці у кривавий тан<br>
										Визволяти братів-українців з ворожих кайдан.<br>
										А ми наших братів-українців визволимо,<br>
										А ми нашу славну Україну, гей, гей, розвеселимо!</p>

									<p>Не хилися, червона калино, маєш білий цвіт.<br>
										Не журися, славна Україно, маєш вільний рід.<br>
										А ми тую червону калину підіймемо,<br>
										А ми нашу славну Україну, гей, гей, розвеселимо!</p>

									<p>Гей, у полі ярої пшениці золотистий лан,<br>
										Розпочали стрільці українські з ворогами тан!<br>
										А ми тую ярую пшеницю ізберемо,<br>
										А ми нашу славну Україну, гей, гей, розвеселимо!</p>

									<p>Як повіє буйнесенький вітер з широких степів,<br>
										Та й прославить по всій Україні січових стрільців.<br>
										А ми тую стрілецькую славу збережемо,<br>
										А ми нашу славну Україну, гей, гей, розвеселимо!</p>
								</div>
							</div>
							<div class="portret">
								<img src="img/kozak.jpg" alt="kozak">
							</div>
							<div class="flag">
								<div class="blue">
									<p><b>Небо</b></p>
								</div>
								<div class="yellow">
									<p><b>Жито</b></p>
								</div>
								<div class="blocks">
									<div class="block1 block">1</div>
									<div class="block2 block">2</div>
									<div class="block3 block">3</div>
									<div class="block4 block">4</div>
								</div>
							</div>
						</div>
					</div>
				</section>
				<section class="PHP10">
					<h2>Розділ 2. Індивідуальні роботи PHP з дисципліни "Вебтехнології та вебдизайн"</h2>
					<div class='cot'>
						<h1 class='cot_output'>Тут буде кот(д) з PHP!</h1>
					</div>
					<?php
					// Встановлюємо українську локаль
					setlocale(LC_TIME, 'uk_UA.UTF-8');
					// Встановлюємо часовий пояс
					date_default_timezone_set('Europe/Kiev');
					// Виводимо поточну дату і час
					echo "<p class='text1'>Сьогодні: " . date("d.m.Y ") . "<span id='time'>" . date("H:i:s") . "</span></p>";

					// Функція для оновлення лічильника відвідувань
					function updateVisitCount($file = 'count.txt') {
						// Перевіряємо, чи існує файл. Якщо існує, зчитуємо його вміст, інакше встановлюємо значення 0
						$count = file_exists($file) ? file_get_contents($file) : 0;
						// Збільшуємо лічильник на 1 і записуємо нове значення в файл
						file_put_contents($file, ++$count);
						return $count;
					}
					$visitCount = updateVisitCount();
					echo "<p class='text1'>Сторінку відвідали $visitCount раз(и)</p>";

					// Зчитуємо вміст файлу
					if (mb_detect_encoding(file_get_contents(__FILE__), 'UTF-8', true)) {
						echo "<p class='text1'>Файл збережений в UTF-8</p>";
					} else {
						echo "<p class='text1'>Файл НЕ збережений в UTF-8</p>";
					}
					
					// Масив з ноутбуками
					$laptops = [
						["model" => "ASUS VivoBook R540MB-GQ084T", "image" => "img/2022-04-18_12-09-13_1.jpg"],
						["model" => "HP 15-bs162ur (4RG67EA)", "image" => "img/2022-04-18_12-10-38_1.jpg"],
						["model" => "ASUS VivoBook M570DD-DM001", "image" => "img/2022-04-18_12-12-20_1.jpg"],
						["model" => "", "image" => "img/cot.png"]
					];
					// Вибираємо випадковий ноутбук при завантаженні сторінки
					$randomLaptop = $laptops[array_rand($laptops)];
					// Перетворюємо масив в JSON, щоб передати його в JavaScript
					$laptops_json = json_encode($laptops);
					?>
					<div class="generate">
						<h2>Випадковий ноутбук</h2>
						<button id="generate-btn" type="submit">Згенерувати інший</button>
						<div id="laptop-container">
							<!-- Випадкова картинка при завантаженні сторінки -->
							<img src="<?php echo $randomLaptop['image']; ?>">
						</div>
					</div>
				</section>
			</div>
			<!-- footer -->
			<?php require "includes/footer.php"; ?>
		</div>
		<!-- form -->
		<?php require "includes/form.php"; ?>
		<script>
			// Функція для оновлення часу в реальному часі
			function updateTime() {
				const now = new Date();
				const hours = String(now.getHours()).padStart(2, '0');
				const minutes = String(now.getMinutes()).padStart(2, '0');
				const seconds = String(now.getSeconds()).padStart(2, '0');
				const currentTime = hours + ':' + minutes + ':' + seconds;
				document.getElementById('time').textContent = currentTime;
			}
			// Оновлення часу кожну секунду
			setInterval(updateTime, 1000);
			// Відразу викликаємо функцію, щоб не чекати 1 секунду
			updateTime();

			// Отримуємо масив ноутбуків з PHP
			const laptops = <?php echo $laptops_json; ?>;
			// Знаходимо кнопку і контейнер для зображення
			const button = document.getElementById('generate-btn');
			const container = document.getElementById('laptop-container');
			// Функція для генерації випадкового ноутбука
			function generateRandomLaptop() {
				// Вибираємо випадковий індекс з масиву
				const randomIndex = Math.floor(Math.random() * laptops.length);
				const laptop = laptops[randomIndex];
				// Створюємо HTML-код для відображення ноутбука
				const html = `
					<img src="${laptop.image}">
				`;
				// Вставляємо HTML в контейнер
				container.innerHTML = html;
			}
			// Додаємо обробник події для кнопки
			button.addEventListener('click', generateRandomLaptop);
		</script>
		<!-- Подключаем файлы скриптов -->
		<script src="js/toast.js"></script>
		<script src="js/cart.js"></script>
		<script src="js/popup.js"></script>
	   <script src="js/form.js"></script>
	</body>
</html>