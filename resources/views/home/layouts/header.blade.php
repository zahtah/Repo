<!-- header section strats -->
<header class="header_section">
    <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
            <a class="navbar-brand" href="index.html"><img width="250" src="images/logo.png" alt="#" /></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class=""> </span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.html">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button"
                            aria-haspopup="true" aria-expanded="true"> <span class="nav-label">Pages <span
                                    class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="about.html">About</a></li>
                            <li><a href="testimonial.html">Testimonial</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product.html">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog_list.html">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.html">Contact</a>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 456.029 456.029"
                                style="enable-background:new 0 0 456.029 456.029;" xml:space="preserve">
                                <g>
                                    <g>
                                        <path
                                            d="M345.6,338.862c-29.184,0-53.248,23.552-53.248,53.248c0,29.184,23.552,53.248,53.248,53.248
                                          c29.184,0,53.248-23.552,53.248-53.248C398.336,362.926,374.784,338.862,345.6,338.862z" />
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <path d="M439.296,84.91c-1.024,0-2.56-0.512-4.096-0.512H112.64l-5.12-34.304C104.448,27.566,84.992,10.67,61.952,10.67H20.48
                                          C9.216,10.67,0,19.886,0,31.15c0,11.264,9.216,20.48,20.48,20.48h41.472c2.56,0,4.608,2.048,5.12,4.608l31.744,216.064
                                          c4.096,27.136,27.648,47.616,55.296,47.616h212.992c26.624,0,49.664-18.944,55.296-45.056l33.28-166.4
                                          C457.728,97.71,450.56,86.958,439.296,84.91z" />
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <path
                                            d="M215.04,389.55c-1.024-28.16-24.576-50.688-52.736-50.688c-29.696,1.536-52.224,26.112-51.2,55.296
                                          c1.024,28.16,24.064,50.688,52.224,50.688h1.024C193.536,443.31,216.576,418.734,215.04,389.55z" />
                                    </g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                                <g>
                                </g>
                            </svg>
                        </a>
                    </li>
                    <form class="form-inline">
                        <button class="btn  my-2 my-sm-0 nav_search-btn" type="submit">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                    </form>

                    @if (Route::has('login'))

                        @auth
                            <nav x-data="{ open: false }">
                                <!-- Primary Navigation Menu -->
                                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                    <div class="flex justify-between">


                                        <!-- Settings Dropdown -->
                                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                                            <x-dropdown align="right" width="48">
                                                <x-slot name="trigger">
                                                    <button
                                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                                        <div>{{ Auth::user()->name }}</div>

                                                        <div class="ml-1">
                                                            <svg class="fill-current h-4 w-4"
                                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    </button>
                                                </x-slot>

                                                <x-slot name="content">
                                                    <x-dropdown-link :href="route('profile.edit')">
                                                        {{ __('Profile') }}
                                                    </x-dropdown-link>

                                                    <!-- Authentication -->
                                                    <form method="POST" action="{{ route('logout') }}">
                                                        @csrf

                                                        <x-dropdown-link :href="route('logout')"
                                                            onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                                            {{ __('Log Out') }}
                                                        </x-dropdown-link>
                                                    </form>
                                                </x-slot>
                                            </x-dropdown>
                                        </div>

                                        <!-- Hamburger -->
                                        <div class="-mr-2 flex items-center sm:hidden">
                                            <button @click="open = ! open"
                                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                                                <svg class="h-6 w-6" stroke="currentColor" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <path :class="{ 'hidden': open, 'inline-flex': !open }"
                                                        class="inline-flex" stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Responsive Navigation Menu -->
                                <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">

                                    <!-- Responsive Settings Options -->
                                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                                        <div class="px-4">
                                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                                {{ Auth::user()->name }}</div>
                                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                                        </div>

                                        <div class="mt-3 space-y-1">
                                            <x-responsive-nav-link :href="route('profile.edit')">
                                                {{ __('Profile') }}
                                            </x-responsive-nav-link>

                                            <!-- Authentication -->
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf

                                                <x-responsive-nav-link :href="route('logout')"
                                                    onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                                    {{ __('Log Out') }}
                                                </x-responsive-nav-link>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </nav>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="btn btn-info">Log in</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="ml-4 btn btn-success">Register</a>
                                </li>
                            @endif
                        @endauth

                    @endif




                </ul>
            </div>
        </nav>
    </div>
</header>
<!-- end header section -->
