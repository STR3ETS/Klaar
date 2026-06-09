// ── Mobile menu toggle ──
const menuBtn = document.getElementById('mobile-menu-btn');
const menu = document.getElementById('mobile-menu');
const menuIcon = document.getElementById('mobile-menu-icon');

if (menuBtn && menu) {
    menuBtn.addEventListener('click', () => {
        const open = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden', open);
        menuIcon.classList.toggle('fa-bars', open);
        menuIcon.classList.toggle('fa-xmark', !open);
    });
}

// ── Mobile accordion ──
document.querySelectorAll('.mobile-accordion-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
        const panel = btn.nextElementSibling;
        const icon = btn.querySelector('i');
        const isOpen = !panel.classList.contains('hidden');

        // Close all other panels
        document.querySelectorAll('.mobile-accordion-panel').forEach((p) => {
            p.classList.add('hidden');
            p.previousElementSibling.querySelector('i').style.transform = '';
        });

        // Toggle current
        if (!isOpen) {
            panel.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        }
    });
});

// ── Nav background on scroll ──
const nav = document.getElementById('main-nav');

if (nav) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 40) {
            nav.style.backgroundColor = 'rgba(23, 19, 14, 0.95)';
            nav.style.backdropFilter = 'blur(12px)';
        } else {
            nav.style.backgroundColor = '';
            nav.style.backdropFilter = '';
        }
    }, { passive: true });
}

// ── Cookie consent ──
const cookieBanner = document.getElementById('cookie-consent');
const cookieAccept = document.getElementById('cookie-accept');
const cookieDecline = document.getElementById('cookie-decline');

if (cookieBanner && !localStorage.getItem('cookie-consent')) {
    cookieBanner.classList.remove('hidden');
}

function dismissCookies(value) {
    localStorage.setItem('cookie-consent', value);
    cookieBanner.style.transition = 'opacity 0.3s, transform 0.3s';
    cookieBanner.style.opacity = '0';
    cookieBanner.style.transform = 'translateY(1rem)';
    setTimeout(() => cookieBanner.classList.add('hidden'), 300);
}

if (cookieAccept) cookieAccept.addEventListener('click', () => dismissCookies('accepted'));
if (cookieDecline) cookieDecline.addEventListener('click', () => dismissCookies('declined'));

// ── Voice command Alpine component (entries page) ──
document.addEventListener('alpine:init', () => {
    Alpine.data('voiceCommand', () => ({
        state: 'idle',
        recognition: null,
        finalTranscript: '',
        interimTranscript: '',
        resultMessage: '',
        hasActions: false,
        silenceTimer: null,
        resultTimer: null,

        toggle() {
            if (this.state === 'listening') {
                this.stopListening();
            } else if (this.state === 'idle' || this.state === 'result') {
                this.startListening();
            }
        },

        startListening() {
            const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SR) {
                this.resultMessage = 'Spraakherkenning niet beschikbaar in deze browser.';
                this.hasActions = false;
                this.state = 'result';
                this.autoHideResult();
                return;
            }

            this.finalTranscript = '';
            this.interimTranscript = '';
            this.resultMessage = '';
            if (this.resultTimer) clearTimeout(this.resultTimer);

            this.recognition = new SR();
            this.recognition.lang = 'nl-NL';
            this.recognition.interimResults = true;
            this.recognition.continuous = true;
            this.recognition.maxAlternatives = 1;

            let lastSpeechTime = Date.now();
            const self = this;

            this.recognition.onresult = (event) => {
                let interim = '';
                for (let i = event.resultIndex; i < event.results.length; i++) {
                    const text = event.results[i][0].transcript;
                    if (event.results[i].isFinal) {
                        self.finalTranscript += text + ' ';
                        lastSpeechTime = Date.now();
                    } else {
                        interim += text;
                    }
                }
                self.interimTranscript = interim;
            };

            this.recognition.onerror = (event) => {
                if (event.error !== 'no-speech' && event.error !== 'aborted') {
                    console.warn('Voice command error:', event.error);
                }
            };

            this.recognition.onend = () => {
                if (self.state === 'listening') {
                    try { self.recognition.start(); } catch (e) {}
                }
            };

            this.recognition.start();
            this.state = 'listening';

            this.silenceTimer = setInterval(() => {
                if (self.state !== 'listening') return;
                const silenceMs = Date.now() - lastSpeechTime;
                if (silenceMs > 2000 && self.finalTranscript.trim().length > 0) {
                    self.stopListening();
                }
            }, 250);
        },

        stopListening() {
            if (this.silenceTimer) {
                clearInterval(this.silenceTimer);
                this.silenceTimer = null;
            }

            if (this.recognition) {
                this.state = '_stopping';
                try { this.recognition.stop(); } catch (e) {}
                this.recognition = null;
            }

            const transcript = this.finalTranscript.trim();
            if (transcript.length < 3) {
                this.state = 'idle';
                return;
            }

            this.executeCommand(transcript);
        },

        async executeCommand(transcript) {
            this.state = 'processing';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch('/api/voice-command', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ transcript }),
                });

                if (!response.ok) throw new Error('Server error');

                const data = await response.json();
                this.resultMessage = data.message || 'Commando verwerkt.';
                this.hasActions = (data.actions_taken || []).some(a => a.count > 0);

                this.state = 'result';

                if (this.hasActions) {
                    this.resultTimer = setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    this.autoHideResult();
                }

            } catch (err) {
                this.resultMessage = 'Er ging iets mis. Probeer opnieuw.';
                this.hasActions = false;
                this.state = 'result';
                this.autoHideResult();
                console.error('Voice command failed:', err);
            }
        },

        autoHideResult() {
            this.resultTimer = setTimeout(() => {
                if (this.state === 'result') this.state = 'idle';
            }, 4000);
        },
    }));
});
