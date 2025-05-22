document.addEventListener("DOMContentLoaded", function () {
	// Отримуємо елементи модального вікна та форми за їх ID
	const popup = document.getElementById("popup");
	const registerFormContainer = document.getElementById("registerFormContainer");
	const loginFormContainer = document.getElementById("loginFormContainer");

	// Функція для відкриття модального вікна
	window.openPopup = function () {
		popup.classList.add("active");
	};

	// Функція для закриття модального вікна
	window.closePopup = function () {
		popup.classList.remove("active");
		// Очищаємо всі поля в обох формах
		resetForm("registerForm");
		resetForm("loginForm");
	};

	// Функція для показу форми реєстрації
	window.showRegisterForm = function () {
		registerFormContainer.style.display = "flex"; // Показуємо форму реєстрації
		loginFormContainer.style.display = "none"; // Ховаємо форму входу
		document.getElementById('popupTitle').innerHTML = 'Створення<br>особистого кабінету'; // Зміна тексту
		resetForm("loginForm"); // Очищаємо всі поля
	};

	// Функція для показу форми входу
	window.showLoginForm = function () {
		registerFormContainer.style.display = "none"; // Ховаємо форму реєстрації
		loginFormContainer.style.display = "flex"; // Показуємо форму входу
		document.getElementById('popupTitle').innerHTML = 'Вхід<br>в особистий кабінет'; // Зміна тексту
		resetForm("registerForm"); // Очищаємо всі поля
	};
});

// Функція очистки полів в формі по айді
function resetForm(formId) {
	document.getElementById(formId).reset();
}