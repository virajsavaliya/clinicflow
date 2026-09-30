@extends('layouts.app')

@section('title', 'Manage Schedule - ClinicFlow')

@section('styles')
<style>
    .page-header { margin-bottom: 1.5rem; }
    .page-title { font-size: 1.25rem; font-weight: 700; margin: 0; }
    .main-grid { display: grid; grid-template-columns: 1fr 350px; gap: 1.5rem; }
    .schedule-card { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); overflow: hidden; }
    .schedule-card .header { padding: 1rem; border-bottom: 1px solid var(--color-gray-200); font-weight: 600; background: var(--color-gray-50); display: flex; justify-content: space-between; align-items: center; }
    .schedule-card .body { padding: 1rem; }
    .day-row { display: flex; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--color-gray-100); }
    .day-row:last-child { border-bottom: none; }
    .day-toggle { width: 50px; }
    .day-name { width: 100px; font-weight: 600; font-size: 0.875rem; }
    .day-times { flex: 1; display: flex; gap: 0.5rem; align-items: center; }
    .day-times input { width: 100px; }
    .day-times .duration { width: 80px; }
    .holiday-card { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); }
    .holiday-card .header { padding: 1rem; border-bottom: 1px solid var(--color-gray-200); font-weight: 600; background: var(--color-gray-50); }
    .holiday-card .body { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .holiday-item { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: var(--color-gray-50); border-radius: var(--radius-md); margin-bottom: 0.5rem; }
    .holiday-item .date { font-weight: 600; font-size: 0.875rem; }
    .holiday-item .reason { font-size: 0.75rem; color: var(--color-gray-500); }
    .add-holiday-form { display: flex; gap: 0.5rem; margin-bottom: 1rem; }
    .add-holiday-form input { flex: 1; }
    @media (max-width: 1024px) { .main-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-calendar-range"></i> Manage Schedule</h1>
    <p class="text-muted mb-0" style="font-size: 0.8rem;">Set your weekly availability and holidays</p>
</div>

<div class="main-grid">
    <div class="schedule-card">
        <div class="header">
            <span><i class="bi bi-clock"></i> Weekly Schedule</span>
            <button class="btn btn-sm btn-dark" onclick="saveSchedule()">
                <i class="bi bi-check-lg"></i> Save Changes
            </button>
        </div>
        <div class="body">
            <form id="scheduleForm">
                @php
                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    $existingSchedules = $schedules->keyBy('day_of_week');
                @endphp
                
                @foreach($days as $day)
                    @php
                        $schedule = $existingSchedules[$day] ?? null;
                    @endphp
                    <div class="day-row">
                        <div class="day-toggle">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                    id="active_{{ $day }}" 
                                    name="schedules[{{ $loop->index }}][is_active]" 
                                    value="1"
                                    {{ $schedule && $schedule->is_active ? 'checked' : '' }}>
                                <input type="hidden" name="schedules[{{ $loop->index }}][day_of_week]" value="{{ $day }}">
                            </div>
                        </div>
                        <div class="day-name">{{ ucfirst($day) }}</div>
                        <div class="day-times">
                            <input type="time" class="form-control form-control-sm" 
                                name="schedules[{{ $loop->index }}][start_time]" 
                                value="{{ $schedule ? substr($schedule->start_time, 0, 5) : '09:00' }}"
                                {{ !$schedule || !$schedule->is_active ? 'disabled' : '' }}>
                            <span class="text-muted">to</span>
                            <input type="time" class="form-control form-control-sm" 
                                name="schedules[{{ $loop->index }}][end_time]" 
                                value="{{ $schedule ? substr($schedule->end_time, 0, 5) : '17:00' }}"
                                {{ !$schedule || !$schedule->is_active ? 'disabled' : '' }}>
                            <span class="text-muted ms-2">Slot:</span>
                            <select class="form-select form-select-sm duration" 
                                name="schedules[{{ $loop->index }}][slot_duration]"
                                {{ !$schedule || !$schedule->is_active ? 'disabled' : '' }}>
                                <option value="10" {{ ($schedule->slot_duration ?? 15) == 10 ? 'selected' : '' }}>10 min</option>
                                <option value="15" {{ ($schedule->slot_duration ?? 15) == 15 ? 'selected' : '' }}>15 min</option>
                                <option value="20" {{ ($schedule->slot_duration ?? 15) == 20 ? 'selected' : '' }}>20 min</option>
                                <option value="30" {{ ($schedule->slot_duration ?? 15) == 30 ? 'selected' : '' }}>30 min</option>
                                <option value="45" {{ ($schedule->slot_duration ?? 15) == 45 ? 'selected' : '' }}>45 min</option>
                                <option value="60" {{ ($schedule->slot_duration ?? 15) == 60 ? 'selected' : '' }}>60 min</option>
                            </select>
                        </div>
                    </div>
                @endforeach
            </form>
            
            <div class="alert alert-info mt-3 mb-0" style="font-size: 0.8rem;">
                <i class="bi bi-info-circle"></i> 
                Toggle a day ON to enable appointments for that day. Set your working hours and preferred appointment slot duration.
            </div>
        </div>
    </div>

    <div>
        <div class="holiday-card">
            <div class="header"><i class="bi bi-calendar-x"></i> Holidays & Days Off</div>
            <div class="body">
                <div class="add-holiday-form">
                    <input type="date" class="form-control form-control-sm" id="holidayDate" min="{{ date('Y-m-d') }}">
                    <input type="text" class="form-control form-control-sm" id="holidayReason" placeholder="Reason (optional)">
                    <button class="btn btn-sm btn-dark" onclick="addHoliday()">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                
                <div id="holidayList">
                    @forelse($holidays as $holiday)
                        <div class="holiday-item" id="holiday_{{ $holiday->id }}">
                            <div>
                                <div class="date">{{ $holiday->date->format('M d, Y') }}</div>
                                <div class="reason">{{ $holiday->reason ?? 'Holiday' }}</div>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeHoliday({{ $holiday->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    @empty
                        <p class="text-muted text-center" style="font-size: 0.8rem;">No upcoming holidays</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="{{ route('doctor.appointments') }}" class="btn btn-outline-secondary w-100">
                <i class="bi bi-arrow-left"></i> Back to Appointments
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Toggle day active/inactive
document.querySelectorAll('[id^="active_"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const row = this.closest('.day-row');
        const inputs = row.querySelectorAll('input[type="time"], select');
        inputs.forEach(input => {
            input.disabled = !this.checked;
        });
    });
});

async function saveSchedule() {
    const form = document.getElementById('scheduleForm');
    const formData = new FormData(form);
    
    // Build schedules array
    const schedules = [];
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    
    days.forEach((day, index) => {
        schedules.push({
            day_of_week: day,
            start_time: formData.get(`schedules[${index}][start_time]`) || '09:00',
            end_time: formData.get(`schedules[${index}][end_time]`) || '17:00',
            slot_duration: parseInt(formData.get(`schedules[${index}][slot_duration]`)) || 15,
            is_active: formData.get(`schedules[${index}][is_active]`) === '1',
        });
    });
    
    try {
        const res = await axios.post('{{ route('doctor.schedule.update') }}', { schedules });
        if (res.data.success) {
            window.showGlobalNotification(res.data.message, 'success');
        }
    } catch (e) {
        window.showGlobalNotification(e.response?.data?.message || 'Error saving schedule', 'error');
    }
}

async function addHoliday() {
    const date = document.getElementById('holidayDate').value;
    const reason = document.getElementById('holidayReason').value;
    
    if (!date) {
        window.showGlobalNotification('Please select a date', 'error');
        return;
    }
    
    try {
        const res = await axios.post('{{ route('doctor.holidays.add') }}', { date, reason });
        if (res.data.success) {
            window.showGlobalNotification(res.data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (e) {
        window.showGlobalNotification(e.response?.data?.message || 'Error adding holiday', 'error');
    }
}

async function removeHoliday(id) {
    if (!confirm('Remove this holiday?')) return;
    
    try {
        const res = await axios.delete(`/doctor/holidays/${id}`);
        if (res.data.success) {
            document.getElementById('holiday_' + id).remove();
            window.showGlobalNotification(res.data.message, 'success');
        }
    } catch (e) {
        window.showGlobalNotification(e.response?.data?.message || 'Error removing holiday', 'error');
    }
}
</script>
@endsection
