document.addEventListener('DOMContentLoaded', function () {
    const navbar = document.querySelector('.navbar-landing');
    if (navbar) {
        window.addEventListener('scroll', function () {
            navbar.classList.toggle('scrolled', window.scrollY > 40);
        });
    }

    const counters = document.querySelectorAll('[data-counter]');
    if (counters.length && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-counter'), 10);
                const suffix = el.getAttribute('data-suffix') || '';
                let current = 0;
                const step = Math.max(1, Math.floor(target / 60));
                const timer = setInterval(function () {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    el.textContent = current + suffix;
                }, 25);
                observer.unobserve(el);
            });
        }, { threshold: 0.3 });

        counters.forEach(function (c) { observer.observe(c); });
    }
});
