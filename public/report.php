<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
	die("У вас немає доступу!");
}

require_once 'includes/db_connect.php';

// Параметри фільтрації
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$dateFilter = $_GET['date_filter'] ?? '';
$userFilter = $_GET['user_filter'] ?? '';
$storeFilter = $_GET['store_filter'] ?? ''; // Цей фільтр застосовується до обох звітів
$availabilityFilter = $_GET['availability_filter'] ?? ''; // Фільтр по наявності (для другого звіту)
$categoryFilter = $_GET['category_filter'] ?? ''; // Фільтр по категорії для обох звітів

// Обробка фільтру по періоду
if ($dateFilter) {
	$now = date('Y-m-d');
	switch ($dateFilter) {
		case 'week':
			$startDate = date('Y-m-d', strtotime('-1 week'));
			break;
		case 'month':
			$startDate = date('Y-m-d', strtotime('-1 month'));
			break;
		case 'year':
			$startDate = date('Y-m-d', strtotime('-1 year'));
			break;
	}
	$endDate = $now;
}

// --- Фільтри для першого звіту (Кліки) ---
$wherePartsClicks = [];

// --- Дата ---
if ($startDate && $endDate) {
	$wherePartsClicks[] = "oc.click_date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
}
// --- Користувач ---
if ($userFilter !== '') {
	$userFilter = intval($userFilter);
	$wherePartsClicks[] = "oc.user_id = $userFilter";
}
// --- Магазин ---
if ($storeFilter !== '') {
	$storeFilter = intval($storeFilter);
	$wherePartsClicks[] = "s.store_id = $storeFilter";
}
// --- Категорія ---
if ($categoryFilter !== '') {
	$categoryFilter = intval($categoryFilter); // Перетворюємо на ціле число для безпеки
	// Умова застосовується до таблиці Products (або її псевдоніму P в запиті)
	$wherePartsClicks[] = "p.category_id = $categoryFilter";
}
$whereClauseClicks = $wherePartsClicks ? 'WHERE ' . implode(' AND ', $wherePartsClicks) : '';

// --- Фільтри для другого звіту (Зведена інформація) ---
$wherePartsSummary = [];

// --- Фільтр по магазину ---
if ($storeFilter !== '') {
	$storeFilter = intval($storeFilter);
	$wherePartsSummary[] = "s.store_id = $storeFilter";
}
// --- Фільтр по категорії ---
if ($categoryFilter !== '') {
	$categoryFilter = intval($categoryFilter); // Перетворюємо на ціле число для безпеки
	// Умова застосовується до таблиці Products (або її псевдоніму p в запиті)
	$wherePartsSummary[] = "p.category_id = $categoryFilter";
}
$whereClauseSummary = $wherePartsSummary ? 'WHERE ' . implode(' AND ', $wherePartsSummary) : '';

// --- Умова HAVING для фільтра по наявності (застосовується тільки до другого звіту після GROUP BY) ---
$havingClauseAvailability = '';
switch ($availabilityFilter) {
	case 'available':
		// Тільки товари, у яких є хоча б одна доступна пропозиція
		$havingClauseAvailability = 'HAVING SUM(CASE WHEN o.availability = 1 THEN 1 ELSE 0 END) > 0';
		break;
	case 'unavailable':
		// Тільки товари, у яких немає доступних пропозицій взагалі
		$havingClauseAvailability = 'HAVING SUM(CASE WHEN o.availability = 1 THEN 1 ELSE 0 END) = 0';
		break;
	// Якщо 'all' або не обрано, HAVING не додається, показуються всі товари
}


// --- SQL Запит для першого звіту (Кліки) ---
$queryClicks = "
	SELECT 
		p.name AS product_name,
		p.image_url,
		COUNT(*) AS clicks,
		GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ', ') AS stores_list
	FROM Orders_Clicks oc
	JOIN Offers o ON oc.offer_id = o.offer_id
	JOIN Products p ON o.product_id = p.product_id
	JOIN Stores s ON o.store_id = s.store_id
	$whereClauseClicks
	GROUP BY p.product_id, p.name, p.image_url
	ORDER BY clicks DESC
";

// --- SQL Запит для другого звіту (Зведена інформація) ---
$querySummary = "
	SELECT
		p.product_id,
		p.name AS product_name,
		p.image_url,
		COUNT(o.offer_id) AS total_offers_count,
		SUM(CASE WHEN o.availability = 1 THEN 1 ELSE 0 END) AS available_offers_count,
		MIN(CASE WHEN o.availability = 1 THEN o.price ELSE NULL END) AS lowest_available_price,
		GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ', ') AS stores_listing_product
	FROM Products p
	JOIN Offers o ON p.product_id = o.product_id
	JOIN Stores s ON o.store_id = s.store_id
	$whereClauseSummary -- Застосовуємо фільтри (магазин, категорія) для пропозицій
	GROUP BY p.product_id, p.name
	$havingClauseAvailability -- Застосовуємо фільтр по наявності після групування
	ORDER BY available_offers_count DESC, p.name;
";

// --- Обробка запитів для експорту CSV ---
if (isset($_GET['download']) && $_GET['download'] === 'csv1') { // Експорт першого звіту
	// Встановлюємо заголовки HTTP для завантаження файлу
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename="report_clicks.csv"');
	$output = fopen('php://output', 'w'); // Відкриваємо потік виводу PHP, який пише прямо у відповідь браузера
	fwrite($output, "\xEF\xBB\xBF"); // Додаємо Byte Order Mark (BOM) для коректного відображення UTF-8 в Excel
	fputcsv($output, ['Назва товару', 'Кількість кліків', 'Магазини, що отримали кліки', 'Посилання на зображення']);

	$resultClicksCSV = mysqli_query($conn, $queryClicks);
	while ($row = mysqli_fetch_assoc($resultClicksCSV)) {
		fputcsv($output, [$row['product_name'], $row['clicks'], $row['stores_list'], $row['image_url']]); // Визначаємо та записуємо заголовки колонок для CSV
	}
	mysqli_free_result($resultClicksCSV); // Звільняємо пам'ять після отримання результатів
	fclose($output);
	exit;
	
} elseif (isset($_GET['download']) && $_GET['download'] === 'csv2') { // Експорт другого звіту
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename="report_summary.csv"');
	$output = fopen('php://output', 'w');
	fwrite($output, "\xEF\xBB\xBF");
	fputcsv($output, ['Назва товару', 'Кількість пропозицій', 'Доступно пропозицій', 'Найнижча ціна', 'Статус наявності', 'Магазини']);

	$resultSummaryCSV = mysqli_query($conn, $querySummary); // Виконуємо запит знову для CSV
	while ($row = mysqli_fetch_assoc($resultSummaryCSV)) {
		// --- Логіка визначення статусу та форматування ціни для CSV ---
		$availability_status_text = ($row['available_offers_count'] > 0) ? 'В наявності' : 'Немає в наявності';
		$lowest_price_formatted = ($row['lowest_available_price'] !== NULL) ? number_format($row['lowest_available_price'], 2, '.', '') . ' грн' : '-';

		// Виведення даних у другий CSV файл ---
		fputcsv($output, [
			$row['product_name'],
			$row['total_offers_count'],
			$row['available_offers_count'],
			$lowest_price_formatted,
			$availability_status_text,
			$row['stores_listing_product']
		]);
	}
	mysqli_free_result($resultSummaryCSV);
	fclose($output);
	exit;
}

// --- Виконання запитів для відображення в HTML ---
$resultClicks = mysqli_query($conn, $queryClicks);
$resultSummary = mysqli_query($conn, $querySummary);

// --- Підрахунок загальних кліків (для першого звіту) ---
$totalClicksQuery = "SELECT COUNT(*) AS total_clicks
							FROM Orders_Clicks oc
							JOIN Offers o ON oc.offer_id = o.offer_id
							JOIN Products p ON o.product_id = p.product_id
							JOIN Stores s ON o.store_id = s.store_id
							$whereClauseClicks";
$totalClicksResult = mysqli_query($conn, $totalClicksQuery);
$totalClicksRow = mysqli_fetch_assoc($totalClicksResult);
$totalClicks = $totalClicksRow['total_clicks'] ?? 0;
if ($totalClicksResult) mysqli_free_result($totalClicksResult); // Звільняємо пам'ять

// --- Витягнути магазини для списку фільтра ---
$storeOptions = [];
$storeResult = mysqli_query($conn, "SELECT store_id, name FROM Stores");
while ($store = mysqli_fetch_assoc($storeResult)) {
	$storeOptions[] = $store;
}
if ($storeResult) mysqli_free_result($storeResult); // Звільняємо пам'ять

// --- Витягнути категорії для списку фільтра ---
$categoryOptions = [];
$categoryResult = mysqli_query($conn, "SELECT category_id, name FROM Categories ORDER BY name");
while ($category = mysqli_fetch_assoc($categoryResult)) {
	$categoryOptions[] = $category;
}
if ($categoryResult) mysqli_free_result($categoryResult); // Звільняємо пам'ять

mysqli_close($conn);

// --- Допоміжна функція для формування URL фільтрів ---
function getCurrentFilters(array $exclude = []): string {
	$filters = $_GET;
	foreach ($exclude as $key) {
		unset($filters[$key]);
	}
	return http_build_query($filters);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
   <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Формування звітів</title>
	<link rel="icon" href="img/icon.svg" type="image/x-icon">
	<style>
		body {
			font-family: Arial, sans-serif;
			background-color: #f4f4f4;
			margin: 20px;
			padding-bottom: 50px;
		}
		h1, h2, h3 {
			text-align: center;
			color: #333;
		}
		h2 {
         margin-top: 40px;
      }
		/* --- Стилі для форми фільтрації --- */
		form {
			margin-bottom: 20px;
			background-color: #e9e9e9; /* Легкий фон для форми */
			padding: 15px;
			border-radius: 8px;
			width: fit-content; /* Ширина по вмісту */
			margin: 0 auto;
			box-shadow: 0 2px 5px rgba(0,0,0,0.05);
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
			gap: 10px;
		}
		form label {
			font-weight: bold;
			display: flex;
			align-items: center;
			gap: 5px;
		}
		input[type="date"], input[type="number"], select, form button {
			padding: 8px;
			border: 1px solid #ccc;
			border-radius: 4px;
		}
		form button {
			padding: 8px 15px;
			background-color: #009879;
			color: white;
			border: none;
			cursor: pointer;
			transition: background-color 0.3s ease; /* Плавна зміна кольору при наведенні */
		}
		form button:hover {
			background-color: #007f65; /* Темніше при наведенні */
		}
		form button:last-of-type { /* Стиль для кнопки "Скинути фільтри" */
			background-color: #555;
		}
		form button:last-of-type:hover {
			background-color: #444;
		}
		/* --- Кінець стилів для форми фільтрації --- */

		/* Стилі для таблиці */
		table {
			width: 90%;
			margin: 30px auto;
			border-collapse: collapse;
			background-color: #fff;
			box-shadow: 0 0 15px rgba(0,0,0,0.1);
			border-radius: 8px; /* Заокруглення кутів таблиці */
			overflow: hidden; /* Щоб заокруглення було видно */
		}
		th, td {
			padding: 12px 15px;
			text-align: left;
			border: 1px solid #ddd;
		}
		th {
			background-color: #009879;
			color: white;
			text-transform: uppercase; /* Заголовки великими літерами */
			font-size: 0.9em;
			letter-spacing: 0.1em;
			position: sticky;
			top: 0;
			z-index: 1;
		}
		tr:nth-child(even) {
			background-color: #f3f3f3;
		}
		tr:hover { /* Підсвітка рядка при наведенні */
			background-color: #e1f5fe;
		}

		/* Стилі для колонки з зображенням */
		.product-image-cell {
			display: flex;
			justify-content: center;
			align-items: center;
		}
		.product-image-cell img {
			max-width: 90px; /* Обмежуємо максимальну ширину зображення */
			max-height: 90px; /* Обмежуємо максимальну висоту зображення */
			border: 1px solid #ddd; /* Додаємо тонку рамку */
			padding: 2px; /* Додаємо невеликий внутрішній відступ */
			box-shadow: 0 1px 3px rgba(0,0,0,0.05); /* Додаємо легку тінь */
		}

		/* --- Cтилі для колонок другого звіту --- */
		.availability-status-cell {
			font-weight: bold;
			text-align: center;
			white-space: nowrap;
		}
		.status-available {
			color: #28a745; /* Зелений */
		}
		.status-unavailable {
			color: #dc3545; /* Червоний */
		}

		/* Стилі для кнопок внизу сторінки */
		.button-container { /* Обгортка для кнопок */
			margin-top: 30px;
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
			align-items: center;
			gap: 20px;
		}
		.export-buttons-group {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
		}
		.export-button, .back-button {
			width: fit-content; /* Ширина по вмісту */
			padding: 12px 20px;
			text-decoration: none;
			border-radius: 5px;
			color: white;
			font-weight: bold;
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
			transition: background-color 0.3s ease;
		}
		.export-button {
			background-color: #009879;
		}
		.export-button:hover {
			background-color: #007f65;
		}
		.back-button { /* Стиль для кнопки Назад */
			background-color: #555;
		}
		.back-button:hover {
			background-color: #444;
		}
   </style>
</head>
<body>

	<h1>Панель адміністратора: Звіти</h1>

   <form method="GET">
      <label>Період (звіт 1):
         <select name="date_filter">
            <option value="">--Не вибрано--</option>
            <option value="week" <?= $dateFilter === 'week' ? 'selected' : '' ?>>Тиждень</option>
            <option value="month" <?= $dateFilter === 'month' ? 'selected' : '' ?>>Місяць</option>
            <option value="year" <?= $dateFilter === 'year' ? 'selected' : '' ?>>Рік</option>
         </select>
      </label>

      <label>Дата вручну (звіт 1):
         <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
         <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
      </label>

      <label>User ID (звіт 1):
         <input type="number" name="user_filter" value="<?= htmlspecialchars($userFilter) ?>" placeholder="Введіть ID користувача">
      </label>

		<label>Магазин (обидва звіти):
			<select name="store_filter">
				<option value="">--Усі--</option>
				<?php foreach ($storeOptions as $store): ?>
					<option value="<?= $store['store_id'] ?>" <?= ($storeFilter == $store['store_id']) ? 'selected' : '' ?>>
						<?= htmlspecialchars($store['name']) ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>

		<label>Категорія (обидва звіти):
			<select name="category_filter">
					<option value="">--Усі--</option>
					<?php foreach ($categoryOptions as $category): ?>
						<option value="<?= $category['category_id'] ?>" <?= ($categoryFilter == $category['category_id']) ? 'selected' : '' ?>>
							<?= htmlspecialchars($category['name']) ?>
						</option>
					<?php endforeach; ?>
			</select>
		</label>

		<label>Наявність (звіт 2):
			<select name="availability_filter">
				<option value="">--Усі товари--</option>
				<option value="available" <?= $availabilityFilter === 'available' ? 'selected' : '' ?>>В наявності</option>
				<option value="unavailable" <?= $availabilityFilter === 'unavailable' ? 'selected' : '' ?>>Немає в наявності</option>
			</select>
		</label>

      <button type="submit">🔍 Фільтрувати</button>
		<button type="button" onclick="window.location='<?= strtok($_SERVER["REQUEST_URI"], '?') ?>'">Скинути фільтри</button>
   </form>

	<h2>📊 Звіт 1: Товари за кількістю кліків</h2>
	<p style="text-align: center; color: #555;">Цей звіт відображає товари з найбільшою кількістю кліків за обраний період (фільтр "Наявність" на цей звіт не впливає).</p>

   <h3>Загальна кількість кліків: <?= (int)$totalClicks ?></h3>

   <table>
      <thead>
         <tr>
            <th>Назва товару</th>
            <th>Кількість кліків</th>
				<th>Магазини, що отримали кліки</th>
				<th>Зображення товару</th>
         </tr>
      </thead>
      <tbody>
         <?php // --- Перевірка результату запиту 1 та цикл ---
			if (mysqli_num_rows($resultClicks) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($resultClicks)): ?>
               <tr>
                  <td><?= htmlspecialchars($row['product_name']) ?></td>
                  <td><?= (int)$row['clicks'] ?></td>
						<td><?= htmlspecialchars($row['stores_list']) ?></td>
						<td class="product-image-cell">
							<?php if (!empty($row['image_url'])): ?>
								<img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Зображення <?= htmlspecialchars($row['product_name']) ?>">
							<?php else: ?>
								Немає зображення
							<?php endif; ?>
						</td>
               </tr>
            <?php endwhile; ?>
         <?php else: ?>
            <tr><td colspan="4">Немає даних для відображення в звіті 1.</td></tr>
         <?php endif; ?>
      </tbody>
   </table>
	<?php if ($resultClicks) mysqli_free_result($resultClicks); // Звільняємо пам'ять після використання результату першого звіту ?> 

	<hr style="margin: 40px auto; width: 90%;">
	<h2>🛒 Звіт 2: Зведена інформація про товари</h2>
	<p style="text-align: center; color: #555;">Цей звіт відображає зведену інформацію про товари та їх пропозиції (враховує фільтри "Магазин", "Категорія" та "Наявність").</p>

	<table>
		<thead>
			<tr>
				<th>Назва товару</th>
				<th>Всього пропозицій</th>
				<th>Доступно пропозицій</th>
				<th class="availability-status-cell">Статус</th>
				<th>Найнижча ціна (наявна)</th>
				<th>Магазини</th>
			</tr>
		</thead>
		<tbody>
			<?php // --- Перевірка результату запиту 2 та цикл ---
			if ($resultSummary && mysqli_num_rows($resultSummary) > 0): ?>
				<?php while ($row = mysqli_fetch_assoc($resultSummary)): ?>
					<tr>
						<td><?= htmlspecialchars($row['product_name']) ?></td>
						<td><?= (int)$row['total_offers_count'] ?></td>
						<td><?= (int)$row['available_offers_count'] ?></td>
							<?php
								// --- ЗМІНА: Логіка визначення статусу для HTML ---
								$availability_status_text = ($row['available_offers_count'] > 0) ? 'В наявності' : 'Немає в наявності';
								$status_class = ($row['available_offers_count'] > 0) ? 'status-available' : 'status-unavailable';
							?>
							<td class="availability-status-cell <?= $status_class ?>">
								<?= $availability_status_text ?>
							</td>
							<td class="lowest-price-cell">
								<?php // --- ЗМІНА: Форматування ціни для HTML ---
								if ($row['lowest_available_price'] !== NULL): ?>
									<?= number_format($row['lowest_available_price'], 2, '.', ' ') ?> грн
								<?php else: ?>
									-
								<?php endif; ?>
							</td>
						<td><?= htmlspecialchars($row['stores_listing_product']) ?></td>
					</tr>
				<?php endwhile; ?>
			<?php else: ?>
				<tr><td colspan="7">Немає даних для відображення в звіті 2.</td></tr>
			<?php endif; ?>
		</tbody>
	</table>
	<?php if ($resultSummary) mysqli_free_result($resultSummary);  // Звільняємо пам'ять після використання результату другого звіту ?>

	<div class="button-container">
		<div class="export-buttons-group">
			<?php // --- Використання функції getCurrentFilters для посилань ---
				$csv1_download_url = '?' . getCurrentFilters(['download']) . '&download=csv1';
				$csv2_download_url = '?' . getCurrentFilters(['download']) . '&download=csv2';
			?>
			<a href="<?= htmlspecialchars($csv1_download_url) ?>" class="export-button">⬇ CSV Звіт 1 (Кліки)</a>
			<a href="<?= htmlspecialchars($csv2_download_url) ?>" class="export-button">⬇ CSV Звіт 2 (Зведений)</a>
		</div>
		<a href="../admin_page.php" class="back-button">⬅ Назад</a>
	</div>

</body>
</html>