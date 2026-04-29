@extends('layouts.tabler')

@section('content')
<div class="container-xl">
    <!-- Page title -->
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Overview</div>
                <h2 class="page-title">Enterprise Dashboard</h2>
            </div>
            <!-- Page title actions removed for clean overview -->
        </div>
    </div>

    <!-- Page tabs -->
    <div class="mb-4">
        <ul class="nav nav-tabs nav-tabs-alt" data-bs-toggle="tabs">
            <li class="nav-item">
                <a href="#tabs-operations" class="nav-link active" data-bs-toggle="tab">Operations Overview</a>
            </li>
            <li class="nav-item">
                <a href="#tabs-financials" class="nav-link" data-bs-toggle="tab">Financial Analytics</a>
            </li>
            <li class="nav-item">
                <a href="#tabs-system" class="nav-link" data-bs-toggle="tab">System Health</a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade active show" id="tabs-operations">
            <div class="row row-cards">
                <!-- Welcome Card -->
                <div class="col-12">
                    <div class="card card-md card-gradient">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-6">
                                    <h2 class="h1">Welcome back, Admin!</h2>
                                    <p class="text-secondary fs-3">Your enterprise dashboard is ready. You have <span class="text-primary font-weight-bold">{{ $stats['total_orders'] }}</span> active orders today.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('erp.orders.index') }}" class="btn btn-primary">View Detailed Reports</a>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 d-none d-md-block">
                                    <div class="text-center">
                                        <img src="/tabler/static/illustrations/light/welcome-on-board.png" alt="Welcome" class="img-fluid rounded" style="max-height: 180px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI Cards with Sparklines -->
                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm card-link-pop">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-primary text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 1 0 -2.7 -2" /><path d="M12 8a3 3 0 1 0 -2.7 -2" /><path d="M7.3 8a3 3 0 1 0 -2.7 -2" /><path d="M3 13h18l-2 7h-14l-2 -7z" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium fs-2">₹ {{ number_format($stats['total_revenue'], 2) }}</div>
                                    <div class="text-secondary text-uppercase tracking-wider fs-5">Total Revenue</div>
                                </div>
                            </div>
                            <div id="sparkline-revenue" class="chart-sparkline mt-3"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm card-link-pop">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-green text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M12 11l0 8" /><path d="M12 11l4.5 -4.5" /><path d="M12 11l-4.5 -4.5" /><path d="M4.5 14.5l15 0" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium fs-2">{{ $stats['total_orders'] }}</div>
                                    <div class="text-secondary text-uppercase tracking-wider fs-5">Total Orders</div>
                                </div>
                            </div>
                            <div id="sparkline-orders" class="chart-sparkline mt-3"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm card-link-pop">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-azure text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium fs-2">{{ $stats['total_products'] }}</div>
                                    <div class="text-secondary text-uppercase tracking-wider fs-5">Active Products</div>
                                </div>
                            </div>
                            <div id="sparkline-products" class="chart-sparkline mt-3"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm card-link-pop">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-facebook text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium fs-2">{{ $stats['total_customers'] }}</div>
                                    <div class="text-secondary text-uppercase tracking-wider fs-5">Total Customers</div>
                                </div>
                            </div>
                            <div id="sparkline-customers" class="chart-sparkline mt-3"></div>
                        </div>
                    </div>
                </div>

                <!-- Business Process Steps -->
                <div class="col-12 mt-4">
                    <div class="card card-md">
                        <div class="card-body">
                            <h3 class="card-title">Order Fulfillment Lifecycle</h3>
                            <div class="subheader mb-4">Real-time tracking of business operations</div>
                            <div class="steps steps-blue steps-counter my-4">
                                <div class="step-item active">
                                    <div class="h4 m-0">Orders Received</div>
                                    <div class="text-secondary">{{ $stats['total_orders'] }} New</div>
                                </div>
                                <div class="step-item active">
                                    <div class="h4 m-0">Invoicing</div>
                                    <div class="text-secondary">{{ $stats['total_invoices'] }} Generated</div>
                                </div>
                                <div class="step-item">
                                    <div class="h4 m-0">Processing</div>
                                    <div class="text-secondary">Ready for Pack</div>
                                </div>
                                <div class="step-item">
                                    <div class="h4 m-0">Shipping</div>
                                    <div class="text-secondary">In Transit</div>
                                </div>
                                <div class="step-item">
                                    <div class="h4 m-0">Completed</div>
                                    <div class="text-secondary">Delivered</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tables Section -->
                <div class="col-12">
                    <div class="card card-md">
                        <div class="card-header border-0">
                            <h3 class="card-title">Recent Operational Orders</h3>
                            <div class="card-actions">
                                <a href="{{ route('erp.orders.index') }}" class="btn btn-sm btn-white">View All</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Party</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                    <tr>
                                        <td><span class="font-weight-bold">{{ $order->order_number }}</span></td>
                                        <td class="text-secondary">{{ $order->party->name ?? 'N/A' }}</td>
                                        <td class="text-secondary">₹ {{ number_format($order->grand_total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status == 'completed' ? 'green' : ($order->status == 'pending' ? 'yellow' : 'blue') }}-lt text-uppercase tracking-wider">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="text-secondary">{{ $order->order_date->format('d M, Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted p-4">No recent orders found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tabs-financials">
            <div class="row row-cards">
                <!-- Financial Charts -->
                <div class="col-lg-8">
                    <div class="card card-md">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <h3 class="card-title">Revenue Trend Analysis</h3>
                                <div class="ms-auto">
                                    <span class="badge bg-green-lt">Growth: +12.5%</span>
                                </div>
                            </div>
                            <div class="subheader mb-2">Monthly financial performance and revenue trajectory</div>
                            <div id="chart-revenue-trend-tab" class="chart-lg"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-md">
                        <div class="card-body">
                            <h3 class="card-title">Order Fulfillment Status</h3>
                            <div class="subheader mb-3">Allocation of order states across current cycle</div>
                            <div id="chart-order-status-tab" class="chart-lg"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tabs-system">
            <div class="row row-cards">
                <!-- System Health Widgets -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-md">
                        <div class="card-body">
                            <h3 class="card-title">Inventory Health</h3>
                            <div class="subheader mb-3">Overall stock status and availability</div>
                            <div class="progress progress-separated mb-3">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 44%" aria-label="Regular"></div>
                                <div class="progress-bar bg-info" role="progressbar" style="width: 19%" aria-label="In Transit"></div>
                                <div class="progress-bar bg-success" role="progressbar" style="width: 9%" aria-label="New"></div>
                            </div>
                            <div class="row">
                                <div class="col-auto d-flex align-items-center pe-2">
                                    <span class="legend-badge bg-primary"></span>
                                    <span>Regular</span>
                                    <span class="d-none d-md-inline d-lg-none d-xxl-inline ms-2 text-secondary">915</span>
                                </div>
                                <div class="col-auto d-flex align-items-center px-2">
                                    <span class="legend-badge bg-info"></span>
                                    <span>In Transit</span>
                                    <span class="d-none d-md-inline d-lg-none d-xxl-inline ms-2 text-secondary">415</span>
                                </div>
                                <div class="col-auto d-flex align-items-center ps-2">
                                    <span class="legend-badge bg-success"></span>
                                    <span>New</span>
                                    <span class="d-none d-md-inline d-lg-none d-xxl-inline ms-2 text-secondary">201</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card card-md">
                        <div class="card-body">
                            <h3 class="card-title">Infrastructure Status</h3>
                            <div class="subheader mb-3">Real-time health of core services</div>
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <span class="status-dot status-dot-animated bg-green d-block"></span>
                                </div>
                                <div class="col">
                                    <div class="text-truncate"> Database Connection </div>
                                </div>
                            </div>
                            <div class="row g-2 align-items-center mt-2">
                                <div class="col-auto">
                                    <span class="status-dot status-dot-animated bg-green d-block"></span>
                                </div>
                                <div class="col">
                                    <div class="text-truncate"> Storage Service </div>
                                </div>
                            </div>
                            <div class="row g-2 align-items-center mt-2">
                                <div class="col-auto">
                                    <span class="status-dot status-dot-animated bg-red d-block"></span>
                                </div>
                                <div class="col">
                                    <div class="text-truncate"> Backup Sync </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-lg-4">
                    <div class="card card-md h-100">
                        <div class="card-header border-0">
                            <h3 class="card-title">Activity History</h3>
                            <div class="card-actions">
                                <span class="badge bg-green-lt">Live</span>
                            </div>
                        </div>
                        <div class="card-body p-0" style="max-height: 380px; overflow-y: auto;">
                            <ul class="timeline px-3 pt-2">
                                @forelse($recentActivities as $activity)
                                @php
                                    $eventColor = match($activity->event) {
                                        'created' => 'green',
                                        'updated' => 'blue',
                                        'deleted' => 'red',
                                        default   => 'azure',
                                    };
                                    $eventIcon = match($activity->event) {
                                        'created' => '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" />',
                                        'updated' => '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" />',
                                        'deleted' => '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />',
                                        default   => '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />',
                                    };
                                @endphp
                                <li class="timeline-event">
                                    <div class="timeline-event-icon bg-{{ $eventColor }}-lt">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-{{ $eventColor }}" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">{!! $eventIcon !!}</svg>
                                    </div>
                                    <div class="card timeline-event-card border-0 shadow-none bg-transparent p-0">
                                        <div class="card-body p-0 pb-2">
                                            <div class="text-secondary float-end small text-nowrap">{{ $activity->created_at->diffForHumans() }}</div>
                                            <h4 class="m-0 text-capitalize">{{ $activity->description }}</h4>
                                            <p class="text-secondary small mb-0">
                                                By: <strong>{{ $activity->causer?->name ?? 'System' }}</strong>
                                                @if($activity->subject_type)
                                                &middot; {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </li>
                                @empty
                                <li class="text-center text-muted py-5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2 text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8v4l3 3" /><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /></svg>
                                    <div>No activity recorded yet.</div>
                                    <div class="small">Activity will appear here as you use the system.</div>
                                </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Shared Sparkline Options
        const sparklineOptions = {
            chart: {
                type: 'area',
                height: 40,
                sparkline: { enabled: true },
                animations: { enabled: true }
            },
            stroke: { width: 2, curve: 'smooth' },
            fill: { opacity: 0.1 },
            tooltip: { fixed: { enabled: false }, x: { show: false }, y: { title: { formatter: (seriesName) => '' } }, marker: { show: false } }
        };

        // Revenue Sparkline
        new ApexCharts(document.getElementById('sparkline-revenue'), {
            ...sparklineOptions,
            series: [{ data: [35, 41, 62, 42, 13, 18, 29, 37, 36, 51, 32, 35] }],
            colors: [tabler.getColor("primary")]
        }).render();

        // Orders Sparkline
        new ApexCharts(document.getElementById('sparkline-orders'), {
            ...sparklineOptions,
            series: [{ data: [12, 15, 10, 20, 25, 22, 18, 24, 30, 28, 35, 32] }],
            colors: [tabler.getColor("green")]
        }).render();

        // Products Sparkline
        new ApexCharts(document.getElementById('sparkline-products'), {
            ...sparklineOptions,
            series: [{ data: [5, 10, 8, 12, 15, 14, 16, 20, 18, 22, 25, 24] }],
            colors: [tabler.getColor("azure")]
        }).render();

        // Customers Sparkline
        new ApexCharts(document.getElementById('sparkline-customers'), {
            ...sparklineOptions,
            series: [{ data: [10, 12, 11, 14, 16, 15, 18, 22, 21, 25, 28, 27] }],
            colors: [tabler.getColor("facebook")]
        }).render();

        // Revenue Trend Chart (Financials Tab)
        const chartRevenue = new ApexCharts(document.getElementById('chart-revenue-trend-tab'), {
            chart: {
                type: "area",
                fontFamily: 'inherit',
                height: 240,
                parentHeightOffset: 0,
                toolbar: { show: false },
                animations: { enabled: true },
            },
            dataLabels: { enabled: false },
            fill: { opacity: .16, type: 'solid' },
            stroke: { width: 2, lineCap: "round", curve: "smooth" },
            series: [{
                name: "Revenue",
                data: @json($revenueTrend->pluck('total')->toArray())
            }],
            tooltip: { theme: 'dark' },
            grid: {
                padding: { top: -20, right: 0, left: -4, bottom: -4 },
                strokeDashArray: 4,
            },
            xaxis: {
                labels: { padding: 0 },
                tooltip: { enabled: false },
                axisBorder: { show: false },
                categories: @json($revenueTrend->pluck('month')->toArray()),
            },
            yaxis: { labels: { padding: 4 } },
            colors: [tabler.getColor("primary")],
            legend: { show: false },
        });
        chartRevenue.render();

        // Order Status Chart (Financials Tab)
        const chartStatus = new ApexCharts(document.getElementById('chart-order-status-tab'), {
            chart: {
                type: "donut",
                fontFamily: 'inherit',
                height: 240,
                sparkline: { enabled: true },
                animations: { enabled: true },
            },
            fill: { opacity: 1 },
            series: @json($orderStatus->pluck('count')->toArray()),
            labels: @json($orderStatus->pluck('status')->toArray()),
            tooltip: { theme: 'dark' },
            grid: { strokeDashArray: 4 },
            colors: [tabler.getColor("primary"), tabler.getColor("green"), tabler.getColor("yellow"), tabler.getColor("red")],
            legend: { show: true, position: 'bottom', offsetTop: 12 },
        });
        chartStatus.render();

        // Fix for charts in hidden tabs
        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', () => {
                window.dispatchEvent(new Event('resize'));
            });
        });
    });
</script>
@endpush
@endsection
