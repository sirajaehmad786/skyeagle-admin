import Swal from "sweetalert2";

export function initAjaxFormValidation(formSelector, rules, messages, extraOptions = {}, arrField = [], rowSelector = "", subrowSelector = "  ") {
    const $form = $(formSelector);
    // Initialize validation and capture the validator instance
    const validator = $form.validate({
        ignore: [],
        rules: rules,
        messages: messages,
        errorClass: "is-invalid",
        validClass: "is-valid",
        errorElement: "div",
        errorPlacement: function(error, element) {
            const skipRequiredFor = extraOptions.skipRequiredFor || [];

            if ((skipRequiredFor.includes(element.attr("name")) || element.attr("name").includes("[]")) && error.text().includes("required")) {
                return;
            }
            error.addClass('invalid-feedback');
            if (element.attr('name') === 'password') {
                error.insertAfter(element.closest('.input-group'));
                return;
            } 
            if (element.hasClass("select2-hidden-accessible")) {
                // Place error after select2 container
                error.insertAfter(element.next('.select2'));
                return;
            }
            error.insertAfter(element);
        },
        highlight: function(element, errorClass, validClass) {
            const name = $(element).attr('name');
            const $el = $(element);
            
            if (this.settings.rules[name]) {
                if ($el.hasClass("select2-hidden-accessible")) {
                    $el.next('.select2').find('.select2-selection').addClass(errorClass).removeClass(validClass);
                    return;
                }
                $(element).addClass(errorClass).removeClass(validClass);
            }

            // for editor
            if ($el.hasClass("editor-hidden-field")) {
                
                const $editorWrap = $el.prev('.snow-editor-cls');
                $editorWrap.find('.ql-editor').addClass(errorClass).removeClass(validClass);
                
                $el.removeClass(errorClass).removeClass(validClass);
                return;
            }
        },
        unhighlight: function(element, errorClass, validClass) {
            const name = $(element).attr('name');
            const $el = $(element);
            if (this.settings.rules[name]) {
                if ($el.hasClass("select2-hidden-accessible")) {
                    $el.next('.select2').find('.select2-selection').addClass(validClass).removeClass(errorClass);
                    return;
                }
                $(element).removeClass(errorClass).addClass(validClass);
            }
            
            // for editor
            if ($el.hasClass("editor-hidden-field")) {
                const $editorWrap = $el.prev('.snow-editor-cls');
                $editorWrap.find('.ql-editor').removeClass(errorClass).addClass(validClass);
                $el.removeClass(errorClass).removeClass(validClass);
                return;
            }
        },
        // submitHandler can call your existing AJAX logic
        submitHandler: function(form) {
            let $form = $(form);
            let submitBtn = $form.find('button[type="submit"]');
            // Run beforeSubmit first so Quill editors (e.g. inclusion/exclusion) sync to hidden inputs
            if (extraOptions.beforeSubmit) {
                if (extraOptions.beforeSubmit($form, null, submitBtn) === false) {
                    return;
                }
            }
            let formData = new FormData(form);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            // Remove old server errors
            $form.find(".is-invalid").removeClass("is-invalid");
            $form.find(".invalid-feedback").remove();

            var editor = [];
            $('.snow-editor-cls').each(function(i, el){
                // Skip inclusion/exclusion editors – they use name="inclusion" and name="exclusion", not description[]
                let $el = $(el);
                if ($el.attr('id') === 'inclusion_editor' || $el.attr('id') === 'exclusion_editor') {
                    return;
                }
                var html = $el.find(".ql-editor").html();
                var jsonEditor = JSON.stringify(html).slice(1, -1);
                let editorEncode = btoa(jsonEditor);
                editor.push(editorEncode);
            });
            if (editor.length) {
                formData.append("description[]", editor);
            }
            
            $('.btn-save').hide();
            $('.btn-loading').show();

            $.ajax({
                url: $form.attr("action"),
                type: $form.attr("method"),
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    submitBtn.prop("disabled", true);
                },
                success: function(response) {
                    if (response.status) {
                        $($form).find(".is-valid, .is-invalid").removeClass("is-valid is-invalid");

                        if (extraOptions.onSuccess) {
                            extraOptions.onSuccess(response);
                        } else {
                            window.location.href = response.redirect;
                        }
                    } else {
                        
                        if (extraOptions.onError) {
                            extraOptions.onError(response);
                        } else {
                            showToastmessage(response.message, 'error');
                        }
                    }
                    $('.btn-save').show().prop("disabled", false);
                    $('.btn-loading').hide();
                },
                error: function(xhr) {
                    submitBtn.prop("disabled", false);
                    $('.btn-save').show().prop("disabled", false);
                    $('.btn-loading').hide();

                    let message = 'Something went wrong. Please try again.';

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            let input = $form.find('[name="' + field + '"]');
                            input.addClass("is-invalid");

                            if (field === "password" && input.closest(".input-group").length) {
                                input.closest(".input-group")
                                    .after('<div class="invalid-feedback d-block">' + messages[0] + '</div>');
                            } else if (field.includes('.')) {
                                let fieldName = field.replace(/\.\d+$/, "[]");
                                let indexedInput = $(`[name="${fieldName}"]`).eq(field.match(/\d+/)?.[0] || 0);
                                if (indexedInput.hasClass("select2")) {
                                    indexedInput.next('.select2-container')
                                        .after('<div class="invalid-feedback d-block">' + messages[0] + '</div>');
                                }
                            } else {
                                input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                            }
                        });

                        const firstError = Object.values(errors)[0];
                        message = Array.isArray(firstError) ? firstError[0] : firstError;
                    } else if (xhr.status === 413) {
                        message = 'Upload failed: request data is too large for the server. Please increase PHP post_max_size / upload_max_filesize.';
                    } else if (xhr.status === 0) {
                        message = 'Network error or upload failed. Please check server upload limits and try again.';
                    } else if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.statusText && xhr.statusText !== 'error') {
                        message = xhr.statusText;
                    }

                    if (extraOptions.onError) {
                        extraOptions.onError({ status: false, message: message });
                    } else {
                        showToastmessage(message, 'error');
                    }
                },
                complete: function() {
                    if (extraOptions.onComplete) {
                        extraOptions.onComplete($form);
                    }
                }
            });
        }
    });

    $('.snow-editor-cls').each(function(index) {
        let editorName = `sub_description[${index}][${index}]`;

        // Add rule to hidden input
        $(`input[name="${editorName}"]`).rules("add", {
            editorRequired: `.snow-editor-cls:eq(${index})`,
            messages: {
                editorRequired: ""
            }
        });
    });

    /* if(subrowSelector!='' && rowSelector!=''){

        applyRulesToIndexedFields($form,arrField);
    } */
    if ((Array.isArray(arrField) && arrField.length>0) || (!Array.isArray(arrField) && typeof arrField === 'object' && arrField !== null)) {
        reindexRows($form,rowSelector);
        applyRulesToIndexedFields($form,arrField);
    }
}

$.validator.addMethod("editorRequired", function(value, element, selector) {
    let html = $(selector).find(".ql-editor").html();
    let text = $(selector).find(".ql-editor").text().trim();

    // Valid if editor has any text or images
    return text.length > 0 || html.includes("<img");
}, "");

// remove rule
export function removeRulesValidate($form){
    $form.find('[name]').each(function() {
        try { $(this).rules('remove'); } catch (e) {}
    });
}

export function reindexParentmultipleRows($form,rowSelector,subrowSelector, Remove = false) {
    var count = $('.multi-sight').length;
    if(Remove){
        count =  count - (1);
    }
    let r = 0;
    $form.find(rowSelector).each(function(rowIndex) {
        $(this).find('[name]').each(function() {
            const $el = $(this);
            
            const name = $el.attr('name');
            const bracketCount = (name.match(/\[/g) || []).length;

            if (!name) return;

            // Remove any [] or [x][y] at the end to normalize the base name
            const base = name.replace(/\[\d*\]\[\d*\]$|\[\]$/g, '').replace(/\[\d+\]$/g, '');
            
            if(bracketCount===1){
                $el.attr('name', `${base}[${rowIndex}]`);
            }
        });
        
        $(this).find(subrowSelector).each(function() {
            
            $(this).find('[name]').each(function() {
                const $el = $(this);
                const name = $el.attr('name');
                if (!name) return;

                const bracketCount = (name.match(/\[/g) || []).length;
                
                // Remove any [] or [x][y] at the end to normalize the base name
                const base = name.replace(/\[\d*\]\[\d*\]$|\[\]$/g, '').replace(/\[\d+\]$/g, '');

                if(bracketCount===2){
                    let i = 0;
                    let rowSubIndex = rowIndex;
                    let rowNameIndex = rowIndex;
                    if(Remove && r==count){
                        rowSubIndex = count;
                        rowNameIndex = rowSubIndex + (1);
                    }

                    $('input[name^="'+base+'['+rowNameIndex+']"]').each(function(){
                        const $el = $(this);
                        $el.attr('name', `${base}[${rowSubIndex}][${i}]`);
                        i++;
                    })
                }
                
            });
        });
        r++;
    });
}

export function reindexRows($form,rowSelector) {
    //const rowSelector = '.multi-row-hotel';

    $form.find(rowSelector).each(function(rowIndex) {
        $(this).find('[name]').each(function() {
            const $el = $(this);
            const name = $el.attr('name');
            if (!name) return;

            if (/\[\]$/.test(name)) {
                const base = name.replace(/\[\]$/, '');
                $el.attr('name', `${base}[${rowIndex}]`);
            } else if (/\[\d+\]$/.test(name)) {
                const base = name.replace(/\[\d+\]$/, '');
                $el.attr('name', `${base}[${rowIndex}]`);
            }
        });
    });
}

export function applyRulesToIndexedFields($form,requiredFields) {
    // remove previous rules (safe)
    /* $form.find('[name]').each(function() {
        try { $(this).rules('remove'); } catch (e) {}
    }); */
    if(Array.isArray(requiredFields) && requiredFields.length>0){
        // add rules for each field
        requiredFields.forEach(base => {
            $form.find(`[name^="${base}["]`).each(function() {
                if ($form.data('validator')) {
                    $(this).rules('add', {
                        required: true,
                        messages: {required:''}
                    });
                }
            });
        });
    }
    
    // 2D array field name validation
    if(!Array.isArray(requiredFields) && typeof requiredFields === 'object' && requiredFields !== null){
        Object.keys(requiredFields).forEach(key => {
            if (Array.isArray(requiredFields[key])) {
                requiredFields[key].forEach(value => {
                    $form.find(`[name^="${key}["][name$="[${value}]"]`).each(function () {
                    $(this).rules('add', {
                            required: true,
                            messages: { required: "" }
                        });
                    });
                });
            }
        });
    }
}

export function closeAndResetModal(modalId) {
    $(modalId).modal('hide');
    $(modalId).find('form')[0].reset();
    $(modalId).find('.is-invalid').removeClass('is-invalid');
    $(modalId).find('.invalid-feedback').remove();
}

export function confirmDelete(url, table = null) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This record will be permanently deleted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content') // safer than hardcoding
                },
                success: function(response) {
                    if (table) {
                        if(response.status){
                            Swal.fire('Deleted!', response.message, 'success');    
                        }else{
                            Swal.fire('Error!',response.message,'error');
                        }                        
                        table.ajax.reload();
                    }else{
                        location.reload();
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });
        }
    });
}
