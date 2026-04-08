function isolateTableHorizontalScroll(selector, api) {
    const $table = $(selector);
    if (!$table.length) return;

    const settings = api.settings()[0];
    const hasScrollY = !!(settings.oScroll && settings.oScroll.sY);
    const scrollWrapClasses = hasScrollY
        ? 'table-responsive-scroll'
        : 'table-responsive-scroll dt-horizontal-scroll-only';

    // Blade often wraps <table> in .table-responsive-scroll; DataTables inserts #id_wrapper between them.
    // Hoist the whole wrapper out so search/pagination are not inside the horizontal scroll area.
    const $wrapper = $table.closest('.dataTables_wrapper');
    if ($wrapper.length) {
        let $node = $wrapper.parent();
        let $outerScroll = $();
        while ($node.length && $node[0] !== document.body) {
            if ($node.hasClass('table-responsive-scroll')) {
                $outerScroll = $node;
                break;
            }
            $node = $node.parent();
        }
        if ($outerScroll.length) {
            $outerScroll.before($wrapper);
            if ($outerScroll.is(':empty')) {
                $outerScroll.remove();
            }
        }
    }

    const $parent = $table.parent();
    if ($parent.hasClass('table-responsive-scroll') && !hasScrollY) {
        if (!$parent.hasClass('dt-horizontal-scroll-only')) {
            $table.unwrap();
        }
    }

    if (!$table.parent().hasClass('table-responsive-scroll')) {
        $table.wrap(`<div class="${scrollWrapClasses}"></div>`);
    } else if (!hasScrollY && !$table.parent().hasClass('dt-horizontal-scroll-only')) {
        $table.parent().addClass('dt-horizontal-scroll-only');
    }
}

function bindTableDropdownsFixedPopper(selector) {
    if (typeof bootstrap === 'undefined' || !bootstrap.Dropdown) return;
    const dt = $(selector).DataTable();
    const $wrap = $(dt.table().container()).closest('.dataTables_wrapper');
    $wrap.find('[data-bs-toggle="dropdown"]').each(function () {
        const toggle = this;
        const existing = bootstrap.Dropdown.getInstance(toggle);
        if (existing) {
            existing.dispose();
        }
        new bootstrap.Dropdown(toggle, { popperConfig: { strategy: 'fixed' } });
    });
}

export function initDataTable(selector, ajaxUrl, columns, extraData = {}, options = {}) {
    const ajaxDataFn = typeof extraData === 'function' ? extraData : () => extraData;
    const { drawCallback: userDrawCallback, ...restOptions } = options;
    const dataTable = $(selector).DataTable({
        dom: '<"top">rt<"bottom d-flex justify-content-between align-items-center"l p>',
        scrollCollapse: true,
        paging: true,
        processing: true,
        serverSide: true,
        stateSave: true,
        autoWidth: false,
        ajax: {
            url: ajaxUrl,
            data: function (d) {
                return $.extend({}, d, ajaxDataFn());
            }
        },
        columns: columns,
        language: {
            paginate: {
                previous: "<i class='ri-arrow-left-s-line'>",
                next: "<i class='ri-arrow-right-s-line'>"
            }
        },
        drawCallback: function () {
            $('#basic-datatable_paginate').addClass('pagination-rounded');
            if (typeof userDrawCallback === 'function') {
                userDrawCallback.apply(this, arguments);
            }
            bindTableDropdownsFixedPopper(selector);
        },
        ...restOptions
    });

    isolateTableHorizontalScroll(selector, dataTable);
    return dataTable;
}
