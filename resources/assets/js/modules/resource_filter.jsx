import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';
import axios from 'axios';

if (document.getElementById('filterPanel')) {

  document.addEventListener('DOMContentLoaded', () => {
    ['selectSubjectAreaParent', 'selectSubjectAreaChild', 'selectResourceType', 'selectLiteracyLevel'].forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;
      new TomSelect(el, {
        plugins: ['remove_button'],
        placeholder: '...',
      });
    });
  });

  function formatOptionLabel(label) {
    const name = String(label || '').toLowerCase();
    return name.charAt(0).toUpperCase() + name.slice(1);
  }

  function appendOptions(selectId, data) {
    const el = document.getElementById(selectId);
    if (!el || !data) return;
    const ts = el.tomselect;
    if (!ts) return;
    ts.clearOptions();
    ts.clearCache();
    Object.entries(data).forEach(([name, id]) => {
      ts.addOption({ value: String(id), text: formatOptionLabel(name) });
    });
    ts.refreshOptions(false);
  }

  function toggleLoading(isLoading) {
    const cfg = window.resourceFilterConfig;
    const btn = document.querySelector('#filterPanel button[type="submit"]');
    const container = document.getElementById('filterPanel');

    if (!btn || !container) return;

    if (isLoading) {
      btn.disabled = true;
      btn.textContent = cfg.loadingText || btn.textContent;
      container.classList.add('opacity-50');
    } else {
      btn.disabled = false;
      btn.textContent = cfg.applyText || btn.textContent;
      container.classList.remove('opacity-50');
    }
  }

  async function getFilterOptions() {
    const cfg = window.resourceFilterConfig;
    const language = document.getElementById('language')?.value;
    if (!language) return;

    toggleLoading(true);

    try {
      const response = await axios.post(cfg.updateOptionsUrl, { language });

      appendOptions('selectSubjectAreaParent', response.data.subjectAreas);
      appendOptions('selectResourceType', response.data.resourceTypes);
      appendOptions('selectLiteracyLevel', response.data.literacyLevels);

      const childEl = document.getElementById('selectSubjectAreaChild');
      if (childEl?.tomselect) {
        childEl.tomselect.clearOptions();
        childEl.tomselect.clearCache();
        childEl.tomselect.refreshOptions(false);
      }
    } catch (error) {
      const cfg = window.resourceFilterConfig;
      alert(cfg.failedMsg || 'Failed to load filter options. Please try again.');
    } finally {
      toggleLoading(false);
    }
  }

  async function getSubjectChildren() {
    const language = document.getElementById('language')?.value;
    const parentSelect = document.getElementById('selectSubjectAreaParent');
    if (!parentSelect) return;

    const selectedIds = Array.from(parentSelect.tomselect?.items ?? []).join(',');
    if (!selectedIds) return;

    try {
      const response = await axios.get(`${baseUrl}/resources/filter/subject`, {
        params: { IDs: selectedIds, language }
      });
      appendOptions('selectSubjectAreaChild', response.data);
    } catch (error) {
      console.error('Error loading subject children:', error);
    }
  }

  document.addEventListener('change', (e) => {
    if (e.target.closest('#language')) getFilterOptions();
    if (e.target.closest('#selectSubjectAreaParent')) getSubjectChildren();
  });
}
