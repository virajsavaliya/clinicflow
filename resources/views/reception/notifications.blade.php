@extends('layouts.app')

@section('title', 'Alerts & Notifications')

@section('styles')
<style>
    .alerts-container { max-width: 900px; margin: 0 auto; }
    .notification-card { background: var(--color-white); border-left: 5px solid; padding: 1.25rem; margin-bottom: 1rem; border-radius: 0.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .notification-card.critical { border-left-color: #dc2626; background: #fef2f2; }
    .notification-card.warning { border-left-color: #f59e0b; background: #fffbeb; }
    .notification-card.info { border-left-color: #0d6efd; background: #eff6ff; }
    .notification-card.success { border-left-color: #16a34a; background: #f0fdf4; }
    .notification-card.patient_called { border-left-color: #8b5cf6; background: #faf5ff; }
    .notification-icon { font-size: 1.75rem; margin-right: 1rem; }
    .notification-content { flex: 1; }
    .notification-title { font-weight: 700; font-size: 0.95rem; margin-bottom: 0.25rem; }
    .notification-message { font-size: 0.9rem; color: var(--color-gray-700); margin-bottom: 0.75rem; }
    .notification-time { font-size: 0.8rem; color: var(--color-gray-500); }
    .notification-actions { display: flex; gap: 0.75rem; margin-top: 1rem; }
    .notification-btn { padding: 0.4rem 1rem; font-size: 0.8rem; border: none; cursor: pointer; background: rgba(0,0,0,0.1); color: var(--color-black); border-radius: 0.25rem; transition: all 0.2s; }
    .notification-btn:hover { background: rgba(0,0,0,0.2); }
    .notification-btn.primary { background: #0d6efd; color: white; }
    .notification-btn.primary:hover { background: #0b5ed7; }
    .notification-badge { display: inline-block; padding: 0.25rem 0.75rem; font-size: 0.7rem; font-weight: 600; background: #0d6efd; color: white; border-radius: 12px; margin-right: 0.5rem; }
    .notification-badge.unread { background: #dc2626; }
    .empty-state { text-align: center; padding: 3rem 2rem; color: var(--color-gray-400); }
    .empty-state i { font-size: 3rem; opacity: 0.2; margin-bottom: 1rem; display: block; }
    .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
</style>
@endsection

@section('content')
<div class="alerts-container">
    <div class="header-section">
        <div>
            <h1 style="font-size: 1.1875rem; font-weight: 700; margin: 0;">Alerts & Notifications</h1>
            <p style="color: var(--color-gray-500); font-size: 0.75rem; margin: 0.5rem 0 0;">{{ count($notifications) }} notification(s)</p>
        </div>
        @if(count($notifications) > 0)
        <button class="btn btn-dark" onclick="markAllRead()" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
            <i class="bi bi-check-all"></i> Mark All Read
        </button>
        @endif
    </div>

    @if(count($notifications) > 0)
        <div id="notifications-list">
            @foreach($notifications as $notif)
            <div class="notification-card {{ $notif->type }}" data-notif-id="{{ $notif->id }}">
                <div style="display: flex; align-items: flex-start;">
                    <i class="bi {{ getNotificationIcon($notif->type) }} notification-icon"></i>
                    <div class="notification-content">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            @if(!$notif->is_read)
                            <span class="notification-badge unread">NEW</span>
                            @endif
                            <div class="notification-title">{{ $notif->title ?? ucfirst(str_replace('_', ' ', $notif->type)) }}</div>
                        </div>
                        <div class="notification-message">{{ $notif->message ?? $notif->data }}</div>
                        <div class="notification-time">
                            <i class="bi bi-clock"></i> {{ $notif->created_at->diffForHumans() }}
                        </div>
                        <div class="notification-actions">
                            @if($notif->type === 'patient_called')
                            <a href="{{ route('reception.dashboard') }}" class="notification-btn primary">
                                <i class="bi bi-speedometer2"></i> Go to Dashboard
                            </a>
                            @elseif(in_array($notif->type, ['low_stock', 'critical_stock']))
                            <a href="{{ route('reception.pharmacy') }}" class="notification-btn primary">
                                <i class="bi bi-capsule"></i> Go to Pharmacy
                            </a>
                            @endif
                            <button class="notification-btn" onclick="dismissNotification({{ $notif->id }})">
                                <i class="bi bi-x"></i> Dismiss
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h5>No Notifications</h5>
            <p style="margin-bottom: 0;">You're all caught up! Check back later for new alerts.</p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
async function dismissNotification(notifId) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await axios.post(`/api/notifications/${notifId}/read`, {}, {
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        
        if (response.data.success) {
            document.querySelector(`[data-notif-id="${notifId}"]`).remove();
            const remaining = document.querySelectorAll('.notification-card').length;
            if (remaining === 0) {
                location.reload();
            }
        }
    } catch (error) {
        console.error('Error dismissing notification:', error);
        alert('Error dismissing notification');
    }
}

async function markAllRead() {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await axios.post('/api/notifications/read-all', {}, {
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        
        if (response.data.success) {
            location.reload();
        }
    } catch (error) {
        console.error('Error marking all as read:', error);
        alert('Error marking all as read');
    }
}

function getNotificationIcon(type) {
    const icons = {
        'patient_called': 'bi-person-check-fill',
        'low_stock': 'bi-exclamation-triangle-fill',
        'critical_stock': 'bi-exclamation-circle-fill',
        'checkup_notification': 'bi-clipboard-check',
        'payment_reminder': 'bi-cash-coin',
        'appointment': 'bi-calendar-event-fill',
        'message': 'bi-chat-dots-fill',
        'system': 'bi-gear-fill'
    };
    return icons[type] || 'bi-bell-fill';
}
</script>
@endsection
