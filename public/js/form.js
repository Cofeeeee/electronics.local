
function handleFormSubmit(currentForm, actionUrl, onSuccessCallback) {
	if (!currentForm || !actionUrl) {
		console.error('Form або action URL не задані.');
		return;
	}

	currentForm.addEventListener('submit', function(event) {
		event.preventDefault();

		// Знаходимо всі потрібні елементи тільки всередині поточної форми
		const sendButton = currentForm.querySelector('.sendButton');
		const haveAccount = currentForm.querySelector('.haveAccount');
		const loadingIndicator = currentForm.querySelector('.loadingIndicator');

		// UI: показати завантаження, заблокувати кнопки
		sendButton.disabled = true;
		haveAccount.style.display = 'none';
		loadingIndicator.style.display = 'block';

		// Створюємо новий об'єкт FormData, який збирає всі дані з полів форми
		const formData = new FormData(currentForm);

		// Відправка даних на сервер через fetch API
		fetch(actionUrl, {
			method: 'POST',
			body: formData
		})
		.then(response => {
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return response.json();
		})
		.then(data => {
			showToast(data.message);
			if (data.status === 'success' && typeof onSuccessCallback === 'function') {
				onSuccessCallback(data);
			}
			currentForm.reset();
		})
		.catch(error => {
			console.error('Помилка при відправці форми:', error.message);
			showToast("Сталася помилка при відправці форми.");
			closePopup();
		})
		.finally(() => {
			// У будь-якому випадку, після завершення запиту
			sendButton.disabled = false;
			haveAccount.style.display = 'block';
			loadingIndicator.style.display = 'none';
		});
	})
}

/* Форма реєстрації */
handleFormSubmit(
	document.getElementById('registerForm'),
	'php/register.php',
	() => {
		setTimeout(() => {
			window.location.href = 'personal_cabinet.php';
		}, 1000); // Затримка перед редиректом
	}
);

/* Форма входу */
handleFormSubmit(
	document.getElementById('loginForm'),
	'php/login.php',
	(data) => {
		setTimeout(() => {
			window.location.href = (data.role === 'admin') ? 'admin_page.php' : 'personal_cabinet.php';
		}, 1000); // Затримка перед редиректом
	}
);