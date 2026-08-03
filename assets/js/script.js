document.addEventListener('DOMContentLoaded', function () {
    const navToggle = document.getElementById('navToggle');
    const navLinks = document.getElementById('navLinks');

    if (!navToggle || !navLinks) {
        console.error('Navigation elements were not found.');
        return;
    }

    function closeNavigation() {
        navLinks.classList.remove('is-open');
        navToggle.classList.remove('is-active');
        navToggle.setAttribute('aria-expanded', 'false');
        navToggle.setAttribute(
            'aria-label',
            'Open navigation menu'
        );
    }

    function openNavigation() {
        navLinks.classList.add('is-open');
        navToggle.classList.add('is-active');
        navToggle.setAttribute('aria-expanded', 'true');
        navToggle.setAttribute(
            'aria-label',
            'Close navigation menu'
        );
    }

    navToggle.addEventListener('click', function () {
        const isOpen = navLinks.classList.contains('is-open');

        if (isOpen) {
            closeNavigation();
        } else {
            openNavigation();
        }
    });

    navLinks.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closeNavigation);
    });

    document.addEventListener('click', function (event) {
        const clickedInsideMenu = navLinks.contains(event.target);
        const clickedMenuButton = navToggle.contains(event.target);

        if (!clickedInsideMenu && !clickedMenuButton) {
            closeNavigation();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeNavigation();
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 720) {
            closeNavigation();
        }
    });
});