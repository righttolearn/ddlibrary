import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

async function searchImages(url = null) {
    const subjectArea = document.getElementById('subject_areas')?.value ?? '';
    const search = document.getElementById('search-input')?.value ?? '';

    document.getElementById('loading-message').style.display = 'block';
    document.getElementById('file-list').innerHTML = '';

    const params = new URLSearchParams({
        search,
        subject_area_id: subjectArea,
        language: localLanguage
    });

    const fullURL = url ?? `${baseUrl}/search-images?${params.toString()}`;

    try {
        const response = await axios.get(fullURL);
        document.getElementById('file-list').innerHTML = response.data;
    } catch (error) {
        console.error('Error searching images:', error);
        alert('Error searching images. Please try again.');
    } finally {
        document.getElementById('loading-message').style.display = 'none';
    }
}

function initializePagination() {
    document.getElementById('file-list').addEventListener('click', (e) => {
        const link = e.target.closest('.pagination a');
        if (!link) return;
        e.preventDefault();
        searchImages(link.getAttribute('href'));
    });
}

function initializeImageSelection() {
    document.getElementById('file-list').addEventListener('click', (e) => {
        const item = e.target.closest('.image-item');
        if (!item) return;
        document.querySelectorAll('.image-item').forEach(el => el.classList.remove('selected'));
        item.classList.add('selected');
    });
}

function displaySelectedImage(imageUrl) {
    const previewImage = document.getElementById('preview-image');
    const selectedImagePreview = document.getElementById('selected-image-preview');
    previewImage.src = imageUrl;
    selectedImagePreview.classList.remove('d-none');
}

function selectImage(id, url) {
    document.getElementById('resource_file_id').value = id;
    displaySelectedImage(url);

    document.querySelectorAll('.bg-success').forEach(el => el.classList.remove('bg-success'));

    const selectedElement = document.querySelector(`.image-${id}`);
    if (selectedElement) selectedElement.classList.add('bg-success');

    bootstrap.Modal.getInstance(document.getElementById('imageManagerModal')).hide();
}

function selectNewImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    document.getElementById('image-name').value = file.name.split('.').slice(0, -1).join('.');

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById('preview');
        img.src = e.target.result;
        img.classList.remove('d-none');

        img.onload = function() {
            const width = img.naturalWidth;
            const height = img.naturalHeight;
            const isSquare = width === height;
            document.getElementById('dimensions').innerHTML = `
                <span class="d-inline-block text-${isSquare ? 'success' : 'danger'} border-radius-5 p-1">
                    ${width} x ${height}
                </span>`;
        };
    };
    reader.readAsDataURL(file);
}

async function uploadNewImage() {
    const formData = new FormData(document.getElementById('upload-form'));
    const submitButton = document.querySelector('#upload-form button[type="submit"]');

    document.querySelectorAll('.error-message').forEach(el => el.remove());
    submitButton.disabled = true;
    submitButton.textContent = 'Uploading...';

    try {
        const response = await axios.post(`${baseUrl}/upload-image`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data.success) {
            document.getElementById('resource_file_id').value = response.data.resource_file_id;
            displaySelectedImage(response.data.imageUrl);
            document.getElementById('upload-form').reset();
            bootstrap.Modal.getInstance(document.getElementById('imageManagerModal')).hide();
            searchImages();
        }
    } catch (error) {
        if (error.response?.status === 422) {
            displayErrors(error.response.data.errors);
        } else {
            alert('Error uploading image. Please try again.');
        }
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Upload';
    }
}

function displayErrors(errors) {
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.getElementById(field);
        if (!input) continue;
        const span = document.createElement('span');
        span.className = 'error-message text-danger small d-block';
        span.textContent = messages.join(', ');
        input.insertAdjacentElement('afterend', span);
    }
}


document.getElementById('upload-form')?.addEventListener('submit', (e) => {
    e.preventDefault();
    uploadNewImage();
});

document.addEventListener('DOMContentLoaded', function() {
    initializePagination();
    initializeImageSelection();

    let cropper;

    document.getElementById('cropper-image').addEventListener('change', function(event) {
        const files = event.target.files;
        const done = (url) => {
            document.getElementById('cropper-image').value = '';
            return url;
        };

        if (files && files.length > 0) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const image = document.createElement('img');
                image.src = e.target.result;

                // Clear previous images in the cropper
                const cropperContainer = document.getElementById('cropper');
                cropperContainer.innerHTML = '';
                cropperContainer.appendChild(image);

                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(image, {
                    aspectRatio: 1, // Square crop
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                    ready() {
                        // Show the download button when the cropper is ready
                        document.getElementById('download-cropped-image').style
                            .display = 'block';
                    }
                });

                // Set the cropper to fill the div
                image.style.width = '100%';
                image.style.height = '100%';
                image.style.objectFit = 'cover'; // Ensures the image covers the div
            };
            reader.readAsDataURL(files[0]);
        }
    });

    document.getElementById('download-cropped-image').addEventListener('click', function() {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas();
            canvas.toBlob((blob) => {
                const file = new File([blob], 'cropped.png', { type: 'image/png' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);

                const fileInput = document.getElementById('image');
                fileInput.files = dataTransfer.files;

                selectNewImage({ target: { files: dataTransfer.files } });
            });
        }
    });
});

document.addEventListener('change', (e) => {
    if (e.target.closest('[data-action="search-images"]')) searchImages();
});
document.addEventListener('keyup', (e) => {
    if (e.target.closest('[data-action="search-images"]')) searchImages();
});

document.addEventListener('change', (e) => {
    if (e.target.closest('[data-action="select-new-image"]')) selectNewImage(e);
});

// Make functions globally accessible
window.searchImages = searchImages;
window.selectNewImage = selectNewImage;
window.selectImage = selectImage;


