<!-- resources/views/menu/printVoucher.blade.php -->
@extends('layouts.main')

@section('content')
<div x-data="voucherFilter()" class="space-y-6">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Voucher Filter</h3>
        </div>
        
        <div class="p-6">
            <form @submit.prevent="loadVoucher" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <!-- Crop Year -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Crop Year</label>
                        <select x-model="filters.crop_year" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Select Year</option>
                            @foreach($cropYear as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Week From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Week From</label>
                        <select x-model="filters.week_from"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">From</option>
                            @foreach($weekNos as $week)
                                <option value="{{ $week }}">{{ $week }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Week To -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Week To</label>
                        <select x-model="filters.week_to"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">To</option>
                            @foreach($weekNos as $week)
                                <option value="{{ $week }}">{{ $week }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Planter Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Planter Names</label>
                        <div class="mt-1 relative" x-data="{ open: false, selected: [] }">
                            <button @click="open = !open" type="button"
                                    class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500">
                                <span class="block truncate" x-text="selected.length ? selected.length + ' selected' : 'All Planters'"></span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </span>
                            </button>
                            
                            <div x-show="open" @click.away="open = false"
                                 class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @foreach($planterNames as $planter)
                                    <div class="cursor-pointer select-none relative px-3 py-2 hover:bg-primary-50">
                                        <input type="checkbox" value="{{ $planter }}" 
                                               x-model="filters.planter_names"
                                               class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                                        <span class="ml-2">{{ $planter }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="resetFilters"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </button>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                        <i class="fas fa-search mr-2"></i>
                        Generate Voucher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- PDF Preview -->
    <div x-show="showPreview" class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Voucher Preview</h3>
            <div class="flex space-x-2">
                <button @click="downloadPDF"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-download mr-2"></i>
                    Download
                </button>
                <button @click="printVoucher"
                        class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>
            </div>
        </div>
        <div class="p-0">
            <iframe :src="pdfUrl" 
                    class="w-full rounded-b-lg" 
                    style="height: 800px; border: none;">
            </iframe>
        </div>
    </div>
</div>

@push('scripts')
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
                    alert('Please select crop year and week range');
                    return;
                }
                
                const params = new URLSearchParams({
                    crop_year: this.filters.crop_year,
                    week_from: this.filters.week_from,
                    week_to: this.filters.week_to,
                    planter_name: this.filters.planter_names
                });
                
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
            },
            
            downloadPDF() {
                window.open(this.pdfUrl.replace('pdf-preview', 'download-pdf'), '_blank');
            },
            
            printVoucher() {
                const iframe = document.querySelector('iframe');
                iframe.contentWindow.print();
            }
        }
    }
</script>
@endpush
@endsection