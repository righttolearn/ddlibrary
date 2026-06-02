document.addEventListener('change', (e) => {
    const select = e.target.closest('[data-action="populate-city"]');
    if (!select) return;

    const provinces = JSON.parse(select.dataset.provinces);
    const selectedOption = select.value;
    const citySelect = document.getElementById('city');
    const textInput = document.getElementById('js-text-city');

    if (parseInt(selectedOption) === 256) {
        textInput.classList.add('d-none');
        citySelect.innerHTML = '<option value=""></option>';
        provinces.forEach(province => {
            citySelect.append(new Option(province.name, province.tnid));
        });
        citySelect.classList.remove('d-none');
    } else {
        citySelect.classList.add('d-none');
        textInput.classList.remove('d-none');
    }
});
