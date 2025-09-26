@extends('layouts.admin')

@section('title', 'Messages - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Client Messages</h1>
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
        <!-- Client List -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Clients</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($clients as $client)
                            <a href="{{ route('admin.messages') }}?client_id={{ $client->id }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request('client_id') == $client->id ? 'active' : '' }}"
                               style="{{ request('client_id') == $client->id ? 'background-color: #730623 !important; border-color: #730623 !important;' : '' }}">
                                <div>
                                    <div class="fw-bold">{{ $client->name }}</div>
                                    <small class="text-muted">{{ $client->email }}</small>
                                </div>
                                @if($client->messages_count > 0)
                                    <span class="badge bg-danger rounded-pill">{{ $client->messages_count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        @if($selectedClient)
                            Messages with {{ $selectedClient->name }}
                        @else
                            Select a client to view messages
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if($selectedClient)
                        <!-- Message History -->
                        <div class="messages-container mb-4" style="height: 400px; overflow-y: auto;">
                            @if($messages->count() > 0)
                                @foreach($messages as $message)
                                    <div class="message {{ $message->sender_type === 'admin' ? 'message-outgoing' : 'message-incoming' }}">
                                                                            <div class="message-content">
                                        <div class="message-header">
                                            <strong>{{ ucfirst($message->sender_type) }}</strong>
                                            <small class="text-muted">{{ $message->created_at->format('M d, Y g:i A') }}</small>
                                        </div>
                                        <div class="message-text">
                                            @if(!empty($message->message))
                                                {{ $message->message }}
                                            @elseif($message->hasAttachment())
                                                <em class="text-muted">File attached</em>
                                            @endif
                                        </div>
                                        
                                        <!-- Read Receipt -->
                                        @if($message->sender_type === 'admin')
                                            <div class="read-receipt mt-1">
                                                <small class="text-white-50">
                                                    <i class="{{ $message->getReadReceiptIcon() }}"></i> 
                                                    {{ ucfirst($message->getReadReceiptStatus()) }}
                                                </small>
                                            </div>
                                        @endif
                                        
                                        @if($message->hasAttachment())
                                            <div class="message-attachment mt-2">
                                                <div class="attachment-preview">
                                                    @if(str_contains(strtolower($message->attachment_type), 'image'))
                                                        <img src="{{ $message->getAttachmentUrl() }}" 
                                                             alt="{{ $message->attachment_name }}" 
                                                             class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                                    @else
                                                        <div class="attachment-file">
                                                            <i class="{{ $message->getFileIcon() }} fa-2x"></i>
                                                            <div class="attachment-info">
                                                                <div class="attachment-name">{{ $message->attachment_name }}</div>
                                                                <div class="attachment-size">{{ $message->getFormattedFileSize() }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <a href="{{ route('admin.messages.attachment', $message) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No messages yet</p>
                                </div>
                            @endif
                        </div>

                        <!-- Send Message Form -->
                        <form action="{{ route('admin.messages.send') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $selectedClient->id }}">
                            
                            <!-- Message Input -->
                            <div class="input-group mb-2">
                                <textarea class="form-control" name="message" rows="3" 
                                          placeholder="Type your message..."></textarea>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            
                            <!-- Attachment Section -->
                            <div class="attachment-section">
                                <div class="d-flex align-items-center">
                                    <label for="attachment" class="btn btn-outline-secondary btn-sm me-2 mb-0">
                                        <i class="fas fa-paperclip"></i> Attach File
                                    </label>
                                    <input type="file" id="attachment" name="attachment" class="d-none" 
                                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                                    <small class="text-muted" id="file-info">No size limit</small>
                                </div>
                                <div id="selected-file" class="mt-2" style="display: none;">
                                    <div class="alert alert-info py-2">
                                        <i class="fas fa-file me-2"></i>
                                        <span id="file-name"></span>
                                        <button type="button" class="btn-close float-end" onclick="removeFile()"></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Select a client to start messaging</h5>
                            <p class="text-muted">Choose a client from the list to view and send messages</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.messages-container {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    padding: 1rem;
    background: #f8f9fa;
}

.message {
    margin-bottom: 1rem;
}

.message-content {
    max-width: 70%;
    padding: 0.75rem;
    border-radius: 0.35rem;
    position: relative;
}

.message-incoming .message-content {
    background: #fff;
    border: 1px solid #e3e6f0;
    margin-right: auto;
}

.message-outgoing .message-content {
    background: #730623;
    color: #fff;
    margin-left: auto;
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.message-outgoing .message-header {
    color: rgba(255, 255, 255, 0.8);
}

.message-text {
    word-wrap: break-word;
}

/* Attachment Styles */
.message-attachment {
    border-top: 1px solid rgba(0,0,0,0.1);
    padding-top: 0.5rem;
    margin-top: 0.5rem;
}

.attachment-preview {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.attachment-file {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: rgba(0,0,0,0.05);
    border-radius: 0.25rem;
    min-width: 200px;
}

.attachment-info {
    flex: 1;
}

.attachment-name {
    font-weight: 500;
    font-size: 0.875rem;
    word-break: break-word;
}

.attachment-size {
    font-size: 0.75rem;
    color: #6c757d;
}

.message-outgoing .attachment-file {
    background: rgba(255,255,255,0.2);
}

.message-outgoing .attachment-size {
    color: rgba(255,255,255,0.7);
}

/* File input styling */
.attachment-section {
    border-top: 1px solid #e3e6f0;
    padding-top: 0.5rem;
}

#selected-file .alert {
    margin-bottom: 0;
    padding: 0.5rem 0.75rem;
}

#selected-file .btn-close {
    padding: 0.25rem;
    font-size: 0.75rem;
}

/* Update button colors to match the new theme */
.btn-primary {
    background-color: #730623 !important;
    border-color: #730623 !important;
}

.btn-primary:hover {
    background-color: #8a0a2a !important;
    border-color: #8a0a2a !important;
}

.btn-outline-primary {
    color: #730623 !important;
    border-color: #730623 !important;
}

.btn-outline-primary:hover {
    background-color: #730623 !important;
    border-color: #730623 !important;
}
</style>

@push('scripts')
<script>
    // Auto-scroll to bottom of messages
    $(document).ready(function() {
        const messagesContainer = $('.messages-container');
        if (messagesContainer.length) {
            messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
        }
        
        // File input handling
        setupFileInput();
        
        // Form validation
        setupFormValidation();
    });
    
    function setupFormValidation() {
        const messageForm = document.querySelector('form[action*="messages.send"]');
        const messageInput = document.querySelector('textarea[name="message"]');
        const fileInput = document.getElementById('attachment');
        
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                const hasMessage = messageInput && messageInput.value.trim() !== '';
                const hasAttachment = fileInput && fileInput.files.length > 0;
                
                if (!hasMessage && !hasAttachment) {
                    e.preventDefault();
                    alert('Please provide a message or attachment.');
                    return;
                }
            });
        }
    }
    
    function setupFileInput() {
        const fileInput = document.getElementById('attachment');
        const fileInfo = document.getElementById('file-info');
        const selectedFile = document.getElementById('selected-file');
        const fileName = document.getElementById('file-name');
        
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Show selected file
                    fileName.textContent = file.name;
                    selectedFile.style.display = 'block';
                    fileInfo.textContent = `Selected: ${file.name} (${formatFileSize(file.size)})`;
                } else {
                    selectedFile.style.display = 'none';
                    fileInfo.textContent = 'No size limit';
                }
            });
        }
    }
    
    function removeFile() {
        const fileInput = document.getElementById('attachment');
        const fileInfo = document.getElementById('file-info');
        const selectedFile = document.getElementById('selected-file');
        
        fileInput.value = '';
        selectedFile.style.display = 'none';
        fileInfo.textContent = 'No size limit';
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
</script>
@endpush
@endsection 