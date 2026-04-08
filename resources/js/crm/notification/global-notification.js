// Real-time notifications disabled (broadcasting removed). Notifications still stored in database.
$(document).ready(function () {
    // Update unread count on bell icon and dropdown without page reload
    function updateNotificationBadge(count) {
        count = parseInt(count, 10) || 0;
        var $bellBadge = $('.ri-notification-3-line').closest('.nav-link').find('.notification-badge');
        if ($bellBadge.length) {
            $bellBadge.text(count);
            $bellBadge.removeClass('bg-secondary bg-danger').addClass(count > 0 ? 'bg-danger' : 'bg-secondary');
        }
        var $header = $('#notification-dropdown-menu .dropdown-header');
        var $unreadBadge = $header.find('.badge');
        if (count > 0) {
            if ($unreadBadge.length) {
                $unreadBadge.text(count + ' unread').removeClass('d-none');
            } else {
                $header.append('<span class="badge bg-danger rounded-pill">' + count + ' unread</span>');
            }
        } else {
            $unreadBadge.addClass('d-none');
        }
        if (count === 0) {
            $('#notification-dropdown-menu .notification-mark-all-wrap').hide();
        }
    }

    // Click on a notification item: mark as read and update count (no reload)
    $(document).on('click', '#notification-dropdown-menu .notification-item[data-id]', function (e) {
        e.preventDefault();
        var $item = $(this);
        if ($item.data('read-at') === '1') return;
        var id = $item.data('id');
        var url = $('#notification-dropdown-menu').data('mark-read-url');
        if (!url || !id) return;
        url = url.replace('__id__', id);
        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $item.removeClass('bg-light border-start border-primary border-3').data('read-at', '1');
                    updateNotificationBadge(res.unread_count);
                }
            }
        });
    });

    // "Mark all as read" via AJAX so count updates without reload
    $(document).on('click', '#notification-dropdown-menu a[href*="read-all"]', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        if (!href) return;
        $.ajax({
            url: href,
            type: 'GET',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function (res) {
                if (res && res.success) {
                    updateNotificationBadge(res.unread_count || 0);
                    $('#notification-dropdown-menu .notification-item[data-id]').each(function () {
                        $(this).removeClass('bg-light border-start border-primary border-3').data('read-at', '1');
                    });
                    $('#notification-dropdown-menu .notification-mark-all-wrap').hide();
                }
            },
            error: function () {
                window.location.href = href;
            }
        });
    });

    if (typeof window.Echo === 'undefined') {
        return;
    }

    if (typeof userId === 'undefined') {
        console.error("userId missing");
        return;
    }

    window.Echo
        .private(`App.Models.User.${userId}`)
        .notification((notification) => {
            const title = notification.title || (notification.data && notification.data.title) || 'Notification';
            const message = notification.message || (notification.data && notification.data.message) || '';

            // Badge update (unread count on top of notification icon) without reload
            const $badge = $('.ri-notification-3-line').closest('.nav-link').find('.notification-badge');
            const currentCount = (parseInt($badge.length ? $badge.text() : 0, 10) || 0);
            const newCount = currentCount + 1;
            if (!$badge.length) {
                $('.ri-notification-3-line').closest('.nav-link').find('.position-relative').append('<span class="notification-badge badge bg-danger rounded-pill position-absolute" style="top: -4px; right: -6px; font-size: 0.7rem; min-width: 1.25rem; height: 1.25rem; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; z-index: 2;">0</span>');
            }
            updateNotificationBadge(newCount);

            // Prepend to notification dropdown only
            const $dropdown = $('#notification-dropdown-menu');
            if ($dropdown.length) {
                $dropdown.find('.dropdown-header').first().after(`
                    <div class="dropdown-item notification-item bg-light" style="cursor:pointer">
                        <strong>${title}</strong>
                        <div class="small text-muted">${message}</div>
                    </div>
                `);
            }
        });

});
