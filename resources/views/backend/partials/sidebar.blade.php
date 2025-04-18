<div class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="fas fa-laugh-wink"></i><span>SMM Admin</span></h4>
    </div>
    
    <div class="nav-section">Main Menu</div>
    <div class="nav-items">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-title="Dashboard">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <a href="#usersSubmenu" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}" data-title="Users">
            <i class="fas fa-users"></i>
            <span>Users</span>
            <i class="fas fa-chevron-right arrow"></i>
        </a>
        <div class="collapse submenu {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="usersSubmenu">
            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                <span>User List</span>
            </a>
            <a href="{{ route('admin.users.create') }}" class="sidebar-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                <span>Add User</span>
            </a>
        </div>

        <a href="#ordersSubmenu" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.orders.*') ? 'true' : 'false' }}" data-title="Orders">
            <i class="fas fa-shopping-cart"></i>
            <span>Orders</span>
            <i class="fas fa-chevron-right arrow"></i>
        </a>
        <div class="collapse submenu {{ request()->routeIs('admin.orders.*') ? 'show' : '' }}" id="ordersSubmenu">
            <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                <span>Order List</span>
            </a>
            <a href="{{ route('admin.orders.pending') }}" class="sidebar-link {{ request()->routeIs('admin.orders.pending') ? 'active' : '' }}">
                <span>Pending Orders</span>
            </a>
        </div>

        <a href="#servicesSubmenu" class="sidebar-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.services.*') ? 'true' : 'false' }}" data-title="Services">
            <i class="fas fa-cog"></i>
            <span>Services</span>
            <i class="fas fa-chevron-right arrow"></i>
        </a>
        <div class="collapse submenu {{ request()->routeIs('admin.services.*') ? 'show' : '' }}" id="servicesSubmenu">
            <a href="{{ route('admin.services.index') }}" class="sidebar-link {{ request()->routeIs('admin.services.index') ? 'active' : '' }}">
                <span>Service List</span>
            </a>
            <a href="{{ route('admin.services.create') }}" class="sidebar-link {{ request()->routeIs('admin.services.create') ? 'active' : '' }}">
                <span>Add Service</span>
            </a>
        </div>

        <a href="#paymentsSubmenu" class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.payments.*') ? 'true' : 'false' }}" data-title="Payments">
            <i class="fas fa-money-bill"></i>
            <span>Payments</span>
            <i class="fas fa-chevron-right arrow"></i>
        </a>
        <div class="collapse submenu {{ request()->routeIs('admin.payments.*') ? 'show' : '' }}" id="paymentsSubmenu">
            <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.index') ? 'active' : '' }}">
                <span>Payment List</span>
            </a>
            <a href="{{ route('admin.payments.pending') }}" class="sidebar-link {{ request()->routeIs('admin.payments.pending') ? 'active' : '' }}">
                <span>Pending Payments</span>
            </a>
        </div>

        <a href="{{ route('admin.pc-profiles.index') }}" class="sidebar-link {{ request()->routeIs('admin.pc-profiles.*') ? 'active' : '' }}" data-title="PC Profiles">
            <i class="fas fa-desktop"></i>
            <span>PC Profiles</span>
        </a>

        <a href="{{ route('admin.chrome.index') }}" class="sidebar-link {{ request()->routeIs('admin.chrome.*') ? 'active' : '' }}" data-title="Chrome">
            <i class="fab fa-chrome"></i>
            <span>Chrome Profiles</span>
        </a>

        <a href="{{ route('admin.submission-batch.index') }}" class="sidebar-link {{ request()->routeIs('admin.submission-batch.*') ? 'active' : '' }}" data-title="Submission Batches">
            <i class="fas fa-layer-group"></i>
            <span>Submission Batches</span>
        </a>

        <a href="{{ route('admin.facebook.index') }}" class="sidebar-link {{ request()->routeIs('admin.facebook.*') ? 'active' : '' }}" data-title="Facebook">
            <i class="fab fa-facebook"></i>
            <span>Facebook Accounts</span>
        </a>

        <a href="{{ route('admin.gmail.index') }}" class="sidebar-link {{ request()->routeIs('admin.gmail.*') ? 'active' : '' }}" data-title="Gmail">
            <i class="fas fa-envelope"></i>
            <span>Gmail Accounts</span>
        </a>

    </div>

    <div class="nav-section">Settings</div>
    <div class="nav-items">
        <a href="#settingsSubmenu" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }}" data-title="Settings">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
            <i class="fas fa-chevron-right arrow"></i>
        </a>
        <div class="collapse submenu {{ request()->routeIs('admin.settings.*') ? 'show' : '' }}" id="settingsSubmenu">
            <a href="{{ route('admin.settings.profile') }}" class="sidebar-link {{ request()->routeIs('admin.settings.profile') ? 'active' : '' }}">
                <span>Profile</span>
            </a>
            <a href="{{ route('admin.settings.security') }}" class="sidebar-link {{ request()->routeIs('admin.settings.security') ? 'active' : '' }}">
                <span>Security</span>
            </a>
        </div>
    </div>
</div> 