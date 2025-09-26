@extends('layouts.client')

@section('title', 'Messages - Client')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Messages</h1>
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
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-comments me-2"></i>Chat with Lana Amawi
                </h5>
            </div>
            <div class="card-body p-0">
                <!-- Chat Messages -->
                <div class="chat-container" style="height: 400px; overflow-y: auto;">
                    <div class="p-3">
                        @if(isset($messages) && count($messages) > 0)
                            @foreach($messages as $message)
                                <div class="d-flex mb-3 {{ $message->sender_type === 'client' ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="chat-message {{ $message->sender_type === 'client' ? 'bg-primary text-white' : 'bg-light' }}" 
                                         style="max-width: 70%; border-radius: 15px; padding: 10px 15px;">
                                        <div class="d-flex align-items-center mb-1">
                                            <strong class="me-2">
                                                {{ $message->sender_type === 'client' ? Auth::user()->name : 'Lana Amawi' }}
                                            </strong>
                                            <small class="text-muted">
                                                {{ $message->created_at->format('g:i A') }}
                                            </small>
                                        </div>
                                        <p class="mb-0">
                                            @if(!empty($message->message))
                                                {{ $message->message }}
                                            @elseif($message->hasAttachment())
                                                <em class="text-muted">File attached</em>
                                            @endif
                                        </p>
                                        
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
                                                    <a href="{{ route('client.messages.attachment', $message) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($message->sender_type === 'admin')
                                            <small class="text-muted">
                                                <i class="fas fa-check-double"></i> Read
                                            </small>
                                        @elseif($message->sender_type === 'client')
                                            <div class="read-receipt mt-1">
                                                <small class="text-white-50">
                                                    <i class="{{ $message->getReadReceiptIcon() }}"></i> 
                                                    {{ ucfirst($message->getReadReceiptStatus()) }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-comments text-muted mb-3" style="font-size: 3rem;"></i>
                                <h6 class="text-muted">No messages yet</h6>
                                <p class="text-muted">Start a conversation with Lana to get the most out of your coaching.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Message Input -->
                <div class="border-top p-3">
                    <form method="POST" action="{{ route('client.messages.send') }}" id="messageForm" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Message Input -->
                        <div class="input-group mb-2">
                            <textarea class="form-control" name="message" id="messageInput" rows="2" 
                                      placeholder="Type your message here..."></textarea>
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
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-container {
    scrollbar-width: thin;
    scrollbar-color: #730623 #f8f9fa;
}

.chat-container::-webkit-scrollbar {
    width: 6px;
}

.chat-container::-webkit-scrollbar-track {
    background: #f8f9fa;
}

.chat-container::-webkit-scrollbar-thumb {
    background: #730623;
    border-radius: 3px;
}

.chat-message {
    word-wrap: break-word;
}

.chat-message.bg-primary {
    background-color: #730623 !important;
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

.chat-message.bg-light {
    background-color: #f8f9fa !important;
    border: 1px solid #dee2e6;
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

.chat-message.bg-primary .attachment-file {
    background: rgba(255,255,255,0.2);
}

.chat-message.bg-primary .attachment-size {
    color: rgba(255,255,255,0.7);
}

/* File input styling */
.attachment-section {
    border-top: 1px solid #dee2e6;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.querySelector('.chat-container');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');

    // Auto-scroll to bottom
    chatContainer.scrollTop = chatContainer.scrollHeight;

    // Handle form submission
    messageForm.addEventListener('submit', function(e) {
        const fileInput = document.getElementById('attachment');
        const hasMessage = messageInput.value.trim() !== '';
        const hasAttachment = fileInput && fileInput.files.length > 0;
        
        if (!hasMessage && !hasAttachment) {
            e.preventDefault();
            alert('Please provide a message or attachment.');
            return;
        }
        
        // Add loading state
        const submitBtn = messageForm.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        submitBtn.disabled = true;
    });

    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });
    
    // File input handling
    setupFileInput();
});

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
@endsection 