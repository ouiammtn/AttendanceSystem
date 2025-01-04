<!-- TopNav -->
<nav class="navbar navbar-top navbar-expand navbar-dark bg-primary border-bottom">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Navbar links -->
            <ul class="navbar-nav align-items-center  ml-md-auto ">
                <li class="nav-item d-xl-none">
                    <!-- Sidenav toggler -->
                    <div class="pr-3 sidenav-toggler sidenav-toggler-dark" data-action="sidenav-pin"
                        data-target="#sidenav-main">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="navbar-nav align-items-center  ml-auto ml-md-0 ">
                <li class="nav-item dropdown">
                    <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                       aria-expanded="false" onkeydown="handleKeyDown(event)" onclick="handleClick(event)">
                        <div class="media align-items-center">
                            <span class="avatar avatar-sm rounded-circle">
                                <img alt="avatar" class="avatar agit vatar-sm rounded-circle" src="{{ asset('storage/me.jpg') }}">
                            </span>
                            <div class="media-body  ml-2  d-none d-lg-block">
                                <span class="mb-0 text-sm  font-weight-bold">{{ Auth::user()->name }}</span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu  dropdown-menu-right ">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome!</h6>
                        </div>
                        <!-- <a href="{{ route('settings.index') }}" class="dropdown-item">
                            <i class="ni ni-settings"></i>
                            <span>Settings</span>
                        </a> -->
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" class="dropdown-item"
                            onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
                            <i class="ni ni-user-run"></i>
                            <span>Logout</span>
                            <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    function handleKeyDown(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault(); // Prevent default behavior like scrolling for Space key
            event.target.click();   // Simulate a click event
        }
    }
    function handleClick(event) {
        // Optional: You can add additional logic here for the click event
        console.log("Dropdown clicked");
    }

    document.addEventListener('DOMContentLoaded', function () {
        const image = document.querySelector('img.avatar');
        if (image) {
            image.addEventListener('error', function () {
                image.src = 'https://picsum.photos/200';
            });
        }
    });
</script>
