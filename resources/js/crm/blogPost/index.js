import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';
import { openInlineSelect } from '../common/inline-floating-select.js';

$(function () {
    const columns = [
        { data: 'title', name: 'title' },
        { data: 'category', name: 'category' },
        { data: 'status', name: 'status' },
        { data: 'is_featured', name: 'is_featured' },
        { data: 'published_at', name: 'published_at' },
        { data: 'views_count', name: 'views_count' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false },
    ];

    $('.filter-select').not('#filter_category_id').select2({ width: '100%', dropdownParent: $('#filter_blog_post_modal') });
    $('#filter_category_id').select2({
        width: '100%',
        dropdownParent: $('#filter_blog_post_modal'),
        ajax: {
            url: window.blogPostCategorySearchUrl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || '',
                    page: params.page || 1,
                    per_page: 20,
                };
            },
            processResults: function (response) {
                return {
                    results: response.data || [],
                    pagination: response.pagination || { more: false },
                };
            },
        },
    });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    let table = initDataTable('#blog-post-table', ajaxUrl, columns, function () {
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

    document.querySelector('#blog-post-table').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (btn) {
            e.preventDefault();
            confirmDelete(btn.getAttribute('href'), $('#blog-post-table').DataTable());
        }
    });

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_blog_post_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_blog_post_modal').find('input').val('');
        $('#filter_blog_post_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });

    bindInlineEditing(table);
});

function getFilters() {
    return {
        category_id: $('#filter_category_id').val(),
        tag_id: $('#filter_tag_id').val(),
        status: $('#filter_status').val(),
        is_featured: $('#filter_is_featured').val(),
        published_from: $('#filter_published_from').val(),
        published_to: $('#filter_published_to').val(),
        created_from: $('#filter_created_from').val(),
        created_to: $('#filter_created_to').val(),
    };
}

function updateFilterIndicator() {
    const hasFilter = Object.values(getFilters()).some(value => value !== '');
    $('#filterIndicator').toggleClass('d-none', !hasFilter);
}

function bindInlineEditing(table) {
    $('#blog-post-table').on('dblclick', '.inline-edit-cell', function () {
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
            $cell.addClass('inline-editing');
            $cell.html($input);
            $input.trigger('focus').select();

            $input.on('keydown', function (e) {
                if (e.key === 'Enter') {
                    submitted = true;
                    saveInline(id, field, $input.val(), table);
                }
                if (e.key === 'Escape') {
                    cancelled = true;
                    $cell.removeClass('inline-editing');
                    $cell.html(oldHtml);
                }
            });
            $input.on('blur', function () {
                if (submitted || cancelled) return;
                if ($input.val() !== oldValue) {
                    saveInline(id, field, $input.val(), table);
                } else {
                    $cell.removeClass('inline-editing');
                    $cell.html(oldHtml);
                }
            });
            return;
        }

        if (type === 'category') {
            openInlineSelect({
                anchor: $cell,
                options: [{ id: '', text: 'Select Category' }],
                selectedValue: oldValue,
                searchable: true,
                placeholder: 'Search category...',
                width: 280,
                pageSize: 20,
                fetchOptions: fetchCategories,
                onSelect: (value) => saveInline(id, field, value, table),
            });
            return;
        }

        openInlineSelect({
            anchor: $cell,
            options: window.blogPostStatuses,
            selectedValue: oldValue,
            searchable: false,
            width: 180,
            onSelect: (value) => saveInline(id, field, value, table),
        });
    });
}

function fetchCategories({ term, page, pageSize }) {
    return $.ajax({
        url: window.blogPostCategorySearchUrl,
        type: 'GET',
        data: {
            q: term,
            page: page,
            per_page: pageSize,
        },
    });
}

function saveInline(id, field, value, table) {
    $.ajax({
        url: `${window.blogPostInlineUrl}/${id}/inline-update`,
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
