<div id="popup" class="popup">
	<a href="#" class="popup__area" onclick="closePopup()"></a>
	<div class="popup__body">
		<div class="popup__content">
			<button class="popup__close" onclick="closePopup()">
				<img src="img/free-icon-close-4947222.png" alt="Закрити">
			</button>
			<!-- Форма реєстрації -->
			<div id="registerFormContainer">
				<div class="popup__form">
					<form id="registerForm" action="php/register.php" method="post">
						<input type="text" placeholder="Ім'я" name="name" title="Придумайте логін" required pattern="^([А-ЯІЇЄҐа-яіїєґ]+)$|^([A-Za-z]+)$">
						<input type="tel" placeholder="Номер телефону" name="tel" title="0663453372" required pattern="^0(50|66|67|68|73|75|77|95|96|97|98|99)\d{7}$">
						<input type="email" placeholder="Email" name="email" title="example@gmail.com" required pattern="^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$">
						<input type="password" placeholder="Пароль" name="password" title="Пароль має містити від 8 до 16 символів" required pattern="^[A-Za-z0-9]{8,16}$">
						<button type="submit" class="sendButton">Зареєструватися</button>
						<p class="haveAccount">Вже є аккаунт? <button class="switch" type="button" onclick="showLoginForm()">Увійти</button></p>
						<div class="loadingIndicator" style="display: none;">
							<div class="spinner"></div>
						</div>
					</form>
				</div>
			</div>
			<!-- Форма реєстрації -->
			<!-- Форма входу -->
			<div id="loginFormContainer">
				<div class="popup__form">
					<form id="loginForm" action="php/login.php" method="post">
						<input type="text" placeholder="Телефон або Email" name="login" title="Телефон або Email" required>
						<input type="password" placeholder="Пароль" name="password" title="Пароль" required>
						<button type="submit" class="sendButton">Увійти</button>
						<p class="haveAccount">Немає аккаунту? <button class="switch" type="button" onclick="showRegisterForm()">Зареєструватися</button></p>
						<div class="loadingIndicator" style="display: none;">
							<div class="spinner"></div>
						</div>
					</form>
				</div>
			</div>
			<!-- Форма входу -->
			<div class="popup__text">
				<div class="popup__item">
					<p id="popupTitle">Створення<br>особистого кабінету</p>
				</div>
			</div>
		</div>
	</div>
</div>