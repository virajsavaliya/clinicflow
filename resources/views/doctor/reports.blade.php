@extends('layouts.app')

@section('title', 'Reports & Analytics - ClinicFlow')

@section('styles')
<style>
    /* Design System Variables */
    :root {
        --color-black: #1a1a1a;
        --color-white: #ffffff;
        --color-gray-50: #f9fafb;
        --color-gray-100: #f3f4f6;
        --color-gray-200: #e5e7eb;
        --color-gray-300: #d1d5db;
        --color-gray-400: #9ca3af;
        --color-gray-500: #6b7280;
        --color-gray-600: #4b5563;
        --color-gray-700: #374151;
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .page-header { margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
    .page-title { font-size: 1.25rem; font-weight: 700; margin: 0; }
    .page-sub { color: var(--color-gray-500); font-size: 0.75rem; margin: 0; }
    
    /* Period Selector Pills */
    .period-selector { display: flex; gap: 0.5rem; background: var(--color-gray-100); padding: 0.25rem; border-radius: 10px; }
    .period-btn { padding: 0.5rem 1rem; border: none; background: transparent; border-radius: 8px; cursor: pointer; font-size: 0.8rem; transition: all 0.2s ease; color: var(--color-gray-600); font-weight: 600; }
    .period-btn:hover { background: rgba(255,255,255,0.5); }
    .period-btn.active { background: var(--color-white); color: var(--color-black); box-shadow: 0 2px 4px rgba(0,0,0,0.06); }
    
    /* Modern Stat Cards */
    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-box { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: 14px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; transition: all 0.2s ease; box-shadow: var(--shadow-sm); }
    .stat-box:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
    .stat-icon.revenue { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: var(--color-white); }
    .stat-icon.patients { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: var(--color-white); }
    .stat-icon.prescriptions { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: var(--color-white); }
    .stat-icon.avg { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: var(--color-white); }
    .stat-content { flex: 1; }
    .stat-content .value { font-size: 1.75rem; font-weight: 700; line-height: 1; color: var(--color-black); }
    .stat-content .label { font-size: 0.75rem; color: var(--color-gray-500); margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500; }
    .stat-content .trend { font-size: 0.75rem; margin-top: 6px; display: inline-flex; align-items: center; gap: 0.25rem; }
    .stat-content .trend.up { color: #16a34a; }
    .stat-content .trend.down { color: #dc2626; }
    
    .chart-row { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
    .chart-card { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: 14px; padding: 1.25rem; box-shadow: var(--shadow-sm); }
    .chart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .chart-title { font-weight: 600; font-size: 1rem; color: var(--color-black); }
    .chart-container { height: 250px; position: relative; }
    
    .table-card { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: 14px; overflow: hidden; box-shadow: var(--shadow-sm); }
    .table-card .header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--color-gray-200); display: flex; justify-content: space-between; align-items: center; background: var(--color-gray-50); }
    .table-card .title { font-weight: 600; font-size: 0.9rem; color: var(--color-black); }
    .export-btns { display: flex; gap: 0.5rem; }
    .export-btn { font-size: 0.75rem; padding: 0.375rem 0.75rem; border-radius: 8px; font-weight: 600; }
    .data-table { width: 100%; }
    .data-table th, .data-table td { padding: 0.875rem 1.25rem; text-align: left; border-bottom: 1px solid var(--color-gray-100); font-size: 0.8rem; }
    .data-table th { background: var(--color-gray-50); font-weight: 600; color: var(--color-gray-600); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .data-table tbody tr:hover { background: var(--color-gray-50); }
    
    @media (max-width: 1024px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } .chart-row { grid-template-columns: 1fr; } }
    @media (max-width: 600px) { .stat-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-graph-up"></i> Reports & Analytics</h1>
    <div class="period-selector">
        <button class="period-btn {{ $period === 'daily' ? 'active' : '' }}" onclick="changePeriod('daily')">Today</button>
        <button class="period-btn {{ $period === 'weekly' ? 'active' : '' }}" onclick="changePeriod('weekly')">This Week</button>
        <button class="period-btn {{ $period === 'monthly' ? 'active' : '' }}" onclick="changePeriod('monthly')">This Month</button>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-box">
        <div class="stat-icon revenue">
            <i class="bi bi-currency-rupee"></i>
        </div>
        <div class="stat-content">
            <div class="value">₹{{ number_format($stats['totalRevenue']) }}</div>
            <div class="label">Total Revenue</div>
            <div class="trend {{ $stats['revenueGrowth'] >= 0 ? 'up' : 'down' }}">
                <i class="bi bi-{{ $stats['revenueGrowth'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                {{ abs($stats['revenueGrowth']) }}% vs prev period
            </div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon patients">
            <i class="bi bi-people-fill"></i>
        </div>
        <div class="stat-content">
            <div class="value">{{ $stats['totalPatients'] }}</div>
            <div class="label">Patients Visited</div>
            <div class="trend {{ $stats['patientGrowth'] >= 0 ? 'up' : 'down' }}">
                <i class="bi bi-{{ $stats['patientGrowth'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                {{ abs($stats['patientGrowth']) }}% vs prev period
            </div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon prescriptions">
            <i class="bi bi-prescription2"></i>
        </div>
        <div class="stat-content">
            <div class="value">{{ $stats['totalPrescriptions'] }}</div>
            <div class="label">Prescriptions</div>
            <div class="trend up">
                <i class="bi bi-file-text"></i> Total written
            </div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon avg">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div class="stat-content">
            <div class="value">₹{{ number_format($stats['avgRevenue']) }}</div>
            <div class="label">Avg. Revenue/Patient</div>
            <div class="trend up">
                <i class="bi bi-person"></i> Per visit
            </div>
        </div>
    </div>
</div>

<div class="chart-row">
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Revenue Trend</div>
        </div>
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Patient Demographics</div>
        </div>
        <div class="chart-container">
            <canvas id="demographicsChart"></canvas>
        </div>
    </div>
</div>

<div class="chart-row">
    <div class="table-card">
        <div class="header">
            <span class="title">Top Medicines Prescribed</span>
            <div class="export-btns">
                <a href="{{ route('doctor.reports.export', ['type' => 'medicines', 'period' => $period]) }}" class="btn btn-outline-secondary export-btn">
                    <i class="bi bi-download"></i> Export
                </a>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Times Prescribed</th>
                    <th>Total Qty</th>
                    <th>Stock Left</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topMedicines as $med)
                <tr>
                    <td><strong>{{ $med['name'] }}</strong></td>
                    <td>{{ $med['times'] }}</td>
                    <td>{{ $med['quantity'] }}</td>
                    <td>
                        @if($med['stock'] < 10)
                            <span class="badge bg-danger">{{ $med['stock'] }}</span>
                        @elseif($med['stock'] < 50)
                            <span class="badge bg-warning text-dark">{{ $med['stock'] }}</span>
                        @else
                            <span class="badge bg-success">{{ $med['stock'] }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">No data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-card">
        <div class="header">
            <span class="title">Recent Visits</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentVisits as $visit)
                <tr>
                    <td>{{ $visit->patient->name ?? 'N/A' }}</td>
                    <td>{{ $visit->created_at->format('M d') }}</td>
                    <td>₹{{ number_format($visit->bill->total ?? 0) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="table-card mb-4">
    <div class="header">
        <span class="title">Inventory Alerts</span>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Medicine</th>
                <th>Current Stock</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventoryAlerts as $alert)
            <tr>
                <td><strong>{{ $alert['name'] }}</strong></td>
                <td>{{ $alert['stock'] }}</td>
                <td>
                    @if($alert['status'] === 'critical')
                        <span class="badge bg-danger">Critical</span>
                    @elseif($alert['status'] === 'low')
                        <span class="badge bg-warning text-dark">Low Stock</span>
                    @elseif($alert['status'] === 'expiring')
                        <span class="badge bg-info">Expiring Soon</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('doctor.medicine') }}" class="btn btn-sm btn-outline-dark">Restock</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted">No alerts</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function changePeriod(period) {
    window.location.href = `{{ route('doctor.reports') }}?period=${period}`;
}

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueData = @json($chartData['revenue'] ?? []);

new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueData.map(d => d.label),
        datasets: [{
            label: 'Revenue',
            data: revenueData.map(d => d.value),
            borderColor: '#212529',
            backgroundColor: 'rgba(33, 37, 41, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => '₹' + value.toLocaleString()
                }
            }
        }
    }
});

// Demographics Chart
const demoCtx = document.getElementById('demographicsChart').getContext('2d');
const demoData = @json($chartData['demographics'] ?? ['male' => 50, 'female' => 45, 'other' => 5]);

new Chart(demoCtx, {
    type: 'doughnut',
    data: {
        labels: ['Male', 'Female', 'Other'],
        datasets: [{
            data: [demoData.male || 0, demoData.female || 0, demoData.other || 0],
            backgroundColor: ['#3b82f6', '#ec4899', '#8b5cf6']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
@endsection
