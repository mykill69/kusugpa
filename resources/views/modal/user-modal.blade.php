<!-- resources/views/modal/user-modal.blade.php -->
<div x-show="showUserModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showUserModal = false"></div>
        <div class="relative inline-block w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900" x-text="editingUser ? 'Edit User' : 'Add New User'"></h3>
                <button @click="showUserModal = false" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form @submit.prevent="saveUser()" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">First Name *</label>
                        <input type="text" x-model="userForm.fname" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Middle Name</label>
                        <input type="text" x-model="userForm.mname"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 bg-gray-50">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Last Name *</label>
                    <input type="text" x-model="userForm.lname" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Username *</label>
                    <input type="text" x-model="userForm.username" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Password <span x-show="!editingUser" class="text-red-500">*</span>
                    </label>
                    <input type="password" x-model="userForm.password" :required="!editingUser"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 bg-gray-50"
                        :placeholder="editingUser ? 'Leave blank to keep current' : 'Enter password'">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Role *</label>
                    <select x-model="userForm.role" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 bg-gray-50">
                        <option value="">Select Role</option>
                        @if (auth()->user()->role === 'Administrator')
                            <option value="Administrator">Administrator</option>
                        @endif
                        @if (in_array(auth()->user()->role, ['Administrator', 'super_admin']))
                            <option value="super_admin">Super Admin</option>
                        @endif
                        @if (auth()->user()->role === 'Administrator')
                            <option value="manager">Manager</option>
                        @endif
                        <option value="loan_officer">Loan Officer</option>
                        <option value="User">User</option>
                        <option value="Viewer">Viewer</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1" x-show="userForm.role">
                        <span x-show="userForm.role === 'Administrator'">Full system control</span>
                        <span x-show="userForm.role === 'super_admin'">Full access, cannot manage admins</span>
                        <span x-show="userForm.role === 'manager'">Full access, cannot manage users</span>
                        <span x-show="userForm.role === 'loan_officer'">Process loans, needs manager approval</span>
                        <span x-show="userForm.role === 'User'">Access based on permissions</span>
                        <span x-show="userForm.role === 'Viewer'">Read-only access</span>
                    </p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showUserModal = false"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-colors shadow-sm"
                        :disabled="loading">
                        <span x-show="!loading" x-text="editingUser ? 'Update User' : 'Create User'"></span>
                        <span x-show="loading"><i class="fas fa-spinner fa-spin mr-1"></i> Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>