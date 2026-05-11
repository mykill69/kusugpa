<!-- resources/views/menu/admin-settings.blade.php -->
@extends('layouts.main')

@section('title', 'System Settings')

@section('content')
    <div x-data="adminSettingsData()" x-init="loadSettings()" class="space-y-6">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-sliders text-2xl"></i>
                <h1 class="text-2xl sm:text-3xl font-bold">System Settings</h1>
            </div>
            <p class="text-primary-100 text-sm">Configure and manage system parameters</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Settings Navigation -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <nav class="space-y-1">
                        <button @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
                            <i class="fas fa-gear w-5"></i> General Settings
                        </button>
                        <button @click="activeTab = 'subscription'"
                            :class="activeTab === 'subscription' ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
                            <i class="fas fa-credit-card w-5"></i> Subscription
                        </button>
                        <button @click="activeTab = 'security'"
                            :class="activeTab === 'security' ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
                            <i class="fas fa-shield-halved w-5"></i> Security & Lock
                        </button>
                        <button @click="activeTab = 'maintenance'"
                            :class="activeTab === 'maintenance' ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
                            <i class="fas fa-wrench w-5"></i> Maintenance
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- General Settings -->
                <div x-show="activeTab === 'general'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">General Settings</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">System Name</label>
                            <input type="text" x-model="settings.system_name"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                        </div>
                        <button @click="saveGeneralSettings()"
                            class="bg-primary-600 text-white rounded-xl px-6 py-2.5 text-sm font-semibold hover:bg-primary-700 transition">
                            <i class="fas fa-save mr-2"></i> Save Settings
                        </button>
                    </div>
                </div>

                <!-- Subscription Settings -->
                <div x-show="activeTab === 'subscription'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Subscription Management</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div>
                                <p class="font-medium text-gray-900">Subscription Status</p>
                                <p class="text-xs mt-0.5">
                                    <span x-show="settings.subscription_status === 'active'" class="text-green-600">● System is accessible</span>
                                    <span x-show="settings.subscription_status !== 'active'" class="text-red-600">● System is locked for non-admin users</span>
                                </p>
                            </div>
                            <button @click="toggleSubscription()" :disabled="saving"
                                :class="settings.subscription_status === 'active' ? 'bg-green-500' : 'bg-red-500'"
                                class="relative w-14 h-7 rounded-full transition-colors duration-300 disabled:opacity-50">
                                <span class="absolute top-0.5 left-0.5 w-6 h-6 rounded-full bg-white shadow-md transition-transform duration-300"
                                    :class="settings.subscription_status === 'active' ? 'translate-x-7' : 'translate-x-0'"></span>
                            </button>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Monthly Fee (₱)</label>
                            <input type="number" x-model="settings.subscription_amount" min="0" step="0.01"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Payment Due Date</label>
                            <input type="date" x-model="settings.subscription_due_date"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="rounded-xl p-4"
                            :class="getDaysRemaining() > 30 ? 'bg-green-50 border border-green-200' : getDaysRemaining() > 7 ? 'bg-amber-50 border border-amber-200' : 'bg-red-50 border border-red-200'">
                            <p class="text-sm font-medium"
                                :class="getDaysRemaining() > 30 ? 'text-green-700' : getDaysRemaining() > 7 ? 'text-amber-700' : 'text-red-700'">
                                <i class="fas mr-1.5" :class="getDaysRemaining() > 30 ? 'fa-check-circle' : getDaysRemaining() > 7 ? 'fa-clock' : 'fa-exclamation-triangle'"></i>
                                <span x-text="getDaysRemaining() + ' days remaining'"></span>
                            </p>
                        </div>
                        <button @click="saveSubscription()"
                            class="w-full bg-purple-600 text-white rounded-xl px-6 py-3 text-sm font-semibold hover:bg-purple-700 transition">
                            <i class="fas fa-save mr-2"></i> Save Subscription Settings
                        </button>
                    </div>
                </div>

                <!-- Security & Lock Settings -->
                <div x-show="activeTab === 'security'" class="space-y-6">
                    <!-- Emergency Lock -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-lock text-red-600"></i>
                            </div>
                            Emergency System Lock
                        </h3>
                        <p class="text-sm text-gray-500 mb-4">Lock the entire system immediately. Only administrators can access the system when locked.</p>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Lock Reason</label>
                                <input type="text" x-model="settings.lock_reason"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500"
                                    placeholder="Reason for locking the system">
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-3">
                                <span class="text-sm text-gray-700">Current Status:</span>
                                <span class="text-sm font-semibold" 
                                      :class="settings.system_locked ? 'text-red-600' : 'text-green-600'"
                                      x-text="settings.system_locked ? '● System is LOCKED' : '● System is OPEN'"></span>
                            </div>
                            <button @click="toggleSystemLock()" :disabled="saving"
                                :class="settings.system_locked ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                                class="w-full py-3 text-white rounded-xl font-medium text-sm transition disabled:opacity-50">
                                <i class="fas mr-2" :class="settings.system_locked ? 'fa-unlock' : 'fa-lock'"></i>
                                <span x-text="settings.system_locked ? 'Unlock System' : 'Lock System Immediately'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Scheduled Lock -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-xmark text-blue-600"></i>
                            </div>
                            Scheduled Lock
                        </h3>
                        <p class="text-sm text-gray-500 mb-4">Schedule a date range when the system will be locked.</p>
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                                <input type="date" x-model="settings.lock_start_date"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                                <input type="date" x-model="settings.lock_end_date"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        <button @click="saveScheduledLock()"
                            class="w-full bg-blue-600 text-white rounded-xl px-6 py-3 text-sm font-semibold hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i> Save Schedule
                        </button>
                    </div>

                    <!-- Admin Security Key -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-key text-red-600"></i>
                            </div>
                            Security Access Key
                        </h3>
                        <div x-data="{
                            showKey: false,
                            passcode: '',
                            passcodeError: false,
                            attempts: 0,
                            locked: false,
                            verifyPasscode() {
                                if (this.passcode === 'KUSUG-ADMIN-VERIFY-2024') {
                                    this.showKey = true;
                                    this.passcodeError = false;
                                    this.attempts = 0;
                                } else {
                                    this.passcodeError = true;
                                    this.attempts++;
                                    if (this.attempts >= 3) {
                                        this.locked = true;
                                        setTimeout(() => { this.locked = false; this.attempts = 0; }, 300000);
                                    }
                                }
                            }
                        }">
                            <div x-show="!showKey && !locked" class="space-y-3">
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                    <p class="text-sm font-medium text-amber-800">Sensitive Information</p>
                                    <p class="text-xs text-amber-600 mt-0.5">Enter admin verification code to view.</p>
                                </div>
                                <div class="flex gap-2">
                                    <input type="password" x-model="passcode" @keyup.enter="verifyPasscode()"
                                        class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500"
                                        placeholder="Enter verification passcode">
                                    <button @click="verifyPasscode()"
                                        class="bg-primary-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold hover:bg-primary-700 transition">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                                <p x-show="passcodeError" class="text-xs text-red-500">
                                    Invalid passcode. <span x-text="3 - attempts + ' attempts remaining'"></span>
                                </p>
                            </div>
                            <div x-show="locked" class="bg-red-50 border border-red-200 rounded-xl p-4">
                                <p class="text-sm font-medium text-red-800">Access Locked</p>
                                <p class="text-xs text-red-600">Too many failed attempts. Wait 5 minutes.</p>
                            </div>
                            <div x-show="showKey" x-transition class="space-y-3">
                                <div class="bg-green-50 border border-green-200 rounded-xl p-3">
                                    <p class="text-xs text-green-700">Verified successfully</p>
                                </div>
                                <div class="relative">
                                    <input type="text" value="{{ config('app.admin_security_key', 'KUSUG-ADMIN-2024') }}" readonly
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 font-mono pr-12">
                                    <button onclick="navigator.clipboard.writeText('{{ config('app.admin_security_key', 'KUSUG-ADMIN-2024') }}')"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-primary-600">
                                        <i class="fas fa-copy text-sm"></i>
                                    </button>
                                </div>
                                <button @click="showKey = false; passcode = ''" class="text-sm text-gray-500">Hide Key</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Settings -->
                <div x-show="activeTab === 'maintenance'" class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-wrench text-yellow-600"></i>
                            </div>
                            Maintenance Mode
                        </h3>
                        <p class="text-sm text-gray-500 mb-4">Enable maintenance mode to display a maintenance message to all non-admin users.</p>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Maintenance Message</label>
                                <textarea x-model="settings.maintenance_message" rows="3"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500"
                                    placeholder="System is under maintenance. Please check back later."></textarea>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-3">
                                <span class="text-sm text-gray-700">Current Status:</span>
                                <span class="text-sm font-semibold" 
                                      :class="settings.maintenance_mode ? 'text-yellow-600' : 'text-green-600'"
                                      x-text="settings.maintenance_mode ? '● Maintenance ACTIVE' : '● System NORMAL'"></span>
                            </div>
                            <button @click="toggleMaintenance()" :disabled="saving"
                                :class="settings.maintenance_mode ? 'bg-red-600 hover:bg-red-700' : 'bg-yellow-600 hover:bg-yellow-700'"
                                class="w-full py-3 text-white rounded-xl font-medium text-sm transition disabled:opacity-50">
                                <i class="fas mr-2" :class="settings.maintenance_mode ? 'fa-play' : 'fa-pause'"></i>
                                <span x-text="settings.maintenance_mode ? 'Disable Maintenance Mode' : 'Enable Maintenance Mode'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Cache Management</h3>
                        <p class="text-sm text-gray-500 mb-4">Clear application cache to refresh views, routes, and configurations.</p>
                        <button @click="clearSystemCache()"
                            class="w-full bg-amber-500 text-white rounded-xl px-6 py-3 text-sm font-semibold hover:bg-amber-600 transition">
                            <i class="fas fa-broom mr-2"></i> Clear All Cache
                        </button>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Database Backup</h3>
                        <p class="text-sm text-gray-500 mb-4">Create a backup of the database and application files.</p>
                        <button @click="createSystemBackup()"
                            class="w-full bg-blue-500 text-white rounded-xl px-6 py-3 text-sm font-semibold hover:bg-blue-600 transition">
                            <i class="fas fa-database mr-2"></i> Create Backup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function adminSettingsData() {
                return {
                    activeTab: 'general',
                    saving: false,
                    settings: {
                        system_name: 'KUSUG-PA',
                        system_locked: false,
                        lock_reason: '',
                        lock_start_date: '',
                        lock_end_date: '',
                        maintenance_mode: false,
                        maintenance_message: '',
                        subscription_status: 'active',
                        subscription_amount: '0.00',
                        subscription_due_date: '',
                    },

                    async loadSettings() {
                        try {
                            const response = await fetch('{{ route("admin.settings.data") }}', {
                                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                            });
                            const data = await response.json();
                            this.settings.system_locked = data.system_locked === true || data.system_locked === '1' || data.system_locked === 1;
                            this.settings.maintenance_mode = data.maintenance_mode === true || data.maintenance_mode === '1' || data.maintenance_mode === 1;
                            this.settings.subscription_status = data.subscription_status || 'active';
                            this.settings.subscription_amount = data.subscription_amount || '0.00';
                            this.settings.subscription_due_date = data.subscription_due_date || '';
                            this.settings.lock_reason = data.lock_reason || '';
                            this.settings.lock_start_date = data.lock_start_date || '';
                            this.settings.lock_end_date = data.lock_end_date || '';
                            this.settings.maintenance_message = data.maintenance_message || '';
                            this.settings.system_name = data.system_name || 'KUSUG-PA';
                        } catch (error) {
                            console.error('Error loading settings:', error);
                        }
                    },

                    async saveSetting(key, value) {
                        this.saving = true;
                        try {
                            const response = await fetch('{{ route("admin.settings.update") }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                                body: JSON.stringify({ key: key, value: value })
                            });
                            const data = await response.json();
                            Swal.fire({ icon: 'success', title: 'Success!', text: data.message, timer: 2000, showConfirmButton: false });
                        } catch (error) {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save setting' });
                        } finally {
                            this.saving = false;
                        }
                    },

                    toggleSystemLock() {
                        const newLockState = !this.settings.system_locked;
                        Swal.fire({
                            title: newLockState ? 'Lock System?' : 'Unlock System?',
                            text: newLockState ? 'All non-admin users will be locked out immediately.' : 'System access will be restored for all users.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: newLockState ? 'Yes, lock it!' : 'Yes, unlock it!',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.settings.system_locked = newLockState;
                                this.saveSetting('system_locked', newLockState ? '1' : '0');
                                if (newLockState) this.saveSetting('lock_reason', this.settings.lock_reason);
                            }
                        });
                    },

                    toggleSubscription() {
                        const newStatus = this.settings.subscription_status === 'active' ? 'inactive' : 'active';
                        this.settings.subscription_status = newStatus;
                        this.saveSetting('subscription_status', newStatus);
                    },

                    toggleMaintenance() {
                        const newMode = !this.settings.maintenance_mode;
                        this.settings.maintenance_mode = newMode;
                        this.saveSetting('maintenance_mode', newMode ? '1' : '0');
                        if (newMode) this.saveSetting('maintenance_message', this.settings.maintenance_message);
                    },

                    saveSubscription() {
                        this.saving = true;
                        Promise.all([
                            this.saveSetting('subscription_status', this.settings.subscription_status),
                            this.saveSetting('subscription_amount', this.settings.subscription_amount),
                            this.saveSetting('subscription_due_date', this.settings.subscription_due_date)
                        ]).finally(() => { this.saving = false; });
                    },

                    saveScheduledLock() {
                        this.saving = true;
                        Promise.all([
                            this.saveSetting('lock_start_date', this.settings.lock_start_date),
                            this.saveSetting('lock_end_date', this.settings.lock_end_date)
                        ]).finally(() => { this.saving = false; });
                    },

                    saveGeneralSettings() {
                        this.saveSetting('system_name', this.settings.system_name);
                    },

                    getDaysRemaining() {
                        if (!this.settings.subscription_due_date) return 0;
                        const dueDate = new Date(this.settings.subscription_due_date);
                        const today = new Date();
                        return Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
                    },

                    clearSystemCache() {
                        Swal.fire({
                            title: 'Clear Cache?',
                            text: 'Clear all application cache.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, clear it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch('{{ route("admin.cache.clear") }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                                }).then(r => r.json()).then(d => {
                                    Swal.fire({ icon: 'success', title: 'Success!', text: d.message, timer: 2000, showConfirmButton: false });
                                });
                            }
                        });
                    },

                    createSystemBackup() {
                        Swal.fire({
                            title: 'Create Backup?',
                            text: 'Create a system backup.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, create it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch('{{ route("admin.backup.create") }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                                }).then(r => r.json()).then(d => {
                                    Swal.fire({ icon: 'success', title: 'Success!', text: d.message, timer: 2000, showConfirmButton: false });
                                });
                            }
                        });
                    }
                }
            }
        </script>
    @endpush
@endsection