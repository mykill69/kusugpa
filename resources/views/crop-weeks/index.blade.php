<!-- resources/views/crop-weeks/index.blade.php -->
@extends('layouts.main')

@section('title', 'Crop Year & Week Management')

@section('content')
    <div x-data="cropWeekData()" class="space-y-6">
        <!-- Page Header -->
        <div
            class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-calendar-week text-2xl"></i>
                <h1 class="text-2xl sm:text-3xl font-bold">Crop Year & Week Management</h1>
            </div>
            <p class="text-primary-100 text-sm">Manage crop years and week numbers</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Crop Years Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="bg-green-100 rounded-lg p-2">
                                <i class="fas fa-calendar-alt text-green-600 text-sm"></i>
                            </div>
                            <h2 class="text-lg font-bold text-gray-900">Crop Years</h2>
                        </div>
                        <span class="text-xs text-gray-500" x-text="cropYears.length + ' entries'"></span>
                    </div>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortCropYears('crop_year')">
                                    Crop Year <i class="fas fa-sort text-gray-300 ml-1"></i>
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortCropYears('weeks_count')">
                                    Weeks <i class="fas fa-sort text-gray-300 ml-1"></i>
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="cropYear in sortedCropYears" :key="cropYear.id">
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="text-sm font-semibold text-gray-900"
                                            x-text="cropYear.crop_year"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm text-gray-600" x-text="cropYear.weeks_count + ' weeks'"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="editCropYear(cropYear.id, cropYear.crop_year)"
                                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            <button @click="deleteCropYear(cropYear.id, cropYear.crop_year)"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="cropYears.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-calendar-alt text-3xl text-gray-200 mb-2 block"></i>
                                    No crop years found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Weeks Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="bg-blue-100 rounded-lg p-2">
                                <i class="fas fa-calendar-week text-blue-600 text-sm"></i>
                            </div>
                            <h2 class="text-lg font-bold text-gray-900">Week Numbers</h2>
                        </div>
                        <div class="flex items-center gap-3">
                            <select x-model="weekCropYearFilter" @change="filterWeeks()"
                                class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:ring-2 focus:ring-primary-500">
                                <option value="">All Years</option>
                                <template x-for="cy in cropYears" :key="cy.id">
                                    <option :value="cy.crop_year" x-text="cy.crop_year"></option>
                                </template>
                            </select>
                            <span class="text-xs text-gray-500" x-text="filteredWeeks.length + ' entries'"></span>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortWeeks('crop_year')">
                                    Crop Year <i class="fas fa-sort text-gray-300 ml-1"></i>
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortWeeks('week_no')">
                                    Week <i class="fas fa-sort text-gray-300 ml-1"></i>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortWeeks('week_start_date')">
                                    Start Date & Time <i class="fas fa-sort text-gray-300 ml-1"></i>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortWeeks('week_end_date')">
                                    End Date & Time <i class="fas fa-sort text-gray-300 ml-1"></i>
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="week in sortedWeeks" :key="week.id">
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3">
                                        <span
                                            class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium"
                                            x-text="week.crop_year"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm font-semibold text-gray-900"
                                            x-text="'Week ' + week.week_no"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-gray-700"
                                                x-text="formatDate(week.week_start_date)"></span>
                                            <span class="text-xs font-mono bg-gray-100 text-gray-600 px-2 py-0.5 rounded"
                                                x-text="formatTime(week.week_start_date)"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-gray-700"
                                                x-text="formatDate(week.week_end_date)"></span>
                                            <span class="text-xs font-mono bg-gray-100 text-gray-600 px-2 py-0.5 rounded"
                                                x-text="formatTime(week.week_end_date)"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="editWeek(week)"
                                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            <button @click="deleteWeek(week.id)"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredWeeks.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-calendar-week text-3xl text-gray-200 mb-2 block"></i>
                                    No weeks found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Edit Crop Year Modal -->
        <div x-show="showCropYearModal" class="fixed inset-0 z-50 flex items-center justify-center"
            style="display: none;">
            <div class="fixed inset-0 bg-black/50" @click="showCropYearModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Crop Year</h3>
                <form @submit.prevent="saveCropYear()">
                    <input type="text" x-model="cropYearForm.crop_year" placeholder="e.g., 20252026" maxlength="8"
                        required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm mb-4 focus:ring-2 focus:ring-primary-500">
                    <div class="flex gap-3">
                        <button type="button" @click="showCropYearModal = false"
                            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm">Cancel</button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Week Modal -->
        <div x-show="showWeekModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
            <div class="fixed inset-0 bg-black/50" @click="showWeekModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Week</h3>
                <form @submit.prevent="saveWeek()" class="space-y-3">
                    <input type="text" x-model="weekForm.crop_year" placeholder="Crop Year (e.g., 20252026)"
                        maxlength="8" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    <input type="number" x-model="weekForm.week_no" placeholder="Week Number" min="1"
                        max="52" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    <input type="datetime-local" x-model="weekForm.week_start_date" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    <input type="datetime-local" x-model="weekForm.week_end_date" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showWeekModal = false"
                            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm">Cancel</button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function cropWeekData() {
            return {
                showCropYearModal: false,
                showWeekModal: false,
                editingCropYearId: null,
                editingWeekId: null,
                cropYearForm: {
                    crop_year: ''
                },
                weekForm: {
                    crop_year: '',
                    week_no: '',
                    week_start_date: '',
                    week_end_date: ''
                },
                cropYearSortField: 'crop_year',
                cropYearSortDir: 'asc',
                weekSortField: 'week_no',
                weekSortDir: 'asc',
                weekCropYearFilter: '',
                cropYears: {!! json_encode($cropYearsData) !!},
                weeks: {!! json_encode($weeksData) !!},

                get sortedCropYears() {
                    return [...this.cropYears].sort((a, b) => {
                        let valA = a[this.cropYearSortField] ?? '';
                        let valB = b[this.cropYearSortField] ?? '';
                        if (typeof valA === 'string') valA = valA.toLowerCase();
                        if (typeof valB === 'string') valB = valB.toLowerCase();
                        if (valA < valB) return this.cropYearSortDir === 'asc' ? -1 : 1;
                        if (valA > valB) return this.cropYearSortDir === 'asc' ? 1 : -1;
                        return 0;
                    });
                },

                get filteredWeeks() {
                    if (!this.weekCropYearFilter) return this.weeks;
                    return this.weeks.filter(w => w.crop_year === this.weekCropYearFilter);
                },

                get sortedWeeks() {
                    return [...this.filteredWeeks].sort((a, b) => {
                        let valA = a[this.weekSortField] ?? '';
                        let valB = b[this.weekSortField] ?? '';
                        if (typeof valA === 'string') valA = valA.toLowerCase();
                        if (typeof valB === 'string') valB = valB.toLowerCase();
                        if (valA < valB) return this.weekSortDir === 'asc' ? -1 : 1;
                        if (valA > valB) return this.weekSortDir === 'asc' ? 1 : -1;
                        return 0;
                    });
                },

                sortCropYears(field) {
                    if (this.cropYearSortField === field) {
                        this.cropYearSortDir = this.cropYearSortDir === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.cropYearSortField = field;
                        this.cropYearSortDir = 'asc';
                    }
                },

                sortWeeks(field) {
                    if (this.weekSortField === field) {
                        this.weekSortDir = this.weekSortDir === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.weekSortField = field;
                        this.weekSortDir = 'asc';
                    }
                },

                filterWeeks() {},

                formatDate(date) {
                    if (!date) return 'N/A';
                    var d = new Date(date.replace(' ', 'T'));
                    return d.toLocaleDateString('en-US', {
                        month: 'short',
                        day: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },

                editCropYear(id, cropYear) {
                    this.editingCropYearId = id;
                    this.cropYearForm = {
                        crop_year: cropYear
                    };
                    this.showCropYearModal = true;
                },

                async saveCropYear() {
                    var url = '/crop-year/' + this.editingCropYearId;
                    try {
                        var response = await fetch(url, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                crop_year: this.cropYearForm.crop_year,
                                user_id: '{{ auth()->id() }}'
                            })
                        });
                        var data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Failed');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        this.showCropYearModal = false;
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message
                        });
                    }
                },

                async deleteCropYear(id, cropYear) {
                    var result = await Swal.fire({
                        title: 'Delete Crop Year?',
                        text: 'Delete "' + cropYear + '"?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        confirmButtonColor: '#dc2626'
                    });
                    if (!result.isConfirmed) return;
                    try {
                        var response = await fetch('/crop-year/' + id, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        var data = await response.json();
                        if (!response.ok) throw new Error(data.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message
                        });
                    }
                },

                editWeek(week) {
                    this.editingWeekId = week.id;
                    this.weekForm = {
                        crop_year: week.crop_year,
                        week_no: week.week_no,
                        week_start_date: week.week_start_date ? week.week_start_date.replace(' ', 'T') : '',
                        week_end_date: week.week_end_date ? week.week_end_date.replace(' ', 'T') : ''
                    };
                    this.showWeekModal = true;
                },

                async saveWeek() {
                    if (new Date(this.weekForm.week_end_date) <= new Date(this.weekForm.week_start_date)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'End date must be after start date'
                        });
                        return;
                    }
                    var url = '/week/' + this.editingWeekId;
                    try {
                        var response = await fetch(url, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                ...this.weekForm,
                                user_id: '{{ auth()->id() }}'
                            })
                        });
                        var data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Failed');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        this.showWeekModal = false;
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message
                        });
                    }
                },

                async deleteWeek(id) {
                    var result = await Swal.fire({
                        title: 'Delete Week?',
                        text: 'This cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        confirmButtonColor: '#dc2626'
                    });
                    if (!result.isConfirmed) return;
                    try {
                        var response = await fetch('/week/' + id, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        var data = await response.json();
                        if (!response.ok) throw new Error(data.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message
                        });
                    }
                },
                // Add this to your adminSettingsData() or the relevant component
                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', {
                        month: 'short',
                        day: '2-digit',
                        year: 'numeric'
                    });
                },

                formatTime(dateString) {
                    if (!dateString) return '--:--:--';
                    const date = new Date(dateString);
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    const seconds = String(date.getSeconds()).padStart(2, '0');
                    return hours + ':' + minutes + ':' + seconds;
                },
            };
        }
    </script>

@endsection
