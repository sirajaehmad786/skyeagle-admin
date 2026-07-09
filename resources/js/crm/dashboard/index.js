import ApexCharts from 'apexcharts/dist/apexcharts.min.js';

$(function () {
    const dashboardUrl = window.dashboardDataUrl;
    let currentPeriod = 'monthly';
    let performanceChart = null;
    let bookingStatusChart = null;
    let packageMixChart = null;

    const chartColors = ['#3e60d5', '#47ad77', '#fa5c7c', '#ffbc00'];
    const dashboardMessageLimit = 4;

    function formatNumber(value) {
        return new Intl.NumberFormat('en-IN').format(Number(value || 0));
    }

    function formatMoney(value) {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 0,
        }).format(Number(value || 0));
    }

    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
    }

    function setLoading(isLoading) {
        $('#dashboard-refresh-btn')
            .prop('disabled', isLoading)
            .find('i')
            .toggleClass('ri-refresh-line', !isLoading)
            .toggleClass('ri-loader-4-line dashboard-spin', isLoading);
    }

    function loadDashboard(period = currentPeriod) {
        currentPeriod = period;
        setLoading(true);

        $.ajax({
            url: dashboardUrl,
            method: 'GET',
            data: {
                period: currentPeriod,
            },
            success: function (response) {
                if (!response.status) {
                    showToastmessage('Dashboard data load failed.', 'error');
                    return;
                }

                renderDashboard(response.data);
            },
            error: function () {
                showToastmessage('Dashboard data load failed.', 'error');
            },
            complete: function () {
                setLoading(false);
            },
        });
    }

    function renderDashboard(data) {
        renderMetrics(data.metrics || []);
        renderPerformanceChart(data.performance || {});
        renderBookingStatus(data.booking_status || []);
        renderPackageMix(data.package_mix || []);
        renderPopularPackages(data.popular_packages || []);
        renderLatestMessages(data.latest_messages || []);
    }

    function renderMetrics(metrics) {
        metrics.forEach(function (metric) {
            const $card = $(`[data-metric-card="${metric.key}"]`);
            const trend = metric.trend || {};
            const trendClass = trend.direction === 'up'
                ? 'text-success'
                : (trend.direction === 'down' ? 'text-danger' : 'text-muted');
            const trendIcon = trend.direction === 'up'
                ? 'ri-arrow-up-line'
                : (trend.direction === 'down' ? 'ri-arrow-down-line' : 'ri-subtract-line');

            $card.attr('href', metric.url || 'javascript:void(0);');
            $card.find('.dashboard-metric-value').text(formatNumber(metric.value));
            $card.find('.dashboard-metric-subtitle').text(metric.subtitle || '');
            $card.find('.dashboard-trend')
                .removeClass('text-success text-danger text-muted')
                .addClass(trendClass)
                .html(`<i class="${trendIcon}"></i> <span>${formatNumber(trend.percent)}% ${escapeHtml(trend.label || '')}</span>`);
        });
    }

    function renderPerformanceChart(performance) {
        const options = {
            series: performance.series || [],
            chart: {
                height: 360,
                type: 'line',
                toolbar: { show: false },
                zoom: { enabled: false },
            },
            stroke: {
                width: [3, 3, 3, 3],
                curve: 'smooth',
            },
            markers: {
                size: 3,
                strokeWidth: 0,
            },
            colors: chartColors,
            xaxis: {
                categories: performance.labels || [],
                labels: {
                    rotate: -35,
                    style: { fontSize: '11px' },
                },
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                labels: {
                    formatter: function (value) {
                        return Math.round(value);
                    },
                },
            },
            grid: {
                borderColor: '#eef2f7',
                strokeDashArray: 4,
            },
            dataLabels: { enabled: false },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
            },
            tooltip: {
                shared: true,
                intersect: false,
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: { height: 310 },
                    legend: { position: 'bottom', horizontalAlign: 'center' },
                },
            }],
        };

        if (performanceChart) {
            performanceChart.updateOptions(options, true, true);
        } else {
            performanceChart = new ApexCharts(document.querySelector('#dashboard-performance-chart'), options);
            performanceChart.render();
        }
    }

    function renderBookingStatus(statuses) {
        const labels = statuses.map((item) => item.label);
        const values = statuses.map((item) => item.count);
        const total = values.reduce((sum, value) => sum + Number(value || 0), 0);
        const chartValues = total > 0 ? values : [1];
        const chartLabels = total > 0 ? labels : ['No bookings'];

        const options = {
            series: chartValues,
            labels: chartLabels,
            chart: {
                height: 252,
                type: 'donut',
            },
            colors: total > 0 ? ['#ffbc00', '#3e60d5', '#47ad77', '#fa5c7c'] : ['#d8dee9'],
            legend: { show: false },
            dataLabels: { enabled: false },
            stroke: { colors: ['transparent'] },
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Requests',
                                formatter: function () {
                                    return formatNumber(total);
                                },
                            },
                        },
                    },
                },
            },
        };

        if (bookingStatusChart) {
            bookingStatusChart.updateOptions(options, true, true);
        } else {
            bookingStatusChart = new ApexCharts(document.querySelector('#dashboard-booking-status-chart'), options);
            bookingStatusChart.render();
        }

        const listHtml = statuses.length
            ? statuses.map((item, index) => `
                <div class="dashboard-status-row">
                    <span><i style="background:${options.colors[index] || '#8391a2'}"></i>${escapeHtml(item.label)}</span>
                    <strong>${formatNumber(item.count)}</strong>
                </div>
            `).join('')
            : '<div class="text-center text-muted py-3">No booking requests found.</div>';

        $('#dashboard-booking-status-list').html(listHtml);
    }

    function renderPackageMix(packageMix) {
        const labels = packageMix.map((item) => item.label);
        const values = packageMix.map((item) => item.count);

        const options = {
            series: [{
                name: 'Packages',
                data: values,
            }],
            chart: {
                height: 245,
                type: 'bar',
                toolbar: { show: false },
            },
            colors: ['#3e60d5'],
            plotOptions: {
                bar: {
                    borderRadius: 5,
                    columnWidth: '42%',
                    distributed: true,
                },
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: labels,
            },
            yaxis: {
                min: 0,
                labels: {
                    formatter: function (value) {
                        return Math.round(value);
                    },
                },
            },
            legend: { show: false },
            grid: {
                borderColor: '#eef2f7',
                strokeDashArray: 4,
            },
        };

        if (packageMixChart) {
            packageMixChart.updateOptions(options, true, true);
        } else {
            packageMixChart = new ApexCharts(document.querySelector('#dashboard-package-mix-chart'), options);
            packageMixChart.render();
        }
    }

    function renderPopularPackages(packages) {
        const html = packages.length
            ? packages.map((item) => `
                <tr>
                    <td>
                        <a href="${escapeHtml(item.url)}" class="dashboard-package-cell">
                            <img src="${escapeHtml(item.image_url)}" alt="${escapeHtml(item.name)}">
                            <span>
                                <strong>${escapeHtml(item.name)}</strong>
                                <small>${escapeHtml(item.code || '')}</small>
                            </span>
                        </a>
                    </td>
                    <td><span class="badge bg-primary-subtle text-primary">${escapeHtml(item.booking_type || '-')}</span></td>
                    <td>
                        <span class="d-block">${formatNumber(item.bookings_count)} bookings</span>
                        <small class="text-muted">${formatNumber(item.enquiries_count)} enquiries</small>
                    </td>
                    <td>
                        <strong>${formatNumber(item.score)}</strong>
                        <small class="d-block text-muted">${formatMoney(item.price)}</small>
                    </td>
                    <td>
                        <span class="badge ${item.status ? 'bg-success' : 'bg-secondary'}">${item.status ? 'Active' : 'Inactive'}</span>
                    </td>
                </tr>
            `).join('')
            : '<tr><td colspan="5" class="text-center text-muted py-4">No package data found.</td></tr>';

        $('#dashboard-popular-packages').html(html);
    }

    function renderLatestMessages(messages) {
        const visibleMessages = messages.slice(0, dashboardMessageLimit);
        const html = visibleMessages.length
            ? visibleMessages.map((item) => `
                <a href="${escapeHtml(item.url)}" class="dashboard-message-item">
                    <div class="dashboard-message-avatar">${escapeHtml((item.name || 'C').substring(0, 1).toUpperCase())}</div>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                            <strong>${escapeHtml(item.name)}</strong>
                            <small class="text-muted">${escapeHtml(item.created_at_label)}</small>
                        </div>
                        <p class="mb-1">${escapeHtml(item.message)}</p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark">${escapeHtml(item.type)}</span>
                            <small class="text-muted">${escapeHtml(item.source || '')}</small>
                        </div>
                    </div>
                </a>
            `).join('')
            : '<div class="text-center text-muted py-4">No customer messages found.</div>';

        $('#dashboard-latest-messages').html(html);
    }

    $('.dashboard-period-switch [data-period]').on('click', function () {
        $('.dashboard-period-switch [data-period]')
            .removeClass('btn-primary active')
            .addClass('btn-light');
        $(this).removeClass('btn-light').addClass('btn-primary active');
        loadDashboard($(this).data('period'));
    });

    $('#dashboard-refresh-btn').on('click', function () {
        loadDashboard(currentPeriod);
    });

    loadDashboard(currentPeriod);
});
