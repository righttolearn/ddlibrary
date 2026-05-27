import $ from 'jquery';
import 'jquery-ui/ui/widgets/autocomplete';
import './image_manager.jsx';

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
    document.querySelectorAll('[data-action="autocomplete"]').forEach(input => {
        bringMeAttr(input.id, input.dataset.url);
    });

    // Translation toggle
    document.addEventListener('change', (e) => {
        const toggle = e.target.closest('[data-action="toggle-translation"]');
        if (!toggle) return;
        document.querySelector('.translation').classList.toggle('d-none', !toggle.checked);
    });
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

document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el);
});

window.toggleTranslation = toggleTranslation;
