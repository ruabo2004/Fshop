<ul class="account-nav">
    <li><a href="{{route('user.index')}}" class="menu-link menu-link_us-s {{Route::is('user.index') ? 'menu-link_active' : ''}}">Dashboard</a></li>
    <li><a href="{{route('user.orders')}}" class="menu-link menu-link_us-s {{Route::is('user.orders') ? 'menu-link_active' : ''}}">Orders</a></li>
    <li><a href="{{route('user.addresses')}}" class="menu-link menu-link_us-s {{Route::is('user.addresses') ? 'menu-link_active' : ''}}">Addresses</a></li>
    <li><a href="{{route('user.account_details')}}" class="menu-link menu-link_us-s {{Route::is('user.account_details') ? 'menu-link_active' : ''}}">Account Details</a></li>
    <li><a href="{{route('user.wishlist')}}" class="menu-link menu-link_us-s {{Route::is('user.wishlist') ? 'menu-link_active' : ''}}">Wishlist</a></li>
    
    <li>
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <a href="{{ route('logout') }}" class="menu-link menu-link_us-s" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
        </form>
    </li>
  </ul>