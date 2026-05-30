<!-- resources/views/menu/dashboard.blade.php -->
@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <div x-data="dashboardData()" class="space-y-6">
        <!-- Welcome Header -->
        <div
            class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Welcome back, {{ auth()->user()->fname }}!</h1>
                    <p class="mt-2 text-primary-100 text-sm sm:text-base">Here's your crop production overview for <span
                            x-text="stats.currentCropYear"></span></p>
                </div>
                <!-- Clock in Welcome Header -->
                <div class="mt-4 sm:mt-0" x-data="clockData()" x-init="startClock()">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-3 flex items-center gap-3">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-300 animate-pulse"></div>
                            <span class="text-xs font-medium text-primary-100 uppercase tracking-wider"
                                x-text="dayName"></span>
                        </div>
                        <div class="w-px h-6 bg-white/20"></div>
                        <div class="flex items-center gap-2">
                            <i class="far fa-calendar-alt text-primary-200 text-sm"></i>
                            <span class="text-sm font-semibold text-white" x-text="dateFormatted"></span>
                        </div>
                        <div class="w-px h-6 bg-white/20"></div>
                        <div class="flex items-center gap-2">
                            <i class="far fa-clock text-primary-200 text-sm"></i>
                            <span class="text-sm font-bold text-white font-mono tracking-wider"
                                x-text="timeFormatted"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Net Cane -->
            <div
                class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all duration-300 group">
                <div
                    class="absolute top-0 right-0 w-20 h-20 bg-green-50 rounded-bl-full opacity-50 group-hover:scale-110 transition-transform">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-green-100 rounded-xl p-2.5">
                            <i class="fas fa-seedling text-green-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">
                            <span x-text="stats.caneChange >= 0 ? '↑' : '↓'"></span> <span
                                x-text="Math.abs(stats.caneChange) + '%'"></span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Total Net Cane</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" x-text="formatNumber(stats.totalNetCane, 0)"></p>
                    <p class="text-xs text-gray-400 mt-0.5">Tons</p>
                </div>
            </div>

            <!-- Total Net Amount -->
            <div
                class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all duration-300 group">
                <div
                    class="absolute top-0 right-0 w-20 h-20 bg-blue-50 rounded-bl-full opacity-50 group-hover:scale-110 transition-transform">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-blue-100 rounded-xl p-2.5">
                            <i class="fas fa-peso-sign text-blue-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Total Value</span>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Net Amount</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">₱<span
                            x-text="formatNumber(stats.totalNetAmount, 0)"></span></p>
                    <p class="text-xs text-gray-400 mt-0.5">Philippine Peso</p>
                </div>
            </div>

            <!-- Total Planters -->
            <div
                class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all duration-300 group">
                <div
                    class="absolute top-0 right-0 w-20 h-20 bg-amber-50 rounded-bl-full opacity-50 group-hover:scale-110 transition-transform">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-amber-100 rounded-xl p-2.5">
                            <i class="fas fa-users text-amber-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Members</span>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Total Planters</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" x-text="stats.totalPlanters"></p>
                    <p class="text-xs text-gray-400 mt-0.5">registered members</p>
                </div>
            </div>

            <!-- Quedan Price -->
            <div
                class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all duration-300 group">
                <div
                    class="absolute top-0 right-0 w-20 h-20 bg-purple-50 rounded-bl-full opacity-50 group-hover:scale-110 transition-transform">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-purple-100 rounded-xl p-2.5">
                            <i class="fas fa-tags text-purple-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-full"
                            x-text="'Type ' + stats.quedanType"></span>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Quedan Price</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">₱<span
                            x-text="formatNumber(stats.quedanPrice, 2)"></span></p>
                    <p class="text-xs text-gray-400 mt-0.5">per lkg</p>
                </div>
            </div>
        </div>

        <!-- Price Cards Row -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 text-xs font-medium uppercase tracking-wider flex items-center">
                            <i class="fas fa-leaf mr-1.5"></i> Molasses Price
                        </p>
                        <p class="text-2xl font-bold mt-1">₱<span x-text="formatNumber(stats.molassesPrice, 2)"></span></p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3">
                        <i class="fas fa-flask text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl shadow-lg p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-xs font-medium uppercase tracking-wider flex items-center">
                            <i class="fas fa-hand-holding-usd mr-1.5"></i> Active Loans
                        </p>
                        <p class="text-2xl font-bold mt-1" x-text="stats.activeLoans"></p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3">
                        <i class="fas fa-coins text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-sky-500 to-indigo-500 rounded-2xl shadow-lg p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-100 text-xs font-medium uppercase tracking-wider flex items-center">
                            <i class="fas fa-chart-line mr-1.5"></i> Avg per Planter
                        </p>
                        <p class="text-2xl font-bold mt-1"
                            x-text="stats.activePlanters > 0 ? formatNumber(stats.totalNetCane / stats.activePlanters, 1) : '0'">
                        </p>
                        <p class="text-xs text-sky-200">tons</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3">
                        <i class="fas fa-calculator text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-fuchsia-500 to-pink-500 rounded-2xl shadow-lg p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-fuchsia-100 text-xs font-medium uppercase tracking-wider flex items-center">
                            <i class="fas fa-calendar-alt mr-1.5"></i> Crop Year
                        </p>
                        <p class="text-xl font-bold mt-1" x-text="stats.currentCropYear"></p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Yearly Production</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Net cane production by crop year</p>
                    </div>
                    <div class="bg-green-50 rounded-lg px-3 py-1.5">
                        <span class="text-xs font-semibold text-green-700">Total: <span
                                x-text="formatNumber(stats.totalNetCane, 0) + ' tons'"></span></span>
                    </div>
                </div>
                <div class="relative w-full" style="height: 320px;">
                    <canvas id="yearlyChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Weekly Trend</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Production trend per week</p>
                    </div>
                    {{-- <div class="bg-blue-50 rounded-lg px-3 py-1.5">
                        <span class="text-xs font-semibold text-blue-700">Week <span
                                x-text="stats.currentWeek"></span></span>
                    </div> --}}
                </div>
                <div class="relative w-full" style="height: 320px;">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Average Chart -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Monthly Production Total</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Total net cane per month for <span
                            x-text="stats.currentCropYear"></span></p>
                </div>
            </div>
            <div class="relative w-full" style="height: 250px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Production Distribution & Loan Overview -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Production Distribution Pie Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Production Distribution</h3>
                        <p class="text-xs text-gray-500 mt-0.5">How top planters contribute to total production</p>
                    </div>
                </div>
                <div class="relative w-full flex items-center justify-center" style="height: 280px;">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>

            <!-- Loan Overview Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Loan Portfolio Overview</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Active loans: <span
                                x-text="loanStats.active_count || 0"></span> | Total: ₱<span
                                x-text="formatNumber(loanStats.total_principal || 0, 0)"></span></p>
                    </div>
                </div>
                <div class="relative w-full" style="height: 280px;">
                    <canvas id="loanChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Alerts & Critical Info -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Alerts Panel -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-bell text-amber-500"></i> Alerts & Notifications
                </h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <template x-for="alert in alerts" :key="alert.title">
                        <div :class="getAlertClass(alert.type)"
                            class="flex items-start gap-3 p-3 rounded-xl border text-sm">
                            <i :class="getAlertIcon(alert.type) + ' mt-0.5'"></i>
                            <div>
                                <p class="font-semibold" x-text="alert.title"></p>
                                <p class="text-xs opacity-75" x-text="alert.message"></p>
                            </div>
                        </div>
                    </template>
                    <div x-show="alerts.length === 0" class="text-center py-4 text-gray-400 text-sm">
                        <i class="fas fa-check-circle text-green-400 text-2xl mb-1 block"></i>
                        All systems normal. No alerts.
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-tachometer-alt text-blue-500"></i> Key Metrics
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-500">Best Week</p>
                        <p class="text-xl font-bold text-green-600"
                            x-text="stats.bestWeek > 0 ? 'Week ' + stats.bestWeek : 'N/A'"></p>
                        <p class="text-xs text-gray-400"
                            x-text="stats.bestWeekCane > 0 ? formatNumber(stats.bestWeekCane, 0) + ' tons' : 'No data'">
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-500">Loan Collection</p>
                        <p class="text-xl font-bold"
                            :class="stats.collectionRate >= 80 ? 'text-green-600' : stats.collectionRate >= 50 ?
                                'text-amber-600' : 'text-red-600'"
                            x-text="stats.collectionRate + '%'"></p>
                        <p class="text-xs text-gray-400">repayment rate</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-500">Pending Approvals</p>
                        <p class="text-xl font-bold"
                            :class="stats.pendingApprovals > 0 ? 'text-amber-600' : 'text-green-600'"
                            x-text="stats.pendingApprovals"></p>
                        <p class="text-xs text-gray-400">loans & requests</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-500">Active Loans</p>
                        <p class="text-xl font-bold text-indigo-600" x-text="loanStats.active_count || 0"></p>
                        <p class="text-xs text-gray-400">currently running</p>
                    </div>
                </div>
            </div>

            <!-- At-Risk Planters -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-500"></i> Low Production Alert
                </h3>
                <p class="text-xs text-gray-500 mb-2">Planters with less than 5 tons this season</p>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <template x-for="planter in riskPlanters" :key="planter.planter_code">
                        <div class="flex items-center justify-between p-2 bg-red-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-900" x-text="planter.planter_name"></span>
                            <span class="text-xs text-red-600 font-semibold"
                                x-text="formatNumber(planter.total_cane, 2) + ' tons'"></span>
                        </div>
                    </template>
                    <div x-show="riskPlanters.length === 0" class="text-center py-4 text-gray-400 text-sm">
                        <i class="fas fa-check-circle text-green-400 text-2xl mb-1 block"></i>
                        All planters performing well.
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Planters & Activities -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
            <!-- Top Planters - Takes 3 columns -->
            <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Top Planters</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Highest contributors this season</p>
                        </div>
                    </div>
                </div>
                <div class="divide-y divide-gray-50">
                    <template x-for="(planter, index) in topPlanters" :key="planter.planter_code">
                        <div class="px-6 py-4 hover:bg-gray-50/50 transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div :class="index === 0 ? 'bg-yellow-100 text-yellow-700' : index === 1 ?
                                        'bg-gray-100 text-gray-700' : index === 2 ? 'bg-orange-100 text-orange-700' :
                                        'bg-gray-50 text-gray-500'"
                                        class="h-10 w-10 rounded-xl flex items-center justify-center font-bold text-sm">
                                        <span x-text="index + 1"></span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900" x-text="planter.planter_name"></p>
                                        <p class="text-xs text-gray-400">Planter #<span
                                                x-text="planter.planter_code"></span></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900"
                                        x-text="formatNumber(planter.total_cane, 1) + ' tons'"></p>
                                    <p class="text-xs text-green-600 font-medium">₱<span
                                            x-text="formatNumber(planter.total_amount, 0)"></span></p>
                                </div>
                            </div>
                            <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5">
                                <div class="bg-green-500 h-1.5 rounded-full"
                                    :style="'width: ' + (planter.total_cane / topPlanters[0].total_cane * 100) + '%'"></div>
                            </div>
                        </div>
                    </template>
                    <div x-show="topPlanters.length === 0" class="px-6 py-12 text-center">
                        <i class="fas fa-users text-4xl text-gray-200 mb-3"></i>
                        <p class="text-gray-500">No planter data available yet</p>
                    </div>
                </div>
            </div>

            <!-- Activities & Prices - Takes 2 columns -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Recent Activities -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">Recent Activities</h3>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <template x-for="activity in activities" :key="activity.id">
                            <div class="px-5 py-3.5 hover:bg-gray-50/50 transition-colors">
                                <div class="flex items-start space-x-3">
                                    <div
                                        :class="'h-9 w-9 rounded-xl flex items-center justify-center flex-shrink-0 ' + activity
                                            .bgColor">
                                        <i :class="activity.icon + ' ' + activity.iconColor + ' text-sm'"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900 line-clamp-2" x-text="activity.description"></p>
                                        <p class="text-xs text-gray-400 mt-1" x-text="activity.time"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Recent Prices -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Price Updates</h3>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400" x-text="recentPrices.length + ' records'"></span>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <div class="overflow-y-auto" style="max-height: 260px;">
                            <table class="w-full">
                                <thead class="sticky top-0 bg-gray-50 z-10">
                                    <tr class="text-xs text-gray-400 border-b border-gray-100">
                                        <th class="py-2.5 px-3 text-left font-medium">Type</th>
                                        <th class="py-2.5 px-3 text-left font-medium">Crop Year</th>
                                        <th class="py-2.5 px-3 text-left font-medium">Week</th>
                                        <th class="py-2.5 px-3 text-left font-medium">Date</th>
                                        <th class="py-2.5 px-3 text-right font-medium">Price</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <template x-for="price in sortedRecentPrices.slice(0, 5)" :key="price.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="py-2.5 px-3">
                                                <span
                                                    :class="price.type === 'Quedan' ? 'bg-green-100 text-green-700' :
                                                        'bg-blue-100 text-blue-700'"
                                                    class="px-2 py-0.5 text-xs font-medium rounded-full"
                                                    x-text="price.type"></span>
                                            </td>
                                            <td class="py-2.5 px-3">
                                                <span
                                                    class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-mono"
                                                    x-text="price.crop_year"></span>
                                            </td>
                                            <td class="py-2.5 px-3 text-sm text-gray-600">
                                                <span x-text="'Week ' + price.week_no"></span>
                                            </td>
                                            <td class="py-2.5 px-3 text-xs text-gray-500" x-text="price.date"></td>
                                            <td class="py-2.5 px-3 text-sm font-semibold text-gray-900 text-right">
                                                ₱<span x-text="formatNumber(price.price, 2)"></span>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="recentPrices.length === 0">
                                        <td colspan="5" class="py-8 text-center text-gray-400">
                                            <i class="fas fa-tags text-2xl text-gray-200 mb-1 block"></i>
                                            No price updates yet
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Show more indicator -->
                    <div x-show="recentPrices.length > 5"
                        class="px-5 py-2.5 border-t border-gray-100 bg-gray-50/50 text-center">
                        <span class="text-xs text-gray-400">
                            Showing 5 of <span x-text="recentPrices.length"></span> records. Scroll for more.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script src="{{ asset('js/dashboard.js') }}"></script>
    @endpush
@endsection
