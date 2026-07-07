import Quill from "quill/dist/quill.min.js";
import { initAjaxFormValidation } from "../common/form-handler.js";

const toolbarOptions = [
    [{ size: [] }],
    ["bold", "italic", "underline", "strike"],
    ["blockquote", "code-block"],
    [{ list: "ordered" }, { list: "bullet" }],
    ["link"],
];

function initContentEditor(editorEl) {
    const quill = new Quill(editorEl, {
        theme: "snow",
        modules: {
            toolbar: toolbarOptions,
        },
    });

    if (editorEl.dataset.content) {
        quill.root.innerHTML = editorEl.dataset.content;
    }

    return quill;
}

function editorContent(quill) {
    const html = quill.root.innerHTML;
    const text = quill.getText().trim();

    return text.length || html.includes("<img") ? html : "";
}

document.addEventListener("DOMContentLoaded", function () {
    const editors = {};

    document.querySelectorAll(".content-page-editor").forEach((editorEl) => {
        const slug = editorEl.id.replace("_editor", "");
        const hidden = document.getElementById(`${slug}_content`);
        const quill = initContentEditor(editorEl);

        editors[slug] = { quill, hidden };

        quill.on("text-change", function () {
            hidden.value = editorContent(quill);
        });
    });

    initAjaxFormValidation("#content_pages_form", {
        "pages[privacy-policy][title]": { required: true },
        "pages[terms-conditions][title]": { required: true },
        "pages[refund-policy][title]": { required: true },
    }, {}, {
        beforeSubmit: function () {
            Object.values(editors).forEach(({ quill, hidden }) => {
                hidden.value = editorContent(quill);
            });
        },
        onSuccess: function (res) {
            showToastmessage(res.message || "Page settings saved successfully.", "success");

            Object.entries(editors).forEach(([slug, editor]) => {
                const savedPage = res.pages?.[slug];
                if (!savedPage) return;

                editor.hidden.value = savedPage.content || "";
                editor.hidden.previousElementSibling.dataset.content = savedPage.content || "";
            });
        },
        onError: function (res) {
            showToastmessage(res.message || "Something went wrong.", "error");
        }
    });
});
