@extends('layouts.admin')

@section('title', 'Settings - Admin Dashboard')

@section('content')
<style>
    .logo-upload-section {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: border-color 0.3s ease;
    }
    
    .logo-upload-section:hover {
        border-color: #730623;
    }
    
    .current-logo-preview {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 10px;
        background: #f8f9fa;
    }
    
    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }
    
    .file-input-wrapper input[type=file] {
        font-size: 100px;
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        cursor: pointer;
    }
    
    .file-input-wrapper .btn {
        position: relative;
        z-index: 1;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Admin Settings</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Logo Settings -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Logo Settings</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.logo') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="logo" class="form-label">Upload New Logo</label>
                                    <div class="logo-upload-section">
                                        <div class="file-input-wrapper">
                                            <input type="file" class="form-control" id="logo" name="logo" 
                                                   accept="image/*" required>
                                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('logo').click()">
                                                <i class="fas fa-cloud-upload-alt me-2"></i>Choose File
                                            </button>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">Click the button above or drag & drop your logo here</small>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">Recommended size: 200x200px or larger</small><br>
                                            <small class="text-muted">Supported formats: PNG, JPG, JPEG, GIF (Max: 2MB)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Current Logo</label>
                                    <div class="text-center">
                                        <img src="{{ asset('images/logo.png') }}" alt="Current Logo" 
                                             class="img-fluid current-logo-preview" style="max-height: 100px; max-width: 100%;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload Logo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Profile Settings -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Settings</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ old('name', $admin->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email', $admin->email) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        <option value="">Select timezone</option>
                                        <option value="UTC" {{ old('timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                        <option value="America/New_York" {{ old('timezone') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                        <option value="America/Chicago" {{ old('timezone') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                        <option value="America/Denver" {{ old('timezone') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                        <option value="America/Los_Angeles" {{ old('timezone') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email_template" class="form-label">Default Email Template</label>
                            <textarea class="form-control" id="email_template" name="email_template" rows="5" 
                                      placeholder="Enter your default email template...">{{ old('email_template') }}</textarea>
                            <div class="form-text">This template will be used for automated emails to clients.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stripe Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Stripe Configuration</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.stripe') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="stripe_key" class="form-label">Stripe Publishable Key</label>
                            <input type="text" class="form-control" id="stripe_key" name="stripe_key" 
                                   value="{{ old('stripe_key', $stripeSettings['stripe_key']) }}" 
                                   placeholder="pk_test_...">
                            <div class="form-text">Your Stripe publishable key (starts with pk_test_ or pk_live_)</div>
                        </div>

                        <div class="mb-3">
                            <label for="stripe_secret" class="form-label">Stripe Secret Key</label>
                            <input type="password" class="form-control" id="stripe_secret" name="stripe_secret" 
                                   value="{{ old('stripe_secret', $stripeSettings['stripe_secret']) }}" 
                                   placeholder="sk_test_...">
                            <div class="form-text">Your Stripe secret key (starts with sk_test_ or sk_live_)</div>
                        </div>

                        <div class="mb-3">
                            <label for="stripe_webhook_secret" class="form-label">Stripe Webhook Secret</label>
                            <input type="password" class="form-control" id="stripe_webhook_secret" name="stripe_webhook_secret" 
                                   value="{{ old('stripe_webhook_secret', $stripeSettings['stripe_webhook_secret']) }}" 
                                   placeholder="whsec_...">
                            <div class="form-text">Your Stripe webhook signing secret (starts with whsec_)</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Stripe Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SMTP Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">SMTP Email Configuration</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.smtp') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_mailer" class="form-label">Mail Driver</label>
                                    <select class="form-select" id="mail_mailer" name="mail_mailer" required>
                                        <option value="smtp" {{ old('mail_mailer', $smtpSettings['mail_mailer']) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="mailgun" {{ old('mail_mailer', $smtpSettings['mail_mailer']) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                        <option value="ses" {{ old('mail_mailer', $smtpSettings['mail_mailer']) == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_host" class="form-label">SMTP Host</label>
                                    <input type="text" class="form-control" id="mail_host" name="mail_host" 
                                           value="{{ old('mail_host', $smtpSettings['mail_host']) }}" 
                                           placeholder="smtp.mailtrap.io" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_port" class="form-label">SMTP Port</label>
                                    <input type="number" class="form-control" id="mail_port" name="mail_port" 
                                           value="{{ old('mail_port', $smtpSettings['mail_port']) }}" 
                                           placeholder="2525" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_encryption" class="form-label">Encryption</label>
                                    <select class="form-select" id="mail_encryption" name="mail_encryption">
                                        <option value="">None</option>
                                        <option value="tls" {{ old('mail_encryption', $smtpSettings['mail_encryption']) == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('mail_encryption', $smtpSettings['mail_encryption']) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_username" class="form-label">SMTP Username</label>
                                    <input type="text" class="form-control" id="mail_username" name="mail_username" 
                                           value="{{ old('mail_username', $smtpSettings['mail_username']) }}" 
                                           placeholder="Your SMTP username">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_password" class="form-label">SMTP Password</label>
                                    <input type="password" class="form-control" id="mail_password" name="mail_password" 
                                           value="{{ old('mail_password', $smtpSettings['mail_password']) }}" 
                                           placeholder="Your SMTP password">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_from_address" class="form-label">From Email Address</label>
                                    <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" 
                                           value="{{ old('mail_from_address', $smtpSettings['mail_from_address']) }}" 
                                           placeholder="noreply@example.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_from_name" class="form-label">From Name</label>
                                    <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" 
                                           value="{{ old('mail_from_name', $smtpSettings['mail_from_name']) }}" 
                                           placeholder="Your Company Name" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save SMTP Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Info -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Information</h6>
                </div>
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $admin->name }}</h5>
                    <p class="text-muted">{{ $admin->email }}</p>
                    <hr>
                    <div class="text-start">
                        <p><strong>Role:</strong> Administrator</p>
                        <p><strong>Member since:</strong> {{ $admin->created_at->format('M d, Y') }}</p>
                        <p><strong>Last login:</strong> {{ $admin->updated_at->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a href="{{ route('admin.clients') }}" class="btn btn-outline-info">
                            <i class="fas fa-users me-2"></i>Manage Clients
                        </a>
                        <a href="{{ route('admin.appointments') }}" class="btn btn-outline-success">
                            <i class="fas fa-calendar me-2"></i>View Appointments
                        </a>
                        <a href="{{ route('admin.messages') }}" class="btn btn-outline-warning">
                            <i class="fas fa-comments me-2"></i>Messages
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-resize textarea
    document.getElementById('email_template').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });

    // Logo preview functionality
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                this.value = '';
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (PNG, JPG, JPEG, or GIF)');
                this.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.querySelector('.current-logo-preview');
                if (preview) {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Drag and drop functionality
    const logoUploadSection = document.querySelector('.logo-upload-section');
    
    logoUploadSection.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#730623';
        this.style.backgroundColor = '#f8f9fa';
    });
    
    logoUploadSection.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = '#dee2e6';
        this.style.backgroundColor = 'transparent';
    });
    
    logoUploadSection.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '#dee2e6';
        this.style.backgroundColor = 'transparent';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            const logoInput = document.getElementById('logo');
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please drop an image file');
                return;
            }
            
            // Validate file size
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                return;
            }
            
            // Set the file to the input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            logoInput.files = dataTransfer.files;
            
            // Trigger change event to show preview
            logoInput.dispatchEvent(new Event('change'));
        }
    });

    // Form validation
    document.querySelector('form[action*="logo"]').addEventListener('submit', function(e) {
        const logoInput = document.getElementById('logo');
        if (!logoInput.files[0]) {
            e.preventDefault();
            alert('Please select a logo file to upload');
            return;
        }
    });
</script>
@endpush
@endsection 