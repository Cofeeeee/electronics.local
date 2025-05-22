$(document).ready(function(){
	// Ініціалізація слайдера для категорій
	$('.slider__category').slick({
		dots: true, // Включення навігаційних точок під слайдером
		slidesToShow: 5, // Відображення 1 слайда одночасно
		slidesToScroll: 1,
		infinite: false,
		responsive: [
			{
				breakpoint: 1200,
				settings: { slidesToShow: 5 }
			},
			{
				breakpoint: 992,
				settings: { slidesToShow: 4 }
			},
			{
				breakpoint: 768,
				settings: { slidesToShow: 3 }
			}
	  	]
	});
});