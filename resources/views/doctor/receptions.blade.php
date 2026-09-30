@extends('layouts.app')

@section('title', 'Reception Staff - ClinicFlow')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="page-title-section">
        <h1><i class="bi bi-people"></i> Reception Staff</h1>
        <p class="page-subtitle">Manage your clinic's reception team members</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReceptionModal">
            <i class="bi bi-plus-circle"></i> Add Staff
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon black">
            <i class="bi bi-people-fill"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ count($receptions) }}</div>
            <div class="stat-label">Total Staff</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ count($receptions) }}</div>
            <div class="stat-label">Active</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="bi bi-clock-history"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $receptions->where('created_at', '>=', now()->startOfMonth())->count() }}
            </div>
            <div class="stat-label">Created This Month</div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header">
        <div><i class="bi bi-list-ul"></i> All Reception Staff</div>
    </div>
    <div class="card-body p-0">
        @if(count($receptions) > 0)
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receptions as $reception)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar"
                                    style="background: var(--gray-100); color: var(--gray-800); border: 1px solid var(--gray-200);">
                                    {{ strtoupper(substr($reception->name, 0, 1)) }}
                                </div>
                                <span style="color: var(--gray-900); font-weight: 600;">{{ $reception->name }}</span>
                            </div>
                        </td>
                        <td>{{ $reception->email }}</td>
                        <td>{{ $reception->created_at ? $reception->created_at->format('M d, Y') : 'N/A' }}</td>
                        <td class="text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="editReception({{ $reception->id }})">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteReception({{ $reception->id }})">
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
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-people"></i>
            </div>
            <h5>No reception staff added yet</h5>
            <p>Click the "Add Staff" button to add your first team member</p>
            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addReceptionModal">
                <i class="bi bi-plus-circle"></i> Add Staff
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addReceptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus" style="color: var(--primary-black);"></i> Add
                    Reception Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('doctor.receptions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Full Name <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter full name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" required
                            placeholder="reception@clinic.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password <span class="required">*</span></label>
                        <input type="password" name="password" class="form-control" required
                            placeholder="Enter password" minlength="8">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Confirm Password <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required
                            placeholder="Confirm password" minlength="8">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle"></i> Create Staff Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editReceptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square" style="color: var(--primary-black);"></i> Edit
                    Reception Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editReceptionForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Full Name <span class="required">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" id="edit_password" class="form-control"
                            placeholder="Leave blank to keep current password">
                        <div class="form-text">Only fill if you want to change the password</div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="edit_password_confirmation"
                            class="form-control" placeholder="Confirm new password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="deleteReceptionForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    // Store reception data for editing
    const receptionData = @json($receptions -> keyBy('id'));

    function editReception(id) {
        const reception = receptionData[id];
        if (!reception) return;

        document.getElementById('edit_name').value = reception.name;
        document.getElementById('edit_email').value = reception.email;
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_password_confirmation').value = '';
        document.getElementById('editReceptionForm').action = `/doctor/receptions/${id}`;

        bootstrap.Modal.getOrCreateInstance(document.getElementById('editReceptionModal')).show();
    }

    function deleteReception(id) {
        if (confirm('Are you sure you want to delete this staff member? This action cannot be undone.')) {
            const form = document.getElementById('deleteReceptionForm');
            form.action = `/doctor/receptions/${id}`;
            form.submit();
        }
    }
</script>
@endsection