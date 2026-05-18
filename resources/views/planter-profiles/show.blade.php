<!-- resources/views/planter-profiles/show.blade.php -->
@extends('layouts.main')

@section('title', 'Planter Profile')

@section('content')
    <div class="space-y-6">
        <div
            class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('planter-profiles.index') }}" class="text-white/70 hover:text-white">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold">{{ $planter->planter_name }}</h1>
                <!-- Status Badge with live indicator -->
                @if ($planter->status === 'active')
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-200 border border-green-500/30">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-300"></span>
                        </span>
                        Active
                    </span>
                @elseif($planter->status === 'suspended')
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-200 border border-red-500/30">
                        <span class="relative flex h-2 w-2">
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-400"></span>
                        </span>
                        Suspended
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-full bg-gray-500/20 text-gray-300 border border-gray-500/30">
                        <span class="relative flex h-2 w-2">
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-400"></span>
                        </span>
                        Inactive
                    </span>
                @endif
            </div>
            <p class="text-primary-100 text-sm">Planter Code: {{ $planter->planter_code }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Production History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Crop Year</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Week</th>
                                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Net Cane</th>
                                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Net Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($productionHistory as $ph)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-2 text-sm">{{ $ph->crop_year }}</td>
                                        <td class="px-4 py-2 text-sm">Week {{ $ph->week_no }}</td>
                                        <td class="px-4 py-2 text-sm text-right">{{ number_format($ph->total_cane, 3) }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-right">₱{{ number_format($ph->total_amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">No production data
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Yearly Totals</h3>
                    @foreach ($yearlyTotals as $yt)
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-600">{{ $yt->crop_year }}</span>
                            <span class="text-sm font-semibold">{{ number_format($yt->total_cane, 2) }} tons</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between pt-3 font-bold">
                        <span>Total</span>
                        <span>{{ number_format($totalCane, 2) }} tons | ₱{{ number_format($totalAmount, 2) }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Profile Info</h3>
                    <div class="space-y-3 text-sm">
                        <div><span class="text-gray-500">Code:</span> <span
                                class="font-medium">{{ $planter->planter_code }}</span></div>
                        <div><span class="text-gray-500">Contact:</span> <span
                                class="font-medium">{{ $planter->contact_number ?: 'N/A' }}</span></div>
                        <div><span class="text-gray-500">Address:</span> <span
                                class="font-medium">{{ $planter->address ?: 'N/A' }}</span></div>
                        <div><span class="text-gray-500">Area:</span> <span
                                class="font-medium">{{ $planter->area_location ?: 'N/A' }}</span></div>
                        <div><span class="text-gray-500">Total Area:</span> <span
                                class="font-medium">{{ $planter->total_area ? number_format($planter->total_area, 2) . ' ha' : 'N/A' }}</span>
                        </div>
                        <div><span class="text-gray-500">Membership:</span> <span
                                class="font-medium">{{ $planter->membership_date ? $planter->membership_date->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
