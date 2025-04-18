<header class="main-header">
    <div class="header-left">
        <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
            <i class="fas fa-angle-left"></i>
        </button>
    </div>
    <div class="header-right d-flex align-items-center">
        <div class="dropdown me-3">
            <a class="nav-link text-secondary" href="#" id="searchDropdown" role="button">
                <i class="fas fa-search"></i>
            </a>
        </div>
        <div class="dropdown me-3">
            <a class="nav-link text-secondary position-relative" href="#" id="notificationsDropdown" role="button">
                <i class="fas fa-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3
                </span>
            </a>
        </div>
        <div class="dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6c5ffc&color=fff" 
                     class="rounded-circle me-2" style="width: 32px; height: 32px;">
                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header> 