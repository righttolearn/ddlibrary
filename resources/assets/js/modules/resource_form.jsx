import $ from 'jquery';
import 'jquery-ui/ui/widgets/autocomplete';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';

window.bringMeAttr = function (id, url)
{
    $( "#"+id )
        // don't navigate away from the field on tab when selecting an item
        .on( "keydown", function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).autocomplete( "instance" ).menu.active ) {
                event.preventDefault();
            }
        })
        .autocomplete({
            source: function( request, response ) {
                $.getJSON( url, {
                    term: extractLast( request.term )
                }, response );
            },
            search: function() {
                // custom minLength
                var term = extractLast( this.value );
                if ( term.length < 2 ) {
                    return false;
                }
            },
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            select: function( event, ui ) {
                var terms = split( this.value );
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push( ui.item.value );
                // add placeholder to get the comma-and-space at the end
                terms.push( "" );
                this.value = terms.join( ", " );
                return false;
            }
        });
}

function split( val ) {
    return val.split( /,\s*/ );
}
function extractLast( term ) {
    return split( term ).pop();
}

document.addEventListener('DOMContentLoaded', () => {

});

function toggleTranslation(checkbox) {
    const translation = document.querySelector('.translation');
    const translator = document.getElementById('translator');

    translation.classList.toggle('d-none', !checkbox.checked);
    translator.required = checkbox.checked;

    if (!checkbox.checked) {
        translator.value = '';
    }
}

// Remove attachment (add a new resource)
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.remove-attachment');
    if (!btn) return;
    if (!confirm(btn.dataset.confirm)) return;
    document.querySelector(`.${btn.dataset.target}`)?.remove();
});

// Add more attachments
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.add_more');
    if (!btn) return;
    e.preventDefault();
    const randomNumber = Math.ceil(Math.random() * 1000);
    const div = document.createElement('div');
    div.className = `d-flex gap-3 mb-2 attachment-${randomNumber}`;
    div.innerHTML = `
        <div class="flex-grow-1">
            <input class="form-control" name="attachments[]" type="file">
        </div>
        <div class="align-self-center">
            <button type="button" class="btn btn-link p-0 text-danger remove-attachment" data-target="attachment-${randomNumber}">
                <i class="ph-light ph-trash"></i>
            </button>
        </div>
    `;
    btn.before(div);
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-action="autocomplete"]').forEach(input => {
        bringMeAttr(input.id, input.dataset.url);
    });

    // Translation toggle
    document.addEventListener('change', (e) => {
        const toggle = e.target.closest('[data-action="toggle-translation"]');
        if (!toggle) return;
        document.querySelector('.translation').classList.toggle('d-none', !toggle.checked);
    });

    ['learning_resources_types', 'educational_use', 'subject_areas'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        new TomSelect(el, {
            plugins: ['remove_button'],
            placeholder: '...',
        });
    });
});

document.addEventListener('change', (e) => {
    const checkbox = e.target.closest('[data-action="level-toggle"]');
    if (!checkbox) return;

    const target = document.getElementById(checkbox.dataset.target);
    if (!target) return;

    target.querySelectorAll('.js-child').forEach(child => {
        child.checked = checkbox.checked;
    });
});

document.addEventListener('click', (e) => {
    const link = e.target.closest('[data-action="confirm-delete"]');
    if (!link) return;
    if (!confirm(link.dataset.confirm)) e.preventDefault();
});

document.addEventListener('change', (e) => {
    if (e.target.closest('[data-action="cc-select"]')) {
        document.querySelectorAll('[name="creative_commons_other"]')
            .forEach(el => el.disabled = true);
    }
    if (e.target.closest('[data-action="cc-other-select"]')) {
        document.querySelectorAll('[name="creative_commons"]')
            .forEach(el => el.disabled = true);
    }
});

document.addEventListener('change', (e) => {
    if (e.target.closest('[data-action="cc-select"]')) {
        document.querySelectorAll('[name="creative_commons_other"]')
            .forEach(el => el.disabled = true);
        const btn = document.querySelector('[data-action="cc-reset"][data-target="creative_commons"]');
        if (btn) btn.style.display = 'inline';
    }
    if (e.target.closest('[data-action="cc-other-select"]')) {
        document.querySelectorAll('[name="creative_commons"]')
            .forEach(el => el.disabled = true);
        const btn = document.querySelector('[data-action="cc-reset"][data-target="creative_commons_other"]');
        if (btn) btn.style.display = 'inline';
    }
});

document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-action="cc-reset"]');
    if (!btn) return;

    const target = btn.dataset.target;
    const opposite = target === 'creative_commons' ? 'creative_commons_other' : 'creative_commons';

    document.querySelectorAll(`[name="${target}"]`).forEach(el => {
        el.checked = false;
        el.disabled = false;
    });
    document.querySelectorAll(`[name="${opposite}"]`)
        .forEach(el => el.disabled = false);

    document.querySelectorAll('[data-action="cc-reset"]')
        .forEach(el => el.style.display = 'none');
});

window.toggleTranslation = toggleTranslation;
