const carousel = document.getElementById('itemCarousel');
const loadImage = (el) => {
  const img = el.querySelector('img[data-src]');
  if (img && !img.src) {
    img.src = img.dataset.src;
  }
};

const preloadNext = (el) => {
  const next = el.nextElementSibling;
  if (next) loadImage(next);
};

preloadNext(carousel.querySelector('.carousel-item.active'));

carousel.addEventListener('slide.bs.carousel', function (event) {
  const next = event.relatedTarget;
  loadImage(next);
  preloadNext(next);
});

document.addEventListener('scroll', function () {
  const btn_booknow = document.querySelector('.booknow');
  const btn_scrollup = document.querySelector('.scrollup');

  // Nếu cuộn xuống quá 200px thì hiện nút
  if (window.scrollY > 200) {
    btn_booknow?.classList?.add('show');
    btn_scrollup?.classList?.add('show');
  } else {
    btn_booknow?.classList?.remove('show');
    btn_scrollup?.classList?.remove('show');
  }
});
