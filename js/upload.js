// A script a form után töltődik be, ezért a mezők már elérhetők.
// A fájl szándékosan nem megy a szerverre: ez csak a feltöltés bemutatója.

const uploadForm = document.querySelector('#upload-form');
const titleInput = document.querySelector('#title');
const dateInput = document.querySelector('#recorded-on');
const descriptionInput = document.querySelector('#description');
const fileInput = document.querySelector('#audio-file');
const dropZone = document.querySelector('#drop-zone');
const fileInfo = document.querySelector('#file-info');
const formErrors = document.querySelector('#form-errors');
const uploadResult = document.querySelector('#upload-result');
const resultTitle = document.querySelector('#result-title');
const resultDate = document.querySelector('#result-date');
const resultDescription = document.querySelector('#result-description');
const resultFile = document.querySelector('#result-file');

function isAllowedAudio(file) {
    const fileName = file.name.toLowerCase();
    return fileName.endsWith('.mp3') || fileName.endsWith('.wav');
}

function formatFileSize(bytes) {
    if (bytes < 1024) {
        return bytes + ' bájt';
    }

    const kilobytes = bytes / 1024;

    if (kilobytes < 1024) {
        return kilobytes.toFixed(1).replace('.', ',') + ' KB';
    }

    const megabytes = kilobytes / 1024;
    return megabytes.toFixed(1).replace('.', ',') + ' MB';
}

function selectedFile() {
    if (fileInput.files.length === 0) {
        return null;
    }

    return fileInput.files[0];
}

function showErrors(messages) {
    formErrors.innerHTML = '';

    const list = document.createElement('ul');

    messages.forEach(function (message) {
        const item = document.createElement('li');
        item.textContent = message;
        list.appendChild(item);
    });

    formErrors.appendChild(list);
    formErrors.hidden = false;
}

function clearErrors() {
    formErrors.innerHTML = '';
    formErrors.hidden = true;
}

function hideResult() {
    uploadResult.hidden = true;
}

function collectErrors() {
    const errors = [];
    const file = selectedFile();

    if (titleInput.value.trim() === '') {
        errors.push('A beszélgetés címe kötelező.');
    }

    if (dateInput.value === '') {
        errors.push('A dátum kötelező.');
    }

    if (fileInput.files.length > 1) {
        errors.push('Egyszerre csak egy hangfájl választható.');
    } else if (!file) {
        errors.push('Válassz egy hangfájlt.');
    } else if (!isAllowedAudio(file)) {
        errors.push('Csak MP3 vagy WAV fájl tölthető fel.');
    } else if (file.size === 0) {
        errors.push('A kiválasztott fájl üres.');
    }

    return errors;
}

function updateFileInfo() {
    const file = selectedFile();

    fileInfo.classList.remove('is-error');
    hideResult();

    if (fileInput.files.length > 1) {
        fileInfo.hidden = false;
        fileInfo.classList.add('is-error');
        fileInfo.textContent = 'Egyszerre csak egy hangfájl választható.';
        return;
    }

    if (!file) {
        fileInfo.hidden = true;
        fileInfo.textContent = '';
        return;
    }

    fileInfo.hidden = false;

    if (!isAllowedAudio(file)) {
        fileInfo.classList.add('is-error');
        fileInfo.textContent = 'Csak MP3 vagy WAV fájl választható. A kiválasztott fájl: ' + file.name;
        return;
    }

    if (file.size === 0) {
        fileInfo.classList.add('is-error');
        fileInfo.textContent = 'A kiválasztott fájl üres.';
        return;
    }

    fileInfo.textContent = 'Kiválasztott fájl: ' + file.name + ' (' + formatFileSize(file.size) + ')';
}

function showResult() {
    const file = selectedFile();
    const description = descriptionInput.value.trim();

    resultTitle.textContent = titleInput.value.trim();
    resultDate.textContent = dateInput.value;
    resultDescription.textContent = description === '' ? 'Nincs megadva' : description;
    resultFile.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
    uploadResult.hidden = false;
}

dropZone.addEventListener('click', function (event) {
    if (event.target === fileInput) {
        return;
    }

    fileInput.click();
});

dropZone.addEventListener('dragover', function (event) {
    event.preventDefault();
    dropZone.classList.add('is-dragover');
});

dropZone.addEventListener('dragleave', function () {
    dropZone.classList.remove('is-dragover');
});

dropZone.addEventListener('drop', function (event) {
    event.preventDefault();
    dropZone.classList.remove('is-dragover');
    fileInput.files = event.dataTransfer.files;
    updateFileInfo();
});

// A böngésző ne nyissa meg a fájlt, ha a zónán kívül engedik el.
document.addEventListener('dragover', function (event) {
    event.preventDefault();
});

document.addEventListener('drop', function (event) {
    event.preventDefault();
});

fileInput.addEventListener('change', updateFileInfo);

uploadForm.addEventListener('reset', function () {
    // A reset esemény a mezők törlése előtt fut, ezért a kiírás utána frissül.
    window.setTimeout(function () {
        clearErrors();
        updateFileInfo();
        hideResult();
    }, 0);
});

uploadForm.addEventListener('submit', function (event) {
    event.preventDefault();

    const errors = collectErrors();

    if (errors.length > 0) {
        showErrors(errors);
        hideResult();
        return;
    }

    clearErrors();
    showResult();
});
