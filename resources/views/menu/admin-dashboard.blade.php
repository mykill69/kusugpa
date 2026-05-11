<!-- resources/views/menu/admin-dashboard.blade.php -->
@extends('layouts.main')

@section('title', 'Admin Panel')

@section('content')
<div x-data="adminDashboardData()" class="space-y-6">
    <!-- Admin Header -->
    <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-shield-halved text-2xl"></i>
                    <h1 class="text-2xl sm:text-3xl font-bold">Admin Control Panel</h1>
                </div>
                <p class="text-admin-300 text-sm">System Administration & Management</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <span class="bg-green-500/20 text-green-400 text-xs px-3 py-1 rounded-full flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                    System Active
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-blue-100 rounded-xl p-2.5">
                    <i class="fas fa-users text-blue-600 text-lg"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 font-medium">Total Users</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalUsers'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-green-100 rounded-xl p-2.5">
                    <i class="fas fa-seedling text-green-600 text-lg"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 font-medium">Total Planters</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalPlanters'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-purple-100 rounded-xl p-2.5">
                    <i class="fas fa-database text-purple-600 text-lg"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 font-medium">Total Records</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalRecords'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-amber-100 rounded-xl p-2.5">
                    <i class="fas fa-cloud-upload-alt text-amber-600 text-lg"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 font-medium">Latest Backup</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['latestBackup'] }}</p>
        </div>
    </div>

    <!-- Admin Menu Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Settings -->
        <a href="{{ route('admin.settings') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-start gap-4">
                <div class="bg-primary-100 rounded-xl p-3 group-hover:bg-primary-200 transition-colors">
                    <i class="fas fa-sliders text-primary-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">System Settings</h3>
                    <p class="text-sm text-gray-500 mt-1">Configure application parameters and preferences</p>
                </div>
            </div>
        </a>

        <!-- User Management -->
        <a href="{{ route('user-management') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-start gap-4">
                <div class="bg-blue-100 rounded-xl p-3 group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-users-gear text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">User Management</h3>
                    <p class="text-sm text-gray-500 mt-1">Manage users, roles, and permissions</p>
                </div>
            </div>
        </a>

        <!-- Cache Clear -->
        <button onclick="clearSystemCache()" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300 group text-left">
            <div class="flex items-start gap-4">
                <div class="bg-orange-100 rounded-xl p-3 group-hover:bg-orange-200 transition-colors">
                    <i class="fas fa-broom text-orange-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Clear Cache</h3>
                    <p class="text-sm text-gray-500 mt-1">Clear application, view, and route cache</p>
                </div>
            </div>
        </button>

        <!-- Backup -->
        <button onclick="createSystemBackup()" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300 group text-left">
            <div class="flex items-start gap-4">
                <div class="bg-green-100 rounded-xl p-3 group-hover:bg-green-200 transition-colors">
                    <i class="fas fa-database text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Create Backup</h3>
                    <p class="text-sm text-gray-500 mt-1">Backup database and application files</p>
                </div>
            </div>
        </button>

        <!-- System Info -->
        <a href="#" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-start gap-4">
                <div class="bg-purple-100 rounded-xl p-3 group-hover:bg-purple-200 transition-colors">
                    <i class="fas fa-circle-info text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">System Info</h3>
                    <p class="text-sm text-gray-500 mt-1">View PHP version, Laravel version, and server info</p>
                </div>
            </div>
        </a>

        <!-- Logs -->
        <a href="#" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-start gap-4">
                <div class="bg-red-100 rounded-xl p-3 group-hover:bg-red-200 transition-colors">
                    <i class="fas fa-file-lines text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Activity Logs</h3>
                    <p class="text-sm text-gray-500 mt-1">View system activity and access logs</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('updates.addCropYear') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors" onclick="event.preventDefault(); openCropYearModal();">
                <i class="fas fa-plus-circle text-green-600"></i> Add Crop Year
            </a>
            <a href="#" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors" onclick="event.preventDefault(); openQuedanPriceModal();">
                <i class="fas fa-tag text-blue-600"></i> Set Quedan Price
            </a>
            <a href="#" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors" onclick="event.preventDefault(); openMolassesPriceModal();">
                <i class="fas fa-flask text-purple-600"></i> Set Molasses Price
            </a>
            <a href="{{ route('summaryReport') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors">
                <i class="fas fa-chart-bar text-orange-600"></i> View Reports
            </a>
        </div>
    </div>
</div>


<script>
    function adminDashboardData() {
        return {};
    }

    function clearSystemCache() {
        Swal.fire({
            title: 'Clear System Cache?',
            text: 'This will clear all application cache including views, routes, and configurations.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, clear it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("admin.cache.clear") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Cache cleared successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to clear cache'
                    });
                });
            }
        });
    }

    function createSystemBackup() {
        Swal.fire({
            title: 'Create System Backup?',
            text: 'This will create a backup of the database and application files.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, create backup!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Creating backup...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('{{ route("admin.backup.create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Backup created successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to create backup'
                    });
                });
            }
        });
    }
</script>

@endsection