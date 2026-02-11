
const uploadForm = document.getElementById('uploadForm');
const fileInput = document.getElementById('fileInput');
const dropZone = document.getElementById('dropZone');
const resultArea = document.getElementById('resultArea');
const shareLink = document.getElementById('shareLink');
const accessMode = document.getElementById('accessMode');
const passwordContainer = document.getElementById('passwordContainer');
const toggleList = document.getElementById('toggleList');
const fileList = document.getElementById('fileList');
const passwordInput = document.getElementById('entered_pass');
const errorMessage = document.getElementById('errorMessage');

// Password Verify Page 
if (passwordInput) {
    passwordInput.addEventListener('input', () => {
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
    });
}

//  Dropdown List
if (toggleList) {
    toggleList.addEventListener('click', async () => {
        if (fileList.style.display === 'none' || fileList.style.display === '') {
            fileList.style.display = 'block';
            fileList.innerHTML = '<p>Loading...</p>';
            try {
                const response = await fetch('files.php');
                const files = await response.json();
                if (files.length === 0) {
                    fileList.innerHTML = '<p>No active files found.</p>';
                    return;
                }
                fileList.innerHTML = files.map(file => `
                    <div class="file-item">
                        <span class="file-name">${file.original_name}</span>
                        <a href="download.php?id=${file.file_key}" target="_blank" class="view-btn">Download</a>
                    </div>
                `).join('');
            } catch (err) {
                fileList.innerHTML = '<p>Error loading files.</p>';
            }
        } else {
            fileList.style.display = 'none';
        }
    });
}

// upload form
if (uploadForm) {
    dropZone.addEventListener('click', () => fileInput.click());

    accessMode.addEventListener('change', () => {
        passwordContainer.style.display = accessMode.value === 'restricted' ? 'block' : 'none';
    });

    uploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!fileInput.files[0]) {
            alert('Please select a file.');
            return;
        }
        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('accessMode', accessMode.value);
        formData.append('password', document.getElementById('filePassword').value);

        const response = await fetch('upload_handler.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.status === 'success') {
            resultArea.style.display = 'block';
            const fullURL = window.location.origin + window.location.pathname.replace('index.php', '') + result.link;
            shareLink.value = fullURL;
        } else {
            alert("Upload failed: " + result.message);
        }
    });
}

// Global Function
function copyLink() {
    if (shareLink) {
        shareLink.select();
        document.execCommand('copy');
        alert('Link copied to clipboard!');
    }
}