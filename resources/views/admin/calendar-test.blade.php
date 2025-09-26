@extends('layouts.admin')

@section('title', 'Calendar Test - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Calendar Test</h1>
        <button class="btn btn-primary" onclick="location.reload()">
            <i class="fas fa-sync-alt me-2"></i>Reload Page
        </button>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Simple Calendar Test</h6>
                </div>
                <div class="card-body">
                    <div id="calendar" style="height: 400px; border: 1px solid #ddd; padding: 10px;">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading calendar...</span>
                            </div>
                            <p class="mt-2">Loading calendar...</p>
                        </div>
                    </div>
                    <div id="debug-info" class="mt-3">
                        <h6>Debug Information:</h6>
                        <ul id="debug-list"></ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Test Controls</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-outline-primary mb-2" onclick="testFullCalendar()">
                        Test FullCalendar Load
                    </button>
                    <br>
                    <button class="btn btn-outline-secondary mb-2" onclick="testCalendarElement()">
                        Test Calendar Element
                    </button>
                    <br>
                    <button class="btn btn-outline-info mb-2" onclick="testCDN()">
                        Test CDN Links
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
#calendar {
    min-height: 400px;
    width: 100%;
    height: 500px;
}

.debug-item {
    padding: 5px;
    margin: 2px 0;
    border-radius: 4px;
}

.debug-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.debug-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.debug-info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
let calendar = null;
let debugList = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Test calendar page loaded');
    debugList = document.getElementById('debug-list');
    
    addDebugInfo('Page loaded successfully', 'success');
    
    // Wait a bit for CDN to load
    setTimeout(() => {
        initializeCalendar();
    }, 1000);
});

function addDebugInfo(message, type = 'info') {
    if (!debugList) return;
    
    const li = document.createElement('li');
    li.className = `debug-item debug-${type}`;
    li.textContent = `${new Date().toLocaleTimeString()}: ${message}`;
    debugList.appendChild(li);
    console.log(message);
}

function initializeCalendar() {
    addDebugInfo('Initializing calendar...', 'info');
    
    // Check if FullCalendar is loaded
    if (typeof FullCalendar === 'undefined') {
        addDebugInfo('ERROR: FullCalendar is not loaded! Check CDN connection.', 'error');
        document.getElementById('calendar').innerHTML = `
            <div class="alert alert-danger">
                <h5>Calendar Failed to Load</h5>
                <p>FullCalendar library could not be loaded. Please check your internet connection and try again.</p>
                <button class="btn btn-primary" onclick="location.reload()">Reload Page</button>
            </div>
        `;
        return;
    }
    
    addDebugInfo('FullCalendar loaded successfully', 'success');
    
    // Initialize FullCalendar
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        addDebugInfo('ERROR: Calendar element not found!', 'error');
        return;
    }
    
    addDebugInfo('Calendar element found', 'success');
    
    try {
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            events: [
                {
                    id: 'test',
                    title: 'Test Appointment',
                    start: new Date().toISOString().split('T')[0] + 'T10:00:00',
                            backgroundColor: '#730623',
        borderColor: '#730623',
                    textColor: '#fff'
                },
                {
                    id: 'test2',
                    title: 'Another Test',
                    start: new Date().toISOString().split('T')[0] + 'T14:00:00',
                            backgroundColor: '#032a57',
        borderColor: '#032a57',
                    textColor: '#fff'
                }
            ],
            height: 'auto',
            eventClick: function(info) {
                alert('Clicked event: ' + info.event.title);
            }
        });
        
        addDebugInfo('Calendar object created', 'success');
        
        calendar.render();
        addDebugInfo('Calendar rendered successfully', 'success');
        
        // Clear loading message
        calendarEl.innerHTML = '';
        calendar.render();
        
    } catch (error) {
        addDebugInfo('ERROR: ' + error.message, 'error');
        console.error('Calendar error:', error);
        document.getElementById('calendar').innerHTML = `
            <div class="alert alert-danger">
                <h5>Calendar Error</h5>
                <p>Error: ${error.message}</p>
                <button class="btn btn-primary" onclick="location.reload()">Reload Page</button>
            </div>
        `;
    }
}

function testFullCalendar() {
    if (typeof FullCalendar !== 'undefined') {
        addDebugInfo('FullCalendar is available', 'success');
        alert('FullCalendar is loaded successfully!');
    } else {
        addDebugInfo('FullCalendar is NOT available', 'error');
        alert('FullCalendar is not loaded!');
    }
}

function testCalendarElement() {
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        addDebugInfo('Calendar element exists', 'success');
        alert('Calendar element found!');
    } else {
        addDebugInfo('Calendar element NOT found', 'error');
        alert('Calendar element not found!');
    }
}

function testCDN() {
    // Test CSS
    const cssLink = document.querySelector('link[href*="fullcalendar"]');
    if (cssLink) {
        addDebugInfo('FullCalendar CSS loaded', 'success');
    } else {
        addDebugInfo('FullCalendar CSS NOT loaded', 'error');
    }
    
    // Test JS
    const jsScript = document.querySelector('script[src*="fullcalendar"]');
    if (jsScript) {
        addDebugInfo('FullCalendar JS loaded', 'success');
    } else {
        addDebugInfo('FullCalendar JS NOT loaded', 'error');
    }
}
</script>
@endpush
@endsection 