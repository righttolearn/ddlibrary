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
        $('.add_more').click(function(e){
            let randomNumber = Math.ceil(Math.random() * 1000)
            e.preventDefault();
            $(this).before(`
                <div class="d-flex gap-3 attachment-${randomNumber}">
                    <div class="flex-grow-1 align-items-center">
                        <input id="resource-file-${randomNumber}"
                            class="form-control col-md-6"
                            id="attachments" name="attachments[]" type="file">
                    </div>
                    <div class="align-self-center">
                        <span class="fa fa-trash text-danger" onclick="removeAttachment('attachment-${randomNumber}')"></span>
                    </div>
                </div>
            `);
        });

        $('input[type="checkbox"]').click(function(e){
            $('#side-submit').show();
        });

        window.addEventListener('load', function() {
            (function() {
                const subjectAreasSelect = document.getElementById('subject_areas');
                const subjectAreasSearch = document.getElementById('subject_areas_search');
                const subjectAreasWrapper = document.getElementById('subject_areas_wrapper');

                if (!subjectAreasSelect || !subjectAreasSearch) return;

                // Show search box when select is focused/clicked
                subjectAreasSelect.addEventListener('focus', function() {
                    subjectAreasSearch.style.display = 'block';
                    setTimeout(function() {
                        subjectAreasSearch.focus();
                    }, 0);
                });

                subjectAreasSelect.addEventListener('click', function() {
                    subjectAreasSearch.style.display = 'block';
                    setTimeout(function() {
                        subjectAreasSearch.focus();
                    }, 0);
                });

                // Store all original options
                const originalOptions = [];
                const optgroups = subjectAreasSelect.querySelectorAll('optgroup');

                optgroups.forEach(function(optgroup) {
                    const groupLabel = optgroup.getAttribute('label');
                    const options = Array.from(optgroup.querySelectorAll('option'));
                    originalOptions.push({
                        optgroup: optgroup,
                        label: groupLabel,
                        options: options.map(function(opt) {
                            return {
                                element: opt,
                                value: opt.value,
                                text: opt.textContent.trim()
                            };
                        })
                    });
                });

                // Filter function
                function filterOptions(searchTerm) {
                    const searchLower = searchTerm.toLowerCase().trim();

                    originalOptions.forEach(function(group) {
                        let groupMatches = false;
                        let hasVisibleOptions = false;

                        // Check if group label matches
                        if (group.label.toLowerCase().includes(searchLower)) {
                            groupMatches = true;
                        }

                        // Filter options within the group
                        group.options.forEach(function(option) {
                            const optionMatches = option.text.toLowerCase().includes(searchLower);

                            if (searchTerm === '' || groupMatches || optionMatches) {
                                option.element.style.display = '';
                                hasVisibleOptions = true;
                            } else {
                                option.element.style.display = 'none';
                            }
                        });

                        // Show/hide optgroup based on matches
                        if (searchTerm === '' || hasVisibleOptions) {
                            group.optgroup.style.display = '';
                        } else {
                            group.optgroup.style.display = 'none';
                        }
                    });
                }

                // Handle search input
                subjectAreasSearch.addEventListener('input', function(e) {
                    filterOptions(e.target.value);
                });

                // Handle keyboard navigation
                subjectAreasSearch.addEventListener('keydown', function(e) {
                    // Allow arrow keys to pass through to select
                    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                        e.preventDefault();
                        subjectAreasSelect.focus();
                        // Trigger arrow key on select
                        const event = new KeyboardEvent('keydown', {
                            key: e.key,
                            bubbles: true
                        });
                        subjectAreasSelect.dispatchEvent(event);
                    }
                });

                // Hide search box when clicking outside
                document.addEventListener('click', function(e) {
                    if (!subjectAreasWrapper.contains(e.target)) {
                        subjectAreasSearch.style.display = 'none';
                        subjectAreasSearch.value = '';
                        filterOptions('');
                    }
                });

                // Keep search box visible when interacting with select
                subjectAreasSelect.addEventListener('mousedown', function() {
                    subjectAreasSearch.style.display = 'block';
                    setTimeout(function() {
                        subjectAreasSearch.focus();
                    }, 0);
                });
            })();
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

window.fnTest = function (check, cchild){
    if($(check).is(':checked')){
        $(check).siblings('#'. cchild).find('.js-child').prop("checked",true);
    }else{
        $(check).siblings('#'.cchild).find('.js-child').prop("checked",false);        
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
