@extends('layouts.app')

@section('title', 'Notification Settings - ClinicFlow')

@section('styles')
<style>
    .settings-container { max-width: 900px; margin: 0 auto; }
    .settings-card { background: white; border: 2px solid #e5e7eb; border-radius: 14px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .settings-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #e5e7eb; }
    .settings-header h3 { font-size: 1.25rem; font-weight: 700; margin: 0; }
    .settings-header .icon { width: 48px; height: 48px; background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    
    .setting-item { padding: 1.25rem; background: #f9fafb; border-radius: 10px; margin-bottom: 1rem; border: 2px solid #e5e7eb; transition: all 0.2s ease; }
    .setting-item:hover { border-color: #d1d5db; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
    .setting-item .setting-info { flex: 1; }
    .setting-item h4 { font-size: 1rem; font-weight: 700; margin: 0 0 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
    .setting-item p { font-size: 0.875rem; color: #6b7280; margin: 0; }
    
    .toggle-switch { position: relative; display: inline-block; width: 60px; height: 30px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; border-radius: 30px; transition: 0.3s; }
    .toggle-slider:before { position: absolute; content: ""; height: 22px; width: 22px; left: 4px; bottom: 4px; background-color: white; border-radius: 50%; transition: 0.3s; }
    input:checked + .toggle-slider { background-color: #22c55e; }
    input:checked + .toggle-slider:before { transform: translateX(30px); }
    
    .time-selector { display: flex; gap: 1rem; margin-top: 1rem; }
    .time-selector select { padding: 0.75rem; border: 2px solid #d1d5db; border-radius: 10px; font-weight: 600; }
    
    .btn-save { background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: white; border: 2px solid #1a1a1a; padding: 0.875rem 2rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.3); }
    
    .test-section { background: #f0f9ff; border: 2px solid #0891b2; padding: 1.5rem; border-radius: 12px; margin-top: 1.5rem; }
    .test-section h4 { color: #0c4a6e; margin: 0 0 1rem; font-weight: 700; }
    .btn-test { background: #0891b2; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 600; cursor: pointer; }
    .btn-test:hover { background: #0e7490; }
</style>
@endsection

@section('content')
<div class="settings-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 0.5rem;">
                <i class="bi bi-bell-fill"></i> Notification Settings
            </h1>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Configure automated appointment reminders</p>
        </div>
        <a href="{{ route('reception.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <form action="{{ route('reception.notifications.save') }}" method="POST">
        @csrf
        
        <!-- WhatsApp Reminders -->
        <div class="settings-card">
            <div class="settings-header">
                <div class="icon"><i class="bi bi-whatsapp"></i></div>
                <h3>WhatsApp Reminders</h3>
            </div>
            
            <div class="setting-item" style="display: flex; align-items: center; gap: 1.5rem;">
                <div class="setting-info">
                    <h4><i class="bi bi-clock-history"></i> 1 Hour Before Appointment</h4>
                    <p>Send reminder 1 hour before the scheduled appointment time</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="whatsapp_1hour" id="whatsapp_1hour" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            
            <div class="setting-item" style="display: flex; align-items: center; gap: 1.5rem;">
                <div class="setting-info">
                    <h4><i class="bi bi-calendar-day"></i> 24 Hours Before Appointment</h4>
                    <p>Send reminder 1 day before the scheduled appointment</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="whatsapp_24hour" id="whatsapp_24hour" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            
            <div class="setting-item" style="display: flex; align-items: center; gap: 1.5rem;">
                <div class="setting-info">
                    <h4><i class="bi bi-check-circle"></i> On Appointment Creation</h4>
                    <p>Send confirmation immediately when appointment is booked</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="whatsapp_instant" id="whatsapp_instant" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        <!-- SMS Reminders -->
        <div class="settings-card">
            <div class="settings-header">
                <div class="icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"><i class="bi bi-chat-dots-fill"></i></div>
                <h3>SMS Reminders</h3>
            </div>
            
            <div class="setting-item" style="display: flex; align-items: center; gap: 1.5rem;">
                <div class="setting-info">
                    <h4><i class="bi bi-clock-history"></i> 1 Hour Before Appointment</h4>
                    <p>Send SMS reminder 1 hour before appointment</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="sms_1hour" id="sms_1hour">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            
            <div class="setting-item" style="display: flex; align-items: center; gap: 1.5rem;">
                <div class="setting-info">
                    <h4><i class="bi bi-calendar-day"></i> 24 Hours Before Appointment</h4>
                    <p>Send SMS reminder 1 day before appointment</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="sms_24hour" id="sms_24hour">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        <!-- Email Reminders -->
        <div class="settings-card">
            <div class="settings-header">
                <div class="icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);"><i class="bi bi-envelope-fill"></i></div>
                <h3>Email Reminders</h3>
            </div>
            
            <div class="setting-item" style="display: flex; align-items: center; gap: 1.5rem;">
                <div class="setting-info">
                    <h4><i class="bi bi-calendar-day"></i> 24 Hours Before Appointment</h4>
                    <p>Send email reminder 1 day before appointment</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="email_24hour" id="email_24hour">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            
            <div class="setting-item" style="display: flex; align-items: center; gap: 1.5rem;">
                <div class="setting-info">
                    <h4><i class="bi bi-check-circle"></i> On Appointment Creation</h4>
                    <p>Send email confirmation when appointment is booked</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="email_instant" id="email_instant">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        <!-- Test Section -->
        <div class="test-section">
            <h4><i class="bi bi-lightning-charge-fill"></i> Test Notifications</h4>
            <p style="margin-bottom: 1rem; color: #0c4a6e;">Send a test notification to verify your configuration</p>
            <div style="display: flex; gap: 1rem;">
                <button type="button" class="btn-test" onclick="testWhatsApp()">
                    <i class="bi bi-whatsapp"></i> Test WhatsApp
                </button>
                <button type="button" class="btn-test" onclick="testSMS()">
                    <i class="bi bi-chat-dots"></i> Test SMS
                </button>
                <button type="button" class="btn-test" onclick="testEmail()">
                    <i class="bi bi-envelope"></i> Test Email
                </button>
            </div>
        </div>

        <div style="margin-top: 2rem; text-align: center;">
            <button type="submit" class="btn-save">
                <i class="bi bi-check-lg"></i> Save Settings
            </button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
function testWhatsApp() {
    alert('Test WhatsApp notification will be sent to the logged-in user\'s phone number');
}

function testSMS() {
    alert('Test SMS notification will be sent to the logged-in user\'s phone number');
}

function testEmail() {
    alert('Test Email notification will be sent to the logged-in user\'s email address');
}
</script>
@endsection
