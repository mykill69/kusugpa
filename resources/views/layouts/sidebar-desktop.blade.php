<!-- resources/views/layouts/sidebar-desktop.blade.php -->
<div class="flex grow flex-col bg-white border-r border-gray-200">
    <div class="flex h-16 shrink-0 items-center px-6 border-b border-gray-100">
        <span class="text-xl font-bold text-primary-700 flex items-center">
            <i class="fas fa-seedling text-primary-600 mr-2"></i>
            KUSUG-PA
        </span>
    </div>
    <nav class="flex flex-1 flex-col overflow-y-auto px-4 pt-4">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    @include('layouts.sidebar-items')
                </ul>
            </li>
        </ul>
    </nav>

    <!-- User Profile at Bottom with Upward Dropdown -->
    <div class="border-t border-gray-100 relative" x-data="profileDropdown()">
        <!-- Dropdown Menu (opens upward) -->
        <div x-show="profileOpen" @click.away="profileOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="absolute bottom-full left-4 right-4 mb-2 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50"
            style="display: none;">

            <!-- User Info Header -->
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->fname }} {{ auth()->user()->lname }}
                </p>
                <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
            </div>

            <!-- Menu Items -->
            <div class="py-1">
                <!-- Admin Panel Link - Only visible to Administrator -->
                @if (auth()->user()->role === 'Administrator')
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-shield-halved w-5 text-center text-indigo-500"></i>
                        <span>System Panel</span>
                    </a>
                @endif

                <!-- Dark/Light Mode Toggle -->
                <button @click="toggleTheme()"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <i
                            :class="isDark ? 'fas fa-sun text-amber-500 w-5 text-center' :
                                'fas fa-moon text-gray-400 w-5 text-center'"></i>
                        <span>Dark Mode</span>
                    </div>
                    <div class="relative">
                        <div :class="isDark ? 'bg-indigo-600' : 'bg-gray-300'"
                            class="w-12 h-6 rounded-full transition-colors duration-300 flex items-center justify-between px-1 cursor-pointer">
                            <i class="fas fa-moon text-white text-xs" x-show="!isDark"></i>
                            <i class="fas fa-sun text-white text-xs" x-show="isDark"></i>
                            <div :class="isDark ? 'translate-x-6' : 'translate-x-0'"
                                class="absolute w-5 h-5 bg-white rounded-full shadow-md transition-transform duration-300 transform left-0.5">
                            </div>
                        </div>
                    </div>
                </button>

                <!-- Divider -->
                <div class="border-t border-gray-100 my-1"></div>

                <!-- Logout -->
                <a href="{{ route('logout') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                    <i class="fas fa-sign-out-alt w-5 text-center"></i>
                    Sign Out
                </a>
            </div>
        </div>


        <!-- Profile Button -->
        <div class="p-4">
            <button @click="profileOpen = !profileOpen"
                class="w-full flex items-center gap-3 p-2 rounded-xl hover:bg-gray-50 transition-colors group">
                <div
                    class="h-10 w-10 rounded-xl bg-primary-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 ring-2 ring-primary-100">
                    {{ strtoupper(substr(auth()->user()->fname, 0, 1)) }}{{ strtoupper(substr(auth()->user()->lname, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0 text-left">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->fname }}
                        {{ auth()->user()->lname }}</p>
                    <p class="text-xs text-gray-500 truncate capitalize">{{ auth()->user()->role }}</p>
                </div>
                <!-- Clear toggle indicator -->
                <div class="flex-shrink-0 flex items-center gap-1">

                    <i class="fas fa-chevron-up text-gray-400 group-hover:text-gray-600 transition-all duration-300"
                        :class="{ 'rotate-180': profileOpen }"></i>
                </div>
            </button>
        </div>
    </div>
</div>
