<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Farm ERP - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    {{-- Sidebar Styling --}}
    <style>
        body {
            overflow-x: hidden; /* Prevent horizontal scroll */
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            transition: all 0.3s;
            position: sticky; /* Make sidebar sticky */
            top: 0;
            height: 100vh; /* Full height */
            z-index: 1000; /* Ensure sidebar is above content */
        }

        #sidebar.collapsed {
            margin-left: -250px; /* Hide sidebar */
        }

        #content {
            width: 100%;
            transition: all 0.3s;
            min-height: 100vh; /* Ensure content takes full height */
        }

        /* Adjust content padding when sidebar is collapsed */
        #sidebar.collapsed + #content {
            /* Adjust as needed if you want content to expand */
        }

        /* Style for active sidebar links */
        .sidebar-sticky .nav-link.active {
            font-weight: bold;
            color: #0d6efd; /* Bootstrap primary color */
            background-color: rgba(0, 123, 255, 0.1); /* Light background */
            border-left: 3px solid #0d6efd;
            padding-left: calc(1rem - 3px); /* Adjust padding */
        }
        .sidebar-sticky .nav-link:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Sidebar header/footer styling (optional) */
        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            background: #f8f9fa; /* Match bg-light */
        }

        .alert-message {
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 4px;
            color: #842029; /* Dark red text */
            background-color: #f8d7da; /* Light red background */
            border: 1px solid #f5c2c7; /* Red border */
            font-size: 0.9em;
        }

        /* Hide text, show only icons when collapsed (More advanced - requires JS to add class to body/sidebar) */
        /*
        body.sidebar-collapsed #sidebar .nav-link span { display: none; }
        body.sidebar-collapsed #sidebar { width: 80px; min-width: 80px; }
        body.sidebar-collapsed #sidebar .nav-link i { margin-right: 0 !important; font-size: 1.2rem; }
        body.sidebar-collapsed #content { padding-left: 80px; }
        */

        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px; /* Hide by default on smaller screens */
            }
            #sidebar.collapsed {
                margin-left: 0; /* Show when 'collapsed' (which means toggled open on small screens) */
            }
            #content {
                width: 100%;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="d-flex"> {{-- Use flexbox for layout --}}

    {{-- Sidebar Column --}}
    <nav id="sidebar" class="bg-light">
        <div class="sidebar-header">
            <h3>Farm ERP</h3>
        </div>
        @include('partials.sidebar') {{-- Sidebar content included here --}}
    </nav>

    {{-- Content Column --}}
    <div id="content">
        @include('layouts.nav') {{-- Include Navbar here --}}

        <main class="p-4"> {{-- Added padding to main content --}}
            @include('partials.alerts')
            @yield('content')
        </main>

        @include('layouts.footer')
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

{{-- Sidebar Toggle Script --}}
<script>
    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('collapsed');
            // Optional: Add class to body for more complex collapsed styling
            // $('body').toggleClass('sidebar-collapsed');
        });

        // Keep sidebar collapsed state across page loads (using localStorage)
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            $('#sidebar').addClass('collapsed');
        }

        $('#sidebarCollapse').on('click', function () {
            if ($('#sidebar').hasClass('collapsed')) {
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        });

    });
</script>

@stack('scripts')
</body>
</html>
