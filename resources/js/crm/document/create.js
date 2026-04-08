import { initAjaxFormValidation } from '../common/form-handler.js';

initAjaxFormValidation("#create_document",
    {
        contact_id: { required: true },
        "documents[]": { required: true }
    },
    {
        contact_id: { required: "" },
        "documents[]": { required: "" }
    },
    {
        onSuccess: function (res) {
            window.location.href = res.redirect_url;
        },
        onError: function (res) {
            showToastmessage(res.message, 'error');
        }
    }
);