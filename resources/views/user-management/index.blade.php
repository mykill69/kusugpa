@extends('layouts.main')

@section('title', 'User Management')

@section('content')
    <div x-data="userManagementData()" class="space-y-6">
        <!-- Page Header -->
        <div
            class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-users text-2xl"></i>
                        <h1 class="text-2xl sm:text-3xl font-bold">User Management</h1>
                    </div>
                    <p class="text-slate-300 text-sm">Manage system users, roles, and permissions</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    <span class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 text-sm">
                        Total Users: <span class="font-bold" x-text="users.length"></span>
                    </span>
                    <button @click="openCreateModal()"
                        class="bg-white text-primary-700 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-primary-50 transition-colors shadow-sm flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i> Add User
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Table Header -->
            <div class="p-4 sm:p-6 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-2">
                        
                        <h2 class="text-lg font-bold text-gray-900">Registered Users</h2>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" x-model="searchQuery" @input="filterUsers()"
                            class="w-full sm:w-72 pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50"
                            placeholder="Search users...">
                    </div>
                </div>
            </div>

            <!-- Table Body -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#
                            </th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                User</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Role</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Permissions</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Created</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(user, index) in filteredUsers" :key="user.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-500" x-text="index + 1"></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div :class="getRoleBadgeClass(user.role).avatar"
                                            class="h-9 w-9 rounded-xl flex items-center justify-center">
                                            <span class="text-sm font-bold" x-text="getInitials(user)"></span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900"
                                                x-text="user.fname + ' ' + (user.lname || '')"></p>
                                            <p class="text-xs text-gray-400" x-text="user.username"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="user.email"></td>
                                <td class="px-4 py-3">
                                    <span :class="getRoleBadgeClass(user.role).badge"
                                        class="px-2.5 py-1 text-xs font-medium rounded-full" x-text="user.role"></span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-if="user.role === 'Administrator'">
                                            <span
                                                class="px-2 py-0.5 text-xs bg-primary-50 text-primary-700 rounded-full font-medium">All
                                                Access</span>
                                        </template>
                                        <template x-if="user.role !== 'Administrator' && user.permissions">
                                            <template x-for="perm in user.permissions" :key="perm.id">
                                                <span
                                                    class="px-2 py-0.5 text-xs bg-blue-50 text-blue-700 rounded-full font-medium"
                                                    x-text="formatPermission(perm.name)"></span>
                                            </template>
                                        </template>
                                        <template
                                            x-if="user.role !== 'Administrator' && (!user.permissions || user.permissions.length === 0)">
                                            <span
                                                class="px-2 py-0.5 text-xs bg-gray-50 text-gray-500 rounded-full font-medium">No
                                                permissions</span>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500" x-text="formatDate(user.created_at)"></td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Permissions Button -->
                                        <button @click="openPermissionsModal(user)" :disabled="!canManagePermissions(user)"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                            :title="canManagePermissions(user) ? 'Manage Permissions' :
                                                'Cannot modify permissions for this role'">
                                            <i class="fas fa-shield-alt text-sm"></i>
                                        </button>

                                        <!-- Edit Button -->
                                        <button @click="openEditModal(user)" :disabled="!canEditUser(user)"
                                            class="p-2 text-primary-600 hover:bg-primary-50 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                            :title="canEditUser(user) ? 'Edit User' : 'Cannot edit this user'">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>

                                        <!-- Delete Button -->
                                        <button @click="deleteUser(user)" :disabled="!canDeleteUser(user)"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                            :title="canDeleteUser(user) ? 'Delete User' : 'Cannot delete this user'">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredUsers.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users text-4xl text-gray-200 mb-3"></i>
                                    <p class="text-gray-500">No users found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="px-4 sm:px-6 py-3 border-t border-gray-100 bg-gray-50/50">
                <p class="text-xs text-gray-500">
                    Showing <span class="font-semibold text-gray-700" x-text="filteredUsers.length"></span>
                    of <span class="font-semibold text-gray-700" x-text="users.length"></span> users
                </p>
            </div>
        </div>

        @include('modal.user-modal')
        @include('modal.permissions-modal')

    </div>

    @push('scripts')
        <script>
            function userManagementData() {
                return {
                    users: @json($users),
                    allPermissions: @json($allPermissions ?? []),
                    filteredUsers: [],
                    searchQuery: '',
                    showUserModal: false,
                    showPermissionsModal: false,
                    editingUser: null,
                    selectedUser: null,
                    loading: false,
                    savingPermissions: false,
                    userForm: {
                        fname: '',
                        mname: '',
                        lname: '',
                        username: '',
                        password: '',
                        role: '',
                    },
                    availablePermissions: [],

                    init() {
                        this.filterUsers();
                        // Format permissions from DB into the format needed by the UI
                        this.availablePermissions = this.allPermissions.map(p => ({
                            id: p.id,
                            slug: p.slug,
                            label: p.name,
                            description: p.description,
                            granted: false
                        }));
                    },

                    getInitials(user) {
                        const first = user.fname ? user.fname.charAt(0).toUpperCase() : '';
                        const last = user.lname ? user.lname.charAt(0).toUpperCase() : '';
                        return first + last || '?';
                    },
                    // Add these methods inside the userManagementData() return object:

                    getPermissionsByGroup(group) {
                        const groups = {
                            'reports': ['view-reports'],
                            'uploads': ['upload-summary', 'upload-trucking', 'upload-fci', 'upload-fuel', 'upload-rentals',
                                'upload-underload', 'upload-transloading'
                            ],
                            'settings': ['set-quedan-price', 'set-molasses-price', 'set-crop-year', 'set-week-number'],
                            'other': ['print-vouchers'],
                        };

                        const slugs = groups[group] || [];
                        return this.availablePermissions.filter(p => slugs.includes(p.slug));
                    },

                    getPermissionIcon(slug) {
                        const icons = {
                            'view-reports': 'fas fa-chart-bar',
                            'print-vouchers': 'fas fa-print',
                            'upload-summary': 'fas fa-file-csv',
                            'upload-trucking': 'fas fa-truck',
                            'upload-fci': 'fas fa-leaf',
                            'upload-fuel': 'fas fa-gas-pump',
                            'upload-rentals': 'fas fa-building',
                            'upload-underload': 'fas fa-weight-scale',
                            'upload-transloading': 'fas fa-exchange-alt',
                            'set-quedan-price': 'fas fa-tags',
                            'set-molasses-price': 'fas fa-flask',
                            'set-crop-year': 'fas fa-calendar-alt',
                            'set-week-number': 'fas fa-calendar-week',
                            'view-loans': 'fas fa-hand-holding-usd',
                            'create-loans': 'fas fa-file-invoice',
                            'approve-loans': 'fas fa-check-double',
                            'process-loan-payments': 'fas fa-coins',
                            'manage-loan-settings': 'fas fa-cogs',
                            'access-admin-panel': 'fas fa-shield-halved',
                            'manage-users': 'fas fa-users-cog',
                        };
                        return icons[slug] || 'fas fa-lock';
                    },

                    getGrantedCount() {
                        return this.availablePermissions.filter(p => p.granted).length;
                    },

                    selectAllPermissions() {
                        this.availablePermissions.forEach(p => p.granted = true);
                    },

                    deselectAllPermissions() {
                        this.availablePermissions.forEach(p => p.granted = false);
                    },

                    getRoleBadgeClass(role) {
                        const roles = {
                            'Administrator': {
                                badge: 'bg-red-100 text-red-700 ring-1 ring-red-300',
                                avatar: 'bg-red-100 text-red-700 ring-2 ring-red-300'
                            },
                            'super_admin': {
                                badge: 'bg-purple-100 text-purple-700 ring-1 ring-purple-300',
                                avatar: 'bg-purple-100 text-purple-700 ring-2 ring-purple-300'
                            },
                            'manager': {
                                badge: 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-300',
                                avatar: 'bg-indigo-100 text-indigo-700 ring-2 ring-indigo-300'
                            },
                            'loan_officer': {
                                badge: 'bg-teal-100 text-teal-700 ring-1 ring-teal-300',
                                avatar: 'bg-teal-100 text-teal-700 ring-2 ring-teal-300'
                            },
                            'User': {
                                badge: 'bg-blue-100 text-blue-700',
                                avatar: 'bg-blue-100 text-blue-700'
                            },
                            'Viewer': {
                                badge: 'bg-gray-100 text-gray-700',
                                avatar: 'bg-gray-100 text-gray-700'
                            },
                        };
                        return roles[role] || {
                            badge: 'bg-gray-100 text-gray-700',
                            avatar: 'bg-gray-100 text-gray-700'
                        };
                    },

                    // Check if current user can edit this user
                    canEditUser(user) {
                        const currentRole = '{{ auth()->user()->role }}';
                        const currentUserId = {{ auth()->id() }};

                        // Administrator can edit everyone INCLUDING themselves
                        if (currentRole === 'Administrator') {
                            return true; // Allow editing self too
                        }

                        // Super admin can edit themselves and Users/Viewers, but not other admins
                        if (currentRole === 'super_admin') {
                            if (user.id === currentUserId) return true; // Can edit self
                            return !['Administrator', 'super_admin'].includes(user.role);
                        }

                        // Other roles cannot edit anyone
                        return false;
                    },

                    // Check if current user can delete this user
                    canDeleteUser(user) {
                        const currentRole = '{{ auth()->user()->role }}';
                        const currentUserId = {{ auth()->id() }};

                        // Cannot delete yourself
                        if (user.id === currentUserId) return false;

                        // Administrator can delete everyone except themselves
                        if (currentRole === 'Administrator') return true;

                        // Super admin can delete Users and Viewers only
                        if (currentRole === 'super_admin') {
                            return !['Administrator', 'super_admin'].includes(user.role);
                        }

                        return false;
                    },

                    // Check if current user can manage permissions of this user
                    canManagePermissions(user) {
                        const currentRole = '{{ auth()->user()->role }}';

                        // Administrator and super_admin have all access by default - no need to manage
                        if (user.role === 'Administrator' || user.role === 'super_admin') return false;

                        // Administrator can manage everyone's permissions
                        if (currentRole === 'Administrator') return true;

                        // Super admin can manage Users and Viewers permissions
                        if (currentRole === 'super_admin') {
                            return ['User', 'Viewer'].includes(user.role);
                        }

                        return false;
                    },

                    formatDate(date) {
                        if (!date) return '';
                        return new Date(date).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                    },

                    formatPermission(slug) {
                        return slug.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    },

                    filterUsers() {
                        const query = this.searchQuery.toLowerCase();
                        this.filteredUsers = this.users.filter(user => {
                            return (user.fname || '').toLowerCase().includes(query) ||
                                (user.lname || '').toLowerCase().includes(query) ||
                                (user.username || '').toLowerCase().includes(query) ||
                                (user.role || '').toLowerCase().includes(query);
                        });
                    },

                    openCreateModal() {
                        this.editingUser = null;
                        this.userForm = {
                            fname: '',
                            mname: '',
                            lname: '',
                            username: '',
                            password: '',
                            role: ''
                        };
                        this.showUserModal = true;
                    },

                    openEditModal(user) {
                        this.editingUser = user;
                        this.userForm = {
                            fname: user.fname || '',
                            mname: user.mname || '',
                            lname: user.lname || '',
                            username: user.username || '',
                            password: '',
                            role: user.role || '',
                        };
                        this.showUserModal = true;
                    },

                    async saveUser() {
                        this.loading = true;
                        try {
                            const url = this.editingUser ?
                                `{{ url('/user-management') }}/${this.editingUser.id}` :
                                '{{ url('/user-management') }}';

                            const method = this.editingUser ? 'PUT' : 'POST';

                            // Only send password if it's filled
                            const formData = {
                                ...this.userForm
                            };
                            if (!formData.password && this.editingUser) {
                                delete formData.password;
                            }

                            const response = await fetch(url, {
                                method: method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(formData)
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                // Handle validation errors
                                if (data.errors) {
                                    const errorMessages = Object.values(data.errors).flat().join('\n');
                                    throw new Error(errorMessages);
                                }
                                throw new Error(data.message || 'Failed to save');
                            }

                            if (this.editingUser) {
                                const index = this.users.findIndex(u => u.id === this.editingUser.id);
                                if (index !== -1) this.users[index] = data.user;
                            } else {
                                this.users.unshift(data.user);
                            }

                            this.showUserModal = false;
                            this.filterUsers();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'Something went wrong'
                            });
                        } finally {
                            this.loading = false;
                        }
                    },
                    async loadPermissions() {
                        try {
                            const response = await fetch('{{ route('permissions.list') }}', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const data = await response.json();
                            this.availablePermissions = data.map(p => ({
                                ...p,
                                granted: this.selectedUser?.permissions?.some(up => up.slug === p.slug) || false
                            }));
                        } catch (error) {
                            console.error('Error loading permissions:', error);
                        }
                    },

                    async deleteUser(user) {
                        const result = await Swal.fire({
                            title: 'Delete User?',
                            text: `Are you sure you want to delete ${user.fname} ${user.lname || ''}? This action cannot be undone.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#dc2626',
                        });

                        if (!result.isConfirmed) return;

                        try {
                            const response = await fetch(`{{ url('/user-management') }}/${user.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            const data = await response.json();

                            if (!response.ok) throw new Error(data.message);

                            this.users = this.users.filter(u => u.id !== user.id);
                            this.filterUsers();

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message
                            });
                        }
                    },

                    openPermissionsModal(user) {
                        if (user.role === 'Administrator' || user.role === 'super_admin') return;

                        this.selectedUser = user;

                        // Reset permissions and set granted status
                        this.availablePermissions = this.allPermissions.map(p => ({
                            id: p.id,
                            slug: p.slug,
                            label: p.name,
                            description: p.description,
                            granted: user.permissions?.some(up => up.id === p.id) || false
                        }));

                        this.showPermissionsModal = true;
                    },

                    togglePermission(perm) {
                        perm.granted = !perm.granted;
                    },

                    async savePermissions() {
                        if (!this.selectedUser) return;

                        this.savingPermissions = true;

                        try {
                            const response = await fetch('{{ route('assign-permissions') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    user_id: this.selectedUser.id,
                                    permissions: this.availablePermissions.map(p => ({
                                        slug: p.slug,
                                        granted: p.granted
                                    }))
                                })
                            });

                            const data = await response.json();

                            if (!response.ok) throw new Error(data.message);

                            // Update user in local array
                            const index = this.users.findIndex(u => u.id === this.selectedUser.id);
                            if (index !== -1) {
                                this.users[index].permissions = data.permissions;
                            }

                            this.showPermissionsModal = false;
                            this.filterUsers();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Permissions updated successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message
                            });
                        } finally {
                            this.savingPermissions = false;
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
