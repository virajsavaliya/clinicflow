@extends('layouts.app')

@section('title', 'Pharmacy - Reception - ClinicFlow')

@section('styles')
<style>
    /* Page Header */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .page-title { font-size: 1.25rem; font-weight: 700; margin: 0; }
    
    /* Stat Cards - Modern Design */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-box { background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .stat-box:hover { box-shadow: 0 8px 16px rgba(0,0,0,0.12); transform: translateY(-3px); border-color: #d1d5db; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .stat-icon.black { background: linear-gradient(135deg, #1a1a1a 0%, #333 100%); color: #fff; }
    .stat-icon.red { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: #fff; }
    .stat-icon.green { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: #fff; }
    .stat-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); color: #fff; }
    .stat-info { flex: 1; }
    .stat-info .value { font-size: 1.75rem; font-weight: 700; line-height: 1; color: #1a1a1a; }
    .stat-info .label { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; font-weight: 500; }
    
    /* Tabs - Modern Design */
    .view-tabs { display: flex; gap: 0.5rem; background: var(--color-gray-100); padding: 0.25rem; border-radius: 12px; border: 2px solid #e5e7eb; margin-bottom: 1.5rem; }
    .view-tab { padding: 0.625rem 1.25rem; border-radius: 10px; font-size: 0.875rem; font-weight: 700; border: 2px solid transparent; background: transparent; cursor: pointer; color: var(--color-gray-600); transition: all 0.2s ease; display: flex; align-items: center; gap: 0.5rem; }
    .view-tab.active { background: var(--color-white); color: var(--color-black); box-shadow: 0 2px 6px rgba(0,0,0,0.1); border-color: #d1d5db; }
    .view-tab:hover:not(.active) { background: rgba(255,255,255,0.6); }
    .view-tab i { font-size: 1rem; }
    
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    
    /* Search Box - Modern */
    .search-section { background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; padding: 1.25rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .search-box { display: flex; gap: 0.75rem; align-items: center; }
    .search-box input { flex: 1; padding: 0.75rem 1rem; border: 2px solid #d1d5db; border-radius: 10px; font-size: 0.9rem; background: var(--color-white); color: var(--color-black); transition: all 0.2s ease; }
    .search-box input:focus { outline: none; border-color: #1a1a1a; box-shadow: 0 0 0 4px rgba(26,26,26,0.1); }
    .search-box input::placeholder { color: #9ca3af; }
    
    /* Medicine Table - Modern Design */
    .table-container { background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .medicine-table { width: 100%; border-collapse: collapse; }
    .medicine-table thead { background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); }
    .medicine-table thead th { padding: 1rem 1.25rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #374151; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb; }
    .medicine-table tbody td { padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem; color: #1f2937; }
    .medicine-table tbody tr { transition: all 0.2s ease; }
    .medicine-table tbody tr:hover { background: #fafbfc; }
    .medicine-table tbody tr:last-child td { border-bottom: none; }
    
    /* Medicine Name Column */
    .medicine-name { font-weight: 700; color: #1a1a1a; font-size: 0.95rem; }
    .medicine-name i { color: #3b82f6; margin-right: 0.5rem; }
    
    /* Status Badges */
    .status-badge { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-badge.low { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    .status-badge.ok { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .status-badge i { font-size: 0.875rem; }
    
    /* Price Display */
    .price { font-weight: 700; color: #1a1a1a; font-size: 0.95rem; }
    
    /* Action Buttons - Modern */
    .action-buttons { display: flex; gap: 0.5rem; }
    .action-btn { padding: 0.5rem 0.875rem; font-size: 0.75rem; font-weight: 700; border-radius: 8px; border: 2px solid; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.375rem; }
    .action-btn i { font-size: 0.875rem; }
    .action-btn.edit { background: #dbeafe; color: #1e40af; border-color: #93c5fd; }
    .action-btn.edit:hover { background: #bfdbfe; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(59,130,246,0.3); }
    .action-btn.stock { background: #fef3c7; color: #92400e; border-color: #fde68a; }
    .action-btn.stock:hover { background: #fde68a; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(245,158,11,0.3); }
    .action-btn.delete { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
    .action-btn.delete:hover { background: #fecaca; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(239,68,68,0.3); }
    
    /* Medicine Cards for Low Stock */
    .medicine-cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem; }
    .medicine-card { background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; padding: 1.25rem; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-left: 4px solid #dc2626; }
    .medicine-card:hover { box-shadow: 0 8px 16px rgba(0,0,0,0.12); transform: translateY(-3px); }
    .medicine-card h6 { margin: 0 0 1rem; font-size: 1rem; font-weight: 700; color: #1a1a1a; display: flex; align-items: center; gap: 0.5rem; }
    .medicine-card h6 i { color: #dc2626; }
    .medicine-info { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.875rem; }
    .medicine-info-item { display: flex; flex-direction: column; gap: 0.25rem; }
    .medicine-info-item .label { font-size: 0.7rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .medicine-info-item .value { font-size: 0.95rem; font-weight: 700; color: #1a1a1a; }
    .medicine-info-item .value.low { color: #dc2626; }
    .medicine-info-item .value.ok { color: #16a34a; }
    .medicine-card .restock-btn { width: 100%; margin-top: 1rem; padding: 0.75rem; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
    .medicine-card .restock-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(34,197,94,0.3); }
    
    /* Empty State */
    .empty-state { padding: 4rem 2rem; text-align: center; }
    .empty-state-icon { width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
    .empty-state-icon i { font-size: 2rem; color: #9ca3af; }
    .empty-state h5 { font-size: 1.125rem; font-weight: 700; color: #374151; margin-bottom: 0.5rem; }
    .empty-state p { font-size: 0.875rem; color: #6b7280; margin-bottom: 1.5rem; }
    
    /* Button Styles */
    .btn { padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 700; border-radius: 10px; border: 2px solid transparent; transition: all 0.2s ease; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .btn-dark { background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: #ffffff; border-color: #1a1a1a; }
    .btn-dark:hover { background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%); border-color: #000000; box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
    
    /* Dispense Form */
    .dispense-container { background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .dispense-container h5 { margin: 0 0 1.5rem; font-size: 1.125rem; font-weight: 700; color: #1a1a1a; display: flex; align-items: center; gap: 0.5rem; }
    .dispense-container h5 i { color: #3b82f6; }
    .form-group { margin-bottom: 1.25rem; }
    .form-group label { display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #374151; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem 1rem; border: 2px solid #d1d5db; background: #ffffff; color: #000000; font-size: 0.9rem; border-radius: 10px; transition: all 0.2s ease; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #1a1a1a; box-shadow: 0 0 0 4px rgba(26,26,26,0.1); }
    .form-group input::placeholder, .form-group textarea::placeholder { color: #9ca3af; }
    
    /* Autocomplete */
    .autocomplete-container { position: relative; }
    .autocomplete-results { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 2px solid #d1d5db; border-top: none; border-radius: 0 0 10px 10px; max-height: 200px; overflow-y: auto; display: none; z-index: 10; box-shadow: 0 4px 12px rgba(0,0,0,0.15); margin-top: -2px; }
    .autocomplete-results.show { display: block; }
    .autocomplete-item { padding: 0.75rem 1rem; cursor: pointer; font-size: 0.875rem; border-bottom: 1px solid #f3f4f6; transition: all 0.2s ease; }
    .autocomplete-item:hover { background: #f9fafb; padding-left: 1.25rem; }
    .autocomplete-item:last-child { border-bottom: none; }
    
    /* Modal Backdrop */
    .custom-modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 9998; visibility: hidden; opacity: 0; transition: all 0.3s ease; pointer-events: none; display: flex; align-items: center; justify-content: center; padding: 4rem 1rem; }
    .custom-modal-backdrop.show { visibility: visible; opacity: 1; pointer-events: auto; }
    
    /* Modal Dialog */
    .custom-modal-dialog { background: white; width: 100%; max-width: 500px; max-height: 100%; display: flex; flex-direction: column; overflow: hidden; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); z-index: 9999; transform: scale(0.9); transition: transform 0.3s ease; border: 3px solid #e5e7eb; margin: auto; }
    .custom-modal-backdrop.show .custom-modal-dialog { transform: scale(1); }
    
    /* Modal Header */
    .custom-modal-header { flex-shrink: 0; background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-radius: 13px 13px 0 0; }
    .custom-modal-header h3 { margin: 0; font-size: 1.25rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; }
    .custom-modal-header .close-btn { background: rgba(255,255,255,0.2); border: none; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: white; font-size: 1.5rem; transition: all 0.2s ease; }
    .custom-modal-header .close-btn:hover { background: rgba(255,255,255,0.35); transform: rotate(90deg); }
    
    /* Modal Body */
    .custom-modal-body { flex: 1; overflow-y: auto; padding: 1.5rem; }
    .custom-modal-footer { padding: 1.25rem 1.5rem; background: #f9fafb; border-top: 2px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 0.75rem; border-radius: 0 0 13px 13px; }
    
    @media (max-width: 1024px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .stats-row { grid-template-columns: 1fr; } .medicine-cards-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-capsule-pill"></i> Pharmacy</h1>
        <p style="color: var(--color-gray-500); font-size: 0.8rem; margin: 0.25rem 0 0;">Medicine inventory & management</p>
    </div>
    <button class="btn btn-dark" onclick="editMedicine(null)">
        <i class="bi bi-plus-circle"></i> Add Medicine
    </button>
</div>

<!-- Stat Cards -->
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-icon black">
            <i class="bi bi-capsule"></i>
        </div>
        <div class="stat-info">
            <div class="value">{{ $medicines->count() }}</div>
            <div class="label">Total Medicines</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon red">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <div class="value">{{ $lowStockMedicines->count() }}</div>
            <div class="label">Low Stock Items</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon green">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="stat-info">
            <div class="value">{{ $medicines->count() - $lowStockMedicines->count() }}</div>
            <div class="label">In Stock</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon blue">
            <i class="bi bi-box-seam"></i>
        </div>
        <div class="stat-info">
            <div class="value">{{ $medicines->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
            <div class="label">Added This Month</div>
        </div>
    </div>
</div>

<div class="view-tabs">
    <button class="view-tab active" onclick="switchTab('inventory')">
        <i class="bi bi-boxes"></i> Inventory
    </button>
    <button class="view-tab" onclick="switchTab('lowstock')">
        <i class="bi bi-exclamation-triangle"></i> Low Stock
    </button>
    <button class="view-tab" onclick="switchTab('dispense')">
        <i class="bi bi-capsule"></i> Dispense
    </button>
</div>

<!-- Inventory Tab -->
<div id="inventory" class="tab-content active">
    <div class="search-section">
        <form method="GET" action="{{ route('reception.pharmacy') }}" class="search-box">
            <input type="text" name="search" placeholder="Search medicine by name..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-dark">
                <i class="bi bi-search"></i> Search
            </button>
        </form>
    </div>

    @if($medicines->count() > 0)
        <div class="table-container">
            <table class="medicine-table">
                <thead>
                    <tr>
                        <th style="width: 32%;">Medicine Name</th>
                        <th style="width: 12%;">Unit Price</th>
                        <th style="width: 10%;">Stock</th>
                        <th style="width: 12%;">Reorder Level</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 22%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicines as $medicine)
                    <tr>
                        <td>
                            <div class="medicine-name">
                                <i class="bi bi-capsule-pill"></i>{{ $medicine->name }}
                            </div>
                        </td>
                        <td><span class="price">₹{{ number_format($medicine->unit_price, 2) }}</span></td>
                        <td><strong>{{ $medicine->stock->qty ?? 0 }}</strong></td>
                        <td>{{ $medicine->reorder_level ?? '-' }}</td>
                        <td>
                            @php
                                $current = $medicine->stock->qty ?? 0;
                                $reorder = $medicine->reorder_level ?? 0;
                            @endphp
                            @if($current <= $reorder)
                                <span class="status-badge low">
                                    <i class="bi bi-exclamation-circle"></i> Low Stock
                                </span>
                            @else
                                <span class="status-badge ok">
                                    <i class="bi bi-check-circle"></i> In Stock
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn edit" onclick="editMedicine({{ $medicine->id }}, '{{ $medicine->name }}', {{ $medicine->unit_price }}, {{ $medicine->reorder_level ?? 0 }})">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="action-btn stock" onclick="adjustStock({{ $medicine->id }}, '{{ $medicine->name }}')">
                                    <i class="bi bi-arrow-left-right"></i> Stock
                                </button>
                                <button class="action-btn delete" onclick="deleteMedicine({{ $medicine->id }}, '{{ $medicine->name }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="table-container">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-box"></i>
                </div>
                <h5>No medicines found</h5>
                <p>{{ request('search') ? 'Try a different search term' : 'Click "Add Medicine" to get started' }}</p>
            </div>
        </div>
    @endif
</div>

<!-- Low Stock Tab -->
<div id="lowstock" class="tab-content">
    @if($lowStockMedicines->count() > 0)
        <div class="medicine-cards-grid">
            @foreach($lowStockMedicines as $medicine)
            <div class="medicine-card">
                <h6>
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $medicine->name }}
                </h6>
                <div class="medicine-info">
                    <div class="medicine-info-item">
                        <span class="label">Current Stock</span>
                        <span class="value low">{{ $medicine->stock->qty ?? 0 }}</span>
                    </div>
                    <div class="medicine-info-item">
                        <span class="label">Unit Price</span>
                        <span class="value">₹{{ number_format($medicine->unit_price, 2) }}</span>
                    </div>
                    <div class="medicine-info-item">
                        <span class="label">Reorder Level</span>
                        <span class="value">{{ $medicine->reorder_level ?? '-' }}</span>
                    </div>
                    <div class="medicine-info-item">
                        <span class="label">Required</span>
                        <span class="value">{{ ($medicine->reorder_level ?? 0) - ($medicine->stock->qty ?? 0) > 0 ? ($medicine->reorder_level ?? 0) - ($medicine->stock->qty ?? 0) : 0 }}</span>
                    </div>
                </div>
                <button class="restock-btn" onclick="adjustStock({{ $medicine->id }}, '{{ $medicine->name }}')">
                    <i class="bi bi-plus-circle"></i> Restock Now
                </button>
            </div>
            @endforeach
        </div>
    @else
        <div class="table-container">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-check-circle" style="color: #22c55e;"></i>
                </div>
                <h5>All medicines in stock</h5>
                <p>No medicines below reorder level</p>
            </div>
        </div>
    @endif
</div>

<!-- Dispense Tab -->
<div id="dispense" class="tab-content">
    <div class="dispense-container">
        <h5>
            <i class="bi bi-capsule"></i>
            Dispense Medicine
        </h5>
        <form id="dispense-form">
            @csrf
            <div class="form-group autocomplete-container">
                <label><i class="bi bi-search"></i> Medicine Name *</label>
                <input type="text" id="medicine-search" placeholder="Start typing medicine name..." required autocomplete="off">
                <input type="hidden" id="dispense-medicine" name="medicine_id">
                <div id="medicine-autocomplete" class="autocomplete-results"></div>
            </div>
            <div class="form-group">
                <label><i class="bi bi-123"></i> Quantity *</label>
                <input type="number" id="dispense-qty" name="quantity" min="1" required placeholder="Enter quantity">
            </div>
            <div class="form-group">
                <label><i class="bi bi-sticky"></i> Notes (Optional)</label>
                <textarea id="dispense-notes" name="notes" rows="3" placeholder="Dosage, instructions, or any special notes..."></textarea>
            </div>
            <button type="submit" class="btn btn-dark">
                <i class="bi bi-check-circle"></i> Dispense Medicine
            </button>
        </form>
    </div>
</div>

<!-- Edit Medicine Modal -->
<div id="edit-modal" class="custom-modal-backdrop">
    <div class="custom-modal-dialog">
        <div class="custom-modal-header">
            <h3 id="modal-title">
                <i class="bi bi-plus-circle"></i>
                <span id="modal-title-text">Add Medicine</span>
            </h3>
            <button class="close-btn" onclick="closeEditModal()">&times;</button>
        </div>
        
        <div class="custom-modal-body">
            <form id="medicine-form" onsubmit="submitMedicineForm(event)">
                <div class="form-group">
                    <label><i class="bi bi-capsule-pill"></i> Medicine Name *</label>
                    <input type="text" id="medicine-name" name="name" required placeholder="Enter medicine name">
                </div>
                <div class="form-group">
                    <label><i class="bi bi-currency-rupee"></i> Unit Price *</label>
                    <input type="number" id="medicine-price" name="unit_price" step="0.01" min="0" required placeholder="0.00">
                </div>
                <div class="form-group">
                    <label><i class="bi bi-arrow-repeat"></i> Reorder Level</label>
                    <input type="number" id="medicine-reorder" name="reorder_level" min="0" value="10" placeholder="Minimum stock level">
                </div>
            </form>
        </div>
        
        <div class="custom-modal-footer">
            <button type="button" class="btn" style="background: #e5e7eb; color: #1f2937; border-color: #d1d5db;" onclick="closeEditModal()">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
            <button type="submit" form="medicine-form" class="btn btn-dark">
                <i class="bi bi-check-circle"></i> Save Medicine
            </button>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div id="adjust-modal" class="custom-modal-backdrop">
    <div class="custom-modal-dialog" style="max-width: 450px;">
        <div class="custom-modal-header" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
            <h3>
                <i class="bi bi-arrow-left-right"></i>
                <span id="adjust-modal-title">Adjust Stock</span>
            </h3>
            <button class="close-btn" onclick="closeAdjustModal()">&times;</button>
        </div>
        
        <div class="custom-modal-body">
            <form id="adjust-form">
                @csrf
                <input type="hidden" id="adjust-medicine-id" name="medicine_id">
                <div class="form-group">
                    <label><i class="bi bi-arrow-down-up"></i> Adjustment Type *</label>
                    <select id="adjust-type" name="adjustment_type" required>
                        <option value="add">Add Stock (+)</option>
                        <option value="remove">Remove Stock (-)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="bi bi-123"></i> Quantity *</label>
                    <input type="number" id="adjust-qty" name="quantity" min="1" required placeholder="Enter quantity">
                </div>
            </form>
        </div>
        
        <div class="custom-modal-footer">
            <button type="button" class="btn" style="background: #e5e7eb; color: #1f2937; border-color: #d1d5db;" onclick="closeAdjustModal()">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
            <button type="submit" form="adjust-form" class="btn" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; border-color: #22c55e;">
                <i class="bi bi-check-circle"></i> Update Stock
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentMedicineId = null;

function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.view-tab').forEach(btn => btn.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
}

function editMedicine(id, name, price, reorder) {
    currentMedicineId = id;
    if (id) {
        document.getElementById('modal-title-text').textContent = 'Edit Medicine';
        document.getElementById('modal-title').querySelector('i').className = 'bi bi-pencil';
        document.getElementById('medicine-name').value = name || '';
        document.getElementById('medicine-price').value = price || '';
        document.getElementById('medicine-reorder').value = reorder || 10;
    } else {
        document.getElementById('modal-title-text').textContent = 'Add Medicine';
        document.getElementById('modal-title').querySelector('i').className = 'bi bi-plus-circle';
        document.getElementById('medicine-form').reset();
    }
    document.getElementById('edit-modal').classList.add('show');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.remove('show');
}

async function submitMedicineForm(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('medicine-form'));
    const data = Object.fromEntries(formData);
    
    try {
        let response;
        if (currentMedicineId) {
            response = await axios.put(`/api/medicines/${currentMedicineId}`, data, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        } else {
            response = await axios.post('/api/medicines', data, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        }
        if (response.data.success) {
            await fetchMedicines();
            closeEditModal();
            window.showGlobalNotification('Medicine saved successfully', 'success');
        }
    } catch (error) {
        window.showGlobalNotification('Error: ' + (error.response?.data?.message || 'Please try again'), 'error');
    }
}

async function deleteMedicine(id, name) {
    if (!confirm(`Delete medicine "${name}"? This cannot be undone.`)) return;
    try {
        const response = await axios.delete(`/api/medicines/${id}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        if (response.data.success) {
            window.showGlobalNotification('Medicine deleted', 'success');
            setTimeout(() => location.reload(), 500);
        }
    } catch (error) {
        window.showGlobalNotification('Error: ' + (error.response?.data?.message || 'Please try again'), 'error');
    }
}

function adjustStock(medicineId, medicineName) {
    document.getElementById('adjust-medicine-id').value = medicineId;
    document.getElementById('adjust-modal-title').textContent = medicineName;
    document.getElementById('adjust-modal').classList.add('show');
}

function closeAdjustModal() {
    document.getElementById('adjust-modal').classList.remove('show');
}

document.getElementById('adjust-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const medicineId = document.getElementById('adjust-medicine-id').value;
    const qty = parseInt(document.getElementById('adjust-qty').value);
    const type = document.getElementById('adjust-type').value;
    
    if (!medicineId || !qty || !type) {
        window.showGlobalNotification('Please fill all fields', 'warning');
        return;
    }
    
    try {
        const response = await axios.post('{{ route("api.medicines.adjust-stock") }}', {
            medicine_id: medicineId,
            qty: qty,
            type: type
        }, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        if (response.data.success) {
            closeAdjustModal();
            document.getElementById('adjust-form').reset();
            window.showGlobalNotification('Stock adjusted successfully', 'success');
            // Reload page to reflect changes
            setTimeout(() => location.reload(), 500);
        }
    } catch (error) {
        const errorMsg = error.response?.data?.message || error.message || 'Failed to adjust stock';
        window.showGlobalNotification('Error: ' + errorMsg, 'error');
        console.error('Stock adjustment error:', error.response?.data);
    }
});

document.getElementById('dispense-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const medicineId = document.getElementById('dispense-medicine').value;
    const qty = parseInt(document.getElementById('dispense-qty').value);
    
    if (!medicineId) {
        alert('Please select a medicine');
        return;
    }
    
    try {
        const response = await axios.post('/api/medicines/adjust-stock', {
            medicine_id: medicineId,
            qty: qty,
            type: 'remove',
            notes: document.getElementById('dispense-notes').value || 'Dispensed from pharmacy'
        }, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        if (response.data.success) {
            window.showGlobalNotification('Medicine dispensed successfully', 'success');
            document.getElementById('dispense-form').reset();
            location.reload();
        }
    } catch (error) {
        window.showGlobalNotification('Error: ' + (error.response?.data?.message || 'Try again'), 'error');
    }
});

// Fetch and render medicines (for real-time UI update)
async function fetchMedicines() {
    try {
        const res = await axios.get('/api/medicines');
        if (res.data && Array.isArray(res.data.medicines)) {
            renderMedicines(res.data.medicines);
        }
    } catch (e) {}
}

function renderMedicines(medicines) {
    // Inventory table
    const tbody = document.querySelector('.medicine-table tbody');
    if (tbody) {
        tbody.innerHTML = medicines.map(medicine => `
            <tr>
                <td><strong>${medicine.name}</strong></td>
                <td>₹${parseFloat(medicine.unit_price).toFixed(2)}</td>
                <td>${medicine.stock?.qty ?? 0}</td>
                <td>${medicine.reorder_level ?? '-'}</td>
                <td>${(medicine.stock?.qty ?? 0) <= (medicine.reorder_level ?? 0) ? '<span style="color: #dc2626; font-weight: 600;"><i class="bi bi-exclamation-circle"></i> Low</span>' : '<span style="color: #16a34a; font-weight: 600;"><i class="bi bi-check-circle"></i> OK</span>'}</td>
                <td style="display: flex; gap: 0.3rem;">
                    <button class="action-btn primary" onclick="editMedicine(${medicine.id}, '${medicine.name}', ${medicine.unit_price}, ${medicine.reorder_level ?? 0})"><i class="bi bi-pencil"></i> Edit</button>
                    <button class="action-btn" onclick="adjustStock(${medicine.id}, '${medicine.name}')"><i class="bi bi-arrow-left-right"></i> Stock</button>
                    <button class="action-btn danger" onclick="deleteMedicine(${medicine.id}, '${medicine.name}')"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `).join('');
    }
    // Update autocomplete data
    medicinesData.length = 0;
    medicines.forEach(m => medicinesData.push({id: m.id, name: m.name, stock: m.stock?.qty ?? 0}));
}

// Medicine autocomplete
const medicineSearch = document.getElementById('medicine-search');
const medicineAutocomplete = document.getElementById('medicine-autocomplete');
const dispenseMedicineInput = document.getElementById('dispense-medicine');

// Get all medicines data
const medicinesData = @json($medicines->map(function($m) { return ['id' => $m->id, 'name' => $m->name, 'stock' => $m->stock->qty ?? 0]; }));

medicineSearch.addEventListener('input', function() {
    const query = this.value.trim().toLowerCase();
    
    if (query.length < 2) {
        medicineAutocomplete.classList.remove('show');
        return;
    }
    
    const results = medicinesData.filter(m => m.name.toLowerCase().includes(query));
    
    if (results.length > 0) {
        medicineAutocomplete.innerHTML = results.map(medicine => `
            <div class="autocomplete-item" onclick="selectMedicine(${medicine.id}, '${medicine.name}', ${medicine.stock})">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="color: #1a1a1a;">${medicine.name}</strong>
                    <span style="color: ${medicine.stock > 0 ? '#16a34a' : '#dc2626'}; font-weight: 600; font-size: 0.8rem;">
                        Stock: ${medicine.stock}
                    </span>
                </div>
            </div>
        `).join('');
        medicineAutocomplete.classList.add('show');
    } else {
        medicineAutocomplete.innerHTML = '<div class="autocomplete-item" style="color: #9ca3af; cursor: default;">No medicines found</div>';
        medicineAutocomplete.classList.add('show');
    }
});

function selectMedicine(id, name, stock) {
    medicineSearch.value = name;
    dispenseMedicineInput.value = id;
    medicineAutocomplete.classList.remove('show');
    document.getElementById('dispense-qty').focus();
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('#medicine-search') && !e.target.closest('#medicine-autocomplete')) {
        medicineAutocomplete.classList.remove('show');
    }
});

// Close modals when clicking on the backdrop (outside modal content)
document.getElementById('edit-modal')?.addEventListener('click', function(e) {
    // Only close if clicking directly on the backdrop, not on modal-dialog or its children
    if (e.target === this) {
        closeEditModal();
    }
});

document.getElementById('adjust-modal')?.addEventListener('click', function(e) {
    // Only close if clicking directly on the backdrop, not on modal-dialog or its children
    if (e.target === this) {
        closeAdjustModal();
    }
});
</script>
@endsection
