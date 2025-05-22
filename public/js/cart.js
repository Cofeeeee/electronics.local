document.addEventListener("DOMContentLoaded", function() {
	const productsContainer = document.querySelector('.products__row'); // Контейнер с товарами
	const cartItemsContainer = document.querySelector('.cart__items'); // Контейнер товаров в корзине
	const emptyCartMessage = document.getElementById('empty-cart-message');	// Сообщение о пустой корзине
	const cartCount = document.querySelector('.cart-count'); // Счетчик товаров для корзины
	
	// Функция для обновления счетчика товаров в корзине
	function updateCartCount(cart_count, triggerAnimation = false) {
		if (!cartCount) return; // Если элемента нет — сваливаем сразу
		
		cartCount.textContent = cart_count > 0 ? cart_count : 0; // Обновляем текст счетчика
		cartCount.classList.toggle('hidden', cart_count === 0); // Скрываем или показываем счетчик
  
		// Анимация, если нужно
		if (cart_count > 0 && triggerAnimation) {
			cartCount.classList.add('updated');
			setTimeout(() => cartCount.classList.remove('updated'), 200);
		}
  	}

	// Функция для обновления состояния корзины
	function checkCartEmpty() {
		if (cartItemsContainer && emptyCartMessage) {
			const isEmpty = cartItemsContainer.querySelectorAll('.cart__item').length === 0;
			cartItemsContainer.style.display = isEmpty ? 'none' : 'flex';
			emptyCartMessage.style.display = isEmpty ? 'flex' : 'none';
		}
  	}

	// Универсальная функция для добавления и удаления товара из корзины
	function updateCart(action, offerId) {
		const formData = new FormData();
		formData.append('offer_id', offerId);

		// URL в зависимости от действия: добавление или удаление
		const url = action === 'add' ? 'php/add_to_cart.php' : 'php/remove_from_cart.php';

		fetch(url, {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			showToast(data.message); 
			if (data.status === 'success') {
				const cart_count = parseInt(data.cart_count, 10); // Преобразуем в целое число
				updateCartCount(cart_count, true);
				if (action === 'remove') {
					const item = document.querySelector(`[data-offer-id="${offerId}"]`).closest('.cart__item');
					// Удаляем товар из DOM
					item.remove();
					checkCartEmpty();
				}
			}
		})
		.catch(error => {
			console.error('Ошибка запроса:', error);
			showToast(action === 'add' ? 'Ошибка при добавлении товара в корзину' : 'Ошибка при удалении товара из корзины');
		});
	}

	// Обработчик для добавления товара в корзину с делегацией
	if (productsContainer) {
		productsContainer.addEventListener('click', function(event) {
			// Ищем элемент с классом 'add-btn', на который был клик (кнопка добавления)
			const button = event.target.closest('.add-btn'); // Ищем, был ли клик на кнопке добавления
			
			// Если клик был на кнопке добавления товара
			if (button) {
				const offerId = button.getAttribute('data-offer-id'); // Получаем 'offer_id' товара
				updateCart('add', offerId); // Добавляем товар в корзину
			}
		});
  	}

	// Обработчик для удаления товара из корзины с делегацией
	if (cartItemsContainer) {
		cartItemsContainer.addEventListener('click', function(event) {
			// Ищем элемент с классом 'remove-btn', на который был клик (кнопка удаления)
			const button = event.target.closest('.remove-btn'); // Ищем, был ли клик на кнопке удаления

			// Если клик был на кнопке удаления товара
			if (button) {
				const offerId = button.getAttribute('data-offer-id'); // Получаем 'offer_id' товара
				updateCart('remove', offerId); // Удаляем товар из корзины
			}
		});
	}

	// При загрузке страницы запрашиваем актуальное количество товаров в корзине
	fetch('php/get_cart_count.php')
		.then(response => response.json())
		.then(data => {
			const cart_count = parseInt(data.cart_count, 10); // Преобразуем в целое число
			updateCartCount(cart_count, false);
		})
		.catch(error => {
			console.error('Ошибка запроса:', error);
			showToast('Ошибка получения количества товаров')
		});
});