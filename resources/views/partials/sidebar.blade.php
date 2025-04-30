{{-- Wrap the main navigation list with @auth directive --}}
@auth
    <div class="sidebar-sticky pt-3">
        <ul class="nav flex-column">
            {{-- Dashboard Link --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('home') ? 'active' : '' }}" href="{{ route('home') }}">
                    <i class="fas fa-tachometer-alt fa-fw me-2"></i> <span>Dashboard</span>
                </a>
            </li>

            {{-- Batch Management Section --}}
            <li class="nav-item">
                @php $batchManagementActive = request()->is('batches*') || request()->is('bird-types*') || request()->is('breeds*') || request()->is('stages*'); @endphp
                <a href="#batchManagementSubmenu" data-bs-toggle="collapse" aria-expanded="{{ $batchManagementActive ? 'true' : 'false' }}" class="nav-link sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase text-decoration-none {{ $batchManagementActive ? '' : 'collapsed' }}">
                    <span><i class="fas fa-layer-group fa-fw me-2"></i> Batch Management</span>
                    <i class="fas fa-chevron-down fa-xs"></i>
                </a>
                <ul class="collapse list-unstyled nav flex-column ms-3 {{ $batchManagementActive ? 'show' : '' }}" id="batchManagementSubmenu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('batches*') ? 'active' : '' }}" href="{{ route('batches.index') }}">
                            <i class="fas fa-kiwi-bird fa-fw me-2"></i> <span>Batches</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('bird-types*') ? 'active' : '' }}" href="{{ route('bird-types.index') }}">
                            <i class="fas fa-dove fa-fw me-2"></i> <span>Bird Types</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('breeds*') ? 'active' : '' }}" href="{{ route('breeds.index') }}">
                            <i class="fas fa-dna fa-fw me-2"></i> <span>Breeds</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('stages*') ? 'active' : '' }}" href="{{ route('stages.index') }}">
                            <i class="fas fa-layer-group fa-fw me-2"></i> <span>Stages</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Daily Operations Section --}}
            <li class="nav-item">
                @php $dailyOpsActive = request()->is('daily-records*') || request()->is('feed-records*') || request()->is('egg-production*'); @endphp
                <a href="#dailyOpsSubmenu" data-bs-toggle="collapse" aria-expanded="{{ $dailyOpsActive ? 'true' : 'false' }}" class="nav-link sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase text-decoration-none {{ $dailyOpsActive ? '' : 'collapsed' }}">
                    <span><i class="fas fa-clipboard-list fa-fw me-2"></i> Daily Operations</span>
                    <i class="fas fa-chevron-down fa-xs"></i>
                </a>
                <ul class="collapse list-unstyled nav flex-column ms-3 {{ $dailyOpsActive ? 'show' : '' }}" id="dailyOpsSubmenu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('daily-records*') ? 'active' : '' }}" href="{{ route('daily-records.index') }}">
                            <i class="fas fa-clipboard-check fa-fw me-2"></i> <span>Daily Records</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('feed-records*') ? 'active' : '' }}" href="{{ route('feed-records.index') }}">
                            <i class="fas fa-utensils fa-fw me-2"></i> <span>Feed Records</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('egg-production*') ? 'active' : '' }}" href="{{ route('egg-production.index') }}">
                            <i class="fas fa-egg fa-fw me-2"></i> <span>Egg Production</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Health Management Section --}}
            <li class="nav-item">
                @php $healthActive = request()->is('diseases*') || request()->is('drugs*') || request()->is('disease-management*') || request()->is('vaccines*') || request()->is('vaccination-logs*') || request()->is('vaccine-schedule*'); @endphp
                <a href="#healthSubmenu" data-bs-toggle="collapse" aria-expanded="{{ $healthActive ? 'true' : 'false' }}" class="nav-link sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase text-decoration-none {{ $healthActive ? '' : 'collapsed' }}">
                    <span><i class="fas fa-notes-medical fa-fw me-2"></i> Health Management</span>
                    <i class="fas fa-chevron-down fa-xs"></i>
                </a>
                <ul class="collapse list-unstyled nav flex-column ms-3 {{ $healthActive ? 'show' : '' }}" id="healthSubmenu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('diseases*') ? 'active' : '' }}" href="{{ route('diseases.index') }}">
                            <i class="fas fa-virus fa-fw me-2"></i> <span>Diseases</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('drugs*') ? 'active' : '' }}" href="{{ route('drugs.index') }}">
                            <i class="fas fa-pills fa-fw me-2"></i> <span>Drugs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('disease-management*') ? 'active' : '' }}" href="{{ route('disease-management.index') }}">
                            <i class="fas fa-notes-medical fa-fw me-2"></i> <span>Disease Records</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('vaccines*') ? 'active' : '' }}" href="{{ route('vaccines.index') }}">
                            <i class="fas fa-syringe fa-fw me-2"></i> <span>Vaccines</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('vaccination-logs*') ? 'active' : '' }}" href="{{ route('vaccination-logs.index') }}">
                            <i class="fas fa-clipboard-check fa-fw me-2"></i> <span>Vaccination Logs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('vaccine-schedule*') ? 'active' : '' }}" href="{{ route('vaccine-schedule.index') }}">
                            <i class="fas fa-calendar-alt fa-fw me-2"></i> <span>Vaccine Schedule</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Inventory & Purchasing Section --}}
            <li class="nav-item">
                @php $inventoryActive = request()->is('feed-types*') || request()->is('purchase-units*') || request()->is('suppliers*') || request()->is('supplier-feed-prices*') || request()->is('purchase-order-statuses*') || request()->is('purchase-orders*'); @endphp
                <a href="#inventorySubmenu" data-bs-toggle="collapse" aria-expanded="{{ $inventoryActive ? 'true' : 'false' }}" class="nav-link sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase text-decoration-none {{ $inventoryActive ? '' : 'collapsed' }}">
                    <span><i class="fas fa-boxes-stacked fa-fw me-2"></i> Inventory & Purchasing</span>
                    <i class="fas fa-chevron-down fa-xs"></i>
                </a>
                <ul class="collapse list-unstyled nav flex-column ms-3 {{ $inventoryActive ? 'show' : '' }}" id="inventorySubmenu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('feed-types*') ? 'active' : '' }}" href="{{ route('feed-types.index') }}">
                            <i class="fas fa-wheat-awn fa-fw me-2"></i> <span>Feed Types</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('purchase-units*') ? 'active' : '' }}" href="{{ route('purchase-units.index') }}">
                            <i class="fas fa-weight-scale fa-fw me-2"></i> <span>Purchase Units</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('suppliers*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                            <i class="fas fa-truck fa-fw me-2"></i> <span>Suppliers</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('supplier-feed-prices*') ? 'active' : '' }}" href="{{ route('supplier-feed-prices.index') }}">
                            <i class="fas fa-tags fa-fw me-2"></i> <span>Supplier Feed Prices</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('purchase-order-statuses*') ? 'active' : '' }}" href="{{ route('purchase-order-statuses.index') }}">
                            <i class="fas fa-check-circle fa-fw me-2"></i> <span>PO Statuses</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('purchase-orders*') ? 'active' : '' }}" href="{{ route('purchase-orders.index') }}">
                            <i class="fas fa-shopping-cart fa-fw me-2"></i> <span>Purchase Orders</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Sales Section --}}
            <li class="nav-item">
                @php $salesActive = request()->is('sales-units*') || request()->is('sales-teams*') || request()->is('sales-prices*') || request()->is('sales-records*'); @endphp
                <a href="#salesSubmenu" data-bs-toggle="collapse" aria-expanded="{{ $salesActive ? 'true' : 'false' }}" class="nav-link sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase text-decoration-none {{ $salesActive ? '' : 'collapsed' }}">
                    <span><i class="fas fa-dollar-sign fa-fw me-2"></i> Sales</span>
                    <i class="fas fa-chevron-down fa-xs"></i>
                </a>
                <ul class="collapse list-unstyled nav flex-column ms-3 {{ $salesActive ? 'show' : '' }}" id="salesSubmenu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('sales-units*') ? 'active' : '' }}" href="{{ route('sales-units.index') }}">
                            <i class="fas fa-box fa-fw me-2"></i> <span>Sales Units</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('sales-teams*') ? 'active' : '' }}" href="{{ route('sales-teams.index') }}">
                            <i class="fas fa-users fa-fw me-2"></i> <span>Sales Team</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('sales-prices*') ? 'active' : '' }}" href="{{ route('sales-prices.index') }}">
                            <i class="fas fa-tag fa-fw me-2"></i> <span>Sales Prices</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('sales-records*') ? 'active' : '' }}" href="{{ route('sales-records.index') }}">
                            <i class="fas fa-receipt fa-fw me-2"></i> <span>Sales Records</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Reports Section --}}
            <li class="nav-item">
                @php $reportsActive = request()->is('reports*'); @endphp
                <a href="#reportsSubmenu" data-bs-toggle="collapse" aria-expanded="{{ $reportsActive ? 'true' : 'false' }}" class="nav-link sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase text-decoration-none {{ $reportsActive ? '' : 'collapsed' }}">
                    <span><i class="fas fa-chart-line fa-fw me-2"></i> Reports</span>
                    <i class="fas fa-chevron-down fa-xs"></i>
                </a>
                <ul class="collapse list-unstyled nav flex-column ms-3 {{ $reportsActive ? 'show' : '' }}" id="reportsSubmenu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.farm-kpis') ? 'active' : '' }}" href="{{ route('reports.farm-kpis') }}">
                            <i class="fas fa-tachometer-alt fa-fw me-2"></i> <span>Farm KPIs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.batch-summary') ? 'active' : '' }}" href="{{ route('reports.batch-summary') }}">
                            <i class="fas fa-boxes fa-fw me-2"></i> <span>Batch Summary</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.batch-performance') ? 'active' : '' }}" href="{{ route('reports.batch-performance') }}">
                            <i class="fas fa-chart-bar fa-fw me-2"></i> <span>Batch Performance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.daily-egg-summary') ? 'active' : '' }}" href="{{ route('reports.daily-egg-summary') }}">
                            <i class="fas fa-chart-pie fa-fw me-2"></i> <span>Daily Egg Summary</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.feed-consumption') ? 'active' : '' }}" href="{{ route('reports.feed-consumption') }}">
                            <i class="fas fa-chart-area fa-fw me-2"></i> <span>Feed Consumption</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.vaccination') ? 'active' : '' }}" href="{{ route('reports.vaccination') }}">
                            <i class="fas fa-syringe fa-fw me-2"></i> <span>Vaccination Report</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.disease-management') ? 'active' : '' }}" href="{{ route('reports.disease-management') }}">
                            <i class="fas fa-notes-medical fa-fw me-2"></i> <span>Disease Report</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.sales-by-salesperson') ? 'active' : '' }}" href="{{ route('reports.sales-by-salesperson') }}">
                            <i class="fas fa-user-tag fa-fw me-2"></i> <span>Sales by Person</span>
                        </a>
                    </li>
                </ul>
            </li>


            {{-- System Section --}}
            <li class="nav-item">
                @php $systemActive = request()->is('audit-logs*') || request()->is('roles*') || request()->is('permissions*') || request()->is('modules*'); @endphp
                <a href="#systemSubmenu" data-bs-toggle="collapse" aria-expanded="{{ $systemActive ? 'true' : 'false' }}" class="nav-link sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase text-decoration-none {{ $systemActive ? '' : 'collapsed' }}">
                    <span><i class="fas fa-cogs fa-fw me-2"></i> System</span>
                    <i class="fas fa-chevron-down fa-xs"></i>
                </a>
                <ul class="collapse list-unstyled nav flex-column ms-3 {{ $systemActive ? 'show' : '' }}" id="systemSubmenu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('audit-logs*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}">
                            <i class="fas fa-history fa-fw me-2"></i> <span>Audit Logs</span>
                        </a>
                    </li>
                    {{-- Add Role/Permission links here --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('roles*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                            <i class="fas fa-user-shield fa-fw me-2"></i> <span>Roles</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('permissions*') ? 'active' : '' }}" href="{{ route('permissions.index') }}">
                            <i class="fas fa-key fa-fw me-2"></i> <span>Permissions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('modules*') ? 'active' : '' }}" href="{{ route('modules.index') }}">
                            <i class="fas fa-puzzle-piece fa-fw me-2"></i> <span>Modules</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
@endauth {{-- End of @auth check --}}

{{-- Optional: Add links for guests (e.g., Login) outside the @auth block --}}
@guest
    <div class="sidebar-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('login') ? 'active' : '' }}" href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt fa-fw me-2"></i> <span>Login</span>
                </a>
            </li>
            {{-- Add register link if applicable --}}
            @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('register') ? 'active' : '' }}" href="{{ route('register') }}">
                        <i class="fas fa-user-plus fa-fw me-2"></i> <span>Register</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endguest
