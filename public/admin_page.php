<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
   die("У вас немає доступу!");
}

// Подключение к базе данных (однократно)
require_once 'includes/db_connect.php';

// Змінна для повідомлень
$message = "";
$error_message = ""; // Окрема змінна для помилок

// AJAX: отримання пропозицій
if (isset($_GET['product_id'])) {
	header('Content-Type: application/json');
	$product_id = $_GET['product_id'];

	// Вибірка пропозицій
	$query = "SELECT offer_id, store_id, price, availability, offer_url FROM Offers WHERE product_id = $product_id";
	$res = mysqli_query($conn, $query);

	$offers = [];
	while ($row = mysqli_fetch_assoc($res)) {
		$row['availability'] = (int)$row['availability'];
		$offers[] = $row;
	}
	echo json_encode($offers);
	exit;
}

// --- Обробка POST-запитів (додавання, оновлення, видалення) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// --- Логіка оновлення доступності пропозиції (обробляється через AJAX) ---
	// Перевіряємо, чи надійшли дані для оновлення існуючих пропозицій
	if (isset($_POST['existing_offers']) && is_array($_POST['existing_offers'])) {
		header('Content-Type: application/json');
		$offers_updated_count = 0;
		$update_errors = [];
		$success = true;

		foreach ($_POST['existing_offers'] as $offer_id => $data) {
			$offer_id = (int) $offer_id;
			$availability = isset($data['availability']) ? (int) $data['availability'] : -1;

			// Дозволяємо тільки 0 або 1 як значення доступності
			if ($availability === 0 || $availability === 1) {
				$query = "UPDATE Offers SET availability = $availability WHERE offer_id = $offer_id";
				$result = mysqli_query($conn, $query);

				if ($result) {
					if (mysqli_affected_rows($conn) > 0) {
						$offers_updated_count++;
					}
				} else {
					$update_errors[] = "ID $offer_id: помилка БД – " . mysqli_error($conn);
					$success = false;
				}
			} else {
				$update_errors[] = "ID $offer_id: некоректне значення availability.";
				$success = false;
			}
		}

		$response_message = "Успішно оновлено $offers_updated_count пропозицій.";
		if ($update_errors) {
			$response_message .= " Помилки: " . implode("; ", $update_errors);
		}

		header('Content-Type: application/json');
		echo json_encode(['status' => $success ? 'success' : 'error', 'message' => $response_message]);
		mysqli_close($conn);
		exit;
	}

	// --- Логіка додавання нового товару або нових пропозицій (якщо це не запит на оновлення) ---
	// Цей блок виконується тільки якщо $_POST['existing_offers'] не було встановлено
	$product_id = null; // Ініціалізуємо знову для цього потоку логіки

	// Обробка додавання нового товару або вибору існуючого
	if (!empty($_POST["product_id"])) {
		// Якщо вибраний існуючий товар
		$product_id = $_POST["product_id"];
		$message = "Вибрано існуючий товар (ID: $product_id). Додаємо пропозиції...";
	} else {
		// Додавання нового товару
		$category_id = $_POST["category_id"];
		$name = $_POST["name"];
		$specifications = $_POST["specifications"];
		$image_url = $_POST["image_url"];

		// SQL-запит для додавання нового товару
		$sql = "INSERT INTO Products (category_id, name, specifications, image_url) 
			VALUES ('$category_id', '$name', '$specifications', '$image_url')";
		
		if (mysqli_query($conn, $sql)) {
			// Якщо товар успішно додано, отримуємо його ID
			$product_id = mysqli_insert_id($conn);
			$message = "Новий товар успішно додано (ID: $product_id)!";
		} else {
			$error_message = "Помилка додавання товару: " . mysqli_error($conn);
		}
	}

	// Якщо product_id визначено (або вибрано існуючий, або успішно додано новий)
	if ($product_id !== null && $error_message === "") {
		$offers_added_count = 0;

		// Можливі номери пропозицій
		foreach ([1, 2] as $i) {
			$store_id = $_POST["store_id_$i"] ?? '';
			$price = $_POST["price_$i"] ?? '';
			$offer_url = $_POST["offer_url_$i"] ?? '';

			// Якщо всі поля заповнені
			if (!empty($store_id) && !empty($price) && !empty($offer_url)) {

				$sql = "INSERT INTO Offers (product_id, store_id, price, offer_url) 
						VALUES ('$product_id', '$store_id', '$price', '$offer_url')";

				if (mysqli_query($conn, $sql)) {
					$offers_added_count++;
				} else {
					$error_message .= " Помилка додавання Пропозиції $i: " . mysqli_error($conn);
				}
			}
		}

		// Повідомлення
		if ($error_message === "" && $offers_added_count > 0) {
			$message .= " Успішно додано $offers_added_count пропозицій.";
		} elseif ($error_message === "" && $offers_added_count === 0) {
			$message .= " Не додано жодної пропозиції (поля пропозицій не були заповнені).";
		}
	} elseif ($product_id === null && $error_message === "") {
		$error_message = "Не вдалося визначити ID товару для додавання пропозицій.";
	}

	// Якщо були помилки, оновлюємо основне повідомлення
	if ($error_message !== "") {
		$message = "Помилка: " . $error_message;
	}
}

// Отримання списку існуючих товарів для вибору
$products_result = mysqli_query($conn, "SELECT product_id, name FROM Products");

// Закриття з'єднання з базою даних
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Додати товар і пропозиції</title>
	<link rel="stylesheet" href="css/admin_page.css">
	<link rel="icon" href="img/icon.svg" type="image/x-icon">
</head>
<body>

	<div class="container">
		<div class="menu">
			<a href="personal_cabinet.php">Персональний кабінет</a>
			<span>/</span>
			<a href="report.php">Сформувати звіт</a>
		</div>
		<h2>Керування товарами та пропозиціями</h2>

		<?php 
		// Виводимо повідомлення або помилку з відповідним класом
		if ($message) {
			$message_class = ($error_message !== "") ? 'error-message' : 'success-message';
			echo "<p class='$message_class'>$message</p>";
		} 
		?>

		<form method="post">
			<label for="product_id">Виберіть товар:</label>
			<select name="product_id" id="product_id">
				<option value="">-- Додати новий товар --</option>
				<?php 
				// Перевірка, чи запит вибірки товарів був успішним
				if ($products_result) {
					// Перемотуємо результат на початок, якщо він вже використовувався
					if (mysqli_num_rows($products_result) > 0) {
						mysqli_data_seek($products_result, 0);
					}
					while ($row = mysqli_fetch_assoc($products_result)): ?>
						<option value="<?php echo $row['product_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
					<?php endwhile; 
					// Звільнення пам'яті результату запиту
					mysqli_free_result($products_result);
				} else {
					echo "<option value=''>Помилка завантаження товарів</option>";
					echo "Помилка завантаження товарів: " . mysqli_error($conn);
				}
				?>
			</select>

			<!-- Дані для нового товару (якщо не вибрано товар) -->
			<div id="new-product-form">
				<hr> <h3>Дані для нового товару</h3>
				<input type="number" name="category_id" placeholder="Категорія (ID)" required>
				<input type="text" name="name" placeholder="Назва товару" required>
				<textarea name="specifications" placeholder="Характеристики" required></textarea>
				<input type="text" name="image_url" placeholder="Посилання на зображення" required>
			</div>

			<div id="existing-offers-section" style="display: none;"></div>

			<hr> <h3>Додати нові пропозиції</h3>
			<p>Заповніть поля для кожної пропозиції, яку хочете додати. Якщо товар вже існує, пропозиції будуть додані до нього.</p>

			<!-- Пропозиція 1 -->
			<h4>Пропозиція 1</h4>
			<input type="number" name="store_id_1" placeholder="ID магазину">
			<input type="number" name="price_1" placeholder="Ціна">
			<input type="text" name="offer_url_1" placeholder="URL пропозиції">

			<!-- Пропозиція 2 -->
			<h4>Пропозиція 2 (Необов'язково)</h4>
			<input type="number" name="store_id_2" placeholder="ID магазину">
			<input type="number" name="price_2" placeholder="Ціна">
			<input type="text" name="offer_url_2" placeholder="URL пропозиції">

			<button type="submit">Додати товар і пропозиції</button>
		</form>

		<a href="index.php" class="back-link">⬅ Назад</a>
	</div>

	<script>
		const newProductForm = document.getElementById('new-product-form');
		const productSelect = document.getElementById('product_id');
		const newInputs = newProductForm.querySelectorAll('input, textarea');
		const existingOffersSection = document.getElementById('existing-offers-section');
		const addOfferInputs = document.querySelectorAll('input[name="store_id_1"], input[name="price_1"], input[name="offer_url_1"]'); // Поля для додавання нових пропозицій
		const submitButton = document.querySelector('button[type="submit"]'); // Кнопка відправки форми
		const form = document.querySelector('form');

		function toggleNewProductForm() {
			if (productSelect.value === "") {
				// Якщо вибрано "-- Додати новий товар --"
				newProductForm.style.display = 'block'; // Показуємо поля нового товару
				newInputs.forEach(input => input.setAttribute('required', 'required')); // Робимо поля нового товару обов'язковими
				existingOffersSection.style.display = 'none'; // Приховуємо блок існуючих пропозицій
				existingOffersSection.innerHTML = ''; // Очищаємо вміст блоку
				submitButton.textContent = 'Додати товар і пропозиції'; // Змінюємо текст кнопки
				addOfferInputs.forEach(input => input.setAttribute('required', 'required')); // Робимо першу пропозицію обов'язковою для нового товару

			} else {
				// Якщо вибрано існуючий товар
				newProductForm.style.display = 'none'; // Приховуємо поля нового товару
				newInputs.forEach(input => input.removeAttribute('required')); // Робимо поля нового товару необов'язковими (вони не надсилатимуться)
				existingOffersSection.style.display = 'block'; // Показуємо блок існуючих пропозицій
				existingOffersSection.innerHTML = 'Завантаження пропозицій...'; // Індикатор завантаження
				submitButton.textContent = 'Додати пропозиції'; // Змінюємо текст кнопки
				addOfferInputs.forEach(input => input.removeAttribute('required')); // Знімаємо required з полів додавання нових пропозицій, якщо товар існує

				// ** AJAX запит для отримання пропозицій **
				fetch('?product_id=' + productSelect.value, {
					method: 'GET',
				})
				.then(response => {
					// Проверяем, что ответ в формате JSON
					const contentType = response.headers.get('content-type');
					if (!contentType || !contentType.includes('application/json')) {
						console.error('Очікувався JSON, але отримано:', response.text());
						throw new TypeError("Получен не JSON ответ.");
					}
					return response.json(); // Парсимо JSON відповідь
				})
				.then(offers => {
					// Успішно отримали пропозиції, відображаємо їх
					existingOffersSection.innerHTML = `<h3>Існуючі пропозиції для цього товару</h3>`; // Очищаем индикатор
					if (offers.length > 0) {
						offers.forEach(offer => {
							// Створюємо HTML для кожного пропозиції
							const offerDiv = document.createElement('div');
							offerDiv.classList.add('existing-offer')
							offerDiv.dataset.offerId = offer.offer_id; // Зберігаємо ID пропозиції в data-атрибуті

							offerDiv.innerHTML = `
								<p>ID Пропозиції: ${offer.offer_id}</p>
								<p>Магазин (ID): ${offer.store_id}</p>
								<p>Ціна: ${offer.price} ₴</p>
								<p>URL: <a href="${offer.offer_url}" target="_blank">Посилання</a></p>
								<label for="availability_${offer.offer_id}">Доступність:</label>
								<select name="existing_offers[${offer.offer_id}][availability]" id="availability_${offer.offer_id}">
									<option value="1" ${offer.availability === 1 ? 'selected' : ''}>Доступний</option>
									<option value="0" ${offer.availability === 0 ? 'selected' : ''}>Недоступний</option>
								</select>
								<button type="button" class="update-availability-button" data-offer-id="${offer.offer_id}">Зберегти зміну</button>
								<span class="status-message" style="margin-left: 10px;"></span>
							`;
							existingOffersSection.appendChild(offerDiv); // Додаємо створений div в блок
						});
					} else {
						existingOffersSection.innerHTML += "<p>Пропозицій для цього товару поки немає.</p>";
					}
				})
				.catch(error => {
					// Обробка помилок AJAX запиту (наприклад, помилка мережі або не-JSON відповідь)
					console.error('Ошибка получения предложений:', error);
					existingOffersSection.innerHTML = '<h3>Існуючі пропозиції для цього товару</h3><p>Не вдалося завантажити пропозиції.</p>';
				});
				// ** Конец AJAX запроса **
			}
		}

		// ** Додаємо обробник подій для кнопок оновлення доступності **
		// Використовуємо делегування подій, оскільки кнопки додаються динамічно
		existingOffersSection.addEventListener('click', function(event) {
			// Перевіряємо, чи клікнули на кнопку із класом 'update-availability-button' або її дочірній елемент
			const updateButton = event.target.closest('.update-availability-button');

			if (updateButton) {
				event.preventDefault(); // Зупиняємо стандартну дію кнопки (хоча вона вже type="button")

				const offerId = updateButton.dataset.offerId; // Отримуємо ID пропозиції з data-атрибута
				// Знаходимо батьківський div пропозиції
				const offerDiv = updateButton.closest('.existing-offer');
				// Знаходимо select доступності всередині цього div
				const availabilitySelect = offerDiv.querySelector(`select[id='availability_${offerId}']`);
				// Знаходимо елемент для виведення повідомлення про статус
				const statusSpan = offerDiv.querySelector('.status-message');

				if (!availabilitySelect) {
					console.error('Не знайдено select доступності для пропозиції ID:', offerId);
					statusSpan.textContent = 'Помилка: Не знайдено поле доступності.';
					statusSpan.style.color = 'red';
					return; // Виходимо, якщо елемент не знайдено
				}

				const availability = availabilitySelect.value; // Отримуємо вибране значення доступності

				// Відображаємо індикатор збереження
				statusSpan.textContent = 'Збереження...';
				statusSpan.style.color = 'gray';
				updateButton.disabled = true; // Вимикаємо кнопку
				availabilitySelect.disabled = true; // Вимикаємо select

				// Готуємо дані для відправки. Використовуємо структуру, яку очікує PHP: existing_offers[offer_id][availability]
				const formData = new URLSearchParams();
				formData.append(`existing_offers[${offerId}][availability]`, availability);
				// offerId також передається як частина ключа масиву existing_offers, тому окремо його в data не обов'язково додавати

				// Відправляємо POST запит на ту ж сторінку
				fetch('', {
					method: 'POST',
					body: formData,
				})
				.then(response => {
					// Перевіряємо, що відповідь у форматі JSON
					const contentType = response.headers.get('content-type');
					if (!contentType || !contentType.includes('application/json')) {
						console.error('Очікувався JSON, але отримано:', response.text()); // Логуємо відповідь сервера
						throw new TypeError("Получен не JSON ответ от сервера при сохранении доступности.");
					}
					return response.json(); // Парсимо JSON відповідь від сервера
				})
				.then(data => {
					// Обробляємо відповідь від сервера
					if (data.status === 'success') {
						statusSpan.textContent = 'Збережено!';
						statusSpan.style.color = 'green';
						// Опціонально: тимчасово підсвітити рядок пропозиції
						offerDiv.style.transition = 'background-color 0.5s ease';
						offerDiv.style.backgroundColor = '#e0ffe0'; // Світло-зелений
						setTimeout(() => {
							offerDiv.style.backgroundColor = ''; // Повернути початковий колір
						}, 1000); // Через 1 секунду
					} else {
						statusSpan.textContent = 'Помилка: ' + data.message;
						statusSpan.style.color = 'red';
						// Опціонально: тимчасово підсвітити рядок пропозиції червоним
						offerDiv.style.transition = 'background-color 0.5s ease';
						offerDiv.style.backgroundColor = '#ffe0e0'; // Світло-червоний
						setTimeout(() => {
							offerDiv.style.backgroundColor = ''; // Повернути початковий колір
						}, 1000);
					}
				})
				.catch(error => {
					// Обробка помилок AJAX запиту (наприклад, помилка мережі)
					console.error('Помилка при збереженні доступності:', error);
					statusSpan.textContent = 'Помилка з\'єднання або обробки!';
					statusSpan.style.color = 'red';
					// Опціонально: тимчасово підсвітити рядок пропозиції червоним
					offerDiv.style.transition = 'background-color 0.5s ease';
					offerDiv.style.backgroundColor = '#ffe0e0'; // Світло-червоний
					setTimeout(() => {
						offerDiv.style.backgroundColor = ''; // Повернути початковий колір
					}, 1000);
				})
				.finally(() => {
					// Відновлюємо кнопку та select незалежно від результату
					updateButton.disabled = false;
					availabilitySelect.disabled = false;
				});
			}
		});

		// ** Обробник події для форми при відправці **
		form.addEventListener('submit', function(event) {
			// Перевіряємо, чи вибрано існуючий товар
			if (productSelect.value !== "") {
				// Якщо вибрано існуючий товар, знаходимо всі select елементи в секції існуючих пропозицій
				const existingOfferSelects = existingOffersSection.querySelectorAll('select[name^="existing_offers"]');

				// Тимчасово вимикаємо ці select елементи перед відправкою
				// Вимкнені елементи не включаються в POST-дані форми
				existingOfferSelects.forEach(select => {
					select.disabled = true;
				});
			}
		});

		// Підключити функцію до зміни значення в select товару
		productSelect.addEventListener('change', toggleNewProductForm);

		// І одразу викликати при завантаженні для встановлення початкового стану
		toggleNewProductForm();
	</script>
</body>
</html>