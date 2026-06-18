import { initAjaxFormValidation } from '../common/form-handler.js';

$('.select2:not(.type-select2)').select2({ width: '100%' });
$('.type-select2').select2({
    width: '100%',
    tags: true,
    createTag: function (params) {
        const term = params.term.trim();

        if (!term) {
            return null;
        }

        return {
            id: term,
            text: term,
            newTag: true
        };
    }
});

initAjaxFormValidation("#create_package_attribute", {
    type: { required: true },
    name: { required: true },
    status: { required: true },
}, {}, {
    skipRequiredFor: ["type", "name", "status"],
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
        showToastmessage(res.message, 'error');
    }
});
