function appendOptions(selectElement, data) {
  if (!selectElement || !data) return
  selectElement.append(new Option("", ""));
  for (const optionName in data) {
    const optionId = data[optionName]
    selectElement.append(new Option(optionName, optionId))
  }
}

function toggleLoading(isLoading) {
  const cfg = window.resourceFilterConfig
  const btn = document.querySelector('input[type="submit"]')
  const container = document.querySelector('#advanced-filter-container')

  if (!btn || !container) return

  if (isLoading) {
    btn.disabled = true
    btn.value = cfg.loadingText || btn.value
    container.style.opacity = '0.6'
  } else {
    btn.disabled = false
    btn.value = cfg.applyText || btn.value
    container.style.opacity = '1'
  }
}

function getFilterOptions() {
  const cfg = window.resourceFilterConfig
  const language = document.querySelector('#language').value
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

  if (!language || !csrfToken) return

  toggleLoading(true)
  $.ajax({
    type: 'POST',
    url: cfg.updateOptionsUrl,
    dataType: 'json',
    data: { _token: csrfToken, language: language },
    success: function (res) {
      const subjectAreaParent = document.querySelector('#selectSubjectAreaParent');
      const resourceType = document.querySelector('#selectResourceType');
      const literacyLevel = document.querySelector('#selectLiteracyLevel');
      
      document.querySelector('#selectSubjectAreaChild').innerHTML ='';
      subjectAreaParent.innerHTML = '';
      resourceType.innerHTML = '';
      literacyLevel.innerHTML = '';

      appendOptions(subjectAreaParent, res.subjectAreas)
      appendOptions(resourceType, res.resourceTypes)
      appendOptions(literacyLevel, res.literacyLevels)
    },
    error: function () {
      alert(cfg.failedMsg || 'Failed to load filter options. Please try again.')
    },
    complete: function () {
      toggleLoading(false)
    },
  })
}

function getSubjectChildren() {
  const language = document.querySelector('#language').value;
  let selected_values = selectSubjectAreaParent.selectedOptions;
  
  selected_values = Array.from(selected_values).map(({ value }) => value);
  $("#selectSubjectAreaChild option").remove();

  $.ajax({
    type: 'GET',
    url: `filter/subject?IDs=${selected_values}&language=${language}`,
    success: function (res) {

      let option = document.createElement('option');
      option.value = "";
      selectSubjectAreaChild.append(option)
      if (res) {
        $.each(res, function(name, id) {
            let option = document.createElement('option');
            option.innerHTML = name;
            option.value = id;
            selectSubjectAreaChild.append(option)
        });
      }
    },
  })
}

window.getFilterOptions = getFilterOptions
window.getSubjectChildren = getSubjectChildren
