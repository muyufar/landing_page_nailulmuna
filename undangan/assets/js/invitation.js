document.addEventListener('DOMContentLoaded', () => {
    const splash = document.getElementById('splash');
    const invitation = document.getElementById('invitation');
    const scroller = document.getElementById('inv-scroll');
    const btnOpen = document.getElementById('btn-open');
    const btnMute = document.getElementById('btn-mute');
    const btnAutoscroll = document.getElementById('btn-autoscroll');
    const config = window.INVITATION_CONFIG || {};

    let audioEl = null;
    let synth = null;
    let isMuted = false;
    let autoScrollTimer = null;
    let autoScrollActive = false;
    let currentSectionIndex = 0;

    function startAudio() {
        if (config.audioMode === 'url' && config.audioUrl) {
            audioEl = new Audio(config.audioUrl);
            audioEl.loop = true;
            audioEl.volume = 0.4;
            audioEl.play().catch(() => {});
        } else if (typeof SholawatSynth !== 'undefined') {
            synth = new SholawatSynth();
            synth.play();
        }
        btnMute?.classList.remove('hidden');
    }

    function toggleMute() {
        isMuted = !isMuted;
        if (audioEl) audioEl.muted = isMuted;
        if (synth) {
            if (isMuted) synth.stop();
            else synth.play();
        }
        if (btnMute) btnMute.textContent = isMuted ? '🔇' : '🔊';
    }

    function getSections() {
        if (!scroller) return [];
        return Array.from(scroller.querySelectorAll('.inv-screen'));
    }

    function scrollToSection(index, smooth = true) {
        const sections = getSections();
        if (!sections.length || !scroller) return;
        const i = Math.max(0, Math.min(index, sections.length - 1));
        currentSectionIndex = i;
        const target = sections[i].offsetTop;

        if (!smooth) {
            scroller.scrollTop = target;
            return;
        }

        const start = scroller.scrollTop;
        const distance = target - start;
        const duration = config.scrollSpeed || 800;
        const startTime = performance.now();

        function step(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const ease = 0.5 - Math.cos(progress * Math.PI) / 2;
            scroller.scrollTop = start + distance * ease;
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    function updateAutoScrollButton() {
        if (!btnAutoscroll) return;
        const label = btnAutoscroll.querySelector('.btn-autoscroll-label');
        const icon = btnAutoscroll.querySelector('.btn-autoscroll-icon');
        if (autoScrollActive) {
            btnAutoscroll.classList.add('is-on');
            btnAutoscroll.classList.remove('is-off');
            if (icon) icon.textContent = '⏸';
            if (label) label.textContent = 'Berhenti';
            btnAutoscroll.title = 'Hentikan gulir otomatis';
        } else {
            btnAutoscroll.classList.remove('is-on');
            btnAutoscroll.classList.add('is-off');
            if (icon) icon.textContent = '▶';
            if (label) label.textContent = 'Gulir';
            btnAutoscroll.title = 'Mulai gulir otomatis';
        }
    }

    function stopAutoScroll() {
        autoScrollActive = false;
        if (autoScrollTimer) {
            clearInterval(autoScrollTimer);
            autoScrollTimer = null;
        }
        updateAutoScrollButton();
    }

    function startAutoScroll() {
        if (!config.autoScroll || !scroller) return;
        autoScrollActive = true;
        updateAutoScrollButton();

        const interval = (config.scrollInterval || 5) * 1000;
        autoScrollTimer = setInterval(() => {
            const sections = getSections();
            if (!sections.length) return;
            let next = currentSectionIndex + 1;
            if (next >= sections.length) next = 0;
            scrollToSection(next);
        }, interval);
    }

    function toggleAutoScroll() {
        if (autoScrollActive) stopAutoScroll();
        else startAutoScroll();
    }

    function initScrollUI() {
        if (!scroller) return;

        scroller.addEventListener('scroll', () => {
            const sections = getSections();
            if (!sections.length) return;
            const scrollTop = scroller.scrollTop;
            let closest = 0;
            let minDist = Infinity;
            sections.forEach((sec, idx) => {
                const dist = Math.abs(sec.offsetTop - scrollTop);
                if (dist < minDist) {
                    minDist = dist;
                    closest = idx;
                }
            });
            currentSectionIndex = closest;
        }, { passive: true });

        if (config.autoScroll) {
            btnAutoscroll?.classList.remove('hidden');
            btnAutoscroll?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                toggleAutoScroll();
            });
        }

        document.querySelectorAll('.screen-hint').forEach((hint) => {
            hint.addEventListener('click', () => {
                stopAutoScroll();
                scrollToSection(currentSectionIndex + 1);
            });
        });
    }

    function openInvitation() {
        document.body.classList.add('inv-open');
        if (config.motionEnabled) {
            document.body.classList.add('inv-motion-on');
        }
        splash?.classList.add('fade-out');
        invitation?.classList.remove('hidden');
        startAudio();

        setTimeout(() => {
            splash?.remove();
            initScrollUI();
            if (config.motionEnabled && window.InvitationMotion) {
                InvitationMotion.start(scroller || document);
            }
            scrollToSection(0, false);
        }, 650);
    }

    btnOpen?.addEventListener('click', openInvitation);
    btnMute?.addEventListener('click', toggleMute);

    // Countdown
    const countdownEl = document.getElementById('countdown');
    if (countdownEl) {
        const target = new Date(countdownEl.dataset.target).getTime();
        const tick = () => {
            const diff = target - Date.now();
            if (diff <= 0) {
                ['cd-days', 'cd-hours', 'cd-mins', 'cd-secs'].forEach((id) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = '00';
                });
                return;
            }
            const set = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.textContent = String(val).padStart(2, '0');
            };
            set('cd-days', Math.floor(diff / 86400000));
            set('cd-hours', Math.floor((diff % 86400000) / 3600000));
            set('cd-mins', Math.floor((diff % 3600000) / 60000));
            set('cd-secs', Math.floor((diff % 60000) / 1000));
        };
        tick();
        setInterval(tick, 1000);
    }

    // RSVP
    const form = document.getElementById('rsvp-form');
    const statusSelect = document.getElementById('rsvp-status');
    const paxField = document.getElementById('pax-field');
    const msgEl = document.getElementById('rsvp-message');
    const guestInput = form?.querySelector('[name="guest_name"]');

    if (guestInput && config.inviteeName && !guestInput.value) {
        guestInput.value = config.inviteeName;
    }

    statusSelect?.addEventListener('change', () => {
        if (paxField) paxField.style.display = statusSelect.value === 'hadir' ? '' : 'none';
    });
    statusSelect?.dispatchEvent(new Event('change'));

    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        stopAutoScroll();
        const btn = form.querySelector('.btn-rsvp');
        btn.disabled = true;
        msgEl?.classList.add('hidden');

        try {
            const res = await fetch((window.INVITATION_CONFIG?.appBase || '') + '/api/rsvp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(Object.fromEntries(new FormData(form))),
            });
            const json = await res.json();
            if (msgEl) {
                msgEl.textContent = json.message;
                msgEl.className = 'rsvp-message ' + (json.success ? 'success' : 'error');
                msgEl.classList.remove('hidden');
            }
            if (json.success) {
                if (!config.inviteeName) form.reset();
                statusSelect?.dispatchEvent(new Event('change'));
            }
        } catch {
            if (msgEl) {
                msgEl.textContent = 'Gagal mengirim. Periksa koneksi internet Anda.';
                msgEl.className = 'rsvp-message error';
                msgEl.classList.remove('hidden');
            }
        }
        btn.disabled = false;
    });
});
