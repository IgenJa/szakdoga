const searchInput = document.querySelector('#conversation-search');
const statusFilter = document.querySelector('#status-filter');
const summary = document.querySelector('#filter-summary');
const emptyMessage = document.querySelector('#filter-empty');
const rows = document.querySelectorAll('#conversation-table tbody tr');

function applyFilters() {
    const query = searchInput.value.trim().toLowerCase();
    const status = statusFilter.value;
    let visibleCount = 0;

    rows.forEach(function (row) {
        const title = row.getAttribute('data-title').toLowerCase();
        const rowStatus = row.getAttribute('data-status');
        const titleMatches = query === '' || title.indexOf(query) !== -1;
        const statusMatches = status === '' || rowStatus === status;
        const visible = titleMatches && statusMatches;

        row.hidden = !visible;

        if (visible) {
            visibleCount++;
        }
    });

    summary.textContent = visibleCount + ' beszélgetés látható';
    emptyMessage.hidden = visibleCount !== 0;
}

searchInput.addEventListener('input', applyFilters);
statusFilter.addEventListener('change', applyFilters);
