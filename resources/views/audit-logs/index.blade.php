<!-- resources/views/audit-logs/index.blade.php -->
@extends('layouts.main')

@section('title', 'Audit Logs')

@section('content')
<div x-data="auditLogData()" x-init="init()" class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-history text-2xl"></i>
                    <h1 class="text-2xl sm:text-3xl font-bold">Audit Logs</h1>
                </div>
                <p class="text-slate-300 text-sm">System activity trail and user actions</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center gap-2">
                <button @click="exportPDF()" class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-white/30 transition">
                    <i class="fas fa-file-pdf mr-2"></i> Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Total Logs</p>
            <p class="text-2xl font-bold text-gray-900" x-text="stats.total"></p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Today</p>
            <p class="text-2xl font-bold text-green-600" x-text="stats.today"></p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Logins</p>
            <p class="text-2xl font-bold text-blue-600" x-text="stats.logins"></p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Errors</p>
            <p class="text-2xl font-bold text-red-600" x-text="stats.errors"></p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                
                <input type="text" x-model="search" @input="applyFilters()" placeholder="Search logs..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Module</label>
                <select x-model="moduleFilter" @change="applyFilters()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
                    <option value="all">All Modules</option>
                    <template x-for="mod in modules" :key="mod">
                        <option :value="mod" x-text="mod.charAt(0).toUpperCase() + mod.slice(1)"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Action</label>
                <select x-model="actionFilter" @change="applyFilters()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
                    <option value="all">All Actions</option>
                    <template x-for="act in actions" :key="act">
                        <option :value="act" x-text="act.charAt(0).toUpperCase() + act.slice(1)"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" x-model="dateFrom" @change="loadFilteredLogs()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" x-model="dateTo" @change="loadFilteredLogs()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
            </div>
            <div>
                <span class="block text-xs text-gray-400 ml-auto" x-text="'Showing ' + filteredLogs.length + ' of ' + totalRecords + ' logs'"></span>
            <button @click="clearFilters()" class="text-gray-500 hover:text-gray-700 text-sm py-2">
                <i class="fas fa-times mr-1"></i> Clear
            </button>
            
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto" style="max-height: 600px; overflow-y: auto;">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortBy('created_at')">
                            Date/Time <i class="fas fa-sort text-gray-300 ml-1"></i>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Module</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="log in paginatedLogs" :key="log.id">
                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="showDetails(log)">
                            <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap" x-text="formatDateTime(log.created_at)"></td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900" x-text="log.user_name || 'System'"></p>
                                <p class="text-xs text-gray-500" x-text="log.user_role || '-'"></p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full" :class="getActionClass(log.action)" x-text="log.action.charAt(0).toUpperCase() + log.action.slice(1)"></span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600" x-text="log.module.charAt(0).toUpperCase() + log.module.slice(1)"></td>
                            <td class="px-4 py-3 text-sm text-gray-700" x-text="truncate(log.description, 80)"></td>
                            <td class="px-4 py-3 text-xs text-gray-500 font-mono" x-text="log.ip_address"></td>
                        </tr>
                    </template>
                    <tr x-show="filteredLogs.length === 0 && !loading">
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-history text-3xl text-gray-200 mb-2 block"></i>
                            No audit logs found
                        </td>
                    </tr>
                    <tr x-show="loading">
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl text-gray-300 mb-2"></i>
                            <p>Loading logs...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">Per page:</span>
                <select x-model="perPage" @change="applyFilters()" class="border border-gray-200 rounded-lg px-2 py-1 text-xs">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="flex items-center gap-3" x-show="totalPages > 1">
                <button @click="currentPage--" :disabled="currentPage === 1" class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50 hover:bg-gray-50">Previous</button>
                <span class="text-sm text-gray-500" x-text="'Page ' + currentPage + ' of ' + totalPages"></span>
                <button @click="currentPage++" :disabled="currentPage >= totalPages" class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50 hover:bg-gray-50">Next</button>
            </div>
            <span class="text-xs text-gray-400" x-text="totalRecords + ' total records'"></span>
        </div>
    </div>

    <!-- Log Detail Modal -->
    <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black/50" @click="showDetailModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Log Details</h3>
                <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 p-2"><i class="fas fa-times"></i></button>
            </div>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">User:</span><span class="font-medium" x-text="logDetail.user_name"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Role:</span><span class="font-medium" x-text="logDetail.user_role"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Action:</span><span x-text="logDetail.action"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Module:</span><span x-text="logDetail.module"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Description:</span><span x-text="logDetail.description"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">IP Address:</span><span class="font-mono" x-text="logDetail.ip_address"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Date:</span><span x-text="logDetail.created_at"></span></div>
                <div x-show="logDetail.details" class="mt-3">
                    <span class="text-gray-500 block mb-1">Details:</span>
                    <pre class="bg-gray-50 p-3 rounded-xl text-xs overflow-x-auto" x-text="JSON.stringify(logDetail.details, null, 2)"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function auditLogData() {
        return {
            allLogs: @json($logs->items()),
            totalRecords: {{ $logs->total() }},
            currentServerPage: {{ $logs->currentPage() }},
            lastServerPage: {{ $logs->lastPage() }},
            modules: @json($modules),
            actions: @json($actions),
            stats: @json($stats),
            search: '',
            moduleFilter: 'all',
            actionFilter: 'all',
            dateFrom: '',
            dateTo: '',
            sortField: 'created_at',
            sortDirection: 'desc',
            currentPage: 1,
            perPage: 25,
            loading: false,
            showDetailModal: false,
            logDetail: {},

            init() {
                // Initial load
            },

            get filteredLogs() {
                let logs = [...this.allLogs];

                if (this.search) {
                    const s = this.search.toLowerCase();
                    logs = logs.filter(l =>
                        (l.description || '').toLowerCase().includes(s) ||
                        (l.user_name || '').toLowerCase().includes(s) ||
                        (l.module || '').toLowerCase().includes(s) ||
                        (l.action || '').toLowerCase().includes(s)
                    );
                }

                if (this.moduleFilter !== 'all') {
                    logs = logs.filter(l => l.module === this.moduleFilter);
                }

                if (this.actionFilter !== 'all') {
                    logs = logs.filter(l => l.action === this.actionFilter);
                }

                // Client-side date filter on current page data
                if (this.dateFrom) {
                    logs = logs.filter(l => (l.created_at || '').substring(0, 10) >= this.dateFrom);
                }
                if (this.dateTo) {
                    logs = logs.filter(l => (l.created_at || '').substring(0, 10) <= this.dateTo);
                }

                logs.sort((a, b) => {
                    let valA = a[this.sortField] ?? '';
                    let valB = b[this.sortField] ?? '';
                    if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                    if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                    return 0;
                });

                return logs;
            },

            get paginatedLogs() {
                const start = (this.currentPage - 1) * this.perPage;
                return this.filteredLogs.slice(start, start + this.perPage);
            },

            get totalPages() {
                return Math.ceil(this.filteredLogs.length / this.perPage);
            },

            applyFilters() {
                this.currentPage = 1;
            },

            async loadFilteredLogs() {
                this.loading = true;
                try {
                    const params = new URLSearchParams();
                    if (this.dateFrom) params.append('date_from', this.dateFrom);
                    if (this.dateTo) params.append('date_to', this.dateTo);
                    params.append('page', 1);
                    
                    const response = await fetch('/audit-logs/load-more?' + params.toString(), {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    
                    this.allLogs = data.data;
                    this.totalRecords = data.total;
                    this.currentServerPage = data.current_page;
                    this.lastServerPage = data.last_page;
                    this.currentPage = 1;
                } catch (e) {
                    console.error('Error loading logs:', e);
                } finally {
                    this.loading = false;
                }
            },

            sortBy(field) {
                if (this.sortField === field) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortField = field;
                    this.sortDirection = 'desc';
                }
            },

            clearFilters() {
                this.search = '';
                this.moduleFilter = 'all';
                this.actionFilter = 'all';
                this.dateFrom = '';
                this.dateTo = '';
                this.loadFilteredLogs();
            },

            showDetails(log) {
                this.logDetail = log;
                this.showDetailModal = true;
            },

            exportPDF() {
                const params = new URLSearchParams();
                if (this.dateFrom) params.append('date_from', this.dateFrom);
                if (this.dateTo) params.append('date_to', this.dateTo);
                if (this.moduleFilter !== 'all') params.append('module', this.moduleFilter);
                window.open('/audit-logs/export/pdf?' + params.toString(), '_blank');
            },

            getActionClass(action) {
                const classes = {
                    'login': 'bg-blue-100 text-blue-700', 'logout': 'bg-gray-100 text-gray-700',
                    'create': 'bg-green-100 text-green-700', 'upload': 'bg-green-100 text-green-700',
                    'update': 'bg-yellow-100 text-yellow-700', 'delete': 'bg-red-100 text-red-700',
                    'approve': 'bg-teal-100 text-teal-700', 'reject': 'bg-red-100 text-red-700',
                    'payment': 'bg-emerald-100 text-emerald-700', 'error': 'bg-red-100 text-red-700',
                    'activate': 'bg-indigo-100 text-indigo-700', 'deactivate': 'bg-orange-100 text-orange-700',
                    'view': 'bg-purple-100 text-purple-700', 'print': 'bg-cyan-100 text-cyan-700',
                    'download': 'bg-sky-100 text-sky-700', 'preview': 'bg-violet-100 text-violet-700',
                    'maintenance': 'bg-amber-100 text-amber-700', 'lock': 'bg-red-100 text-red-700',
                    'unlock': 'bg-green-100 text-green-700', 'enable': 'bg-green-100 text-green-700',
                    'disable': 'bg-red-100 text-red-700', 'failed_login': 'bg-red-100 text-red-700',
                };
                return classes[action] || 'bg-gray-100 text-gray-700';
            },

            formatDateTime(date) {
                if (!date) return '';
                const d = new Date(date);
                return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) + ' ' +
                       d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
            },

            truncate(str, len) {
                if (!str) return '';
                return str.length > len ? str.substring(0, len) + '...' : str;
            }
        }
    }
</script>
@endpush
@endsection