export function setupUpload() {
    document.getElementById('uploadCSVForm')?.addEventListener('submit', submitUploadCSVForm);

    window.openUploadModal = function () {
        const modal = document.getElementById('uploadModal');
        const form = document.getElementById('uploadCSVForm');
        const errorBox = document.getElementById('uploadError');

        form.reset();
        errorBox.textContent = '';
        errorBox.classList.add('hidden');

        modal.classList.remove('hidden');
    };

    window.closeUploadModal = function () {
        document.getElementById('uploadModal')?.classList.add('hidden');
    };
}

async function submitUploadCSVForm(e) {
    e.preventDefault();

    const form = e.target;
    const fileInput = document.getElementById('csvFile');
    const errorBox = document.getElementById('uploadError');
    errorBox.textContent = '';
    errorBox.classList.add('hidden');

    if (!fileInput || !fileInput.files.length) {
        errorBox.textContent = window.translations?.select_file_error || 'Bitte wählen Sie eine CSV-Datei aus.';
        errorBox.classList.remove('hidden');
        return;
    }

    const formData = new FormData();
    formData.append('file', fileInput.files[0]);

    try {
        const response = await fetch('/import', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData,
        });

        const result = await response.json();

        if (!response.ok || result.status === 'error') {
            const messages = result.errors?.file || [result.message || 'Upload failed'];
            errorBox.innerHTML = messages.map(msg => `<div>${msg}</div>`).join('');
            errorBox.classList.remove('hidden');
            return;
        }

        window.closeUploadModal();
        setTimeout(() => { window.location.reload(); }, 1000);

    } catch (err) {
        console.error(err);
        errorBox.textContent = window.translations?.server_error || 'Serverfehler.';
        errorBox.classList.remove('hidden');
    }
}
