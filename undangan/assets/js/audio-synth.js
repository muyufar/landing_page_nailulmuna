/**
 * Web Audio API Sholawat Synthesizer
 * Generates ambient instrumental tones without external MP3 files.
 */
class SholawatSynth {
    constructor() {
        this.ctx = null;
        this.nodes = [];
        this.gain = null;
        this.playing = false;
    }

    init() {
        if (this.ctx) return;
        this.ctx = new (window.AudioContext || window.webkitAudioContext)();
        this.gain = this.ctx.createGain();
        this.gain.gain.value = 0.15;
        this.gain.connect(this.ctx.destination);
    }

    play() {
        this.init();
        if (this.playing) return;
        this.playing = true;

        const notes = [261.63, 293.66, 329.63, 349.23, 392.00, 440.00, 493.88];
        const melody = [0, 2, 4, 2, 0, 2, 4, 5, 4, 2, 0, 1, 0];

        let step = 0;
        const playNote = () => {
            if (!this.playing) return;

            const freq = notes[melody[step % melody.length]];
            const osc = this.ctx.createOscillator();
            const noteGain = this.ctx.createGain();

            osc.type = 'sine';
            osc.frequency.value = freq;

            const now = this.ctx.currentTime;
            noteGain.gain.setValueAtTime(0, now);
            noteGain.gain.linearRampToValueAtTime(0.3, now + 0.1);
            noteGain.gain.exponentialRampToValueAtTime(0.01, now + 1.2);

            osc.connect(noteGain);
            noteGain.connect(this.gain);
            osc.start(now);
            osc.stop(now + 1.3);

            this.nodes.push(osc);
            step++;
            setTimeout(playNote, 800);
        };

        // Pad drone
        const drone = this.ctx.createOscillator();
        const droneGain = this.ctx.createGain();
        drone.type = 'triangle';
        drone.frequency.value = 130.81;
        droneGain.gain.value = 0.08;
        drone.connect(droneGain);
        droneGain.connect(this.gain);
        drone.start();
        this.nodes.push(drone);

        playNote();
    }

    stop() {
        this.playing = false;
        this.nodes.forEach(n => { try { n.stop(); } catch (_) {} });
        this.nodes = [];
    }

    toggle() {
        if (this.playing) {
            this.stop();
            return false;
        }
        this.play();
        return true;
    }
}

window.SholawatSynth = SholawatSynth;
