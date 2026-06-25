/**
 * Polling tanpa reload — cegah halaman berkedip
 */
const LivePoll = {
    intervalMs: 15000,

    start(fn) {
        this.stop();
        if (document.hidden) return;
        fn();
        this._timer = setInterval(() => {
            if (!document.hidden) fn();
        }, this.intervalMs);
        document.addEventListener('visibilitychange', this._onVisibility);
        this._pollFn = fn;
    },

    stop() {
        if (this._timer) clearInterval(this._timer);
        document.removeEventListener('visibilitychange', this._onVisibility);
    },

    _onVisibility: null,
    _pollFn: null,
    _timer: null,

    init() {
        LivePoll._onVisibility = () => {
            if (!document.hidden && LivePoll._pollFn) {
                LivePoll._pollFn();
            }
        };
    },

    setText(id, value) {
        const el = document.getElementById(id);
        if (el && el.textContent !== String(value)) {
            el.textContent = value;
        }
    },

    setLiveLabel(id) {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = 'Live · ' + new Date().toLocaleTimeString('id-ID');
        }
    },
};

LivePoll.init();
