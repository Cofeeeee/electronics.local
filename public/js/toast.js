// Функция показа сообщения
function showToast(message, duration = 2000) {
	const toast = document.createElement('div') // Создаем элемент div
	toast.classList.add('toast') // Добавляем класс для созданного элемента
	toast.textContent = String(message) // Вставляем содержимое сообщения

	// Добавляем стили для тоста
	toast.style = `
		position: fixed;
		top: 1.25rem;
		left: 50%;
		transform: translateX(-50%);
		background: #fff;
		color: #000;
		padding: 0.875rem 1.5rem;
		border-radius: 0.5rem;
		box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
		font-weight: 500;
		opacity: 0;
		pointer-events: none;
		transition: opacity 0.3s ease, top 0.3s ease;
		z-index: 9999;
		text-align: center;
	`;

	// Добавляем в body
	document.body.appendChild(toast);

	// Принудительная перекомпоновка для обеспечения первой анимации
	void toast.offsetHeight;

	// Анимация при появлении
	toast.style.opacity = '1';
	toast.style.top = '2.5rem';  // Сдвигаем вниз при появлении
	toast.style.pointerEvents = 'auto';

	// Анимация при исчезновении
	setTimeout(() => {
		toast.style.opacity = '0';
		toast.style.top = '1.25rem';  // Возвращаем вверх при исчезновении
		toast.style.pointerEvents = 'none';

		// Удаляем элемент после анимации
		setTimeout(() => {
			toast.remove();
		 }, 200);  // После завершения анимации (200ms)
	}, duration);
}