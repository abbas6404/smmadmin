@extends('frontend.layouts.master')

@section('title', 'Add Funds')

@section('content')
<!-- WhatsApp Admin Contact Banner -->
<div class="alert alert-success mb-4 shadow-sm whatsapp-banner">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="fab fa-whatsapp fa-3x text-success whatsapp-icon-pulse"></i>
            </div>
            <div>
                <h3 class="mb-0">Adding Funds? Contact Admin First!</h3>
                <p class="mb-0">Get <strong>instant approval</strong> and assistance with your fund deposit</p>
            </div>
        </div>
        <a href="https://wa.me/+8801533651935?text=Hello,%20I%20want%20to%20add%20funds%20to%20my%20account.%20Please%20assist%20me." target="_blank" class="btn btn-lg btn-success">
            <i class="fab fa-whatsapp me-2"></i>
            <strong>Contact Admin: 01533651935</strong>
        </a>
    </div>
</div>

<!-- Page Header -->
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Add Funds</h2>
                <div class="text-muted mt-1">Add money to your account balance</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('funds.index') }}" class="btn">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Balance History
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('funds.process') }}" method="POST" id="payment-form" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Details</h3>
                </div>
                <div class="card-body">
                    <!-- Amount Input with Currency and Payment Method Selector -->
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Amount</label>
                                <div class="input-group input-group-flat">
                                    <span class="input-group-text">
                                        $
                                    </span>
                                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                        placeholder="Enter amount" step="0.01" min="1" required
                                        value="{{ old('amount') }}">
                                </div>
                                @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Payment Method</label>
                                <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="bkash" {{ old('payment_method') == 'bkash' ? 'selected' : '' }}>bKash</option>
                                    <option value="nagad" {{ old('payment_method') == 'nagad' ? 'selected' : '' }}>Nagad</option>
                                    <option value="rocket" {{ old('payment_method') == 'rocket' ? 'selected' : '' }}>Rocket</option>
                                    <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                                @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Cards -->
                    <div class="mb-4 payment-methods-container" style="display: none;">
                        <!-- bKash -->
                        <div class="payment-info" id="bkash-info">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <h3 class="card-title">bKash Payment Details</h3>
                                    <div class="mb-3">
                                        <div class="datagrid">
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">bKash Number</div>
                                                <div class="datagrid-content">
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">01XXXXXXXXX</span>
                                                        <button type="button" class="btn btn-icon btn-sm" onclick="copyNumber('01XXXXXXXXX')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Account Type</div>
                                                <div class="datagrid-content">Personal</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Account Name</div>
                                                <div class="datagrid-content">John Doe</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mb-0">
                                        <h4 class="alert-title">Important Instructions:</h4>
                                        <ul class="mb-0">
                                            <li>Send the exact amount you entered above</li>
                                            <li>Use Personal bKash account only</li>
                                            <li>Keep the Transaction ID safe</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nagad -->
                        <div class="payment-info" id="nagad-info" style="display: none;">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <h3 class="card-title">Nagad Payment Details</h3>
                                    <div class="mb-3">
                                        <div class="datagrid">
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Nagad Number</div>
                                                <div class="datagrid-content">
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">01XXXXXXXXX</span>
                                                        <button type="button" class="btn btn-icon btn-sm" onclick="copyNumber('01XXXXXXXXX')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Account Type</div>
                                                <div class="datagrid-content">Personal</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Account Name</div>
                                                <div class="datagrid-content">John Doe</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mb-0">
                                        <h4 class="alert-title">Important Instructions:</h4>
                                        <ul class="mb-0">
                                            <li>Send the exact amount you entered above</li>
                                            <li>Use Personal Nagad account only</li>
                                            <li>Keep the Transaction ID safe</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rocket -->
                        <div class="payment-info" id="rocket-info" style="display: none;">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <h3 class="card-title">Rocket Payment Details</h3>
                                    <div class="mb-3">
                                        <div class="datagrid">
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Rocket Number</div>
                                                <div class="datagrid-content">
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">01XXXXXXXXX</span>
                                                        <button type="button" class="btn btn-icon btn-sm" onclick="copyNumber('01XXXXXXXXX')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Account Type</div>
                                                <div class="datagrid-content">Personal</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Account Name</div>
                                                <div class="datagrid-content">John Doe</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mb-0">
                                        <h4 class="alert-title">Important Instructions:</h4>
                                        <ul class="mb-0">
                                            <li>Send the exact amount you entered above</li>
                                            <li>Use Personal Rocket account only</li>
                                            <li>Keep the Transaction ID safe</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer -->
                        <div class="payment-info" id="bank-info" style="display: none;">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <h3 class="card-title">Bank Account Details</h3>
                                    <div class="mb-3">
                                        <div class="datagrid">
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Bank Name</div>
                                                <div class="datagrid-content">Example Bank</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Account Name</div>
                                                <div class="datagrid-content">Your Company Name</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Account Number</div>
                                                <div class="datagrid-content">
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">1234567890</span>
                                                        <button type="button" class="btn btn-icon btn-sm" onclick="copyNumber('1234567890')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Branch</div>
                                                <div class="datagrid-content">Main Branch</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mb-0">
                                        <h4 class="alert-title">Important Instructions:</h4>
                                        <ul class="mb-0">
                                            <li>Transfer the exact amount you entered above</li>
                                            <li>Include your username in transfer reference</li>
                                            <li>Keep the transfer receipt safe</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Details -->
                    <div class="card card-sm mb-4">
                        <div class="card-body">
                            <h3 class="card-title">Transaction Details</h3>
                            
                            <!-- Sender Number -->
                            <div class="mb-3">
                                <label class="form-label required">Your Mobile/Account Number</label>
                                <input type="text" name="sender_number" class="form-control @error('sender_number') is-invalid @enderror" 
                                    placeholder="Enter the number you sent payment from"
                                    value="{{ old('sender_number') }}" required>
                                @error('sender_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Transaction ID -->
                            <div class="mb-3">
                                <label class="form-label required">Transaction ID/Reference</label>
                                <input type="text" name="transaction_reference" class="form-control @error('transaction_reference') is-invalid @enderror" 
                                    placeholder="Enter the transaction ID you received"
                                    value="{{ old('transaction_reference') }}" required>
                                @error('transaction_reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    Enter the Transaction ID you received after completing the payment
                                </small>
                            </div>

                            <!-- Payment Screenshot -->
                            <div>
                                <label class="form-label">Payment Screenshot</label>
                                <input type="file" name="payment_proof" class="form-control @error('payment_proof') is-invalid @enderror" 
                                    accept="image/*,.pdf">
                                @error('payment_proof')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    Accepted formats: JPG, PNG, PDF (Max: 2MB)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary" id="submit-button">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="loading-spinner"></span>
                        Submit Payment Details
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Information Card -->
    <div class="col-lg-4">
        <div class="row row-cards">
            <!-- Quick Actions -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Quick Actions</h3>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('funds.index') }}" class="list-group-item list-group-item-action">
                                <span class="text-primary me-2">
                                    <i class="fas fa-history"></i>
                                </span>
                                View Balance History
                            </a>
                            <a href="#" class="list-group-item list-group-item-action" onclick="window.open('https://wa.me/+8801533651935', '_blank')">
                                <span class="text-success me-2">
                                    <i class="fab fa-whatsapp fa-lg"></i>
                                </span>
                                <span class="d-flex align-items-center justify-content-between w-100">
                                    <span>Contact Admin via WhatsApp</span>
                                    <span class="badge bg-success text-white pulse-badge">01533651935</span>
                                </span>
                            </a>
                            <a href="mailto:support@example.com" class="list-group-item list-group-item-action">
                                <span class="text-info me-2">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                Email Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Important Notes</h3>
                        <div class="text-muted mb-3">
                            Please read these instructions carefully before making a payment:
                        </div>
                        <ul class="list-unstyled space-y-2">
                            <li>
                                <span class="badge bg-success me-2">
                                    <i class="fab fa-whatsapp"></i>
                                </span>
                                <strong>Contact admin on WhatsApp BEFORE making payment</strong>
                            </li>
                            <li>
                                <span class="badge bg-green-lt me-2">
                                    <i class="fas fa-check"></i>
                                </span>
                                Send exact amount entered
                            </li>
                            <li>
                                <span class="badge bg-green-lt me-2">
                                    <i class="fas fa-check"></i>
                                </span>
                                Use personal accounts only
                            </li>
                            <li>
                                <span class="badge bg-green-lt me-2">
                                    <i class="fas fa-check"></i>
                                </span>
                                Keep transaction ID safe
                            </li>
                            <li>
                                <span class="badge bg-green-lt me-2">
                                    <i class="fas fa-check"></i>
                                </span>
                                Upload payment screenshot
                            </li>
                            <li>
                                <span class="badge bg-green-lt me-2">
                                    <i class="fas fa-check"></i>
                                </span>
                                Wait for verification (24h max)
                            </li>
                            <li>
                                <span class="badge bg-success me-2">
                                    <i class="fab fa-whatsapp"></i>
                                </span>
                                <strong>Contact admin on WhatsApp for faster approval</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Need Help -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Need Help?</h3>
                        <div class="text-muted mb-3">
                            Our support team is available 24/7 to assist you:
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-phone text-primary me-2"></i>
                                +880 1XXXXXXXXX
                            </li>
                            <li class="mb-2">
                                <i class="fab fa-whatsapp text-success me-2 fa-lg"></i>
                                <strong>Admin WhatsApp:</strong> 
                                <a href="https://wa.me/+8801533651935" target="_blank" class="ms-2 btn btn-sm btn-success">
                                    <i class="fab fa-whatsapp me-1"></i>
                                    <strong>01533651935</strong>
                                    <i class="fas fa-external-link-alt ms-1" style="font-size: 0.7em;"></i>
                                </a>
                            </li>
                            <li>
                                <i class="fas fa-envelope text-info me-2"></i>
                                support@example.com
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.querySelector('select[name="payment_method"]');
    const paymentMethodsContainer = document.querySelector('.payment-methods-container');
    const paymentInfos = document.querySelectorAll('.payment-info');
    
    // Show WhatsApp contact popup on page load
    setTimeout(() => {
        if (!sessionStorage.getItem('whatsappReminderShown')) {
            Swal.fire({
                title: 'Contact Admin Before Payment!',
                html: 'For <strong>fastest approval</strong>, contact admin on WhatsApp <strong>before making your payment</strong>.<br><br>' +
                      '<a href="https://wa.me/+8801533651935?text=Hello,%20I%20want%20to%20add%20funds%20to%20my%20account.%20Please%20assist%20me." target="_blank" class="btn btn-success">' +
                      '<i class="fab fa-whatsapp me-2"></i>Contact Admin Now: 01533651935</a>',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#25D366',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Contact Admin Now',
                cancelButtonText: 'Continue to Form'
            }).then((result) => {
                sessionStorage.setItem('whatsappReminderShown', 'true');
                
                if (result.isConfirmed) {
                    // Open WhatsApp immediately
                    window.open('https://wa.me/+8801533651935?text=Hello,%20I%20want%20to%20add%20funds%20to%20my%20account.%20Please%20assist%20me.', '_blank');
                }
            });
        }
    }, 1000);
    
    // Handle payment method change
    paymentMethodSelect.addEventListener('change', function() {
        const selectedMethod = this.value;
        
        // Show/hide payment methods container
        if (selectedMethod) {
            paymentMethodsContainer.style.display = 'block';
        } else {
            paymentMethodsContainer.style.display = 'none';
        }
        
        // Hide all payment info sections
        paymentInfos.forEach(info => info.style.display = 'none');
        
        // Show selected payment info
        const selectedInfo = document.getElementById(selectedMethod + '-info');
        if (selectedInfo) {
            selectedInfo.style.display = 'block';
        }
    });

    // Trigger change event on load if a method is selected
    if (paymentMethodSelect.value) {
        paymentMethodSelect.dispatchEvent(new Event('change'));
    }

    // Form submission
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const loadingSpinner = document.getElementById('loading-spinner');

    form.addEventListener('submit', function(e) {
        // Check if user has been reminded to contact admin
        if (!window.paymentReminderShown) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Did You Contact Admin?',
                html: 'For <strong>instant approval</strong>, we recommend contacting admin on WhatsApp before submitting.<br><br>' +
                      '<a href="https://wa.me/+8801533651935?text=Hello,%20I%20want%20to%20add%20funds%20to%20my%20account.%20Please%20assist%20me." target="_blank" class="btn btn-success">' +
                      '<i class="fab fa-whatsapp me-2"></i>Contact Admin: 01533651935</a>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#25D366',
                confirmButtonText: 'Submit Anyway',
                cancelButtonText: 'Contact Admin First'
            }).then((result) => {
                window.paymentReminderShown = true;
                
                if (result.isConfirmed) {
                    // Submit the form
                    submitButton.disabled = true;
                    loadingSpinner.classList.remove('d-none');
                    form.submit();
                } else {
                    // Open WhatsApp
                    window.open('https://wa.me/+8801533651935?text=Hello,%20I%20want%20to%20add%20funds%20to%20my%20account.%20Please%20assist%20me.', '_blank');
                }
            });
            
            return false;
        }
        
        // Normal submission process
        submitButton.disabled = true;
        loadingSpinner.classList.remove('d-none');
    });
});

// Copy number function
function copyNumber(number) {
    navigator.clipboard.writeText(number).then(() => {
        const button = event.target.closest('button');
        const icon = button.querySelector('i');
        const originalClass = icon.className;
        
        icon.className = 'fas fa-check text-success';
        button.classList.add('btn-success');
        
        setTimeout(() => {
            icon.className = originalClass;
            button.classList.remove('btn-success');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy text: ', err);
    });
}
</script>
@endpush

@push('styles')
<style>
.payment-method-card {
    display: block;
    cursor: pointer;
    margin: 0;
    padding: 0;
}

.payment-method-input {
    display: none;
}

.payment-method-input:checked + .card {
    border-color: var(--tblr-primary);
    border-width: 2px;
}

.payment-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    margin-right: 1rem;
    font-size: 1.25rem;
}

.datagrid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

.datagrid-item {
    padding: 0.5rem 0;
}

.datagrid-title {
    font-size: 0.875rem;
    color: var(--tblr-muted);
    margin-bottom: 0.25rem;
}

.datagrid-content {
    font-weight: 500;
}

.space-y-2 > * + * {
    margin-top: 0.5rem;
}

.form-select {
    padding: 0.5rem 2.25rem 0.5rem 0.75rem;
    background-color: #fff;
    border: 1px solid var(--tblr-border-color);
    border-radius: var(--tblr-border-radius);
    font-size: 0.875rem;
}

.form-select:focus {
    border-color: var(--tblr-primary);
    box-shadow: 0 0 0 0.25rem rgba(var(--tblr-primary-rgb), 0.1);
}

.payment-methods-container {
    transition: all 0.3s ease;
}

/* WhatsApp pulse animation */
.pulse-badge {
    position: relative;
    animation: pulse 2s infinite;
    box-shadow: 0 0 0 rgba(40, 167, 69, 0.4);
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
    }
}

/* WhatsApp Banner Styling */
.whatsapp-banner {
    background: linear-gradient(135deg, #dcfce7 0%, #22c55e 100%);
    border: none;
    border-radius: 12px;
}

.whatsapp-banner .btn-success {
    background-color: #25D366;
    border-color: #25D366;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.whatsapp-banner .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.whatsapp-icon-pulse {
    animation: whatsappPulse 2s infinite;
}

@keyframes whatsappPulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}
</style>
@endpush 