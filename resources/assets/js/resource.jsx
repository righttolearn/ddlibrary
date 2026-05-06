import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

function setActive(option) {
    // Remove active class from all buttons
    document.querySelectorAll('.image-manager-options .btn').forEach(btn => {
        btn.classList.remove('active', 'btn-outline-primary');
        btn.classList.add('btn-outline-secondary');
    });

    // Add active class to the clicked button
    if (option === 'select') {
        document.getElementById('select-image-option').classList.add('active', 'btn-outline-primary');
    } else if (option === 'upload') {
        document.getElementById('upload-image-option').classList.add('active', 'btn-outline-primary');
    } else if (option === 'crop') {
        document.getElementById('cropper-image-option').classList.add('active', 'btn-outline-primary');
    }

    // Show/hide content based on the active button
    document.getElementById('select-image-content').style.display = option === 'select' ? 'block' : 'none';
    document.getElementById('upload-image-content').style.display = option === 'upload' ? 'block' : 'none';
    document.getElementById('cropper-image-content').style.display = option === 'crop' ? 'block' : 'none';
}

// Initialize the default view
setActive('select');

function searchImages(url = null) {
    const subjectArea = document.getElementById('subject_areas').value;
    const search = document.getElementById('search-input').value;

    // Show loading message
    document.getElementById('loading-message').style.display = 'block';
    document.getElementById('file-list').innerHTML = ''; // Clear previous results

    // Prepare the query parameters
    const params = new URLSearchParams({
        search: search,
        subject_area_id: subjectArea,
        language: localLanguage
    });

    let fullURL = url ? url : `${baseUrl}/search-images?${params.toString()}`

    fetch(`${fullURL}`, {
            method: 'GET'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text(); // Assuming the response is HTML
        })
        .then(responseText => {
            document.getElementById('file-list').innerHTML = responseText;
            initializePagination();
            initializeImageSelection();
        })
        .catch(error => {
            console.error('Error searching images:', error);
            alert('Error searching images. Please try again.');
        })
        .finally(() => {
            // Hide loading message
            document.getElementById('loading-message').style.display = 'none';
        });
}

function initializePagination() {
    $('.pagination a').on('click', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        searchImages(url);
    });
}

function initializeImageSelection() {
    $('.image-item').on('click', function() {
        $('.image-item').removeClass('selected');
        $(this).addClass('selected');
        selectImageBtn.show();
    });
}


function selectImage(id, url) {
    $('#resource_file_id').val(id);
    displaySelectedImage(url);
    const resourceLists = document.querySelectorAll('.bg-success');

    // Iterate over the NodeList and remove the 'bg-success' class
    resourceLists.forEach((element) => {
        element.classList.remove('bg-success');
    });

    // Select the first element with the class 'first-class'
    const firstElement = document.querySelector(`.image-${id}`);

    // Check if the element exists and add the 'bg-success' class
    if (firstElement) {
        firstElement.classList.add('bg-success');
    }
    bootstrap.Modal.getInstance(document.getElementById('exampleModal')).hide();

}

function displaySelectedImage(imageUrl) {
    // Select the image element using its ID
    const previewImage = document.getElementById('preview-image');

    // Set the 'src' attribute to the image URL
    previewImage.setAttribute('src', imageUrl);

    // Show the selected image preview
    const selectedImagePreview = document.getElementById('selected-image-preview');
    selectedImagePreview.style.display = 'block'; // Change to 'block' to show the element
}

function selectNewImage() {
    const file = event.target.files[0];
    if (file) {
        const fileName = file.name.split('.').slice(0, -1).join('.');
        $('#image-name').val(fileName);
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('preview');
            img.src = e.target.result;

            img.onload = function() {
                const width = img.naturalWidth;
                const height = img.naturalHeight;
                $("#dimensions").html(
                    `<span class='d-inline-block text-${width == height ? 'success' :'danger'} white border-radius-5 p-1'>${width} x ${height}</span>`
                )
            };

            img.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

}

function uploadNewImage() {
    const formData = new FormData($('#upload-form')[0]);
    const submitButton = $('#upload-form button[type="submit"]');

    // Clear previous error messages
    $('.error-message').remove();

    // Disable submit button and show loading text
    submitButton.prop('disabled', true).html('Uploading...');

    $.ajax({
        url: `${baseUrl}/upload-image`,
        type: 'post',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function success(response) {
            if (response.success) {
                $('#resource_file_id').val(response.resource_file_id);
                displaySelectedImage(response.imageUrl);
                $('#upload-form')[0].reset();
                alert('Image uploaded successfully!');
                
                bootstrap.Modal.getInstance(document.getElementById('exampleModal')).hide();
                
                searchImages();
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = JSON.parse(xhr.responseText).errors;
                displayErrors(errors);
            } else {
                alert('Error uploading image. Please try again.');
            }
        },
        complete: function() {
            // Re-enable submit button and restore original text
            submitButton.prop('disabled', false).html('Upload');
        }
    });
}

function displayErrors(errors) {
    for (const [field, messages] of Object.entries(errors)) {
        const inputField = $(`#${field}`);
        const errorMessage = messages.join(', ');
        inputField.after(`<span class="error-message text-danger">${errorMessage}</span>`);
    }
}


const uploadForm = document.getElementById('upload-form');

uploadForm.addEventListener('submit', function(e) {
    e.preventDefault();
    uploadNewImage();
});



document.addEventListener('DOMContentLoaded', function() {
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
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'cropped-image.png'; // Default file name
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });
        }
    });
});

function toggleTranslation(checkbox) {
    if(checkbox.checked){
        $(".translation").removeClass('d-none')
        $("#translator").val('').attr('required', true);
    }else{
        $(".translation").addClass('d-none');
        $("#translator").attr('required', false);
    }
}

// Make functions globally accessible
window.setActive = setActive;
window.searchImages = searchImages;
window.selectNewImage = selectNewImage;
window.selectImage = selectImage;
window.toggleTranslation = toggleTranslation;

