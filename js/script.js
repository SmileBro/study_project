let userBox = document.querySelector('.header .header-2 .user-box');
let navbar = document.querySelector('.header .header-2 .navbar');
let head1 = document.querySelector('.header .header-1');
let current_page = window.location.pathname;
document.querySelector('#user-btn').onclick = () => {
    userBox.classList.toggle('active');
    navbar.classList.remove('active');
};

document.querySelector('#menu-btn').onclick = () => {
    navbar.classList.toggle('active');
    userBox.classList.remove('active');
};

window.onscroll = () => {
    userBox.classList.remove('active');
    navbar.classList.remove('active');
    if (window.scrollY > head1.offsetHeight) {
        document.querySelector('.heading').classList.add('active');
        document.querySelector('.header .header-2').classList.add('active');
    } else {
        document.querySelector('.heading').classList.remove('active');
        document.querySelector('.header .header-2').classList.remove('active');
    }
};

document.addEventListener('DOMContentLoaded', function() {
    let navLinks = document.querySelectorAll('.navbar a');
    navLinks.forEach(function(link) {
        if (link.pathname === current_page) {
            link.classList.add('active');
        }
        if (current_page === '/details.php') {
            navLinks[2].classList.add('active');
        }
    });
});

document.querySelector('#close-update').onclick = () => {
    document.querySelector('.edit-form').style.display = 'none';
    window.location = window.location.href.split('?')[0];
};

