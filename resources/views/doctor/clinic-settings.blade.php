@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<style>
    .logo-preview {
        background: #f9fafb;
        padding: 2rem;
        border-radius: 12px;
        border: 2px dashed #e5e7eb;
        min-height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .logo-preview img {
        max-width: 100%;
        height: auto;
        max-height: 200px;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .no-logo-state {
        text-align: center;
        color: #9ca3af;
    }

    .no-logo-state i {
        font-size: 4rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }

    .delete-logo-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
    }

    .delete-logo-btn:hover {
        background: #b91c1c;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 38, 38, 0.3);
    }

    .logo-actions {
        margin-top: 1rem;
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .crop-body-container {
        padding: 1.25rem;
        background: #111;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 300px;
        max-height: 60vh;
    }

    .crop-body-container img {
        max-width: 100%;
        display: block;
    }

    .crop-tools {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .crop-tool-btn {
        background: #f3f4f6;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.45rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        color: #374151;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .crop-tool-btn:hover {
        background: #e5e7eb;
        border-color: #d1d5db;
    }

    .crop-action-btns {
        display: flex;
        gap: 0.5rem;
    }

    .btn-crop-cancel {
        padding: 0.65rem 1.25rem;
        border: 2px solid #e5e7eb;
        background: #fff;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-crop-cancel:hover {
        background: #f3f4f6;
    }

    .btn-crop-apply {
        padding: 0.65rem 1.5rem;
        border: none;
        background: linear-gradient(135deg, #1a1a1a 0%, #374151 100%);
        color: #fff;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.875rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .btn-crop-apply:hover {
        background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
        transform: translateY(-1px);
    }

    .crop-ratio-btns {
        display: flex;
        gap: 0.4rem;
    }

    .crop-ratio-btn {
        background: #f3f4f6;
        border: 2px solid #e5e7eb;
        border-radius: 6px;
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        color: #6b7280;
        transition: all 0.2s;
    }

    .crop-ratio-btn.active,
    .crop-ratio-btn:hover {
        background: #1a1a1a;
        color: #fff;
        border-color: #1a1a1a;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-hospital"></i> Clinic Settings</h1>
                <p class="text-muted mb-0">Manage your clinic information and branding</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-building"></i> Clinic Information</h5>
                </div>
                <div class="card-body">
                    <form id="clinicSettingsForm" method="POST" action="{{ route('doctor.clinic.update') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="clinic_name" class="form-label">
                                <i class="bi bi-building"></i> Clinic Name *
                            </label>
                            <input type="text" class="form-control @error('clinic_name') is-invalid @enderror"
                                id="clinic_name" name="clinic_name"
                                value="{{ old('clinic_name', $profile->clinic_name ?? '') }}" required
                                placeholder="e.g., Dr. Smith's Medical Clinic">
                            @error('clinic_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="default_doctor_fee" class="form-label">
                                <i class="bi bi-currency-rupee"></i> Default Consultation Fee *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number"
                                    class="form-control @error('default_doctor_fee') is-invalid @enderror"
                                    id="default_doctor_fee" name="default_doctor_fee"
                                    value="{{ old('default_doctor_fee', $profile->default_doctor_fee ?? '0') }}"
                                    step="0.01" min="0" required placeholder="0.00">
                                @error('default_doctor_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">This will be the default fee for consultations</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-image"></i> Clinic Logo
                            </label>
                            {{-- Hidden: real file input (triggers cropper) --}}
                            <input type="file" id="clinic_logo_raw" accept="image/*" style="display:none;">
                            {{-- Hidden: cropped base64 data sent to server --}}
                            <input type="hidden" name="clinic_logo_cropped" id="clinic_logo_cropped">
                            {{-- Visible: upload trigger button --}}
                            <div>
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="document.getElementById('clinic_logo_raw').click()">
                                    <i class="bi bi-upload"></i> Choose & Crop Logo
                                </button>
                                <span id="cropFileNameLabel" class="ms-2 text-muted" style="font-size:0.875rem;"></span>
                            </div>
                            @error('clinic_logo_cropped')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted d-block mt-1">Recommended: square image, PNG/JPG, max
                                2MB. You can crop after selecting.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Settings
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-image"></i> Current Logo</h5>
                </div>
                <div class="card-body">
                    <div class="logo-preview" id="logoPreviewContainer">
                        @if($profile && $profile->clinic_logo_url)
                        <button type="button" class="delete-logo-btn" onclick="deleteLogo()" id="deleteLogoBtn">
                            <i class="bi bi-trash"></i> Delete Logo
                        </button>
                        <img src="{{ $profile->clinic_logo_url }}" alt="Clinic Logo" id="currentLogo"
                            onerror="this.style.display='none'; document.getElementById('noLogoState').style.display='block';">
                        @else
                        <div class="no-logo-state" id="noLogoState">
                            <i class="bi bi-image"></i>
                            <p class="mb-0">No logo uploaded</p>
                        </div>
                        @endif
                    </div>
                    <div class="logo-actions" id="logoActions" style="display: none;">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cancelPreview()">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Quick Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            Keep your clinic name clear and professional
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            Set competitive consultation fees
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            Upload a professional logo for branding
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success"></i>
                            These settings will appear on bills and prescriptions
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Crop Modal ── --}}
<div class="modal fade" id="cropModalOverlay" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-crop"></i> Crop Your Logo</h5>
                <button type="button" class="btn-close" onclick="closeCropModal()" aria-label="Close"></button>
            </div>
            <div class="p-0 crop-body-container">
                <img id="cropImage" src="" alt="Crop preview">
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="crop-tools">
                    {{-- Ratio buttons --}}
                    <div class="crop-ratio-btns">
                        <button type="button" class="crop-ratio-btn active" onclick="setRatio(1,1,this)"
                            title="1:1 Square">1:1</button>
                        <button type="button" class="crop-ratio-btn" onclick="setRatio(4,3,this)"
                            title="4:3">4:3</button>
                        <button type="button" class="crop-ratio-btn" onclick="setRatio(16,9,this)"
                            title="16:9">16:9</button>
                        <button type="button" class="crop-ratio-btn" onclick="setRatio(NaN,NaN,this)"
                            title="Free">Free</button>
                    </div>
                    {{-- Tool buttons --}}
                    <button type="button" class="crop-tool-btn" onclick="cropper.rotate(-90)" title="Rotate left"><i
                            class="bi bi-arrow-counterclockwise"></i> Rotate</button>
                    <button type="button" class="crop-tool-btn"
                        onclick="cropper.scaleX(cropper.getData().scaleX === -1 ? 1 : -1)" title="Flip horizontal"><i
                            class="bi bi-symmetry-vertical"></i> Flip</button>
                    <button type="button" class="crop-tool-btn" onclick="cropper.reset()" title="Reset"><i
                            class="bi bi-arrow-repeat"></i> Reset</button>
                </div>
                <div class="crop-action-btns">
                    <button type="button" class="btn btn-outline-secondary" onclick="closeCropModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="applyCrop()">
                        <i class="bi bi-check-lg"></i> Apply & Use
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
    // ── Form validation ──
    document.getElementById('clinicSettingsForm').addEventListener('submit', function (e) {
        const clinicName = document.getElementById('clinic_name').value.trim();
        const doctorFee = parseFloat(document.getElementById('default_doctor_fee').value);
        if (!clinicName) { e.preventDefault(); alert('Please enter a clinic name'); return; }
        if (isNaN(doctorFee) || doctorFee < 0) { e.preventDefault(); alert('Please enter a valid consultation fee'); return; }
    });

    let cropper = null;
    let cropModalInstance = null;

    document.addEventListener('DOMContentLoaded', function () {
        cropModalInstance = new bootstrap.Modal(document.getElementById('cropModalOverlay'));
    });

    const rawInput = document.getElementById('clinic_logo_raw');
    const cropImage = document.getElementById('cropImage');
    const croppedInput = document.getElementById('clinic_logo_cropped');
    const fileNameLabel = document.getElementById('cropFileNameLabel');

    rawInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > 20 * 1024 * 1024) {
            alert('File size must be less than 20MB');
            rawInput.value = '';
            return;
        }
        if (!file.type.match('image.*')) {
            alert('Please select an image file');
            rawInput.value = '';
            return;
        }

        // Show size info in label
        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
        fileNameLabel.textContent = `${file.name} (${sizeMB} MB)${sizeMB > 1 ? ' — will be compressed automatically' : ''}`;

        const reader = new FileReader();
        reader.onload = function (ev) {
            cropImage.src = ev.target.result;
            cropModalInstance.show();

            // Setup cropper after modal is shown to ensure correct dimensions
            setTimeout(() => {
                // Destroy previous cropper if any
                if (cropper) { cropper.destroy(); cropper = null; }

                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,          // default: 1:1 square
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.85,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    background: true,
                });
            }, 300);
        };
        reader.readAsDataURL(file);
    });

    function setRatio(w, h, btn) {
        if (!cropper) return;
        cropper.setAspectRatio(isNaN(w) ? NaN : w / h);
        document.querySelectorAll('.crop-ratio-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }

    function applyCrop() {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({ width: 400, height: 400, imageSmoothingEnabled: true, imageSmoothingQuality: 'high' });
        const dataURL = canvas.toDataURL('image/png');

        // Put cropped preview in the logo preview box
        const previewContainer = document.getElementById('logoPreviewContainer');
        let img = document.getElementById('currentLogo');
        const noLogoState = document.getElementById('noLogoState');
        const deleteBtn = document.getElementById('deleteLogoBtn');

        if (!img) {
            img = document.createElement('img');
            img.id = 'currentLogo';
            img.alt = 'Logo Preview';
            previewContainer.appendChild(img);
        }
        if (noLogoState) noLogoState.style.display = 'none';
        if (deleteBtn) deleteBtn.style.display = 'none';
        img.src = dataURL;
        img.style.display = 'block';

        // Set hidden input for form submission
        croppedInput.value = dataURL;
        fileNameLabel.textContent = rawInput.files[0]?.name ?? '';

        // Show cancel action
        document.getElementById('logoActions').style.display = 'flex';

        closeCropModal();
    }

    function closeCropModal() {
        if (cropModalInstance) {
            cropModalInstance.hide();
        }
        if (cropper) { cropper.destroy(); cropper = null; }
        rawInput.value = '';
    }

    // ── Cancel preview ──
    let originalLogoSrc = '';
    const currentLogoEl = document.getElementById('currentLogo');
    if (currentLogoEl) originalLogoSrc = currentLogoEl.src;

    function cancelPreview() {
        croppedInput.value = '';
        fileNameLabel.textContent = '';

        const currentLogo = document.getElementById('currentLogo');
        const noLogoState = document.getElementById('noLogoState');
        const deleteBtn = document.getElementById('deleteLogoBtn');
        const logoActions = document.getElementById('logoActions');

        if (originalLogoSrc) {
            if (currentLogo) { currentLogo.src = originalLogoSrc; currentLogo.style.display = 'block'; }
            if (deleteBtn) deleteBtn.style.display = 'flex';
        } else {
            if (currentLogo) currentLogo.style.display = 'none';
            if (noLogoState) noLogoState.style.display = 'block';
        }
        logoActions.style.display = 'none';
    }

    // ── Delete logo ──
    function deleteLogo() {
        if (!confirm('Are you sure you want to delete the clinic logo? This action cannot be undone.')) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("doctor.clinic.delete-logo") }}';

        const csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const method = document.createElement('input');
        method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
        form.appendChild(method);

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection