<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top" style="height:80px;">
    <a href="{{ route('index') }}" class="d-flex align-items-center justify-content-center w-100">
        <img 
            src="{{ asset('assets/images/WaterLogo.png') }}"
            alt="logo"
            class="img-fluid"
            style="max-height:65px; width:auto;"
        >
    </a>
</div>

    <ul class="nav">
        {{-- <li class="nav-item profile">
            <div class="profile-desc">

                <a href="#" id="profile-dropdown" data-bs-toggle="dropdown"><i
                        class="mdi mdi-dots-vertical"></i></a>
                <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list"
                    aria-labelledby="profile-dropdown">
                    <a href="#" class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-dark rounded-circle">
                                <i class="mdi mdi-settings text-primary"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <p class="preview-subject ellipsis mb-1 text-small">Account settings</p>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-dark rounded-circle">
                                <i class="mdi mdi-onepassword  text-info"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <p class="preview-subject ellipsis mb-1 text-small">Change Password</p>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-dark rounded-circle">
                                <i class="mdi mdi-calendar-today text-success"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <p class="preview-subject ellipsis mb-1 text-small">To-do list</p>
                        </div>
                    </a>
                </div>
            </div>
        </li> --}}
        {{-- <li class="nav-item nav-category">
            <span class="nav-link">Navigation</span>
        </li> --}}
         <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('allocations.homee') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-home"></i>
                </span>
                <span class="menu-title">صفحه نخست</span>
            </a>
        </li>
        @auth
            
        
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('reports.allocations') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">داشبورد</span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('allocations.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-file-document-box"></i>
                </span>
                <span class="menu-title">جداول تخصیص</span>

            </a>
            <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="pages/ui-features/dropdowns.html">Dropdowns</a></li>
                    <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('all-categories') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-playlist-play"></i>
                </span>
                <span class="menu-title">دسته بندی ها</span>
            </a>
        </li>
        @role('admin')
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('all-users') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-account-group"></i>
                </span>
                <span class="menu-title">کاربران</span>
            </a>
        </li>
        @endrole
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-chart-bar"></i>
                </span>
                <span class="menu-title">نمودارها</span>
            </a>
        </li>
        
        {{-- <li class="nav-item menu-items">
            <a class="nav-link" href="pages/icons/mdi.html">
                <span class="menu-icon">
                    <i class="mdi mdi-contacts"></i>
                </span>
                <span class="menu-title">Icons</span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <span class="menu-icon">
                    <i class="mdi mdi-security"></i>
                </span>
                <span class="menu-title">User Pages</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="auth">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="pages/samples/blank-page.html"> Blank
                            Page </a></li>
                    <li class="nav-item"> <a class="nav-link" href="pages/samples/error-404.html"> 404 </a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="pages/samples/error-500.html"> 500 </a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="pages/samples/login.html"> Login </a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="pages/samples/register.html"> Register
                        </a></li>
                </ul>
            </div>
        </li> --}}
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('user.logs') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-file-document-box"></i>
                </span>
                <span class="menu-title">لاگ ورود و خروج</span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('profile.edit') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-account-group"></i>
                </span>
                <span class="menu-title">پروفایل</span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <span class="menu-icon">
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-dropdown-link :href="route('logout')"
                    onclick="event.preventDefault();
                this.closest('form').submit();">
                    {{ __('خروج') }}
                </x-dropdown-link>
            </form>
            </span>
        </li>
        @endauth
    </ul>
</nav>
