/**
 * ShareVault - Fully Optimized script.js
 * Features: Drag & Drop, AJAX Upload, Voice Notifications, and File Management
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
const uploadLoader = document.getElementById('uploadLoader');

// --- 1. Dynamic File List with Delete ---
if (toggleList) {
    toggleList.addEventListener('click', async () => {
        if (fileList.style.display === 'none' || fileList.style.display === '') {
            fileList.style.display = 'block';
            fileList.innerHTML = '<p class="loading-text" style="padding:15px;">Loading your files...</p>';
            try {
                const response = await fetch('files.php');
                const files = await response.json();

                if (files.length === 0) {
                    fileList.innerHTML = '<p style="padding:15px;">No active files found.</p>';
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
                fileList.innerHTML = '<p style="padding:15px;">Error loading files.</p>';
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
        dropZone.style.borderColor = "var(--primary)";
    }

    accessMode.addEventListener('change', () => {
        passwordContainer.style.display = accessMode.value === 'restricted' ? 'block' : 'none';
    });

    // --- 3. AJAX Upload & Link Generation Logic ---
    uploadForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const file = fileInput.files[0];
        if (!file) {
            alert('Please select or drop a file first.');
            return;
        }

        // File Size Check (50MB)
        const MAX_SIZE_MB = 50;
        if (file.size > MAX_SIZE_MB * 1024 * 1024) {
            alert(`File is too large! Max size is ${MAX_SIZE_MB}MB.`);
            return;
        }

        // UI Feedback
        uploadLoader.style.display = 'flex';
        const progressContainer = document.getElementById('progressContainer');
        const progressBarFill = document.getElementById('progressBarFill');
        const progressPercent = document.getElementById('progressPercent');
        progressContainer.style.display = 'block';

        const formData = new FormData();
        formData.append('file', file);
        formData.append('accessMode', accessMode.value);
        formData.append('password', document.getElementById('filePassword').value);

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
                    // Success UI Logic
                    uploadLoader.style.display = 'none';
                    resultArea.style.display = 'block';

                    const fullURL = window.location.origin +
                        window.location.pathname.replace('index.php', '') +
                        result.link;
                    shareLink.value = fullURL;

                    // --- SPEECH API TRIGGER ---
                    speakMessage("Upload complete. Thank you for using Share Vault. Your secure link is ready.");

                    uploadForm.reset();
                    dropZone.querySelector('p').innerHTML = "Drag & drop files or <span>Browse</span>";
                    dropZone.style.borderColor = "rgba(255, 255, 255, 0.2)";
                    progressContainer.style.display = 'none';

                    resultArea.scrollIntoView({ behavior: 'smooth' });
                } else {
                    uploadLoader.style.display = 'none';
                    alert("Upload failed: " + (result.message || "Unknown error"));
                }
            } catch (err) {
                uploadLoader.style.display = 'none';
                alert("Server error. Check the console for details.");
            }
        };

        xhr.open('POST', 'upload_handler.php');
        xhr.send(formData);
    });
}

// --- 4. Global Functions ---

// Function to handle Text-to-Speech
function speakMessage(message) {
    if ('speechSynthesis' in window) {
        // Stop any current speech
        window.speechSynthesis.cancel();

        const utterThis = new SpeechSynthesisUtterance(message);
        utterThis.pitch = 1.1;
        utterThis.rate = 1.0;
        utterThis.lang = 'en-US';

        window.speechSynthesis.speak(utterThis);
    }
}

async function deleteFile(fileKey) {
    if (confirm("Are you sure you want to permanently delete this file?")) {
        try {
            const response = await fetch(`delete_handler.php?id=${fileKey}`);
            const result = await response.json();
            if (result.status === 'success') {
                const element = document.getElementById(`file-${fileKey}`);
                if (element) element.remove();

                // Voice feedback for removing a file
                speakMessage("File removed from the vault.");
            } else {
                alert("Error: " + result.message);
            }
        } catch (err) {
            alert("Error connecting to server.");
        }
    }
}

function copyLink() {
    if (shareLink && shareLink.value !== "#" && shareLink.value !== "") {
        shareLink.select();
        shareLink.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(shareLink.value).then(() => {
            const copyBtn = document.querySelector('.link-box button');
            const originalIcon = copyBtn.innerHTML;
            copyBtn.innerHTML = "<i class='bx bx-check'></i>";

            // Voice Feedback for copy
            speakMessage("Link copied to clipboard.");

            setTimeout(() => copyBtn.innerHTML = originalIcon, 2000);
        });
    } else {
        alert("No link to copy!");
    }
}