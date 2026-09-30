@extends('layouts.app')

@section('title', 'Pharmacy - Doctor - ClinicFlow')

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
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .pharmacy-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .page-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
    }

    .page-sub {
        color: var(--color-gray-500);
        font-size: 0.75rem;
        margin: 0;
    }

    /* Pill-style Tabs */
    .tabs {
        display: flex;
        gap: 0.5rem;
        background: var(--color-gray-100);
        padding: 0.25rem;
        border-radius: 10px;
        margin-bottom: 1.25rem;
    }

    .tab-btn {
        padding: 0.625rem 1rem;
        background: transparent;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--color-gray-600);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .tab-btn:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .tab-btn.active {
        background: var(--color-white);
        color: var(--color-black);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Modern Table */
    .table-container {
        background: var(--color-white);
        border: 1px solid var(--color-gray-200);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .medicine-table {
        width: 100%;
    }

    .medicine-table thead th {
        background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-white) 100%);
        padding: 0.875rem 1rem;
        border-bottom: 2px solid var(--color-gray-200);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--color-gray-600);
        letter-spacing: 0.5px;
    }

    .medicine-table tbody td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid var(--color-gray-100);
        font-size: 0.8rem;
    }

    .medicine-table tbody tr:hover {
        background: var(--color-gray-50);
    }

    .medicine-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Action Buttons */
    .action-btns {
        display: flex;
        gap: 0.375rem;
    }

    .action-btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        border: none;
        cursor: pointer;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .action-btn.edit {
        background: #dbeafe;
        color: #1e40af;
    }

    .action-btn.edit:hover {
        background: #bfdbfe;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
    }

    .action-btn.stock {
        background: #fef3c7;
        color: #92400e;
    }

    .action-btn.stock:hover {
        background: #fde68a;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(245, 158, 11, 0.2);
    }

    .action-btn.delete {
        background: #fee2e2;
        color: #991b1b;
    }

    .action-btn.delete:hover {
        background: #fecaca;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 38, 38, 0.2);
    }

    /* Search Box */
    .search-box {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }

    .search-box input {
        flex: 1;
        max-width: 400px;
        padding: 0.75rem 1rem;
        border: 1px solid var(--color-gray-200);
        background: var(--color-white);
        color: var(--color-black);
        border-radius: 10px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .search-box input:focus {
        outline: none;
        border-color: var(--color-black);
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.625rem;
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.low {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }

    .status-badge.ok {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #15803d;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--color-gray-400);
        background: var(--color-gray-50);
        border-radius: 14px;
        border: 1px solid var(--color-gray-200);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    /* Custom Modal Overlay */
    .med-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        /* pointer-events: none so the overlay doesn't block clicks on the dialog */
        pointer-events: none;
        align-items: center;
        justify-content: center;
        padding: 4rem 1rem;
    }

    .med-modal-overlay.show {
        display: flex;
    }

    /* The backdrop is a separate full-screen layer behind the dialog */
    .med-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 9998;
        pointer-events: auto;
        cursor: pointer;
    }

    .med-modal-overlay .modal-dialog {
        background: white;
        border-radius: 14px;
        width: 100%;
        max-width: 520px;
        max-height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        margin: auto;
        position: relative;
        z-index: 10000;
        /* Re-enable pointer-events on the dialog itself */
        pointer-events: auto;
    }
    .med-modal-overlay .modal-dialog .modal-header {
        flex-shrink: 0;
    }
    .med-modal-overlay .modal-dialog .modal-body {
        flex: 1;
        overflow-y: auto;
    }
    .med-modal-overlay .modal-dialog .modal-footer {
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<div class="pharmacy-header">
    <div>
        <h1 class="page-title"><i class="bi bi-capsule"></i> Medicines</h1>
        <p class="page-sub">Medicine inventory & management</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('doctor.inventory-alerts') }}" class="btn btn-outline-dark"
            style="padding: 0.625rem 1rem; font-size: 0.875rem; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-bell-fill"></i> Inventory Alerts
        </a>
        <button class="btn btn-dark" onclick="editMedicine(null)"
            style="padding: 0.625rem 1rem; font-size: 0.875rem; border-radius: 10px; font-weight: 600;">
            <i class="bi bi-plus-circle"></i> Add Medicine
        </button>
    </div>
</div>

<div class="tabs">
    <button class="tab-btn active" onclick="switchTab('inventory')"><i class="bi bi-boxes"></i> Inventory</button>
    <button class="tab-btn" onclick="switchTab('lowstock')"><i class="bi bi-exclamation-triangle"></i> Low
        Stock</button>
</div>

<!-- Inventory Tab -->
<div id="inventory" class="tab-content active">
    <div class="search-box">
        <input type="text" id="medicineSearch" placeholder="Search medicine by name..."
            style="flex: 1; max-width: 300px; padding: 0.5rem 0.75rem; border: 1px solid var(--color-gray-200); background: white; color: black; border-radius: 4px;">
        <button type="button" class="btn btn-dark" style="padding: 0.5rem 0.75rem; font-size: 0.75rem;"><i
                class="bi bi-search"></i></button>
    </div>

    @if($medicines->count() > 0)
    <div class="table-container">
        <table class="medicine-table">
            <thead>
                <tr>
                    <th style="width: 28%;">Medicine Name</th>
                    <th style="width: 10%;">Unit Price</th>
                    <th style="width: 10%;">Stock</th>
                    <th style="width: 10%;">Reorder</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 30%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medicines as $medicine)
                <tr>
                    <td><strong>{{ $medicine->name }}</strong></td>
                    <td>₹{{ number_format($medicine->unit_price, 2) }}</td>
                    <td><strong>{{ $medicine->stock->qty ?? 0 }}</strong></td>
                    <td>{{ $medicine->reorder_level ?? '-' }}</td>
                    <td>
                        @php
                        $current = $medicine->stock->qty ?? 0;
                        $reorder = $medicine->reorder_level ?? 0;
                        @endphp
                        @if($current <= $reorder) <span class="status-badge low"><i
                                class="bi bi-exclamation-circle-fill"></i> Low</span>
                            @else
                            <span class="status-badge ok"><i class="bi bi-check-circle-fill"></i> OK</span>
                            @endif
                    </td>
                    <td>
                        <div class="action-btns">
                            <button class="action-btn edit"
                                onclick="editMedicine({{ $medicine->id }}, '{{ $medicine->name }}', {{ $medicine->unit_price }}, {{ $medicine->reorder_level ?? 0 }})"><i
                                    class="bi bi-pencil"></i> Edit</button>
                            <button class="action-btn stock"
                                onclick="adjustStock({{ $medicine->id }}, '{{ $medicine->name }}')"><i
                                    class="bi bi-box-seam"></i> Stock</button>
                            <button class="action-btn delete"
                                onclick="deleteMedicine({{ $medicine->id }}, '{{ $medicine->name }}')"><i
                                    class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <p style="font-size: 0.875rem; font-weight: 600; color: var(--color-gray-600); margin-bottom: 0.25rem;">No
            medicines found</p>
        <p style="font-size: 0.8rem; margin: 0;">Add your first medicine to get started</p>
    </div>
    @endif
</div>

<!-- Low Stock Tab -->
<div id="lowstock" class="tab-content">
    @php
    $lowStockMedicines = $medicines->filter(function($m) {
    $current = $m->stock->qty ?? 0;
    $reorder = $m->reorder_level ?? 0;
    return $current <= $reorder; }); @endphp @if($lowStockMedicines->count() > 0)
        <table class="medicine-table">
            <thead>
                <tr>
                    <th style="width: 28%;">Medicine Name</th>
                    <th style="width: 10%;">Unit Price</th>
                    <th style="width: 10%;">Stock</th>
                    <th style="width: 10%;">Reorder</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 30%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockMedicines as $medicine)
                <tr>
                    <td><strong>{{ $medicine->name }}</strong></td>
                    <td>₹{{ number_format($medicine->unit_price, 2) }}</td>
                    <td><span style="color: #dc2626; font-weight: 600;">{{ $medicine->stock->qty ?? 0 }}</span></td>
                    <td>{{ $medicine->reorder_level ?? '-' }}</td>
                    <td>
                        <span style="color: #dc2626; font-weight: 600;"><i class="bi bi-exclamation-circle"></i>
                            Low</span>
                    </td>
                    <td style="display: flex; gap: 0.3rem;">
                        <button class="action-btn primary"
                            onclick="editMedicine({{ $medicine->id }}, '{{ $medicine->name }}', {{ $medicine->unit_price }}, {{ $medicine->reorder_level ?? 0 }})"><i
                                class="bi bi-pencil"></i> Edit</button>
                        <button class="action-btn"
                            onclick="adjustStock({{ $medicine->id }}, '{{ $medicine->name }}')"><i
                                class="bi bi-arrow-left-right"></i> Stock</button>
                        <button class="action-btn danger"
                            onclick="deleteMedicine({{ $medicine->id }}, '{{ $medicine->name }}')"><i
                                class="bi bi-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <p style="font-size: 0.875rem;">No low stock medicines</p>
        </div>
        @endif
</div>

<!-- Edit Medicine Modal -->
<div id="edit-modal" class="med-modal-overlay">
    <!-- Backdrop: clicking this closes the modal -->
    <div class="med-backdrop" onclick="closeEditModal()" id="edit-backdrop"></div>
    <div class="modal-dialog" id="edit-modal-dialog">
        <!-- Modal Header -->
        <div
            style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); color: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-radius: 12px 12px 0 0; margin: 0;">
            <h3 id="modal-title" style="margin: 0; font-size: 1.4rem; font-weight: 700;">
                <i class="bi bi-plus-circle" style="margin-right: 0.5rem;"></i>Add Medicine
            </h3>
            <button type="button" onclick="closeEditModal()"
                style="background: rgba(255,255,255,0.3); border: none; font-size: 1.8rem; cursor: pointer; color: white; padding: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 4px; transition: all 0.2s;">&times;</button>
        </div>

        <!-- Modal Content -->
        <div style="padding: 2rem; background: white;">
            <form id="medicine-form" onsubmit="submitMedicineForm(event)">
                <div class="form-group"
                    style="background: #f9fafb; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
                    <label
                        style="font-weight: 700; color: #1f2937; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Medicine
                        Name *</label>
                    <input type="text" id="medicine-name" name="name" required
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; background: white; color: black; font-size: 1rem; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div class="form-group"
                    style="background: #f9fafb; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
                    <label
                        style="font-weight: 700; color: #1f2937; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Unit
                        Price *</label>
                    <input type="number" id="medicine-price" name="unit_price" step="0.01" min="0" required
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; background: white; color: black; font-size: 1rem; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div class="form-group"
                    style="background: #f9fafb; padding: 0.75rem; border-radius: 6px; margin-bottom: 1.5rem;">
                    <label
                        style="font-weight: 700; color: #1f2937; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Reorder
                        Level</label>
                    <input type="number" id="medicine-reorder" name="reorder_level" min="0" value="10"
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; background: white; color: black; font-size: 1rem; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div
                    style="display: flex; gap: 0.75rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-dark"
                        style="flex: 1; padding: 0.75rem 1rem; font-size: 0.95rem; font-weight: 600; border-radius: 6px; background: #0d6efd; color: white; border: none; cursor: pointer; transition: all 0.2s;">
                        <i class="bi bi-check-circle" style="margin-right: 0.5rem;"></i>Save Medicine
                    </button>
                    <button type="button" class="btn btn-light"
                        style="flex: 1; padding: 0.75rem 1rem; font-size: 0.95rem; font-weight: 600; border-radius: 6px; background: #e5e7eb; color: #1f2937; border: none; cursor: pointer; transition: all 0.2s;"
                        onclick="closeEditModal()">
                        <i class="bi bi-x-circle" style="margin-right: 0.5rem;"></i>Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div id="adjust-modal" class="med-modal-overlay">
    <!-- Backdrop: clicking this closes the modal -->
    <div class="med-backdrop" onclick="closeAdjustModal()" id="adjust-backdrop"></div>
    <div class="modal-dialog" id="adjust-modal-dialog" style="max-width: 450px;">
        <!-- Modal Header -->
        <div
            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-radius: 12px 12px 0 0; margin: 0;">
            <h3 style="margin: 0; font-size: 1.4rem; font-weight: 700;">
                <i class="bi bi-arrow-left-right" style="margin-right: 0.5rem;"></i><span id="adjust-modal-title">Adjust
                    Stock</span>
            </h3>
            <button type="button" onclick="closeAdjustModal()"
                style="background: rgba(255,255,255,0.3); border: none; font-size: 1.8rem; cursor: pointer; color: white; padding: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 4px; transition: all 0.2s;">&times;</button>
        </div>

        <!-- Modal Content -->
        <div style="padding: 2rem; background: white;">
            <form id="adjust-form">
                @csrf
                <input type="hidden" id="adjust-medicine-id" name="medicine_id">
                <div class="form-group"
                    style="background: #f9fafb; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
                    <label
                        style="font-weight: 700; color: #1f2937; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Adjustment
                        Type *</label>
                    <select id="adjust-type" name="adjustment_type" required
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; background: white; color: black; font-size: 1rem; border-radius: 6px; box-sizing: border-box;">
                        <option value="add">Add Stock</option>
                        <option value="remove">Remove Stock</option>
                    </select>
                </div>
                <div class="form-group"
                    style="background: #f9fafb; padding: 0.75rem; border-radius: 6px; margin-bottom: 1.5rem;">
                    <label
                        style="font-weight: 700; color: #1f2937; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Quantity
                        *</label>
                    <input type="number" id="adjust-qty" name="quantity" min="1" required
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; background: white; color: black; font-size: 1rem; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div
                    style="display: flex; gap: 0.75rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-dark"
                        style="flex: 1; padding: 0.75rem 1rem; font-size: 0.95rem; font-weight: 600; border-radius: 6px; background: #10b981; color: white; border: none; cursor: pointer; transition: all 0.2s;">
                        <i class="bi bi-check-circle" style="margin-right: 0.5rem;"></i>Update Stock
                    </button>
                    <button type="button" onclick="closeAdjustModal()" class="btn btn-light"
                        style="flex: 1; padding: 0.75rem 1rem; font-size: 0.95rem; font-weight: 600; border-radius: 6px; background: #e5e7eb; color: #1f2937; border: none; cursor: pointer; transition: all 0.2s;">
                        <i class="bi bi-x-circle" style="margin-right: 0.5rem;"></i>Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Tab Switching
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(tabName).classList.add('active');
        event.target.classList.add('active');
    }

    // Edit / Add Medicine Modal
    function editMedicine(id, name, price, reorder) {
        const titleEl = document.getElementById('modal-title');
        const form = document.getElementById('medicine-form');

        if (id === null) {
            titleEl.innerHTML = '<i class="bi bi-plus-circle" style="margin-right:0.5rem;"></i>Add Medicine';
            form.reset();
            form.removeAttribute('data-medicine-id');
            document.getElementById('medicine-price').value = '';
            document.getElementById('medicine-reorder').value = '10';
        } else {
            titleEl.innerHTML = '<i class="bi bi-pencil" style="margin-right:0.5rem;"></i>Edit Medicine';
            document.getElementById('medicine-name').value = name || '';
            document.getElementById('medicine-price').value = price || '';
            document.getElementById('medicine-reorder').value = reorder || '10';
            form.setAttribute('data-medicine-id', id);
        }

        document.getElementById('edit-modal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('edit-modal').classList.remove('show');
        document.body.style.overflow = '';
    }

    // Adjust Stock Modal
    function adjustStock(medicineId, medicineName) {
        document.getElementById('adjust-medicine-id').value = medicineId;
        document.getElementById('adjust-modal-title').textContent = 'Adjust Stock - ' + medicineName;
        document.getElementById('adjust-form').reset();
        document.getElementById('adjust-type').value = 'add';
        document.getElementById('adjust-modal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeAdjustModal() {
        document.getElementById('adjust-modal').classList.remove('show');
        document.body.style.overflow = '';
    }

    // Delete Medicine
    function deleteMedicine(medicineId, medicineName) {
        if (!confirm('Are you sure you want to delete ' + medicineName + '?')) return;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        axios.delete('/api/medicines/' + medicineId, { headers: { 'X-CSRF-TOKEN': csrfToken } })
            .then(response => {
                if (response.data.success) {
                    window.showGlobalNotification(medicineName + ' deleted successfully!', 'success');
                    location.reload();
                }
            })
            .catch(error => {
                const message = error.response?.data?.message || 'Failed to delete medicine';
                window.showGlobalNotification(message, 'error');
            });
    }

    // Medicine Form Submit
    async function submitMedicineForm(event) {
        event.preventDefault();
        const form = document.getElementById('medicine-form');
        const medicineId = form.getAttribute('data-medicine-id');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const formData = {
            name: document.getElementById('medicine-name').value,
            unit_price: document.getElementById('medicine-price').value,
            reorder_level: document.getElementById('medicine-reorder').value,
        };
        const isEdit = !!medicineId;
        const url = isEdit ? '/api/medicines/' + medicineId : '/api/medicines';
        const method = isEdit ? 'put' : 'post';
        try {
            const response = await axios[method](url, formData, { headers: { 'X-CSRF-TOKEN': csrfToken } });
            if (response.data.success) {
                window.showGlobalNotification(isEdit ? 'Medicine updated!' : 'Medicine added!', 'success');
                closeEditModal();
                location.reload();
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to save medicine';
            window.showGlobalNotification(message, 'error');
        }
    }

    // Stock Adjust Form Submit
    document.addEventListener('DOMContentLoaded', function () {
        const adjustForm = document.getElementById('adjust-form');
        if (adjustForm) {
            adjustForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const medicineId = document.getElementById('adjust-medicine-id').value;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const data = {
                    adjustment_type: document.getElementById('adjust-type').value,
                    quantity: document.getElementById('adjust-qty').value,
                };
                try {
                    const response = await axios.post('/api/medicines/' + medicineId + '/stock', data, {
                        headers: { 'X-CSRF-TOKEN': csrfToken }
                    });
                    if (response.data.success) {
                        window.showGlobalNotification('Stock updated!', 'success');
                        closeAdjustModal();
                        location.reload();
                    }
                } catch (error) {
                    const msg = error.response?.data?.message || 'Failed to update stock';
                    window.showGlobalNotification(msg, 'error');
                }
            });
        }

        // Medicine search filter
        const searchInput = document.getElementById('medicineSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.medicine-table tbody tr').forEach(row => {
                    row.style.display = row.cells[0].textContent.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        }

        // Backdrop click handlers are inline onclick="closeEditModal()" / closeAdjustModal()
        // No JS listener needed here — the .med-backdrop divs handle it directly.
    });
</script>
@endsection