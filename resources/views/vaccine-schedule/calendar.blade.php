@extends('layouts.app')

@section('title', 'Vaccine Calendar')

@push('styles')
    {{-- Add FullCalendar related CSS --}}
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
    {{-- FullCalendar Core CSS is usually included via JS bundle, but good to have base styles --}}
    <style>
        #calendar-container {
            padding: 1rem;
            background-color: #fff;
            border-radius: 0.375rem; /* Match card radius */
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); /* Add subtle shadow */
        }
        /* Customize event colors based on status (using colors from controller) */
        .fc-event {
            border: none !important; /* Override default border if needed */
            color: #fff !important; /* Ensure text is white */
            padding: 2px 5px;
            cursor: pointer;
        }
        /* Popover styling */
        .popover-header {
            font-weight: bold;
        }
        .popover-body {
            font-size: 0.9rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-calendar-day"></i> Vaccine Schedule Calendar</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('vaccine-schedule.timetable') }}" class="btn btn-warning me-1" title="Timetable View">
                    <i class="fas fa-list-alt"></i> Timetable
                </a>
                <a href="{{ route('vaccine-schedule.index') }}" class="btn btn-secondary" title="List View">
                    <i class="fas fa-list"></i> List View
                </a>
                <a href="{{ route('vaccine-schedule.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Schedule Item
                </a>
            </div>
        </div>

        {{-- Add a legend (optional) --}}
        <div class="mb-3">
            <span class="badge bg-info me-2">Scheduled</span>
            <span class="badge bg-success me-2">Administered</span>
            <span class="badge bg-danger me-2">Missed</span>
        </div>

        <div id="calendar-container">
            <div id='calendar'></div>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- FullCalendar Core and Plugins JS --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    {{-- Bootstrap 5 Plugin for FullCalendar (optional, for theming) --}}
    {{-- <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.11/index.global.min.js'></script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            // Event data passed from the controller
            {{--// Use {!! !!} to output raw JSON without escaping quotes--}}
            var calendarEvents = {!! json_encode($events) !!};

            var calendar = new FullCalendar.Calendar(calendarEl, {
                // --- Basic Setup ---
                themeSystem: 'bootstrap5', // Use Bootstrap 5 theming
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' // View options
                },
                initialView: 'dayGridMonth', // Default view
                weekends: true, // Show weekends
                navLinks: true, // Clickable day/week names to navigate views
                editable: false, // Don't allow dragging/resizing events
                dayMaxEvents: true, // Allow "more" link when too many events

                // --- Events ---
                events: calendarEvents, // Load events passed from controller
                eventColor: '#378006', // Default event color (can be overridden by event.color)

                // --- Event Rendering & Interaction ---
                eventDidMount: function(info) {
                    // Attach Bootstrap Popover for details on hover
                    var props = info.event.extendedProps;
                    var popoverContent = `
                    <strong>Batch:</strong> ${props.batch || 'N/A'}<br>
                    <strong>Vaccine:</strong> ${props.vaccine || 'N/A'}<br>
                    <strong>Status:</strong> <span class="badge bg-${props.status === 'administered' ? 'success' : (props.status === 'missed' ? 'danger' : 'info')}">${props.status ? props.status.charAt(0).toUpperCase() + props.status.slice(1) : 'N/A'}</span>
                `;

                    var popover = new bootstrap.Popover(info.el, {
                        title: info.event.title,
                        content: popoverContent,
                        placement: 'auto',
                        trigger: 'hover', // Show on hover
                        html: true, // Allow HTML in content
                        container: 'body', // Avoid issues within calendar constraints
                        customClass: 'vaccine-popover' // Optional custom class
                    });
                }, // <<< Comma after function definition

                eventClick: function(info) {
                    // Prevent default browser navigation if URL exists
                    info.jsEvent.preventDefault();
                    if (info.event.url) {
                        // Open the event URL (the show view for the schedule item)
                        window.open(info.event.url, "_self"); // Open in same tab
                    }
                } // <<< No comma after the last function/property

            }); // <<< Closing brace and parenthesis for new FullCalendar.Calendar()

            calendar.render();
        }); // <<< Closing brace and parenthesis for addEventListener()
    </script>
@endpush
