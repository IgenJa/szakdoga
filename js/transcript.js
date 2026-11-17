const searchInput = document.querySelector('#transcript-search');
const segments = document.querySelectorAll('.segment');
const searchEmpty = document.querySelector('#search-empty');

if (searchInput) {
    searchInput.addEventListener('input', function () {
        const query = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        segments.forEach(function (segment) {
            const text = segment.textContent.toLowerCase();
            const matches = query === '' || text.indexOf(query) !== -1;
            segment.hidden = !matches;

            if (matches) {
                visibleCount++;
            }
        });

        searchEmpty.hidden = visibleCount !== 0;
    });
}

const audio = document.querySelector('#conversation-audio');
const timestampButtons = document.querySelectorAll('.timestamp-button');

if (audio) {
    timestampButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const start = Number(button.getAttribute('data-start'));

            if (Number.isNaN(start)) {
                return;
            }

            audio.currentTime = start;

            const playback = audio.play();

            if (playback) {
                playback.catch(function () {
                    // A pozíció beáll, akkor is, ha a böngésző nem indítja el a hangot.
                });
            }
        });
    });
}
