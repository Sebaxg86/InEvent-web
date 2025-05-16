/* === CAROUSEL JS === */
(function(){
  const $track = document.querySelector('.carousel-track');
  const $slides =  Array.from(document.querySelectorAll('.slide'));
  const $dots  =  Array.from(document.querySelectorAll('.nav-dot'));
  let index = 0,  timer;

  /* Carga diferida de imágenes */
  $slides.forEach(slide=> slide.style.backgroundImage = `url(${slide.dataset.src})`);

  function gotoSlide(i){
    index = (i + $slides.length) % $slides.length;
    $track.style.transform = `translateX(-${index*100}%)`;
    $dots.forEach((d,k)=> d.setAttribute('aria-selected', k===index));
  }

  /* Autoplay */
  function play(){ timer = setInterval(()=> gotoSlide(index+1), parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--duration'))*1000); }
  function stop(){ clearInterval(timer); }

  /* Puntitos */
  $dots.forEach((d,i)=> d.addEventListener('click', ()=>{ stop(); gotoSlide(i); play(); }));

  /* Pausar al poner mouse encima */
  document.querySelector('.hero-carousel').addEventListener('mouseenter', stop);
  document.querySelector('.hero-carousel').addEventListener('mouseleave', play);

  /* Iniciar */
  gotoSlide(0);
  play();
})();