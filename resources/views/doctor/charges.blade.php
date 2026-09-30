@extends('layouts.app')

@section('title', 'Service Charges - ClinicFlow')

@section('content')
<div class="page-header">
    <div class="page-title-section">
        <h1><i class="bi bi-lightning-charge"></i> Service Charges</h1>
        <p class="page-subtitle">Manage consultation fees and service charges</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addChargeModal">
            <i class="bi bi-plus-circle"></i> Add New Charge
        </button>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon black"><i class="bi bi-lightning-charge-fill"></i></div>
        <div class="stat-content">
            <div class="stat-value">{{ count($charges) }}</div>
            <div class="stat-label">Total Services</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="bi bi-check-circle-fill"></i></div>
        <div class="stat-content">
            <div class="stat-value">{{ collect($charges)->where('is_active', true)->count() }}</div>
            <div class="stat-label">Active Services</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><i class="bi bi-pause-circle-fill"></i></div>
        <div class="stat-content">
            <div class="stat-value">{{ collect($charges)->where('is_active', false)->count() }}</div>
            <div class="stat-label">Inactive Services</div>
        </div>
    </div>
</div>

<!-- Charges Table -->
<div class="card">
    <div class="card-header">
        <div><i class="bi bi-list-ul"></i> All Service Charges</div>
    </div>
    <div class="card-body p-0">
        @if(count($charges) > 0)
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Service Name</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($charges as $index => $charge)
                    <tr>
                        <td class="text-muted fw-semibold">{{ $index + 1 }}</td>
                        <td><span class="fw-semibold text-gray-900">{{ $charge['title'] }}</span></td>
                        <td>
                            <span class="badge badge-success fs-6"><i class="bi bi-currency-rupee"></i>{{
                                number_format($charge['price'], 2) }}</span>
                        </td>
                        <td>
                            @if($charge['is_active'])
                            <span class="badge badge-success"><i class="bi bi-circle-fill"
                                    style="font-size:0.5rem; margin-right:4px;"></i> Active</span>
                            @else
                            <span class="badge badge-secondary"><i class="bi bi-circle-fill"
                                    style="font-size:0.5rem; margin-right:4px;"></i> Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-sm btn-outline-secondary edit-charge-btn"
                                    data-id="{{ $charge['id'] }}" data-title="{{ $charge['title'] }}"
                                    data-price="{{ $charge['price'] }}"
                                    data-active="{{ $charge['is_active'] ? '1' : '0' }}" data-bs-toggle="modal"
                                    data-bs-target="#editChargeModal">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger delete-charge-btn" data-id="{{ $charge['id'] }}"
                                    data-title="{{ $charge['title'] }}">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-lightning-charge"></i>
            </div>
            <h5>No service charges found</h5>
            <p>Click "Add New Charge" to create your first service charge</p>
            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addChargeModal">
                <i class="bi bi-plus-circle"></i> Add New Charge
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Add Charge Modal -->
<div class="modal fade" id="addChargeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Add New Service Charge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addChargeForm" method="POST" action="{{ route('doctor.charges.create') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_title" class="form-label">Service Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="add_title" name="title" required
                            placeholder="e.g., General Consultation">
                    </div>
                    <div class="form-group mb-0">
                        <label for="add_price" class="form-label">Price <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-currency-rupee"></i></span>
                            <input type="number" class="form-control" id="add_price" name="price" step="0.01" min="0.01"
                                required placeholder="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Charge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Charge Modal -->
<div class="modal fade" id="editChargeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Service Charge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editChargeForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_charge_id" name="charge_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_title" class="form-label">Service Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_price" class="form-label">Price <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-currency-rupee"></i></span>
                            <input type="number" class="form-control" id="edit_price" name="price" step="0.01"
                                min="0.01" required>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <div class="form-check form-switch rounded"
                            style="background: var(--gray-50); padding: 0.75rem 1rem 0.75rem 3rem; border: 1px solid var(--gray-200);">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active"
                                value="1" style="transform: scale(1.2);">
                            <label class="form-check-label fw-semibold ms-2" for="edit_is_active">Active Service</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Charge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Edit charge button handler
    document.querySelectorAll('.edit-charge-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const title = this.dataset.title;
            const price = this.dataset.price;
            const active = this.dataset.active === '1';

            document.getElementById('edit_charge_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_is_active').checked = active;

            // Update form action
            document.getElementById('editChargeForm').action = '{{ route("doctor.charges.update", "") }}/' + id;
        });
    });

    // Delete charge button handler
    document.querySelectorAll('.delete-charge-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const title = this.dataset.title;

            if (confirm(`Are you sure you want to delete "${title}"?`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("doctor.charges.delete", "") }}/' + id;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Form validation
    document.getElementById('addChargeForm').addEventListener('submit', function (e) {
        const price = parseFloat(document.getElementById('add_price').value);
        if (isNaN(price) || price < 0.01) {
            e.preventDefault();
            alert('Please enter a valid price (minimum ₹0.01)');
        }
    });

    document.getElementById('editChargeForm').addEventListener('submit', function (e) {
        const price = parseFloat(document.getElementById('edit_price').value);
        if (isNaN(price) || price < 0.01) {
            e.preventDefault();
            alert('Please enter a valid price (minimum ₹0.01)');
        }
    });
</script>
@endsection