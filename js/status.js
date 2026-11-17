// Időzített bemutató. A késleltetés itt állítható.
const stepDelayMs = 400;
const stepSize = 10;
const failureAt = 60;

const statusLabels = {
    waiting: 'Várakozás',
    processing: 'Feldolgozás',
    completed: 'Befejezve',
    failed: 'Sikertelen',
};

const statusLabel = document.querySelector('#status-label');
const statusMessage = document.querySelector('#status-message');
const progress = document.querySelector('#progress');
const progressBar = document.querySelector('#progress-bar');
const progressValue = document.querySelector('#progress-value');
const startButton = document.querySelector('#start-demo');
const resetButton = document.querySelector('#reset-demo');
const failureBox = document.querySelector('#simulate-failure');

let timerId = null;
let currentProgress = 0;
let running = false;

function setStatus(state, message, percent) {
    statusLabel.textContent = statusLabels[state];
    statusMessage.textContent = message;
    statusMessage.classList.toggle('is-error', state === 'failed');
    progressBar.style.width = percent + '%';
    progress.setAttribute('aria-valuenow', String(percent));
    progressValue.textContent = percent + '%';
}

function stopTimer() {
    window.clearTimeout(timerId);
    timerId = null;
    running = false;
    startButton.disabled = false;
}

function finish(state, message, percent) {
    stopTimer();
    setStatus(state, message, percent);
}

function tick(shouldFail) {
    currentProgress += stepSize;

    if (shouldFail && currentProgress >= failureAt) {
        finish(
            'failed',
            'A bemutató feldolgozás sikertelen. Valós hiba nem történt.',
            failureAt
        );
        return;
    }

    if (currentProgress >= 100) {
        finish(
            'completed',
            'A bemutató feldolgozás befejeződött. Valós elemzés nem készült.',
            100
        );
        return;
    }

    setStatus('processing', 'A bemutató feldolgozás folyamatban van.', currentProgress);
    timerId = window.setTimeout(function () {
        tick(shouldFail);
    }, stepDelayMs);
}

startButton.addEventListener('click', function () {
    if (running) {
        return;
    }

    running = true;
    startButton.disabled = true;
    currentProgress = 0;
    setStatus('waiting', 'A bemutató a sorban vár.', 0);

    timerId = window.setTimeout(function () {
        tick(failureBox.checked);
    }, stepDelayMs);
});

resetButton.addEventListener('click', function () {
    stopTimer();
    currentProgress = 0;
    setStatus('waiting', 'A bemutató a sorban vár.', 0);
});
