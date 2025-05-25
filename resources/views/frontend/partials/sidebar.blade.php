<div class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="fas fa-laugh-wink"></i><span>SMM Controller</span></h4>
    </div>
    
    <div class="nav-section">Dashboard</div>
    <div class="nav-items">
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <div class="nav-section">Services</div>
    <div class="nav-items">
        <a href="#servicesSubmenu" class="sidebar-link" data-bs-toggle="collapse" aria-expanded="false" data-title="Services">
            <i class="fas fa-star"></i>
            <span>Services</span>
            <i class="fas fa-chevron-right arrow"></i>
        </a>
        <div class="collapse submenu" id="servicesSubmenu">
            <a href="{{ route('services') }}" class="sidebar-link {{ request()->routeIs('services') ? 'active' : '' }}">
                <span>All Services</span>
            </a>
            <a href="#" class="sidebar-link">
                <span>Popular Services</span>
            </a>
            <a href="#" class="sidebar-link">
                <span>New Services</span>
            </a>
        </div>
    </div>

    <div class="nav-section">Tools</div>
    <div class="nav-items">
        <a href="{{ route('uid-finder') }}" class="sidebar-link {{ request()->routeIs('uid-finder') ? 'active' : '' }}">
            <i class="fas fa-search"></i>
            <span>UID Finder</span>
        </a>
    </div>

    <div class="nav-section">Orders</div>
    <div class="nav-items">
        <a href="#ordersSubmenu" class="sidebar-link" data-bs-toggle="collapse" aria-expanded="false" data-title="Orders">
            <i class="fas fa-shopping-cart"></i>
            <span>Orders</span>
            <i class="fas fa-chevron-right arrow"></i>
        </a>
        <div class="collapse submenu" id="ordersSubmenu">
            <a href="{{ route('orders.index') }}" class="sidebar-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                <span>All Orders</span>
            </a>
            <a href="{{ route('orders.index') }}?status=pending" class="sidebar-link {{ request('status') == 'pending' ? 'active' : '' }}">
                <span>Pending Orders</span>
            </a>
            <a href="{{ route('orders.index') }}?status=completed" class="sidebar-link {{ request('status') == 'completed' ? 'active' : '' }}">
                <span>Completed Orders</span>
            </a>
        </div>
    </div>

    <div class="nav-section">Account Management</div>
    <div class="nav-items">
        <a href="#profileSubmenu" class="sidebar-link" data-bs-toggle="collapse" aria-expanded="false" data-title="Profile">
            <i class="fas fa-user-circle"></i>
            <span>My Profile</span>
            <i class="fas fa-chevron-right arrow"></i>
        </a>
        <div class="collapse submenu" id="profileSubmenu">
            <a href="{{ route('profile.index') }}" class="sidebar-link {{ request()->routeIs('profile.index') && !request()->hasAny(['tab']) ? 'active' : '' }}">
                <span>Personal Info</span>
            </a>
            <a href="{{ route('profile.index') }}?tab=security" class="sidebar-link {{ request()->has('tab') && request('tab') == 'security' ? 'active' : '' }}">
                <span>Security Settings</span>
            </a>
            <a href="{{ route('profile.index') }}?tab=password" class="sidebar-link {{ request()->has('tab') && request('tab') == 'password' ? 'active' : '' }}">
                <span>Change Password</span>
            </a>
        </div>

        <a href="#fundsSubmenu" class="sidebar-link" data-bs-toggle="collapse" aria-expanded="false" data-title="Funds">
            <i class="fas fa-wallet"></i>
            <span>Funds</span>
            <i class="fas fa-chevron-right arrow"></i>
        </a>
        <div class="collapse submenu" id="fundsSubmenu">
            <a href="{{ route('funds.index') }}" class="sidebar-link {{ request()->routeIs('funds.index') && !request()->is('*/add') ? 'active' : '' }}">
                <span>Balance & History</span>
            </a>
            <a href="{{ route('funds.add') }}" class="sidebar-link {{ request()->routeIs('funds.add') ? 'active' : '' }}">
                <span>Add Funds</span>
            </a>
        </div>
    </div>

    <div class="nav-section">Support</div>
    <div class="nav-items">
        <a href="#supportSubmenu" class="sidebar-link" data-bs-toggle="collapse" aria-expanded="false" data-title="Support">
            <i class="fas fa-headset"></i>
            <span>Support</span>
            <i class="fas fa-chevron-right arrow"></i>
        </a>
        <div class="collapse submenu" id="supportSubmenu">
            <a href="#" class="sidebar-link">
                <span>Tickets</span>
            </a>
            <a href="#" class="sidebar-link">
                <span>FAQ</span>
            </a>
            <a href="#" class="sidebar-link">
                <span>Contact Us</span>
            </a>
        </div>
    </div>
</div> 