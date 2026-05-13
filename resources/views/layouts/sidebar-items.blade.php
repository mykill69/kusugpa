<!-- resources/views/layouts/sidebar-items.blade.php -->

<!-- Dashboard -->
<li>
    <a href="{{ route('dashboard') }}"
        class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }}">
        <i class="fas fa-tachometer-alt h-6 w-6 shrink-0"></i>
        Dashboard
    </a>
</li>

<!-- Print Vouchers -->
<li>
    <a href="{{ route('printVoucher') }}"
        class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('printVoucher') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }}">
        <i class="fas fa-print h-6 w-6 shrink-0"></i>
        Print Vouchers
    </a>
</li>

<!-- REMOVED: Standalone Summary Reports - now only in Reports dropdown -->

<!-- Weekly Uploads - Visible to Admin and users with upload permissions -->
@php
    $canUpload =
        auth()->user()->role === 'Administrator' ||
        auth()
            ->user()
            ->hasAnyPermission([
                'upload-summary',
                'upload-trucking',
                'upload-fuel',
                'upload-rentals',
                'upload-underload',
                'upload-transloading',
                'upload-fci',
                'upload-mudpress',
            ]);
@endphp

@if ($canUpload)
    <li x-data="{ open: false }">
        <button @click="open = !open"
            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-primary-600 hover:bg-gray-50">
            <i class="fas fa-upload h-6 w-6 shrink-0"></i>
            Weekly Uploads
            <i class="fas fa-chevron-down ml-auto h-5 w-5 shrink-0 transition-transform"
                :class="{ 'rotate-180': open }"></i>
        </button>
        <ul x-show="open" class="mt-1 space-y-1 ml-9">
            <li>
                <button onclick="openUploadModal('summary')"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <span>Summary</span>
                </button>
            </li>
            <li>
                <button onclick="openUploadModal('trucking')"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <span>Trucking Allowance</span>
                </button>
            </li>
            <li>
                <button onclick="openUploadModal('fci')"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <span>Fresh Cane Incentive</span>
                </button>
            </li>
            <li>
                <button onclick="openUploadModal('fuel')"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <span>Fuel</span>
                </button>
            </li>
            <li>
                <button onclick="openUploadModal('rentals')"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <span>Rentals</span>
                </button>
            </li>
            <li>
                <button onclick="openUploadModal('underload')"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <span>Underload</span>
                </button>
            </li>
            <li>
                <button onclick="openUploadModal('transloading')"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <span>Transloading</span>
                </button>
            </li>
            <li>
                <button onclick="openUploadModal('mudpress')"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <span>Mudpress</span>
                </button>
            </li>
        </ul>
    </li>
@endif
<!-- Settings - Visible to Admin and users with settings permissions -->
@php
    $canManageSettings =
        auth()->user()->role === 'Administrator' ||
        auth()
            ->user()
            ->hasAnyPermission(['set-quedan-price', 'set-molasses-price', 'set-crop-year', 'set-week-number']);
@endphp

@if ($canManageSettings)
    <li x-data="{ open: false }">
        <button @click="open = !open"
            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-primary-600 hover:bg-gray-50">
            <i class="fas fa-cog h-6 w-6 shrink-0"></i>
            Weekly Updates
            <i class="fas fa-chevron-down ml-auto h-5 w-5 shrink-0 transition-transform"
                :class="{ 'rotate-180': open }"></i>
        </button>
        <ul x-show="open" class="mt-1 space-y-1 ml-9">
            <li>
                <button onclick="openQuedanPriceModal()"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <span>Quedan Price</span>
                </button>
            </li>
            <li>
                <button onclick="openMolassesPriceModal()"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <span>Molasses Price</span>
                </button>
            </li>
            <li>
                <button onclick="openCropYearModal()"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <span>Crop Year</span>
                </button>
            </li>
            <li>
                <button onclick="openWeekNumberModal()"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <span>Week Number</span>
                </button>
            </li>
        </ul>
    </li>
@endif

<!-- Crop & Week Management -->
@if (in_array(auth()->user()->role, ['Administrator', 'super_admin', 'manager']))
    <li>
        <a href="{{ route('crop-weeks.index') }}"
            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('crop-weeks.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }}">
            <i class="fas fa-calendar-week h-6 w-6 shrink-0"></i>
            Crop & Week Management
        </a>
    </li>
@endif

<!-- Reports -->
@php
    $canViewReports = auth()->user()->role === 'Administrator' || auth()->user()->hasPermission('view-reports');
@endphp

@if ($canViewReports)
    <li x-data="{ open: {{ request()->routeIs('summaryReport') ? 'true' : 'false' }} }">
        <button @click="open = !open"
            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('summaryReport') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }}">
            <i class="fas fa-chart-pie h-6 w-6 shrink-0"></i>
            Reports
            <i class="fas fa-chevron-down ml-auto h-5 w-5 shrink-0 transition-transform"
                :class="{ 'rotate-180': open }"></i>
        </button>
        <ul x-show="open" class="mt-1 space-y-1 ml-9">
            <li>
                <a href="{{ route('summaryReport') }}"
                    class="group flex w-full items-center rounded-md p-2 text-sm {{ request()->routeIs('summaryReport') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                    <i class="fas fa-chart-bar mr-2 text-xs"></i> Summary Reports
                </a>
            </li>
            <li>
                <a href="#"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <i class="fas fa-truck mr-2 text-xs"></i> TLS Reports
                </a>
            </li>
            <li>
                <a href="#"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <i class="fas fa-receipt mr-2 text-xs"></i> Voucher Reports
                </a>
            </li>
            <li>
                <a href="#"
                    class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <i class="fas fa-tags mr-2 text-xs"></i> Weekly Prices
                </a>
            </li>
        </ul>
    </li>
@endif

<!-- Planter Profiles -->
<li>
    <a href="#"
        class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-primary-600 hover:bg-gray-50">
        <i class="fas fa-seedling h-6 w-6 shrink-0"></i>
        Planter Profiles
    </a>
</li>

<!-- Loans - Show if user has any loan permission -->
@php
    $canViewLoans =
        in_array(auth()->user()->role, ['Administrator', 'super_admin', 'manager', 'loan_officer']) ||
        auth()->user()->hasPermission('view-loans') ||
        auth()->user()->hasPermission('create-loans') ||
        auth()->user()->hasPermission('approve-loans') ||
        auth()->user()->hasPermission('process-loan-payments') ||
        auth()->user()->hasPermission('manage-loan-settings');
@endphp

@if ($canViewLoans)
    <li x-data="{ open: {{ request()->routeIs('loans.*') ? 'true' : 'false' }} }">
        <button @click="open = !open"
            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('loans.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }}">
            <i class="fas fa-hand-holding-usd h-6 w-6 shrink-0"></i>
            <span class="flex-1 text-left">Loans</span>
            <i class="fas fa-chevron-down ml-auto h-4 w-4 shrink-0 transition-transform"
                :class="{ 'rotate-180': open }"></i>
        </button>
        <ul x-show="open" class="mt-1 space-y-1 ml-9">
            <li>
                <a href="{{ route('loans.index') }}"
                    class="group flex w-full items-center rounded-md p-2 text-sm {{ request()->routeIs('loans.index') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                    <i class="fas fa-list w-4 mr-2"></i> All Loans
                </a>
            </li>
            @if (in_array(auth()->user()->role, ['Administrator', 'super_admin', 'manager', 'loan_officer']) ||
                    auth()->user()->hasPermission('create-loans'))
                {{-- <li>
                    <button
                        onclick="document.querySelector('[x-data]').__x.$data.openNewLoanModal ? document.querySelector('[x-data]').__x.$data.openNewLoanModal() : window.location='{{ route('loans.create') }}'"
                        class="group flex w-full items-center rounded-md p-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                        <i class="fas fa-plus-circle w-4 mr-2"></i> New Loan
                    </button>
                </li> --}}
            @endif
            <li>
                <a href="{{ route('loans.reports') }}"
                    class="group flex w-full items-center rounded-md p-2 text-sm {{ request()->routeIs('loans.reports') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                    <i class="fas fa-chart-bar w-4 mr-2"></i> Loan Reports
                </a>
            </li>
            @if (in_array(auth()->user()->role, ['Administrator', 'super_admin', 'manager']) ||
                    auth()->user()->hasPermission('manage-loan-settings'))
                <li>
                    <a href="{{ route('loans.settings') }}"
                        class="group flex w-full items-center rounded-md p-2 text-sm {{ request()->routeIs('loans.settings') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                        <i class="fas fa-cog w-4 mr-2"></i> Loan Settings
                    </a>
                </li>
            @endif
        </ul>
    </li>
@endif

<!-- User Management - Admin and Super Admin only -->
@if (auth()->user()->role === 'Administrator' || auth()->user()->role === 'super_admin')
    <li>
        <a href="{{ route('user-management') }}"
            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('user-management*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }}">
            <i class="fas fa-users-cog h-6 w-6 shrink-0"></i>
            User Management
        </a>
    </li>
@endif
