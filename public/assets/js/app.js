document.addEventListener('DOMContentLoaded', function() {
    console.log('ZEngine Framework Loaded');

    const csrfToken = document.querySelector('input[name="_token"]');
    if (csrfToken) {
        window.csrfToken = csrfToken.value;
    }

    const forms = document.querySelectorAll('form[data-ajax="true"]');
    forms.forEach(form => {
        form.addEventListener('submit', handleAjaxSubmit);
    });
});

async function handleAjaxSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const method = form.getAttribute('method') || 'POST';
    const action = form.getAttribute('action');

    try {
        const response = await fetch(action, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        console.log('Response:', data);

        if (response.ok) {
            form.dispatchEvent(new CustomEvent('ajax:success', { detail: data }));
        } else {
            form.dispatchEvent(new CustomEvent('ajax:error', { detail: data }));
        }
    } catch (error) {
        console.error('Error:', error);
        form.dispatchEvent(new CustomEvent('ajax:error', { detail: error }));
    }
}

window.ZEngine = {
    version: '1.0.0',

    async fetch(url, options = {}) {
        const defaults = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (window.csrfToken) {
            defaults.headers['X-CSRF-Token'] = window.csrfToken;
        }

        const config = Object.assign({}, defaults, options);

        try {
            const response = await fetch(url, config);
            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            throw error;
        }
    }
};
