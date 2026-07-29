<nav x-data="{ open: false }" class="bg-white border-b border border-secondary">
    <!-- Primary Navigation Menu -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div style="height: 64px;" class="d-flex justify-content-between">
            <div class="d-flex">
                <!-- Logo -->
                <div class="flex-shrink-0 d-flex align-items-center">
                    <a href="{{ route('landing') }}">
                        <x-application-logo style="height: 36px;" class="d-block w-auto fill-current text-secondary" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="d-none space-x-8 sm:-my-px sm:ms-10 sm:d-flex">
                    <x-nav-link :href="route('landing')" :active="request()->routeIs('landing')">
                        {{ __('Beranda') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="d-none sm:d-flex sm:align-items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="d-inline-d-flex align-items-center px-3 py-2 border border-transparent fs-6 leading-4 fw-medium rounded text-secondary bg-white hover:text-secondary focus: transition ease-in-out">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('pelanggan.riwayat')">
                         Riwayat Pesanan
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
            <div class="-me-2 d-flex align-items-center sm:d-none">
                <button @click="open = ! open" class="d-inline-d-flex align-items-center justify-content-center p-2 rounded text-secondary hover:text-secondary hover:bg-light focus: focus:bg-light focus:text-secondary transition ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'d-none': open, 'd-inline-d-flex': ! open }" class="d-inline-d-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'d-none': ! open, 'd-inline-d-flex': open }" class="d-none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'d-block': open, 'd-none': ! open}" class="d-none sm:d-none">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('landing')" :active="request()->routeIs('landing')">
                {{ __('Beranda') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border border-secondary">
            <div class="px-4">
                <div class="fw-medium text-base text-secondary">{{ Auth::user()->name }}</div>
                <div class="fw-medium fs-6 text-secondary">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <x-dropdown-link :href="route('pelanggan.riwayat')">
    Riwayat Pesanan
</x-dropdown-link>

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
