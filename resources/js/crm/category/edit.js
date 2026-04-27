import { initAjaxFormValidation } from '../common/form-handler.js';

initAjaxFormValidation("#update_category_fr", {
    name: { required: true },
}, {}, {
    skipRequiredFor: ["name"],
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
        showToastmessage(message, 'error');
    }
});