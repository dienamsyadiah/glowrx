document.addEventListener("DOMContentLoaded", function() {
  const images = document.querySelectorAll('.images-wrapper img');
  const texts = document.querySelectorAll('.text-group h3');
  const bullets = document.querySelectorAll('.bullets span');
  let index = 0;

  function showSlide(index) {
    images.forEach(image => image.classList.remove('show'));
    texts.forEach(text => text.style.display = 'none');
    bullets.forEach(bullet => bullet.classList.remove('active'));

    images[index].classList.add('show');
    texts[index].style.display = 'block';
    bullets[index].classList.add('active');
  }

  function nextSlide() {
    index = (index + 1) % images.length;
    showSlide(index);
  }

  function prevSlide() {
    index = (index - 1 + images.length) % images.length;
    showSlide(index);
  }

  bullets.forEach((bullet, i) => {
    bullet.addEventListener('click', function() {
      index = i;
      showSlide(index);
    });
  });

  setInterval(nextSlide, 5000); // Change slide every 5 seconds
});

const inputs = document.querySelectorAll(".input-field");
const toggle_btn = document.querySelectorAll(".toggle");
const main = document.querySelector("main");
const bullets = document.querySelectorAll(".bullets span");
const images = document.querySelectorAll(".image");

inputs.forEach((inp) => {
  inp.addEventListener("focus", () => {
    inp.classList.add("active");
  });
  inp.addEventListener("blur", () => {
    if (inp.value != "") return;
    inp.classList.remove("active");
  });
});

toggle_btn.forEach((btn) => {
  btn.addEventListener("click", () => {
    main.classList.toggle("sign-up-mode");
  });
});

function moveSlider() {
  let index = this.dataset.value;

  let currentImage = document.querySelector(`.img-${index}`);
  images.forEach((img) => img.classList.remove("show"));
  currentImage.classList.add("show");

  const textSlider = document.querySelector(".text-group");
  textSlider.style.transform = `translateY(${-(index - 1) * 2.2}rem)`;

  bullets.forEach((bull) => bull.classList.remove("active"));
  this.classList.add("active");
}

bullets.forEach((bullet) => {
  bullet.addEventListener("click", moveSlider);
});