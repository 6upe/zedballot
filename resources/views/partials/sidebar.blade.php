<div class="main-sidebar sidebar-style-2">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="{{ route('dashboard') }}">XXXXXXXXXX</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="{{ route('dashboard') }}">XX</a>
    </div>

    <ul class="sidebar-menu">
      <li class="menu-header">Main</li>

      <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
      </li>

      @php $pollsActive = request()->routeIs('polls.*') ? 'active' : ''; @endphp
      <li class="nav-item dropdown {{ $pollsActive }}">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-poll"></i> <span>Polls</span></a>
        <ul class="dropdown-menu" @if(request()->routeIs('polls.*')) style="display:block;" @endif>
          <li><a class="nav-link {{ request()->routeIs('polls.index') ? 'active' : '' }}" href="{{ route('polls.index') }}">Active Polls</a></li>
          <li><a class="nav-link {{ request()->routeIs('polls.create') ? 'active' : '' }}" href="{{ route('polls.create') }}">Create Poll</a></li>
        </ul>
      </li>

          <li class="nav-item {{ request()->routeIs('profile.show') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}" href="{{ route('profile.show') }}"><i class="fas fa-user"></i> <span>My Profile</span></a>
      </li>
    </ul>

    <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
      <a href="/" class="btn btn-primary btn-lg btn-block btn-icon-split">
        <i class="fas fa-home"></i> Help & Support
      </a>
    </div>

  </aside>
</div>
