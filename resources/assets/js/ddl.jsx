import '../css/ddl.css';
import './bootstrap.js';
import 'jquery-ui/dist/jquery-ui';
import Cookies from 'js-cookie';
import * as bootstrap from 'bootstrap';
import lazysizes from 'lazysizes';
import axios from "axios";
import './modules/resource_filter.jsx';
import './modules/glossary.jsx';
import './modules/resource_form.jsx';

window.Cookies = Cookies;
window.bootstrap = bootstrap;

if(window.jQuery){
    $(document).ready(function(){

        $('input[type="checkbox"]').click(function(e){
            $('#side-submit').show();
        });
    });
}

window.favorite = function (elementId, baseUrl, resourceId) {
    let csrf = $('meta[name="csrf-token"]').attr('content');
    
    $.ajax({
        type: "POST",
        url: baseUrl,
        data: {
            resourceId: resourceId,
            _token: csrf
        },
        success: function(data) {

            if (data.action === "added") {
                $('#' + elementId).addClass("active");
                updateFavoriteCount(data.favorite_count);
            } else if (data.action === "deleted") {
                $('#' + elementId).removeClass("active");
                updateFavoriteCount(data.favorite_count);
            } else if (data.action === "notloggedin") {
                $('#favoriteModal').show();
            }
        },
        error: function(xhr, status, error) {
            console.error("Error occurred: " + error);
        }
    });
}

function updateFavoriteCount(count) {
    $(".resource-favorites").text(count);
}

window.showHide = function (itself, elementId)
{
    var theElement = document.getElementById(elementId);

    if (theElement.style.display === "none") {
        theElement.style.display = "block";
    } else {
        theElement.style.display = "none";
    }

    if (itself.className.indexOf("js-fa-plus") == -1) {  
        itself.className += " js-fa-plus";
    } else { 
        itself.className = itself.className.replace(" js-fa-plus", " fa-minus");
    }
}

function togglePassword(icon='password-toggle-icon', input = 'user-password') {
    let toggleIcon = document.querySelector(`.${icon}`);
    let passwordInput = document.querySelector(`.${input}`);

    if (toggleIcon.classList.contains('fa-eye')) {
        toggleIcon.classList.add("fa-eye-slash");
        toggleIcon.classList.remove("fa-eye");
        passwordInput.setAttribute("type", "password");
    } else {
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
        passwordInput.setAttribute("type", "text");
    }
}

document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-action="resourceFavorite"]');
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
