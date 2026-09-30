@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('styles')
<style>
    /* Stat Cards */
    .stat-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; transition: all 0.2s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
    .stat-icon-box { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-icon-box i { font-size: 1.5rem; }
    .stat-icon-box.black { background: linear-gradient(135deg, #1a1a1a 0%, #374151 100%); color: #fff; }
    .stat-icon-box.green { background: linear-gradient(135deg, #16a34a 0%, #4ade80 100%); color: #fff; }
    .stat-icon-box.amber { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); color: #fff; }
    .stat-icon-box.blue { background: linear-gradient(135deg, #2563eb 0%, #60a5fa 100%); color: #fff; }
    .stat-content h3 { font-size: 1.75rem; font-weight: 700; margin: 0; color: #1a1a1a; line-height: 1; }
    .stat-content p { font-size: 0.8rem; color: #6b7280; margin: 0.25rem 0 0; font-weight: 500; }
    
    @media (max-width: 1024px) { .stat-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .stat-row { grid-template-columns: 1fr; } }
    
    .action-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 2rem; text-align: center; transition: all 0.2s ease; height: 100%; }
    .action-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
    .action-card .icon-box { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
    .action-card .icon-box i { font-size: 1.75rem; }
    .action-card .icon-box.black { background: linear-gradient(135deg, #1a1a1a 0%, #374151 100%); color: #fff; }
    .action-card .icon-box.green { background: linear-gradient(135deg, #16a34a 0%, #4ade80 100%); color: #fff; }
    .action-card h5 { font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: #1a1a1a; }
    .action-card p { font-size: 0.8rem; color: #6b7280; margin-bottom: 1rem; }
    
    .table-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden; }
    .table-card .header { padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; }
    .table-card .header h5 { margin: 0; font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; }
</style>
@endsection

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem;"><i class="bi bi-shield-check"></i> Admin Dashboard</h1>
    <p style="color: #6b7280; font-size: 0.8rem; margin: 0.25rem 0 0;">Manage users and system settings</p>
</div>

<!-- Stat Cards -->
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon-box black">
            <i class="bi bi-person-badge"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_doctors'] }}</h3>
            <p>Doctors</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-box green">
            <i class="bi bi-people"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_receptions'] }}</h3>
            <p>Reception Staff</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-box amber">
            <i class="bi bi-shield-check"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_admins'] }}</h3>
            <p>Admins</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-box blue">
            <i class="bi bi-person-circle"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_users'] }}</h3>
            <p>Total Users</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="action-card">
            <div class="icon-box black">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <h5>Create Doctor</h5>
            <p>Add a new doctor to the system</p>
            <a href="{{ route('admin.doctors.create') }}" class="btn btn-dark">
                <i class="bi bi-plus-circle"></i> Create Doctor
            </a>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="action-card">
            <div class="icon-box green">
                <i class="bi bi-list-ul"></i>
            </div>
            <h5>Manage Doctors</h5>
            <p>View and manage all doctors</p>
            <a href="{{ route('admin.doctors') }}" class="btn btn-dark">
                <i class="bi bi-list"></i> View Doctors
            </a>
        </div>
    </div>
</div>


<div class="table-card">
    <div class="header">
        <h5><i class="bi bi-clock-history"></i> Recent Doctors & Admins</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Specialization</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($user->role === 'doctor')
                                        <span class="badge bg-primary">Doctor</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->role === 'doctor' && $user->doctorProfile)
                                        {{ $user->doctorProfile->specialization ?? 'N/A' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $user->created_at ? $user->created_at->format('M d, Y H:i') : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No doctors found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endsection
