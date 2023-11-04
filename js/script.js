let userBox = document.querySelector('.header .header-2 .user-box');

document.querySelector('#user-btn').onclick = () => {
    userBox.classList.toggle('active');
    navbar.classList.remove('active');
};

let navbar = document.querySelector('.header .header-2 .navbar');

document.querySelector('#menu-btn').onclick = () => {
    navbar.classList.toggle('active');
    userBox.classList.remove('active');
};

window.onscroll = () => {
    userBox.classList.remove('active');
    navbar.classList.remove('active');

    if (window.scrollY > 70) {
        document.querySelector('.header .header-2').classList.add('active');
    } else {
        document.querySelector('.header .header-2').classList.remove('active');
    }
};

document.addEventListener('DOMContentLoaded', function() {
    let navLinks = document.querySelectorAll('.navbar a');
    navLinks.forEach(function(link) {
        if (link.href === window.location.href) {
            link.classList.add('active');
        }
    });
});

document.querySelector('#close-update').onclick = () => {
    document.querySelector('.edit-form').style.display = 'none';
    window.location = window.location.href.split('?')[0];
};

