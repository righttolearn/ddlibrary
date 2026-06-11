import axios from "axios";

document.addEventListener('DOMContentLoaded', () => {
    const successMsg = document.getElementById('success_msg');
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

    const showMessage = (msg) => {
        successMsg.textContent = msg;
        successMsg.classList.remove('d-none');
        setTimeout(() => successMsg.classList.add('d-none'), 5000);
    };

    // Inline edit
    document.querySelectorAll('td[contenteditable="true"]').forEach(cell => {
        let originalValue = '';

        cell.addEventListener('focus', () => {
            originalValue = cell.innerHTML;
        });

        cell.addEventListener('keydown', async (e) => {
            if (e.key === 'Escape') {
                cell.innerHTML = originalValue;
                cell.blur();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                const string = cell.innerHTML
                    .split('\n')
                    .map(v => v.trim())
                    .filter(v => v !== '')
                    .join('\n');

                try {
                    await axios.post(`/glossary/update`, {
                        data: [
                            cell.dataset.id,
                            cell.dataset.type,
                            cell.dataset.language,
                            string
                        ]
                    });
                    showMessage('Updated successfully!');
                } catch {
                    console.error('Request to update glossary item failed.');
                }
                cell.blur();
            }
        });
    });

    // Delete
    document.querySelectorAll('.glossary_delete').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Are you sure you would like to delete the glossary item?')) return;
            try {
                await axios.post(`/glossary/delete/${btn.dataset.id}`);
                showMessage('Item deleted successfully! Page will reload now.');
                setTimeout(() => location.reload(), 2000);
            } catch {
                console.error('Request to delete glossary item failed.');
            }
        });
    });

    // Approve
    document.querySelectorAll('.glossary_approve').forEach(btn => {
        btn.addEventListener('click', async () => {
            try {
                await axios.post(`/glossary/approve/${btn.dataset.id}`);
                showMessage('Item approved! Page will reload now.');
                setTimeout(() => location.reload(), 2000);
            } catch {
                console.error('Request to approve glossary item failed.');
            }
        });
    });

    // Auto-hide session status
    const addSuccess = document.getElementById('add_success');
    if (addSuccess) {
        setTimeout(() => addSuccess.classList.add('d-none'), 5000);
    }
});
