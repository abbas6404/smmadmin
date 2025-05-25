@extends('frontend.layouts.master')

@section('title', 'Facebook UID Finder')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Facebook UID Finder</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This tool extracts <strong>numeric Facebook IDs</strong> from URLs. Paste any Facebook URL below and we'll try to extract the numeric ID (e.g., 100011200601769).
                    </div>
                    
                    <form id="uidFinderForm" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-10">
                                <label for="url" class="form-label">Facebook URL</label>
                                <input type="url" class="form-control" id="url" name="url" placeholder="https://www.facebook.com/username or https://www.facebook.com/profile.php?id=100011200601769" required>
                                <div class="invalid-feedback" id="urlError"></div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100" id="extractButton">
                                    <i class="fas fa-search me-2"></i>Extract UID
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="platform" id="platform" value="facebook">
                    </form>
                    
                    <div id="resultContainer" class="d-none">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Extraction Result</h5>
                                <div class="alert alert-success" id="resultMessage"></div>
                                
                                <div class="mb-3">
                                    <label for="resultUID" class="form-label fw-bold">Numeric Facebook ID:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="resultUID" readonly>
                                        <button class="btn btn-outline-primary" type="button" id="copyButton">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">This is a unique numeric identifier that can be used for targeting Facebook accounts in your orders.</small>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-secondary btn-sm" id="resetButton">
                                        <i class="fas fa-redo me-1"></i> Extract Another
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm" id="useIdButton">
                                        <i class="fas fa-check-circle me-1"></i> Use This ID
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <h5>Supported Facebook URL Formats</h5>
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <i class="fab fa-facebook me-2"></i>Facebook URL Examples
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>Profile URL: <code>https://www.facebook.com/username</code></li>
                                    <li>Profile ID URL: <code>https://www.facebook.com/profile.php?id=100011200601769</code></li>
                                    <li>Page URL: <code>https://www.facebook.com/pagename</code></li>
                                    <li>Page ID URL: <code>https://www.facebook.com/pages/name/123456789012345</code></li>
                                    <li>Short URL: <code>https://fb.com/username</code></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <i class="fas fa-external-link-alt me-2"></i>External UID Finder Services
                            </div>
                            <div class="card-body">
                                <p>If our tool can't extract the UID, you can try these external services:</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                <h5 class="card-title">Lookup-ID.com</h5>
                                                <p class="card-text">Official service for finding Facebook numeric IDs</p>
                                                <a href="https://lookup-id.com/" target="_blank" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-external-link-alt me-1"></i> Visit Website
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                <h5 class="card-title">GetUID.live</h5>
                                                <p class="card-text">Alternative tool for extracting Facebook UIDs</p>
                                                <a href="https://getuid.live/" target="_blank" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-external-link-alt me-1"></i> Visit Website
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const form = $('#uidFinderForm');
    const resultContainer = $('#resultContainer');
    const resultMessage = $('#resultMessage');
    const resultUID = $('#resultUID');
    const extractButton = $('#extractButton');
    const copyButton = $('#copyButton');
    const resetButton = $('#resetButton');
    const useIdButton = $('#useIdButton');
    const urlInput = $('#url');
    
    // Handle form submission
    form.on('submit', function(e) {
        e.preventDefault();
        
        // Reset previous errors
        urlInput.removeClass('is-invalid');
        $('#urlError').text('');
        
        // Get form values
        const url = urlInput.val().trim();
        
        // Basic validation
        if (!url) {
            urlInput.addClass('is-invalid');
            $('#urlError').text('Please enter a valid Facebook URL');
            return;
        }
        
        // Check if it's a Facebook URL
        if (!url.includes('facebook.com') && !url.includes('fb.com')) {
            urlInput.addClass('is-invalid');
            $('#urlError').text('Please enter a valid Facebook URL');
            return;
        }
        
        // Show loading state
        extractButton.prop('disabled', true);
        extractButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Extracting...');
        
        // Send AJAX request
        $.ajax({
            url: '{{ route("uid-finder.extract") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                url: url,
                platform: 'facebook'
            },
            success: function(response) {
                // Show result
                resultContainer.removeClass('d-none');
                resultMessage.html('<strong>Success!</strong> ' + response.message);
                resultUID.val(response.uid);
                
                // Store original URL for reference
                resultUID.data('source-url', url);
                
                // Scroll to result
                $('html, body').animate({
                    scrollTop: resultContainer.offset().top - 100
                }, 500);
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                
                if (response && response.errors && response.errors.url) {
                    urlInput.addClass('is-invalid');
                    $('#urlError').text(response.errors.url[0]);
                } else if (response && response.message) {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Extraction Failed',
                        text: response.message,
                        confirmButtonColor: '#3085d6'
                    });
                } else {
                    // Generic error
                    Swal.fire({
                        icon: 'error',
                        title: 'Extraction Failed',
                        text: 'An unexpected error occurred. Please try again.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            },
            complete: function() {
                // Reset button state
                extractButton.prop('disabled', false);
                extractButton.html('<i class="fas fa-search me-2"></i>Extract UID');
            }
        });
    });
    
    // Handle copy button
    copyButton.on('click', function() {
        resultUID.select();
        document.execCommand('copy');
        
        // Show copied feedback
        const originalText = copyButton.html();
        copyButton.html('<i class="fas fa-check"></i> Copied!');
        setTimeout(function() {
            copyButton.html(originalText);
        }, 1500);
    });
    
    // Handle reset button
    resetButton.on('click', function() {
        // Clear the form
        urlInput.val('');
        
        // Hide results
        resultContainer.addClass('d-none');
        
        // Focus on URL input
        urlInput.focus();
    });
    
    // Handle use ID button
    useIdButton.on('click', function() {
        const uid = resultUID.val();
        
        // Show confirmation
        Swal.fire({
            icon: 'success',
            title: 'ID Ready to Use',
            text: `The Facebook UID "${uid}" has been prepared for use in your orders.`,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Continue to Orders'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to orders page
                window.location.href = '{{ route("orders.index") }}';
            }
        });
        
        // You can also store the UID in localStorage for later use
        localStorage.setItem('facebook_uid', uid);
        localStorage.setItem('facebook_url', resultUID.data('source-url'));
    });
});
</script>
@endpush 