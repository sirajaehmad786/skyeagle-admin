import { initAjaxFormValidation } from '../common/form-handler.js';

initAjaxFormValidation("#update_blog_tag", {
    name: { required: true },
    status: { required: true },
}, {}, {
    skipRequiredFor: ["name", "status"],
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
