@extends('layouts.app')

@section('title', 'Inventory Alerts - ClinicFlow')

@section('styles')
<style>
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
        --radius-sm: 6px;
        --radius-md: 10px;
        --radius-lg: 12px;
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: var(--color-gray-50); color: var(--color-black); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    
    /* Header */
    .inventory-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .page-title { font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 0.75rem; color: var(--color-black); }
    .page-title i { font-size: 1.75rem; }
    .page-sub { font-size: 0.875rem; color: var(--color-gray-500); margin-top: 0.25rem; }
    
    /* Stat Cards */
    .stat-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-bottom: 1.5rem; }
    .stat-card { background: linear-gradient(135deg, var(--color-white) 0%, #fafafa 100%); border-radius: 14px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid var(--color-gray-100); transition: all 0.2s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; font-size: 1.5rem; }
    .stat-icon.danger { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: white; }
    .stat-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); color: white; }
    .stat-icon.info { background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); color: white; }
    .stat-value { font-size: 2rem; font-weight: 700; color: var(--color-black); line-height: 1; }
    .stat-label { font-size: 0.75rem; color: var(--color-gray-500); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-top: 0.5rem; }
    
    /* Alert Section */
    .alert-section { background: var(--color-white); border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid var(--color-gray-100); margin-bottom: 1.5rem; overflow: hidden; }
    .section-header { background: linear-gradient(135deg, var(--color-black) 0%, #2d2d2d 100%); color: white; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; }
    .section-title { font-size: 1.125rem; font-weight: 700; display: flex; align-items: center; gap: 0.75rem; }
    .section-badge { background: rgba(255,255,255,0.2); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.875rem; font-weight: 600; }
    
    /* Modern Table */
    .table-container { overflow-x: auto; }
    .modern-table { width: 100%; border-collapse: collapse; }
    .modern-table thead { background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); }
    .modern-table th { padding: 1rem 1.25rem; text-align: left; font-size: 0.75rem; font-weight: 700; color: var(--color-gray-600); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--color-gray-200); }
    .modern-table td { padding: 1.25rem; border-bottom: 1px solid var(--color-gray-100); font-size: 0.875rem; color: var(--color-black); vertical-align: middle; }
    .modern-table tbody tr { transition: all 0.15s ease; }
    .modern-table tbody tr:hover { background: var(--color-gray-50); }
    .modern-table tbody tr:last-child td { border-bottom: none; }
    
    /* Medicine Name */
    .medicine-name { font-weight: 600; color: var(--color-black); font-size: 0.9375rem; }
    .medicine-category { font-size: 0.75rem; color: var(--color-gray-500); margin-top: 0.125rem; }
    
    /* Status Badges */
    .status-badge { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; }
    .status-badge.out-of-stock { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; }
    .status-badge.critical { background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%); color: #9a3412; }
    .status-badge.low { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; }
    .status-badge.expired { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; }
    .status-badge.expiring-soon { background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%); color: #9a3412; }
    .status-badge.expiring { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; }
    
    /* Stock Progress Bar */
    .stock-progress { width: 120px; height: 8px; background: var(--color-gray-200); border-radius: 4px; overflow: hidden; position: relative; }
    .stock-progress-fill { height: 100%; border-radius: 4px; transition: width 0.3s ease; }
    .stock-progress-fill.danger { background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%); }
    .stock-progress-fill.warning { background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%); }
    .stock-progress-fill.success { background: linear-gradient(90deg, #16a34a 0%, #22c55e 100%); }
    .stock-percentage { font-size: 0.75rem; color: var(--color-gray-600); margin-top: 0.25rem; }
    
    /* Action Button */
    .action-btn { background: linear-gradient(135deg, var(--color-black) 0%, #2d2d2d 100%); color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.375rem; text-decoration: none; }
    .action-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); color: white; }
    .action-btn.outline { background: white; color: var(--color-black); border: 1px solid var(--color-gray-300); }
    .action-btn.outline:hover { background: var(--color-gray-50); border-color: var(--color-black); }
    
    /* Empty State */
    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-state i { font-size: 4rem; color: #22c55e; margin-bottom: 1rem; opacity: 0.8; }
    .empty-state h3 { font-size: 1.125rem; font-weight: 600; color: var(--color-black); margin-bottom: 0.5rem; }
    .empty-state p { color: var(--color-gray-500); font-size: 0.875rem; }
    
    /* Settings Section */
    .settings-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; padding: 1.5rem; }
    .setting-item { display: flex; align-items: flex-start; gap: 0.75rem; }
    .setting-checkbox { width: 20px; height: 20px; border: 2px solid var(--color-gray-300); border-radius: 6px; cursor: pointer; flex-shrink: 0; margin-top: 0.125rem; }
    .setting-checkbox:checked { background: linear-gradient(135deg, var(--color-black) 0%, #2d2d2d 100%); border-color: var(--color-black); }
    .setting-label { font-size: 0.875rem; color: var(--color-black); font-weight: 500; }
    .setting-description { font-size: 0.75rem; color: var(--color-gray-500; margin-top: 0.125rem; }
    
    /* Days Remaining Badge */
    .days-badge { font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem; }
    .days-badge.critical { color: #dc2626; }
    .days-badge.warning { color: #f59e0b; }
    .days-badge.expired { color: #991b1b; }
    
    /* Stock Number */
    .stock-number { font-weight: 600; font-size: 1rem; }
    .stock-number.low { color: #dc2626; }
    .stock-number.medium { color: #f59e0b; }
    .stock-number.high { color: #16a34a; }
    
    @media (max-width: 768px) { 
        .stat-cards { grid-template-columns: 1fr; }
        .settings-grid { grid-template-columns: 1fr; }
        .inventory-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
    }
</style>
@endsection

@section('content')
<div class="inventory-header">
    <div>
        <h1 class="page-title"><i class="bi bi-exclamation-triangle-fill"></i> Inventory Alerts</h1>
        <p class="page-sub">Monitor low stock levels and expiring medicines</p>
    </div>
    <a href="{{ route('doctor.medicine') }}" class="action-btn">
        <i class="bi bi-box-seam-fill"></i> Manage Inventory
    </a>
</div>

<!-- Stat Cards -->
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="bi bi-box-seam"></i>
        </div>
        <div class="stat-value">{{ count($lowStockItems) }}</div>
        <div class="stat-label">Low Stock Items</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="bi bi-calendar-x"></i>
        </div>
        <div class="stat-value">{{ count($expiringItems) }}</div>
        <div class="stat-label">Expiring Soon</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="bi bi-exclamation-circle"></i>
        </div>
        <div class="stat-value">{{ count($lowStockItems) + count($expiringItems) }}</div>
        <div class="stat-label">Total Alerts</div>
    </div>
</div>

<!-- Low Stock Items -->
<div class="alert-section">
    <div class="section-header">
        <span class="section-title">
            <i class="bi bi-box-seam"></i> Low Stock Items
        </span>
        <span class="section-badge">{{ count($lowStockItems) }}</span>
    </div>
    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Current Stock</th>
                    <th>Min Required</th>
                    <th>Status</th>
                    <th>Stock Level</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lowStockItems as $item)
                <tr>
                    <td>
                        <div class="medicine-name">{{ $item['name'] }}</div>
                        @if(isset($item['category']))
                        <div class="medicine-category">{{ $item['category'] }}</div>
                        @endif
                    </td>
                    <td>
                        @php
                            $stockClass = $item['current_stock'] == 0 ? 'low' : ($item['current_stock'] <= 5 ? 'low' : ($item['current_stock'] <= 15 ? 'medium' : 'high'));
                        @endphp
                        <span class="stock-number {{ $stockClass }}">{{ $item['current_stock'] }}</span>
                    </td>
                    <td>{{ $item['min_stock'] }}</td>
                    <td>
                        @if($item['status'] === 'out_of_stock')
                            <span class="status-badge out-of-stock">
                                <i class="bi bi-x-circle-fill"></i> Out of Stock
                            </span>
                        @elseif($item['status'] === 'critical')
                            <span class="status-badge critical">
                                <i class="bi bi-exclamation-triangle-fill"></i> Critical
                            </span>
                        @else
                            <span class="status-badge low">
                                <i class="bi bi-dash-circle-fill"></i> Low
                            </span>
                        @endif
                    </td>
                    <td>
                        @php
                            $percentage = min(100, ($item['current_stock'] / max(1, $item['min_stock'])) * 100);
                            $colorClass = $percentage < 25 ? 'danger' : ($percentage < 50 ? 'warning' : 'success');
                        @endphp
                        <div class="stock-progress">
                            <div class="stock-progress-fill {{ $colorClass }}" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="stock-percentage">{{ number_format($percentage, 0) }}%</div>
                    </td>
                    <td>
                        <a href="{{ route('doctor.medicine') }}" class="action-btn">
                            <i class="bi bi-plus-circle"></i> Restock
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-check-circle-fill"></i>
                            <h3>All Stock Levels Healthy</h3>
                            <p>No medicines are running low on stock</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Expiring Items -->
<div class="alert-section">
    <div class="section-header">
        <span class="section-title">
            <i class="bi bi-calendar-x"></i> Expiring Items
        </span>
        <span class="section-badge">{{ count($expiringItems) }}</span>
    </div>
    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Batch No.</th>
                    <th>Expiry Date</th>
                    <th>Days Remaining</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expiringItems as $item)
                <tr>
                    <td>
                        <div class="medicine-name">{{ $item['name'] }}</div>
                    </td>
                    <td>{{ $item['batch_number'] ?? 'N/A' }}</td>
                    <td>{{ $item['expiry_formatted'] }}</td>
                    <td>
                        @if($item['days_until_expiry'] < 0)
                            <span class="days-badge expired">
                                <i class="bi bi-x-circle-fill"></i> {{ abs($item['days_until_expiry']) }} days ago
                            </span>
                        @elseif($item['days_until_expiry'] <= 7)
                            <span class="days-badge critical">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $item['days_until_expiry'] }} days
                            </span>
                        @else
                            <span class="days-badge warning">
                                <i class="bi bi-clock-fill"></i> {{ $item['days_until_expiry'] }} days
                            </span>
                        @endif
                    </td>
                    <td><span class="stock-number medium">{{ $item['quantity'] }}</span></td>
                    <td>
                        @if($item['status'] === 'expired')
                            <span class="status-badge expired">
                                <i class="bi bi-x-circle-fill"></i> Expired
                            </span>
                        @elseif($item['status'] === 'expiring_soon')
                            <span class="status-badge expiring-soon">
                                <i class="bi bi-exclamation-triangle-fill"></i> Expiring Soon
                            </span>
                        @else
                            <span class="status-badge expiring">
                                <i class="bi bi-clock-fill"></i> Expiring
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-check-circle-fill"></i>
                            <h3>No Expiring Items</h3>
                            <p>No medicines are expiring in the next 60 days</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Alert Settings -->
<div class="alert-section">
    <div class="section-header">
        <span class="section-title">
            <i class="bi bi-gear-fill"></i> Alert Settings
        </span>
    </div>
    <div class="settings-grid">
        <div class="setting-item">
            <input type="checkbox" class="setting-checkbox" id="emailAlerts" checked>
            <label for="emailAlerts" class="setting-label">
                <div>Send daily email alerts for low stock</div>
                <div class="setting-description">Receive notifications when medicines run low</div>
            </label>
        </div>
        <div class="setting-item">
            <input type="checkbox" class="setting-checkbox" id="dashboardAlerts" checked>
            <label for="dashboardAlerts" class="setting-label">
                <div>Show alerts on dashboard</div>
                <div class="setting-description">Display inventory warnings on main dashboard</div>
            </label>
        </div>
        <div class="setting-item">
            <input type="checkbox" class="setting-checkbox" id="expiryAlerts" checked>
            <label for="expiryAlerts" class="setting-label">
                <div>Alert 30 days before medicine expiry</div>
                <div class="setting-description">Get notified before medicines expire</div>
            </label>
        </div>
        <div class="setting-item">
            <input type="checkbox" class="setting-checkbox" id="criticalOnly">
            <label for="criticalOnly" class="setting-label">
                <div>Only notify for critical levels</div>
                <div class="setting-description">Reduce alerts to only critical situations</div>
            </label>
        </div>
    </div>
</div>
@endsection
