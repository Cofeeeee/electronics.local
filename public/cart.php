<?php
session_start();

// Подключение к базе данных (однократно)
require_once 'includes/db_connect.php';

$cookie_name = "cart";
$cart_items = [];

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];

   // Отримуємо товари з кошика користувача
   $sql = "SELECT p.product_id, p.name, p.image_url, o.price, o.offer_id
            FROM Cart c
            JOIN Offers o ON c.offer_id = o.offer_id
            JOIN Products p ON o.product_id = p.product_id
            WHERE c.user_id = $user_id";
   $result = mysqli_query($conn, $sql);
   while ($row = mysqli_fetch_assoc($result)) {
      $cart_items[] = $row;
   }
} else {
   // Неавторизований користувач — читаємо куки
   $cart_cookie = isset($_COOKIE[$cookie_name]) ? json_decode($_COOKIE[$cookie_name], true) : [];

   if (is_array($cart_cookie) && count($cart_cookie) > 0) {
      $ids = implode(",", array_map('intval', $cart_cookie));
      $sql = "SELECT p.product_id, p.name, p.image_url, o.price, o.offer_id
					FROM Offers o
					JOIN Products p ON o.product_id = p.product_id
					WHERE o.offer_id IN ($ids)";
      $result = mysqli_query($conn, $sql);
      while ($row = mysqli_fetch_assoc($result)) {
         $cart_items[] = $row;
      }
   }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
   <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Кошик</title>
	<link rel="icon" href="img/icon.svg" type="image/x-icon">
	<style>
	body {
		font-family: 'Segoe UI', sans-serif;
		background-color: #f7f7f7;
		margin: 0;
		padding: 20px;
	}

	.container {
		max-width: 900px;
		margin: auto;
		background: #fff;
		padding: 30px;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
		border-radius: 12px;
	}

	h2 {
		margin-bottom: 20px;
		color: #333;
	}

	.cart__items {
		display: flex;
		flex-wrap: wrap;
		gap: 30px;
	}

	.cart__item {
		width: calc(33.333% - 20px);
		background: #fafafa;
		border: 1px solid #ddd;
		border-radius: 10px;
		text-align: center;
		padding: 15px;
		box-sizing: border-box;
		display: flex;
		flex-direction: column;
		align-items: center;
		transition: box-shadow 0.2s ease-in-out;
	}

	.cart__item:hover {
		box-shadow: 0 6px 16px rgba(0,0,0,0.1);
	}

	.cart__item img {
		border-radius: 6px;
		margin-bottom: 10px;
	}

	.cart__item h3 {
		font-size: 16px;
		color: #444;
		margin: 10px 0 5px;
		text-align: center;
	}

	.cart__item p {
		font-weight: bold;
		color: #2a7f3d;
		margin: 0 0 10px;
	}

	.remove-btn {
		background: #e74c3c;
		color: #fff;
		border: none;
		padding: 8px 14px;
		border-radius: 6px;
		cursor: pointer;
		transition: background 0.2s ease-in-out;
	}

	.remove-btn:hover {
		background: #c0392b;
	}
	</style>
</head>
<body>
   <div class="container">
		<h2>Ваш кошик</h2>
		<div class="cart__items"  style="<?= count($cart_items) > 0 ? '' : 'display:none;' ?>">
			<?php foreach ($cart_items as $item): ?>
				<div class="cart__item">
					<a href="product.php?id=<?= $item['product_id'] ?>" class="cart__link" style="text-decoration: none; color: inherit;">
						<img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="100">
						<h3><?= htmlspecialchars($item['name']) ?></h3>
					</a>
					<p><?= number_format($item['price'], 0, '', ' ') ?> ₴</p>
					<button class="remove-btn" data-offer-id="<?= $item['offer_id'] ?>">Видалити</button>
				</div>
			<?php endforeach; ?>
		</div>
		<p id="empty-cart-message" style="<?= count($cart_items) > 0 ? 'display:none;' : '' ?>">Кошик порожній.</p>
   </div>
	<!-- Подключаем файлы скриптов -->
	<script src="js/toast.js"></script>
	<script src="js/cart.js"></script>
</body>
</html>