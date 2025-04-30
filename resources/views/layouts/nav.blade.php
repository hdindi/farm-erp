<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top"> {{-- Added sticky-top --}}
    <div class="container-fluid"> {{-- Changed to container-fluid --}}

        {{-- Sidebar Toggle Button --}}
        <button type="button" id="sidebarCollapse" class="btn btn-light me-3">
            <i class="fas fa-align-left"></i>
        </button>

        <a class="navbar-brand" href="{{ route('home') }}">Farm ERP</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            {{-- Keep existing nav items --}}
            <ul class="navbar-nav me-auto">
                {{-- Example: Remove redundant links if they are in sidebar --}}
                {{-- <li class="nav-item d-lg-none"> --}}
                {{--    <a class="nav-link" href="{{ route('batches.index') }}">Batches</a> --}}
                {{-- </li> --}}
                {{-- ... other nav items if needed ... --}}
            </ul>
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
{{--                             Add profile link if you have one--}}
                             <li><a class="dropdown-item" href="#">Profile</a></li>
                             <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
