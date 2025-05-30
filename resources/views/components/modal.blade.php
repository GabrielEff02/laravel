<style>
    .custom-btn {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }



    .custom-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    /* Enhanced Modal Styles */
    .custom-confirmation-modal.fade .modal-dialog {
        transform: translateY(-50px) scale(0.8);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .custom-confirmation-modal.fade.show .modal-dialog {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    .custom-confirmation-modal .modal-backdrop {
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(3px);
    }

    .custom-confirmation-modal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    }

    .custom-confirmation-modal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom: none;
        padding: 1.5rem 2rem;
        position: relative;
    }

    .custom-confirmation-modal .modal-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }

    .custom-confirmation-modal .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        margin: 0;
    }

    .custom-confirmation-modal .modal-title i {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem;
        border-radius: 50%;
        margin-right: 0.75rem;
        font-size: 1.1rem;
    }

    .btn-close-custom-confirm {
        border: none;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        font-size: 1.1rem;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        opacity: 0.8;
    }

    .btn-close-custom-confirm:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
        opacity: 1;
    }

    .custom-confirmation-modal .modal-body {
        padding: 2.5rem 2rem;
        text-align: center;
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    }

    .confirmation-icon-custom {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: #e17055;
        box-shadow: 0 10px 25px rgba(253, 203, 110, 0.3);
        animation: pulse-custom 2s infinite;
    }

    @keyframes pulse-custom {
        0% {
            transform: scale(1);
            box-shadow: 0 10px 25px rgba(253, 203, 110, 0.3);
        }

        50% {
            transform: scale(1.05);
            box-shadow: 0 15px 35px rgba(253, 203, 110, 0.4);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 10px 25px rgba(253, 203, 110, 0.3);
        }
    }

    .modal-message-custom {
        color: #2d3436;
        font-size: 1.1rem;
        font-weight: 500;
        line-height: 1.6;
        margin: 0;
    }

    .custom-confirmation-modal .modal-footer {
        border-top: none;
        padding: 1.5rem 2rem 2rem;
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        gap: 1rem;
    }

    .btn-custom-confirm {
        border-radius: 12px;
        font-weight: 600;
        padding: 0.75rem 2rem;
        font-size: 0.95rem;
        border: none;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-custom-confirm::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-custom-confirm:hover::before {
        left: 100%;
    }

    .btn-outline-secondary-custom {
        background: linear-gradient(135deg, #b2bec3, #636e72);
        color: white;
        box-shadow: 0 5px 15px rgba(99, 110, 114, 0.3);
    }

    .btn-outline-secondary-custom:hover {
        background: linear-gradient(135deg, #636e72, #2d3436);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 110, 114, 0.4);
    }

    .btn-success-custom {
        background: linear-gradient(135deg, #00b894, #00a085);
        box-shadow: 0 5px 15px rgba(0, 184, 148, 0.3);
        color: white;
    }

    .btn-success-custom:hover {
        background: linear-gradient(135deg, #00a085, #00896b);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 184, 148, 0.4);
        color: white;
    }

    .btn-custom-confirm i {
        margin-right: 0.5rem;
    }

    /* Responsive Design */
    @media (max-width: 576px) {
        .custom-confirmation-modal .modal-dialog {
            margin: 1rem;
        }

        .custom-confirmation-modal .modal-body {
            padding: 2rem 1.5rem;
        }

        .custom-confirmation-modal .modal-footer {
            padding: 1.5rem;
            flex-direction: column;
        }

        .btn-custom-confirm {
            width: 100%;
        }
    }
</style>

<!-- Modal HTML dengan ID/Class yang Unik -->
<div class="modal fade custom-confirmation-modal" id="customConfirmationModal" tabindex="-1"
    aria-labelledby="customConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customConfirmationModalLabel">
                    <i class="fas fa-question-circle"></i>
                    Konfirmasi Tindakan
                </h5>
                <button type="button" class="btn-close-custom-confirm" data-bs-dismiss="modal" aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="confirmation-icon-custom">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <p id="customConfirmationModalMessage" class="modal-message-custom">
                    Apakah Anda yakin ingin melanjutkan proses ini?
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-custom-confirm btn-outline-secondary-custom"
                    data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Batal
                </button>
                <button type="button" id="customConfirmSubmitBtn" class="btn btn-custom-confirm btn-success-custom">
                    <i class="fas fa-check me-2"></i>
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript dengan ID/Class yang Unik -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentForm = null;
        let currentAction = null;

        // Handle confirmation button clicks
        $(document).on('click', '.btn-confirm-submit', function(e) {
            e.preventDefault();

            // Store the current form or action
            currentForm = $(this).closest('form');
            currentAction = $(this);

            // Get custom message or use default
            const message = $(this).data('message') || "Apakah Anda yakin ingin melanjutkan proses ini?";
            $('#customConfirmationModalMessage').text(message);

            // Show modal with animation
            const modal = new bootstrap.Modal(document.getElementById('customConfirmationModal'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();

            // Add entrance animation
            setTimeout(() => {
                document.querySelector('.confirmation-icon-custom').style.animation =
                    'pulse-custom 2s infinite';
            }, 100);
        });

        // Handle confirm submit
        $('#customConfirmSubmitBtn').on('click', function() {
            // Add loading state
            const originalText = $(this).html();
            $(this).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
            $(this).prop('disabled', true);

            // Submit the form immediately (remove setTimeout for production)
            if (currentForm && currentForm.length > 0) {
                currentForm.submit();
            } else {
                // Handle non-form actions
                console.log('Action confirmed:', currentAction.data('message'));

                // Reset button state
                $(this).html(originalText);
                $(this).prop('disabled', false);

                // Hide modal
                const modal = bootstrap.Modal.getInstance(document.getElementById(
                    'customConfirmationModal'));
                if (modal) modal.hide();
            }
        });

        // Handle modal close events
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById(
                    'customConfirmationModal'));
                if (modal) modal.hide();
            });
        });

        // Reset modal state when hidden
        document.getElementById('customConfirmationModal').addEventListener('hidden.bs.modal', function() {
            currentForm = null;
            currentAction = null;

            // Reset confirm button
            const confirmBtn = document.getElementById('customConfirmSubmitBtn');
            confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i>Ya, Lanjutkan';
            confirmBtn.disabled = false;
        });
    });
</script>