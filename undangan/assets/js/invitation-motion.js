/**
 * Mesin animasi ornamen — Web Animations API (andal di mobile)
 */
(function () {
    const running = new Map();

    const KEYFRAMES = {
        melayang: [
            { transform: 'translate3d(0,0,0) scale(1)' },
            { transform: 'translate3d(0,-16px,0) scale(1.05)' },
            { transform: 'translate3d(0,0,0) scale(1)' },
        ],
        goyang: [
            { transform: 'rotate(-5deg) translateX(-4px)' },
            { transform: 'rotate(5deg) translateX(4px)' },
            { transform: 'rotate(-5deg) translateX(-4px)' },
        ],
        denyut: [
            { transform: 'scale(1)', opacity: 0.85 },
            { transform: 'scale(1.1)', opacity: 1 },
            { transform: 'scale(1)', opacity: 0.85 },
        ],
        berputar: [
            { transform: 'rotate(0deg)' },
            { transform: 'rotate(360deg)' },
        ],
        gelombang: [
            { transform: 'translate(0,0)' },
            { transform: 'translate(12px,-10px)' },
            { transform: 'translate(-8px,-6px)' },
            { transform: 'translate(0,0)' },
        ],
        kilau: [
            { opacity: 0.4, filter: 'brightness(1)' },
            { opacity: 1, filter: 'brightness(1.5)' },
            { opacity: 0.4, filter: 'brightness(1)' },
        ],
        hujan: [
            { transform: 'translateY(-5vh)', opacity: 0 },
            { transform: 'translateY(50vh)', opacity: 0.8 },
            { transform: 'translateY(110vh)', opacity: 0 },
        ],
    };

    const TIMING = {
        melayang:  { duration: 4000, easing: 'ease-in-out' },
        goyang:    { duration: 3000, easing: 'ease-in-out' },
        denyut:    { duration: 2500, easing: 'ease-in-out' },
        berputar:  { duration: 12000, easing: 'linear' },
        gelombang: { duration: 5000, easing: 'ease-in-out' },
        kilau:     { duration: 2000, easing: 'ease-in-out' },
        hujan:     { duration: 9000, easing: 'linear' },
    };

    function animateEl(el, mode, delay = 0) {
        if (!mode || mode === 'none' || !KEYFRAMES[mode]) return;
        stopEl(el);
        const anim = el.animate(KEYFRAMES[mode], {
            ...TIMING[mode],
            iterations: Infinity,
            delay,
        });
        running.set(el, anim);
    }

    function stopEl(el) {
        const a = running.get(el);
        if (a) { a.cancel(); running.delete(el); }
    }

    function ornamentSelector() {
        return [
            '.ornament-img.ornament-top',
            '.ornament-img.ornament-bottom',
            '.ornament-img.ornament-divider',
            '.ornament-top-default',
            '.ornament-bottom-default',
            '.ornament-divider-default',
            '.section-divider img',
            '.section-divider .ornament-default',
        ].join(', ');
    }

    function startSectionMotions(root) {
        const sel = ornamentSelector();
        root.querySelectorAll('.inv-screen[data-anim]').forEach((screen) => {
            const mode = screen.dataset.anim;
            if (!mode || mode === 'none') return;

            screen.querySelectorAll(sel).forEach((el, i) => {
                animateEl(el, mode, i * 150);
            });
        });
    }

    function stopAll() {
        running.forEach((a) => a.cancel());
        running.clear();
    }

    window.InvitationMotion = {
        start(root) {
            stopAll();
            startSectionMotions(root || document);
        },
        stop: stopAll,
        refresh(root) {
            this.start(root);
        },
    };
})();
