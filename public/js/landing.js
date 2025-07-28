// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    document.querySelector(this.getAttribute('href')).scrollIntoView({
      behavior: 'smooth'
    });
  });
});

// For change on scroll
const navbar = document.getElementById('mainNavbar');
let lastScrollY = window.scrollY;
let ticking = false;

// Fungsi untuk update navbar
function updateNavbar() {
  const scrollY = window.scrollY;

  if (scrollY > 50) {
    // Efek fade in gradual
    const opacity = Math.min((scrollY - 50) / 50, 1); // 0-1 dalam 50px scroll
    navbar.style.backgroundColor = `rgba(52, 58, 64, ${opacity})`;
    navbar.classList.add('navbar-solid');
  } else {
    navbar.style.backgroundColor = 'transparent';
    navbar.classList.remove('navbar-solid');
  }

  // Efek scaling pada padding
  const scaleFactor = Math.max(0.5, 1 - (scrollY / 100));
  navbar.style.padding = `${25 * scaleFactor}px 0`;

  lastScrollY = scrollY;
  ticking = false;
}

// Change navbar style on scroll
window.addEventListener('scroll', function () {
  if (!ticking) {
    window.requestAnimationFrame(updateNavbar);
    ticking = true;
  }
});

// Tooltip untuk ikon
document.addEventListener('DOMContentLoaded', function () {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  });
});

// Loading Page
document.addEventListener('DOMContentLoaded', function () {
  window.addEventListener('load', function () {
    document.body.classList.add('loaded');

    setTimeout(function () {
      document.getElementById('loading').remove();
    }, 500);
  });
});

// Navbar Green
document.addEventListener('DOMContentLoaded', function() {
  const navbar = document.getElementById('mainNavbar');
  const toggler = document.querySelector('.navbar-toggler');
  
  toggler.addEventListener('click', function() {
    navbar.classList.toggle('navbar-expanded');
  });
});