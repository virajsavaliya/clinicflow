/**
 * Reception Dashboard Logic v7.2
 * Modal-based interaction with safe global fallbacks.
 * Fixed: Remove stray modal backdrops on page load
 */

console.log('✅ External Dashboard Script v7.2 Loaded');

// Clean up any leftover modal backdrops on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('[Dashboard] Cleaning up modal backdrops...');
    
    // Remove all modal backdrops
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => {
        console.log('[Dashboard] Removing backdrop:', backdrop);
        backdrop.remove();
    });
    
    // Remove modal-open class from body
    document.body.classList.remove('modal-open');
    
    // Reset body styles
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    
    console.log('[Dashboard] Cleanup complete. Page should be clickable now.');
});

(function() {
    const modalId = 'existingPatientModal';

    function log(msg) {
        console.log('[Dashboard v7.1] ' + msg);
    }

    function getModalInstance() {
        const modalEl = document.getElementById(modalId);
        if (!modalEl || typeof bootstrap === 'undefined') return null;
        return bootstrap.Modal.getOrCreateInstance(modalEl);
    }

    // --- Global Fallbacks (for old cached HTML with onclick) ---
    window.showExistingPatientForm = function() {
        const modal = getModalInstance();
        if (modal) {
            modal.show();
        } else {
            log('Modal not found, fallback to patients page');
            window.location.href = '/reception/patients';
        }
    };

    window.showNewPatientForm = function() {
        window.location.href = '/reception/patients';
    };

    window.hidePatientForms = function() {
        const modal = getModalInstance();
        if (modal) modal.hide();
    };

    window.deleteFromQueue = async function(visitId) {
        if(!confirm('Remove from queue?')) return;
        try {
            await axios.delete('/api/queue/' + visitId);
            location.reload();
        } catch(e) {
            alert('Error removing');
        }
    };

    // --- Modal Search Logic ---
    function initModalLogic() {
        const searchInput = document.getElementById('modal_patient_search');
        const list = document.getElementById('modal-autocomplete-list');
        const hiddenId = document.getElementById('modal_patient_id');
        const infoBox = document.getElementById('selected-patient-info');
        const notFoundBox = document.getElementById('patient-not-found');
        const submitBtn = document.getElementById('btn-add-queue');

        if (!searchInput || !list) return;

        searchInput.addEventListener('input', function() {
            const q = this.value.trim();
            if (q.length < 2) {
                list.style.display = 'none';
                if (notFoundBox) notFoundBox.style.display = 'none';
                return;
            }

            axios.get('/reception/search-patients?q=' + encodeURIComponent(q))
                .then(res => {
                    if (!res.data || res.data.length === 0) {
                        list.style.display = 'none';
                        // Show "Patient Not Found" message
                        if (notFoundBox) notFoundBox.style.display = 'block';
                        return;
                    }

                    // Hide "Not Found" message when results exist
                    if (notFoundBox) notFoundBox.style.display = 'none';

                    list.innerHTML = res.data.map(p => {
                        const safeName = (p.name || 'Unknown').replace(/'/g, "\\'");
                        const safeAge = p.age || p.calculated_age || '-';
                        return `
                            <a href="#" class="list-group-item list-group-item-action"
                               data-id="${p.id}"
                               data-name="${safeName}"
                               data-age="${safeAge}"
                               onclick="window.selectPatient(${p.id}, '${safeName}', '${safeAge}'); return false;">
                               ${safeName} <small class="text-muted">(${safeAge}y)</small>
                            </a>
                        `;
                    }).join('');
                    list.style.display = 'block';
                })
                .catch(err => console.error(err));
        });

        window.selectPatient = function(id, name, age) {
            if (hiddenId) hiddenId.value = id;
            const displayName = document.getElementById('display_name');
            const displayId = document.getElementById('display_id');
            const displayAge = document.getElementById('display_age');

            if (displayName) displayName.textContent = name;
            if (displayId) displayId.textContent = id;
            if (displayAge) displayAge.textContent = age;

            if (infoBox) infoBox.style.display = 'block';
            if (notFoundBox) notFoundBox.style.display = 'none';
            list.style.display = 'none';
            searchInput.value = '';
            if (submitBtn) submitBtn.disabled = false;
        };

        const modalEl = document.getElementById(modalId);
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', function () {
                if (hiddenId) hiddenId.value = '';
                if (infoBox) infoBox.style.display = 'none';
                if (notFoundBox) notFoundBox.style.display = 'none';
                if (submitBtn) submitBtn.disabled = true;
                if (searchInput) searchInput.value = '';
                
                // Clean up any leftover backdrops
                setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }, 100);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModalLogic);
    } else {
        initModalLogic();
    }
})();
