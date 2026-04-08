import ApexCharts from 'apexcharts';

function renderLeadsBookingsChart(config) {
    const el = document.querySelector('#leads-bookings-chart');
    if (!el || !config) return;

    const colors = el.dataset.colors ? el.dataset.colors.split(',') : ['#3e60d5', '#47ad77'];

    const options = {
        chart: {
            type: 'line',
            height: 260,
            toolbar: { show: false },
            zoom: { enabled: false },
        },
        stroke: {
            curve: 'smooth',
            width: 3,
        },
        dataLabels: {
            enabled: false,
        },
        colors: colors,
        series: [
            {
                name: 'Leads',
                data: config.leadSeries || [],
            },
            {
                name: 'Bookings',
                data: config.bookingSeries || [],
            },
        ],
        xaxis: {
            categories: config.labels || [],
        },
        yaxis: {
            labels: {
                formatter: (val) => Math.round(val),
            },
        },
        legend: {
            position: 'top',
        },
    };

    const chart = new ApexCharts(el, options);
    chart.render();
}

function renderRevenueChart(config) {
    const el = document.querySelector('#revenue-chart');
    if (!el || !config) return;

    const colors = el.dataset.colors ? el.dataset.colors.split(',') : ['#16a7e9'];

    const options = {
        chart: {
            type: 'bar',
            height: 260,
            toolbar: { show: false },
        },
        plotOptions: {
            bar: {
                columnWidth: '45%',
                borderRadius: 4,
            },
        },
        dataLabels: { enabled: false },
        colors: colors,
        series: [
            {
                name: 'Revenue',
                data: config.series || [],
            },
        ],
        xaxis: {
            categories: config.labels || [],
        },
        yaxis: {
            labels: {
                formatter: (val) => `₹${Number(val).toFixed(0)}`,
            },
        },
        tooltip: {
            y: {
                formatter: (val) => `₹${Number(val).toFixed(2)}`,
            },
        },
    };

    const chart = new ApexCharts(el, options);
    chart.render();
}

function renderBookingStatusChart(config) {
    const el = document.querySelector('#booking-status-chart');
    if (!el || !config) return;

    const colors = el.dataset.colors ? el.dataset.colors.split(',') : ['#47ad77', '#3e60d5', '#16a7e9', '#f15776'];

    const options = {
        chart: {
            type: 'donut',
            height: 260,
        },
        labels: config.labels || [],
        series: config.series || [],
        colors: colors,
        legend: {
            position: 'bottom',
        },
        dataLabels: {
            enabled: true,
        },
    };

    const chart = new ApexCharts(el, options);
    chart.render();
}

const rupeeSymbol = '₹';
const chartInstances = {};

function destroyChart(key) {
    if (chartInstances[key]) {
        chartInstances[key].destroy();
        chartInstances[key] = null;
    }
}

function renderLeadOverviewChart(pipelineCounts) {
    const el = document.querySelector('#lead-overview-chart');
    if (!el || !pipelineCounts || typeof pipelineCounts !== 'object') return;

    destroyChart('leadOverview');

    const labels = Object.keys(pipelineCounts);
    const series = Object.values(pipelineCounts);
    const colors = el.dataset.colors ? el.dataset.colors.split(',') : ['#3e60d5'];

    const options = {
        chart: {
            type: 'bar',
            height: 320,
            toolbar: { show: false },
        },
        plotOptions: {
            bar: {
                horizontal: true,
                columnWidth: '60%',
                borderRadius: 4,
                distributed: true,
                dataLabels: {
                    position: 'bottom',
                },
            },
        },
        colors: colors,
        dataLabels: {
            enabled: true,
            formatter: (val) => val,
        },
        series: [
            {
                name: 'Leads',
                data: series,
            },
        ],
        xaxis: {
            categories: labels,
            title: {
                text: 'Count',
            },
        },
        yaxis: {
            labels: {
                maxWidth: 140,
            },
        },
        legend: {
            show: false,
        },
        tooltip: {
            y: {
                formatter: (val) => val + ' lead(s)',
            },
        },
    };

    const chart = new ApexCharts(el, options);
    chart.render();
    chartInstances['leadOverview'] = chart;
}

function renderPaymentMonthlyRevenueChart(config) {
    const el = document.querySelector('#payment-monthly-revenue-chart');
    if (!el || !config) return;

    destroyChart('paymentMonthlyRevenue');

    const colors = el.dataset.colors ? el.dataset.colors.split(',') : ['#3e60d5'];

    const options = {
        chart: {
            type: 'bar',
            height: 280,
            toolbar: { show: false },
        },
        plotOptions: {
            bar: {
                columnWidth: '55%',
                borderRadius: 4,
            },
        },
        dataLabels: { enabled: false },
        colors: colors,
        series: [
            {
                name: 'Revenue',
                data: config.series || [],
            },
        ],
        xaxis: {
            categories: config.labels || [],
        },
        yaxis: {
            labels: {
                formatter: (val) => `${rupeeSymbol}${Number(val).toLocaleString('en-IN', { maximumFractionDigits: 0 })}`,
            },
        },
        tooltip: {
            y: {
                formatter: (val) => `${rupeeSymbol}${Number(val).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`,
            },
        },
    };

    const chart = new ApexCharts(el, options);
    chart.render();
    chartInstances['paymentMonthlyRevenue'] = chart;
}

function renderPaymentDailyCollectionChart(config) {
    const el = document.querySelector('#payment-daily-collection-chart');
    if (!el || !config) return;

    destroyChart('paymentDailyCollection');

    const colors = el.dataset.colors ? el.dataset.colors.split(',') : ['#47ad77'];

    const options = {
        chart: {
            type: 'line',
            height: 280,
            toolbar: { show: false },
            zoom: { enabled: false },
        },
        stroke: {
            curve: 'smooth',
            width: 2,
        },
        dataLabels: { enabled: false },
        colors: colors,
        series: [
            {
                name: 'Collection',
                data: config.series || [],
            },
        ],
        xaxis: {
            categories: config.labels || [],
        },
        yaxis: {
            labels: {
                formatter: (val) => `${rupeeSymbol}${Number(val).toLocaleString('en-IN', { maximumFractionDigits: 0 })}`,
            },
        },
        tooltip: {
            y: {
                formatter: (val) => `${rupeeSymbol}${Number(val).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`,
            },
        },
    };

    const chart = new ApexCharts(el, options);
    chart.render();
    chartInstances['paymentDailyCollection'] = chart;
}

function updateDashboardFromData(data) {
    const formatNum = (n) => Number(n).toLocaleString();
    const rupee = rupeeSymbol;

    if (data.statCards) {
        const el = document.getElementById('stat-total-contacts');
        if (el) el.textContent = formatNum(data.statCards.total_contacts ?? 0);
        const el2 = document.getElementById('stat-active-leads');
        if (el2) el2.textContent = formatNum(data.statCards.active_leads ?? 0);
        const el3 = document.getElementById('stat-today-new-leads');
        if (el3) el3.textContent = formatNum(data.statCards.today_new_leads ?? 0);
    }

    if (data.paymentOverview) {
        const po = data.paymentOverview;
        const fmt = (n) => Number(n).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const el1 = document.getElementById('po-total-confirmed');
        if (el1) el1.textContent = rupee + fmt(po.total_confirmed_booking_value ?? 0);
        const el2 = document.getElementById('po-total-revenue');
        if (el2) el2.textContent = rupee + fmt(po.total_revenue ?? 0);
        const el3 = document.getElementById('po-pending');
        if (el3) el3.textContent = rupee + fmt(po.pending_payments_amount ?? 0);
        const el4 = document.getElementById('po-overdue');
        if (el4) el4.textContent = rupee + fmt(po.overdue_payments_amount ?? 0);
    }

    if (data.pipelineCounts) {
        window.pipelineCounts = data.pipelineCounts;
        renderLeadOverviewChart(data.pipelineCounts);
    }

    if (data.monthlyRevenueByYear) {
        window.paymentMonthlyRevenue = data.monthlyRevenueByYear;
        renderPaymentMonthlyRevenueChart(data.monthlyRevenueByYear);
    }

    if (data.dailyCollection) {
        window.paymentDailyCollection = data.dailyCollection;
        renderPaymentDailyCollectionChart(data.dailyCollection);
    }

    const breakdown = data.bookingStatusBreakdown || data.chartBookingStatus;
    if (breakdown && breakdown.statusCounts) {
        const totalUpcomingEl = document.getElementById('booking-total-upcoming');
        if (totalUpcomingEl) totalUpcomingEl.textContent = formatNum(breakdown.totalUpcoming ?? 0);
        document.querySelectorAll('.booking-status-count').forEach((span) => {
            const status = span.getAttribute('data-status');
            if (status) span.textContent = formatNum(breakdown.statusCounts[status] ?? 0);
        });
    }

    if (Array.isArray(data.upcomingBookings)) {
        const tbody = document.getElementById('dashboard-upcoming-bookings-tbody');
        if (tbody) {
            const statusBadge = (key, label) => {
                if (key === 'on_trip') return `<span class="badge bg-info">${label}</span>`;
                if (key === 'confirmed') return `<span class="badge bg-primary">${label}</span>`;
                if (key === 'completed') return `<span class="badge bg-secondary">${label}</span>`;
                if (key === 'cancelled') return `<span class="badge bg-danger">${label}</span>`;
                return `<span class="badge bg-secondary">${label}</span>`;
            };
            tbody.innerHTML = data.upcomingBookings.length === 0
                ? '<tr><td colspan="6" class="text-center text-muted py-4">No upcoming confirmed bookings.</td></tr>'
                : data.upcomingBookings.map((b) => `
                    <tr>
                        <td><span class="fw-medium">${escapeHtml(b.booking_id)}</span></td>
                        <td>${escapeHtml(b.contact_name)}</td>
                        <td>${escapeHtml(b.start_date)}</td>
                        <td>${escapeHtml(b.end_date)}</td>
                        <td>${statusBadge(b.status_key, b.status)}</td>
                        <td class="text-end"><a href="${escapeHtml(b.show_url)}" class="btn btn-soft-primary btn-sm">View</a></td>
                    </tr>
                `).join('');
        }
    }
}

function escapeHtml(text) {
    if (text == null) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', () => {
    const data = window.DashboardCharts || {};
    renderLeadsBookingsChart(data.leadsBookings);
    renderRevenueChart(data.revenueTrend);
    renderBookingStatusChart(data.bookingStatus);
    renderLeadOverviewChart(window.pipelineCounts);
    renderPaymentMonthlyRevenueChart(window.paymentMonthlyRevenue);
    renderPaymentDailyCollectionChart(window.paymentDailyCollection);

    function fetchDashboardData(params) {
        const url = (document.getElementById('dashboard-payment-year') || document.getElementById('dashboard-user-filter'))?.getAttribute('data-dashboard-url');
        if (!url) return Promise.resolve();
        const range = typeof window.dashboardRange !== 'undefined' ? window.dashboardRange : 'month';
        const paymentYear = typeof params.payment_year !== 'undefined' ? params.payment_year : (typeof window.dashboardPaymentYear !== 'undefined' ? window.dashboardPaymentYear : new Date().getFullYear());
        const userId = params.user_id !== undefined ? params.user_id : (document.getElementById('dashboard-user-filter')?.value ?? '');
        const search = new URLSearchParams({ range, payment_year: paymentYear });
        if (userId) search.set('user_id', userId);
        return fetch(`${url}?${search.toString()}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then((res) => res.json())
            .then((data) => {
                updateDashboardFromData(data);
                window.dashboardPaymentYear = paymentYear;
            });
    }

    const userFilter = document.getElementById('dashboard-user-filter');
    if (userFilter) {
        const url = userFilter.getAttribute('data-dashboard-url');
        if (url) {
            userFilter.addEventListener('change', function () {
                const userId = this.value;
                userFilter.disabled = true;
                fetchDashboardData({ user_id: userId })
                    .catch(() => {})
                    .finally(() => {
                        userFilter.disabled = false;
                    });
            });
        }
    }

    const paymentYearSelect = document.getElementById('dashboard-payment-year');
    if (paymentYearSelect) {
        const url = paymentYearSelect.getAttribute('data-dashboard-url');
        if (url) {
            paymentYearSelect.addEventListener('change', function () {
                const year = this.value;
                paymentYearSelect.disabled = true;
                fetchDashboardData({ payment_year: year })
                    .catch(() => {})
                    .finally(() => {
                        paymentYearSelect.disabled = false;
                    });
            });
        }
    }
});

