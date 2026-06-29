import { initAjaxFormValidation } from '../common/form-handler.js';

function bindDestinationRepeater() {
    document.addEventListener('click', function (event) {
        const addAttraction = event.target.closest('#addAttraction');
        const addFaq = event.target.closest('#addFaq');
        const remove = event.target.closest('.remove-row');

        if (addAttraction) {
            document.querySelector('#attractionRows').insertAdjacentHTML('beforeend', `
                <div class="input-group mb-2 destination-repeat-row">
                    <input type="text" name="popular_attractions[]" class="form-control" placeholder="Popular attraction">
                    <button type="button" class="btn btn-outline-danger remove-row"><i class="ri-delete-bin-line"></i></button>
                </div>
            `);
        }

        if (addFaq) {
            document.querySelector('#faqRows').insertAdjacentHTML('beforeend', `
                <div class="border rounded p-2 mb-2 destination-repeat-row">
                    <input type="text" name="faq_question[]" class="form-control mb-2" placeholder="Question">
                    <textarea name="faq_answer[]" class="form-control mb-2" rows="2" placeholder="Answer"></textarea>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="ri-delete-bin-line"></i> Remove</button>
                </div>
            `);
        }

        if (remove) {
            const wrap = remove.closest('.destination-repeat-row');
            if (wrap && wrap.parentElement.querySelectorAll('.destination-repeat-row').length > 1) {
                wrap.remove();
            }
        }
    });
}

export function initDestinationForm(selector) {
    bindDestinationRepeater();

    initAjaxFormValidation(selector, {
        name: { required: true },
    }, {}, {
        skipRequiredFor: ['name'],
        beforeSubmit: function () {
            document.querySelector('.btn-save')?.classList.add('d-none');
            document.querySelector('.btn-loading')?.classList.remove('d-none');
        },
        onSuccess: function (res) {
            window.location.href = res.redirect_url;
        },
        onError: function (res) {
            document.querySelector('.btn-save')?.classList.remove('d-none');
            document.querySelector('.btn-loading')?.classList.add('d-none');
            showToastmessage(res.message || 'Something went wrong.', 'error');
        }
    });
}
