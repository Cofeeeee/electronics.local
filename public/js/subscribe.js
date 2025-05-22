document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('subscribe-form');
  const message = document.getElementById('subscribe-message');

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const email = form.elements['email'].value;

    fetch('php/subscribe.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'email=' + encodeURIComponent(email),
    })
      .then(response => response.text())
      .then(text => {
        message.textContent = text;
        message.style.color = 'green';
        form.reset();
      })
      .catch(() => {
        message.textContent = 'Сталася помилка. Спробуйте пізніше.';
        message.style.color = 'red';
      });
  });
});