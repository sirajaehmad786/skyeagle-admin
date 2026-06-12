import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    clearSavedBlogCommentTableState();

    const columns = [
        { data: 'post', name: 'post' },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'message', name: 'message' },
        { data: 'status', name: 'status' },
        { data: 'approval', name: 'approval', orderable: false, searchable: false },
        { data: 'approved_at', name: 'approved_at' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false },
    ];

    $('.filter-select').select2({ width: '100%', dropdownParent: $('#filter_blog_comment_modal') });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    let table = initDataTable('#blog-comment-table', ajaxUrl, columns, function () {
        return getFilters();
    }, { stateSave: false });

    let typingTimer;
    $('#commonSearch').on('keyup', function () {
        clearTimeout(typingTimer);
        let value = $(this).val();
        typingTimer = setTimeout(function () {
            table.search(value).draw();
        }, 400);
    });

    document.querySelector('#blog-comment-table').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (btn) {
            e.preventDefault();
            confirmDelete(btn.getAttribute('href'), $('#blog-comment-table').DataTable());
        }
    });

    $('#blog-comment-table').on('click', '.comment-approval-btn:not(:disabled)', function () {
        updateApprovalStatus($(this), table);
    });

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_blog_comment_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_blog_comment_modal').find('input').val('');
        $('#filter_blog_comment_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });

    $(document).on('click', '.message-cell', function () {
        $('#messageModal .modal-body').text($(this).data('full'));
        $('#messageModal').modal('show');
    });
});

function clearSavedBlogCommentTableState() {
    try {
        Object.keys(localStorage).forEach((key) => {
            if (key.includes('DataTables_blog-comment-table')) {
                localStorage.removeItem(key);
            }
        });
    } catch (e) {
        return;
    }
}

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

function updateApprovalStatus($button, table) {
    const id = $button.data('id');
    const status = $button.data('status');
    const originalHtml = $button.html();

    $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

    $.ajax({
        url: `${window.blogCommentApprovalUrl}/${id}/approval`,
        type: 'PATCH',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            status: status,
        },
        success: function (res) {
            showToastmessage(res.message || 'Comment approval updated successfully', 'success');
            table.ajax.reload(null, false);
        },
        error: function (xhr) {
            showToastmessage(xhr.responseJSON?.message || 'Comment approval update failed', 'error');
            $button.prop('disabled', false).html(originalHtml);
        }
    });
}
