import axios from "axios";

window.favorite = async function(elementId, url, resourceId) {
    try {
        const response = await axios.post(url, { resourceId });
        if (response.data.action === 'added') {
            document.getElementById(elementId).classList.add('active');
        } else if (response.data.action === 'deleted') {
            document.getElementById(elementId).classList.remove('active');
        }
        document.querySelectorAll('.resource-favorites')
            .forEach(el => el.textContent = response.data.favorite_count);
    } catch (error) {
        console.error('Favorite error:', error);
    }
}

window.updateFavoriteCount = function (favoriteCount) {
    $('#favoriteCount').text(favoriteCount);
}

document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-action="favorite"]');
    if (!btn) return;

    if (btn.dataset.authenticated === 'false') {
        alert('Please login to mark a resource as your favorite');
        return;
    }

    favorite('resourceFavorite', baseUrl + '/resources/favorite/', btn.dataset.resource);
    const icon = btn.querySelector('i');
    if (icon.classList.contains('ph-fill')) {
        icon.classList.replace('ph-fill', 'ph-light');
        icon.style.color = '';
    } else {
        icon.classList.replace('ph-light', 'ph-fill');
        icon.style.color = 'gold';
    }
});

document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-action="download-counter"]');
    if (btn) downloadCounter(btn);
});

window.downloadCounter = function(element) {
    const data = {
        file_id: element.getAttribute('data-file'),
        resource_id: element.getAttribute('data-resource'),
    };

    axios.post('/resource/download_counter', data)
        .then(response => {
            console.log('Record stored successfully:', response.data);
        })
        .catch(error => {
            console.error('Error storing record:', error);
        });
}
