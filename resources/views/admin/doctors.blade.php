@extends('layouts.app')

@section('title', 'Manage Doctors')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-person-badge"></i> Manage Doctors</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Doctor
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Specialization</th>
                            <th>License#</th>
                            <th>Phone</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($doctors as $doctor)
                        <tr>
                            <td>
                                <strong>{{ $doctor->name }}</strong>
                            </td>
                            <td>{{ $doctor->email }}</td>
                            <td>{{ $doctor->doctorProfile->specialization ?? 'N/A' }}</td>
                            <td>{{ $doctor->doctorProfile->license_number ?? 'N/A' }}</td>
                            <td>{{ $doctor->doctorProfile->phone ?? 'N/A' }}</td>
                            <td>{{ $doctor->created_at ? $doctor->created_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.users.delete', $doctor->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this doctor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No doctors found. <a href="{{ route('admin.doctors.create') }}">Create one</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($doctors->hasPages())
                <div class="mt-3">
                    {{ $doctors->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
