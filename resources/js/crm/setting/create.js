import Quill from "quill/dist/quill.min.js";

function initQuillEditor(editorId, hiddenId, placeholder) {
    const editorEl = document.getElementById(editorId);
    if (!editorEl) return null;
    const quill = new Quill(editorEl, {
        theme: "snow",
        placeholder: placeholder,
    });
    const existingContent = editorEl.dataset.content;
    if (existingContent) {
        quill.root.innerHTML = existingContent;
    }
    return quill;
}

function getEditorContent(quill) {
    if (!quill) return "";
    let content = quill.root.innerHTML;
    if (content === "<p><br></p>") content = "";
    return content;
}

$(document).ready(function () {
    const descriptionQuill = initQuillEditor("description_editor", "description_hidden", "Write Terms & Conditions...");
    const visaPolicyQuill = initQuillEditor("visa_policy_editor", "visa_policy_hidden", "Write Visa Policy...");
    const paymentPolicyQuill = initQuillEditor("payment_policy_editor", "payment_policy_hidden", "Write Payment Policy...");

    $("#save_terms_condition_fr").on("submit", function (e) {
        e.preventDefault();

        $("#description_hidden").val(getEditorContent(descriptionQuill));
        $("#visa_policy_hidden").val(getEditorContent(visaPolicyQuill));
        $("#payment_policy_hidden").val(getEditorContent(paymentPolicyQuill));

        let form = $(this);

        $.ajax({
            url: form.attr("action"),
            method: "POST",
            data: form.serialize(),

            beforeSend: function () {
                $(".btn-save").hide();
                $(".btn-loading").show();
            },

            success: function (res) {
                $(".btn-save").show();
                $(".btn-loading").hide();
                showToastmessage("Settings saved successfully!", "success");
            },

            error: function () {
                $(".btn-save").show();
                $(".btn-loading").hide();
                showToastmessage("Something went wrong", "error");
            }
        });
    });
});
