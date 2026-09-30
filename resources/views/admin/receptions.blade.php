@extends('layouts.app')

@section('title', 'Manage Reception Staff')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="bi bi-people"></i> Manage Reception Staff</h2>
            <p class="text-muted">Reception users are created by doctors</p>
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
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receptions as $reception)
                        <tr>
                            <td>
                                <strong>{{ $reception->name }}</strong>
                            </td>
                            <td>{{ $reception->email }}</td>
                            <td>{{ $reception->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.users.delete', $reception->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this reception user?');">
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
                            <td colspan="4" class="text-center text-muted py-4">
                                No reception users found. Doctors can create reception users.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($receptions->hasPages())
                <div class="mt-3">
                    {{ $receptions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
