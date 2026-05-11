<!-- resources/views/modal/permissions-modal.blade.php -->
<div x-show="showPermissionsModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showPermissionsModal = false"></div>
        <div class="relative inline-block w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-primary-50 to-white">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Manage Page Access</h3>
                    <p class="text-sm text-gray-500">
                        Configure pages for <span class="font-semibold text-primary-700" x-text="selectedUser?.fname + ' ' + selectedUser?.lname"></span>
                    </p>
                </div>
                <button @click="showPermissionsModal = false" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-700">Available Permissions</span>
                    <div class="flex items-center gap-2">
                        <button @click="selectAllPermissions()" class="text-xs font-medium text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-lg transition-colors">
                            <i class="fas fa-check-double mr-1"></i> Select All
                        </button>
                        <button @click="deselectAllPermissions()" class="text-xs font-medium text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors">
                            <i class="fas fa-times-circle mr-1"></i> Clear All
                        </button>
                    </div>
                </div>
                <div class="space-y-2 max-h-96 overflow-y-auto pr-2">
                    <template x-for="perm in availablePermissions" :key="perm.slug">
                        <label class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-xl hover:border-primary-200 hover:bg-primary-50/30 transition-all cursor-pointer group">
                            <div class="flex items-start gap-3">
                                <div class="h-9 w-9 rounded-lg bg-gray-100 group-hover:bg-primary-100 flex items-center justify-center flex-shrink-0 transition-colors">
                                    <i :class="getPermissionIcon(perm.slug) + ' text-gray-500 group-hover:text-primary-600 text-sm transition-colors'"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900" x-text="perm.label"></p>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="perm.description"></p>
                                </div>
                            </div>
                            <div class="relative flex-shrink-0">
                                <input type="checkbox" :checked="perm.granted" @change="togglePermission(perm)" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary-500 peer-focus:ring-2 peer-focus:ring-primary-300 peer-focus:ring-offset-2 transition-all duration-300 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:shadow-sm after:transition-all after:duration-300 peer-checked:after:translate-x-full"></div>
                            </div>
                        </label>
                    </template>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 flex gap-3">
                    <button @click="showPermissionsModal = false" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancel</button>
                    <button @click="savePermissions()" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-colors shadow-sm" :disabled="savingPermissions">
                        <span x-show="!savingPermissions"><i class="fas fa-save mr-2"></i> Save Permissions</span>
                        <span x-show="savingPermissions"><i class="fas fa-spinner fa-spin mr-2"></i> Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>