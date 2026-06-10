import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';
import { openInlineSelect } from '../common/inline-floating-select.js';

$(function () {
    const columns = [
        { data: 'name', name: 'name' },
        { data: 'status', name: 'status' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false },
    ];

    $('.filter-select').select2({ width: '100%', dropdownParent: $('#filter_blog_tag_modal') });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    let table = initDataTable('#blog-tag-table', ajaxUrl, columns, function () {
        return getFilters();
    });

    let typingTimer;
    $('#commonSearch').on('keyup', function () {
        clearTimeout(typingTimer);
        let value = $(this).val();
        typingTimer = setTimeout(function () {
            table.search(value).draw();
        }, 400);
    });

    document.querySelector('#blog-tag-table').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (btn) {
            e.preventDefault();
            confirmDelete(btn.getAttribute('href'), $('#blog-tag-table').DataTable());
        }
    });

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_blog_tag_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_blog_tag_modal').find('input').val('');
        $('#filter_blog_tag_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });

    bindInlineEditing(table);
});

function getFilters() {
    return {
        status: $('#filter_status').val(),
        created_from: $('#filter_created_from').val(),
        created_to: $('#filter_created_to').val(),
    };
}

function updateFilterIndicator() {
    const hasFilter = Object.values(getFilters()).some(value => value !== '');
    $('#filterIndicator').toggleClass('d-none', !hasFilter);
}

function bindInlineEditing(table) {
    $('#blog-tag-table').on('dblclick', '.inline-edit-cell', function () {
        const $cell = $(this);
        if ($cell.find('.inline-edit-control').length) return;

        const id = $cell.data('id');
        const field = $cell.data('field');
        const type = $cell.data('type');
        const oldValue = String($cell.data('value') ?? '');
        const oldHtml = $cell.html();

        if (type === 'text') {
            const $input = $(`<input type="text" class="form-control form-control-sm inline-edit-control">`).val(oldValue);
            let submitted = false;
            let cancelled = false;
            $cell.html($input);
            $input.trigger('focus').select();

            $input.on('keydown', function (e) {
                if (e.key === 'Enter') {
                    submitted = true;
                    saveInline(id, field, $input.val(), table);
                }
                if (e.key === 'Escape') {
                    cancelled = true;
                    $cell.html(oldHtml);
                }
            });
            $input.on('blur', function () {
                if (submitted || cancelled) return;
                if ($input.val() !== oldValue) {
                    saveInline(id, field, $input.val(), table);
                } else {
                    $cell.html(oldHtml);
                }
            });
            return;
        }

        openInlineSelect({
            anchor: $cell,
            options: window.blogTagStatuses,
            selectedValue: oldValue,
            searchable: false,
            width: 180,
            onSelect: (value) => saveInline(id, field, value, table),
        });
    });
}

function saveInline(id, field, value, table) {
    $.ajax({
        url: `${window.blogTagInlineUrl}/${id}/inline-update`,
        type: 'PATCH',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            field: field,
            value: value,
        },
        success: function (res) {
            showToastmessage(res.message || 'Updated successfully', 'success');
            table.ajax.reload(null, false);
        },
        error: function (xhr) {
            showToastmessage(xhr.responseJSON?.message || 'Update failed', 'error');
            table.ajax.reload(null, false);
        }
    });
}
