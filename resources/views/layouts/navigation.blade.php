<!-- resources/views/layouts/navigation.blade.php -->
<nav class="sticky top-0 z-20 bg-white border-b border-gray-100 shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <!-- Left: Mobile Menu + Logo -->
            <div class="flex items-center">
                <button @click="sidebarOpen = !sidebarOpen" type="button"
                    class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 lg:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <i class="fas fa-bars text-lg"></i>
                </button>

                <div class="flex items-center ml-2 lg:ml-0">
                    <span class="text-lg sm:text-xl font-bold text-primary-700 flex items-center">
                       Sugarcane Crop Management & Recording System Dashboard
                    </span>
                </div>
            </div>

            <!-- Right: Crop Year & Week Filters + User Menu -->
            <div class="flex items-center space-x-3">
                <!-- Crop Year & Week Filters -->
                <div class="hidden md:flex items-center space-x-2" x-data="filterData()">
                    <!-- Crop Year Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition-all duration-200">
                            <i class="fas fa-calendar-alt text-primary-600 text-xs"></i>
                            <span x-text="selectedCropYear || 'Crop Year'"></span>
                            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50 max-h-52 overflow-y-auto">
                            <div class="px-3 py-1.5 text-xs font-semibold text-gray-400 uppercase">Crop Year</div>
                            <template x-for="year in cropYears" :key="year">
                                <button @click="selectCropYear(year); open = false"
                                    :class="selectedCropYear === year ? 'bg-primary-50 text-primary-700 font-medium' :
                                        'text-gray-700 hover:bg-gray-50'"
                                    class="w-full text-left px-4 py-2 text-sm transition-colors flex items-center justify-between">
                                    <span x-text="year"></span>
                                    <i x-show="selectedCropYear === year"
                                        class="fas fa-check text-primary-600 text-xs"></i>
                                </button>
                            </template>
                            <div x-show="cropYears.length === 0" class="px-4 py-2 text-sm text-gray-400">Loading...
                            </div>
                        </div>
                    </div>

                    <!-- Week Number Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition-all duration-200">
                            <i class="fas fa-calendar-week text-primary-600 text-xs"></i>
                            <span x-text="selectedWeek ? 'Week ' + selectedWeek : 'Week No'"></span>
                            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50 max-h-52 overflow-y-auto">
                            <div class="px-3 py-1.5 text-xs font-semibold text-gray-400 uppercase">Week Number</div>
                            <button @click="selectWeek(''); open = false"
                                :class="!selectedWeek ? 'bg-primary-50 text-primary-700 font-medium' :
                                    'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-4 py-2 text-sm transition-colors flex items-center justify-between">
                                <span>All Weeks</span>
                                <i x-show="!selectedWeek" class="fas fa-check text-primary-600 text-xs"></i>
                            </button>
                            <template x-for="week in weeks" :key="week">
                                <button @click="selectWeek(week); open = false"
                                    :class="selectedWeek == week ? 'bg-primary-50 text-primary-700 font-medium' :
                                        'text-gray-700 hover:bg-gray-50'"
                                    class="w-full text-left px-4 py-2 text-sm transition-colors flex items-center justify-between">
                                    <span x-text="'Week ' + week"></span>
                                    <i x-show="selectedWeek == week" class="fas fa-check text-primary-600 text-xs"></i>
                                </button>
                            </template>
                            <div x-show="weeks.length === 0" class="px-4 py-2 text-sm text-gray-400">Loading...</div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</nav>

<script>
    function filterData() {
        return {
            cropYears: [],
            weeks: [],
            selectedCropYear: '',
            selectedWeek: '',

            init() {
                this.loadFilterOptions();
                window.addEventListener('refresh-filters', () => {
                    this.loadFilterOptions();
                });
            },

            async loadFilterOptions() {
                try {
                    const response = await fetch('{{ route('dashboard.data') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const data = await response.json();

                    if (data.stats && !this.selectedCropYear) {
                        this.selectedCropYear = data.stats.currentCropYear || '';
                    }
                    if (data.stats && !this.selectedWeek) {
                        this.selectedWeek = data.stats.currentWeek || '';
                    }

                    if (data.availableYears) {
                        this.cropYears = data.availableYears;
                    } else if (data.yearlyData && data.yearlyData.labels) {
                        this.cropYears = data.yearlyData.labels;
                    }

                    await this.loadWeeksForYear(this.selectedCropYear);
                } catch (error) {
                    console.error('Error loading filters:', error);
                }
            },

            async loadWeeksForYear(cropYear) {
                if (!cropYear) return;

                try {
                    const response = await fetch('{{ route('dashboard.weekly') }}?year=' + cropYear, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const data = await response.json();

                    if (data.labels) {
                        this.weeks = data.labels.map(label =>
                            parseInt(label.replace('Week ', ''))
                        ).filter(w => !isNaN(w));
                    }
                } catch (error) {
                    console.error('Error loading weeks:', error);
                }
            },

            selectCropYear(year) {
                this.selectedCropYear = year;
                this.selectedWeek = '';
                this.loadWeeksForYear(year);
                this.dispatchFilterChange();
            },

            selectWeek(week) {
                this.selectedWeek = week || '';
                this.dispatchFilterChange();
            },

            dispatchFilterChange() {
                window.dispatchEvent(new CustomEvent('filter-changed', {
                    detail: {
                        cropYear: this.selectedCropYear,
                        week: this.selectedWeek
                    }
                }));
            }
        }
    }

    function clockData() {
        return {
            timeFormatted: '',
            dateFormatted: '',
            dayName: '',

            startClock() {
                this.updateClock();
                setInterval(() => {
                    this.updateClock();
                }, 1000);
            },

            updateClock() {
                const now = new Date();
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');

                this.timeFormatted = `${hours}:${minutes}:${seconds}`;
                this.dateFormatted = `${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}`;
                this.dayName = days[now.getDay()];
            }
        }
    }
</script>
