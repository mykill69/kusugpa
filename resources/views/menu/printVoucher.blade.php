<!-- resources/views/menu/printVoucher.blade.php -->
@extends('layouts.main')

@section('title', 'Print Vouchers')

@section('content')
<div x-data="voucherFilter()" class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-print text-2xl"></i>
                    <h1 class="text-2xl sm:text-3xl font-bold">Print Vouchers</h1>
                </div>
                <p class="text-primary-100 text-sm">Generate and print check vouchers for planters</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center gap-3">
                <span class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 text-sm" x-show="showPreview">
                    <i class="fas fa-file-invoice mr-1"></i> Preview Ready
                </span>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <!-- FIX: Added overflow-visible to prevent clipping of dropdown -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-visible">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="bg-primary-100 rounded-lg p-2">
                    <i class="fas fa-filter text-primary-600 text-sm"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Voucher Filters</h2>
            </div>
        </div>

        <!-- FIX: Added overflow-visible here too -->
        <div class="p-6 overflow-visible">
            <form @submit.prevent="loadVoucher" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Crop Year -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            <i class="fas fa-calendar-alt text-primary-500 mr-1"></i> Crop Year *
                        </label>
                        <select x-model="filters.crop_year" 
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50 transition">
                            <option value="">Select Crop Year</option>
                            @foreach($cropYear as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Week From -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            <i class="fas fa-play-circle text-primary-500 mr-1"></i> Week From *
                        </label>
                        <select x-model="filters.week_from"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50 transition">
                            <option value="">Select Week</option>
                            @foreach($weekNos as $week)
                                <option value="{{ $week }}">Week {{ $week }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Week To -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            <i class="fas fa-stop-circle text-primary-500 mr-1"></i> Week To *
                        </label>
                        <select x-model="filters.week_to"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50 transition">
                            <option value="">Select Week</option>
                            @foreach($weekNos as $week)
                                <option value="{{ $week }}">Week {{ $week }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Planter Selection -->
                    <!-- FIX: Removed overflow-hidden from this container -->
                    <div class="relative">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            <i class="fas fa-user text-primary-500 mr-1"></i> Planter Names
                        </label>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-left bg-gray-50 hover:bg-white focus:ring-2 focus:ring-primary-500 transition flex items-center justify-between">
                                <span class="truncate" x-text="filters.planter_names.length ? filters.planter_names.length + ' planter(s) selected' : 'All Planters'"></span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>
                            
                            <!-- FIX: Increased z-index to 50 and added positioning -->
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto left-0">
                                <div class="sticky top-0 bg-white p-2 border-b border-gray-100 z-10">
                                    <button @click="filters.planter_names = []; open = false" type="button" class="text-xs text-primary-600 hover:text-primary-700">Clear All</button>
                                    <button @click="filters.planter_names = {{ json_encode($planterNames->toArray()) }}; open = false" type="button" class="text-xs text-primary-600 hover:text-primary-700 ml-3">Select All</button>
                                </div>
                                @foreach($planterNames as $planter)
                                    <label class="flex items-center px-4 py-2 hover:bg-primary-50 cursor-pointer transition">
                                        <input type="checkbox" value="{{ $planter }}" 
                                               x-model="filters.planter_names"
                                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 mr-3">
                                        <span class="text-sm text-gray-700 truncate">{{ $planter }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="resetFilters"
                            class="inline-flex items-center px-5 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </button>
                    <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 border border-transparent rounded-xl text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="!filters.crop_year || !filters.week_from || !filters.week_to">
                        <i class="fas fa-file-pdf mr-2"></i> Generate Voucher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- PDF Preview -->
    <div x-show="showPreview" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <div class="bg-green-100 rounded-lg p-2">
                    <i class="fas fa-check-circle text-green-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Voucher Preview</h3>
                    <p class="text-xs text-gray-500">
                        Crop Year: <span x-text="filters.crop_year"></span> | 
                        Weeks: <span x-text="filters.week_from + ' - ' + filters.week_to"></span> |
                        Planters: <span x-text="filters.planter_names.length ? filters.planter_names.length : 'All'"></span>
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <button @click="downloadPDF"
                        class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    <i class="fas fa-download mr-2 text-blue-500"></i> Download
                </button>
                <button @click="printVoucher"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
            </div>
        </div>
        <div class="p-0 bg-gray-100">
            <iframe :src="pdfUrl" class="w-full" style="height: 800px; border: none;"></iframe>
        </div>
    </div>

    <!-- Empty State -->
    <div x-show="!showPreview" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-print text-3xl text-gray-300"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">No Voucher Generated</h3>
        <p class="text-sm text-gray-500 max-w-md mx-auto">
            Select a crop year, week range, and optionally specific planters, then click "Generate Voucher" to preview and print.
        </p>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function voucherFilter() {
        return {
            filters: {
                crop_year: '{{ $selectedCropYear ?? '' }}',
                week_from: '{{ $weekFrom ?? '' }}',
                week_to: '{{ $weekTo ?? '' }}',
                planter_names: []
            },
            showPreview: false,
            pdfUrl: '',
            
            loadVoucher() {
                if (!this.filters.crop_year || !this.filters.week_from || !this.filters.week_to) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Filters',
                        text: 'Please select crop year and week range.',
                        confirmButtonColor: '#16a34a'
                    });
                    return;
                }
                
                const params = new URLSearchParams({
                    crop_year: this.filters.crop_year,
                    week_from: this.filters.week_from,
                    week_to: this.filters.week_to,
                });
                
                if (this.filters.planter_names.length > 0) {
                    this.filters.planter_names.forEach(name => {
                        params.append('planter_name[]', name);
                    });
                }
                
                this.pdfUrl = '{{ route("voucher.pdf") }}?' + params.toString();
                this.showPreview = true;
            },
            
            resetFilters() {
                this.filters = {
                    crop_year: '',
                    week_from: '',
                    week_to: '',
                    planter_names: []
                };
                this.showPreview = false;
                this.pdfUrl = '';
            },
            
            downloadPDF() {
                if (this.pdfUrl) {
                    const downloadUrl = this.pdfUrl.replace('pdf-preview', 'download-pdf');
                    window.open(downloadUrl, '_blank');
                }
            },
            
            printVoucher() {
                const iframe = document.querySelector('iframe');
                if (iframe && iframe.contentWindow) {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                }
            }
        }
    }
</script>
@endpush
@endsection