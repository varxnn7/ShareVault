/**
 * ShareVault - Fully Optimized script.js
 */

const uploadForm = document.getElementById('uploadForm');
const fileInput = document.getElementById('fileInput');
const dropZone = document.getElementById('dropZone');
const resultArea = document.getElementById('resultArea');
const shareLink = document.getElementById('shareLink');
const accessMode = document.getElementById('accessMode');
const passwordContainer = document.getElementById('passwordContainer');
const toggleList = document.getElementById('toggleList');
const fileList = document.getElementById('fileList');

// --- 1. Dynamic File List with Delete ---
if (toggleList) {
    toggleList.addEventListener('click', async () => {
        if (fileList.style.display === 'none' || fileList.style.display === '') {
            fileList.style.display = 'block';
            fileList.innerHTML = '<p class="loading-text">Loading your files...</p>';
            try {
                const response = await fetch('files.php');
                const files = await response.json();

                if (files.length === 0) {
                    fileList.innerHTML = '<p style="padding:10px;">No active files found.</p>';
                    return;
                }

                fileList.innerHTML = files.map(file => `
                    <div class="file-item" id="file-${file.file_key}">
                        <span class="file-name">${file.original_name}</span>
                        <div class="file-actions">
                            <a href="download.php?id=${file.file_key}" target="_blank" class="view-btn">View</a>
                            <button onclick="deleteFile('${file.file_key}')" class="delete-btn">Delete</button>
                        </div>
                    </div>
                `).join('');
            } catch (err) {
                fileList.innerHTML = '<p style="padding:10px;">Error loading files.</p>';
            }
        } else {
            fileList.style.display = 'none';
        }
    });
}

// --- 2. Drag & Drop Logic ---
if (uploadForm) {
    dropZone.addEventListener('click', () => fileInput.click());

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
        }, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-active'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-active'), false);
    });

    dropZone.addEventListener('drop', (e) => {
        const droppedFiles = e.dataTransfer.files;
        if (droppedFiles.length > 0) {
            fileInput.files = droppedFiles;
            handleFileSelection(droppedFiles[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFileSelection(fileInput.files[0]);
        }
    });

    function handleFileSelection(file) {
        dropZone.querySelector('p').innerHTML = `Selected: <strong>${file.name}</strong>`;
        dropZone.style.borderColor = "#6366f1";
    }

    accessMode.addEventListener('change', () => {
        passwordContainer.style.display = accessMode.value === 'restricted' ? 'block' : 'none';
    });

    // --- 3. AJAX Upload Logic ---
    uploadForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const file = fileInput.files[0];
        if (!file) {
            alert('Please select or drop a file first.');
            return;
        }

        // --- FILE SIZE LIMIT CHECK ---
        const MAX_SIZE_MB = 50;
        const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;
        if (file.size > MAX_SIZE_BYTES) {
            alert(`File is too large! Maximum allowed size is ${MAX_SIZE_MB}MB.`);
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('accessMode', accessMode.value);
        formData.append('password', document.getElementById('filePassword').value);

        const submitBtn = uploadForm.querySelector('button[type="submit"]');
        const progressContainer = document.getElementById('progressContainer');
        const progressBarFill = document.getElementById('progressBarFill');
        const progressPercent = document.getElementById('progressPercent');

        progressContainer.style.display = 'block';
        submitBtn.disabled = true;
        submitBtn.innerText = "Uploading...";

        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBarFill.style.width = percent + '%';
                progressPercent.innerText = percent + '%';
            }
        });

        xhr.onload = () => {
            try {
                const result = JSON.parse(xhr.responseText);
                if (xhr.status === 200 && result.status === 'success') {
                    resultArea.style.display = 'block';
                    const fullURL = window.location.origin + window.location.pathname.replace('index.php', '') + result.link;
                    shareLink.value = fullURL;

                    uploadForm.reset();
                    dropZone.querySelector('p').innerHTML = "Drag & drop files or <span>Browse</span>";
                    dropZone.style.borderColor = "rgba(255, 255, 255, 0.2)";
                } else {
                    alert("Upload failed: " + (result.message || "Unknown error"));
                }
            } catch (err) {
                alert("Server error. Please check your connection.");
            }
            resetUI();
        };

        xhr.onerror = () => {
            alert("An error occurred during upload.");
            resetUI();
        };

        function resetUI() {
            submitBtn.disabled = false;
            submitBtn.innerText = "Generate Secure Link";
            progressContainer.style.display = 'none';
            progressBarFill.style.width = '0%';
        }

        xhr.open('POST', 'upload_handler.php');
        xhr.send(formData);
    });
}

// --- 4. Global Functions ---

async function deleteFile(fileKey) {
    if (confirm("Are you sure you want to permanently delete this file?")) {
        try {
            const response = await fetch(`delete_handler.php?id=${fileKey}`);
            const result = await response.json();
            if (result.status === 'success') {
                const element = document.getElementById(`file-${fileKey}`);
                if (element) element.remove();
            } else {
                alert("Could not delete file: " + result.message);
            }
        } catch (err) {
            alert("Error connecting to server.");
        }
    }
}

function copyLink() {
    const shareLink = document.getElementById('shareLink');
    if (shareLink && shareLink.value !== "#" && shareLink.value !== "") {
        shareLink.select();
        shareLink.setSelectionRange(0, 99999);

        navigator.clipboard.writeText(shareLink.value).then(() => {
            alert("Link copied to clipboard!");

            const copyBtn = document.querySelector('.link-box button');
            const originalIcon = copyBtn.innerHTML;
            copyBtn.innerHTML = "<i class='bx bx-check'></i>";
            setTimeout(() => copyBtn.innerHTML = originalIcon, 2000);
        });
    } else {
        alert("No link to copy!");
    }
}