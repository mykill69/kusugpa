// public/js/dashboard.js
function dashboardData() {
    return {
        yearlyChart: null,
        weeklyChart: null,
        productionChart: null,
        distributionChart: null, // NEW
        stats: {
            totalNetCane: 0, totalNetAmount: 0, activePlanters: 0, totalPlanters: 0,
            currentCropYear: '', currentWeek: 0, quedanPrice: 0, molassesPrice: 0,
            activeLoans: 0, quedanType: 'N/A', caneChange: 0, amountChange: 0,
            totalDeductions: 0, averageYield: 0, bestWeek: 0, bestWeekCane: 0,
            collectionRate: 0, riskPlanters: 0, pendingApprovals: 0
        },
        yearlyData: { labels: [], datasets: [{ data: [] }] },
        weeklyData: { labels: [], datasets: [{ data: [] }] },
        monthlyData: { labels: [], datasets: [{ data: [] }] },
        distributionData: { labels: [], datasets: [{ data: [], backgroundColor: [] }] }, // NEW
        activities: [],
        topPlanters: [],
        recentPrices: [],
        alerts: [],
        recommendations: [],
        riskPlanters: [],
        loanStats: {},
        loanChart: null,

        init() {
            this.loadDashboardData();
            window.addEventListener('filter-changed', (event) => {
                this.loadDashboardData(event.detail.cropYear, event.detail.week);
            });
            window.addEventListener('refresh-dashboard', () => {
                this.loadDashboardData();
            });
        },

        async loadDashboardData(cropYear = null, week = null) {
            try {
                let url = '/dashboard/data';
                let params = new URLSearchParams();
                if (cropYear) params.append('crop_year', cropYear);
                if (week) params.append('week_no', week);
                if (params.toString()) url += '?' + params.toString();

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                if (!response.ok) throw new Error('Failed to load');
                const data = await response.json();

                this.stats = data.stats;
                this.yearlyData = data.yearlyData;
                this.weeklyData = data.weeklyData;
                this.monthlyData = data.monthlyData || { labels: [], datasets: [{ data: [] }] };
                this.distributionData = data.distributionData || { labels: [], datasets: [{ data: [], backgroundColor: [] }] };
                this.activities = data.activities;
                this.topPlanters = data.topPlanters;
                this.recentPrices = data.recentPrices;
                this.alerts = data.alerts || [];
                this.recommendations = data.recommendations || [];
                this.riskPlanters = data.riskPlanters || [];
                this.loanStats = data.loanStats || {};

                setTimeout(() => { this.createCharts(); }, 300);
            } catch (error) { console.error('Error:', error); }
        },

        get sortedRecentPrices() {
            return [...this.recentPrices].sort((a, b) => {
                const idA = parseInt(String(a.id).replace(/\D/g, ''));
                const idB = parseInt(String(b.id).replace(/\D/g, ''));
                return idB - idA;
            });
        },

        createCharts() {
            const yCanvas = document.getElementById('yearlyChart');
            const wCanvas = document.getElementById('weeklyChart');
            const mCanvas = document.getElementById('monthlyChart');
            const dCanvas = document.getElementById('distributionChart');

            if (this.yearlyChart) { this.yearlyChart.destroy(); this.yearlyChart = null; }
            if (this.weeklyChart) { this.weeklyChart.destroy(); this.weeklyChart = null; }
            if (this.productionChart) { this.productionChart.destroy(); this.productionChart = null; }
            if (this.distributionChart) { this.distributionChart.destroy(); this.distributionChart = null; }

            // Yearly Chart
            if (yCanvas) {
                const ctx1 = yCanvas.getContext('2d');
                const gradient1 = ctx1.createLinearGradient(0, 0, 0, 320);
                gradient1.addColorStop(0, 'rgba(34, 197, 94, 0.3)');
                gradient1.addColorStop(1, 'rgba(34, 197, 94, 0.02)');
                this.yearlyChart = new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: this.yearlyData.labels,
                        datasets: [{
                            label: 'Net Cane (tons)',
                            data: this.yearlyData.datasets[0].data,
                            backgroundColor: gradient1,
                            borderColor: '#22c55e',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
                    }
                });
            }

            // Weekly Chart
            if (wCanvas) {
                const ctx2 = wCanvas.getContext('2d');
                const gradient2 = ctx2.createLinearGradient(0, 0, 0, 320);
                gradient2.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                gradient2.addColorStop(1, 'rgba(59, 130, 246, 0)');
                this.weeklyChart = new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: this.weeklyData.labels,
                        datasets: [{
                            label: 'Net Cane (tons)',
                            data: this.weeklyData.datasets[0].data,
                            borderColor: '#3b82f6',
                            backgroundColor: gradient2,
                            borderWidth: 2.5, tension: 0.4, fill: true,
                            pointRadius: 5, pointBackgroundColor: '#fff',
                            pointBorderColor: '#3b82f6', pointBorderWidth: 2.5, pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
                    }
                });
            }
           // In createCharts() - Fix loan chart
const lCanvas = document.getElementById('loanChart');
if (this.loanChart) { this.loanChart.destroy(); this.loanChart = null; }

if (lCanvas) {
    const ctx5 = lCanvas.getContext('2d');
    const loanData = this.loanStats || {};
    const totalPrincipal = parseFloat(loanData.total_principal) || 0;
    const totalBalance = parseFloat(loanData.total_balance) || 0;
    const collected = Math.max(0, totalPrincipal - totalBalance); // Prevent negative
    const remainingBalance = totalPrincipal - collected;

    // If no loans, show empty state
    if (totalPrincipal === 0) {
        this.loanChart = new Chart(ctx5, {
            type: 'doughnut',
            data: {
                labels: ['No active loans'],
                datasets: [{ data: [1], backgroundColor: ['#e5e7eb'], borderWidth: 0 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });
        
        // Draw "No Data" text
        const originalDraw = this.loanChart.draw;
        this.loanChart.draw = function() {
            originalDraw.apply(this, arguments);
            if (this.ctx) {
                const width = this.width, height = this.height, ctx = this.ctx;
                ctx.restore();
                ctx.font = "1em sans-serif";
                ctx.textBaseline = "middle";
                ctx.fillStyle = '#9ca3af';
                const text = 'No Loans';
                ctx.fillText(text, Math.round((width - ctx.measureText(text).width) / 2), height / 2);
                ctx.save();
            }
        };
        return;
    }

    this.loanChart = new Chart(ctx5, {
        type: 'doughnut',
        data: {
            labels: ['Remaining Balance', 'Collected'],
            datasets: [{
                data: [remainingBalance, collected],
                backgroundColor: [
                    'rgba(239, 68, 68, 0.7)',
                    'rgba(34, 197, 94, 0.7)'
                ],
                borderColor: ['#ef4444', '#22c55e'],
                borderWidth: 2,
                hoverBorderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 11 },
                        usePointStyle: true,
                        pointStyleWidth: 8,
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                            return context.label + ': ₱' + Number(context.raw).toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Add center text
    const originalDraw = this.loanChart.draw;
    this.loanChart.draw = function() {
        originalDraw.apply(this, arguments);
        if (this.ctx) {
            const width = this.width, height = this.height, ctx = this.ctx;
            ctx.restore();
            const fontSize = (height / 180).toFixed(2);
            ctx.font = fontSize + "em sans-serif";
            ctx.textBaseline = "middle";
            
            const rate = totalPrincipal > 0 ? ((collected / totalPrincipal) * 100).toFixed(0) : 0;
            ctx.fillStyle = rate >= 80 ? '#22c55e' : rate >= 50 ? '#f59e0b' : '#ef4444';
            const text = rate + '%';
            ctx.fillText(text, Math.round((width - ctx.measureText(text).width) / 2), height / 2 - 10);
            
            ctx.font = (fontSize * 0.5) + "em sans-serif";
            ctx.fillStyle = '#6b7280';
            const subText = 'Collected';
            ctx.fillText(subText, Math.round((width - ctx.measureText(subText).width) / 2), height / 2 + 20);
            ctx.save();
        }
    };
}
            // Monthly Chart
            if (mCanvas) {
                const ctx3 = mCanvas.getContext('2d');
                this.productionChart = new Chart(ctx3, {
                    type: 'bar',
                    data: {
                        labels: this.monthlyData.labels,
                        datasets: [{
                            label: 'Monthly Avg (tons)',
                            data: this.monthlyData.datasets[0].data,
                            backgroundColor: 'rgba(168, 85, 247, 0.4)',
                            borderColor: '#a855f7',
                            borderWidth: 2,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
                    }
                });
            }

            // Distribution Pie Chart
            if (dCanvas) {
                const ctx4 = dCanvas.getContext('2d');
                this.distributionChart = new Chart(ctx4, {
                    type: 'doughnut',
                    data: {
                        labels: this.distributionData.labels,
                        datasets: [{
                            data: this.distributionData.datasets[0].data,
                            backgroundColor: this.distributionData.datasets[0].backgroundColor || [
                                '#22c55e', '#3b82f6', '#a855f7', '#f59e0b', '#ef4444',
                                '#06b6d4', '#ec4899', '#8b5cf6', '#14b8a6', '#f97316'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff',
                            hoverBorderWidth: 3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '55%',
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    padding: 12,
                                    font: { size: 10 },
                                    usePointStyle: true,
                                    pointStyleWidth: 8,
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.raw / total) * 100).toFixed(1);
                                        return context.label + ': ' + context.raw.toFixed(1) + ' tons (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        },

        getAlertClass(type) {
            const classes = {
                'warning': 'bg-amber-50 border-amber-200 text-amber-800',
                'danger': 'bg-red-50 border-red-200 text-red-800',
                'info': 'bg-blue-50 border-blue-200 text-blue-800',
                'success': 'bg-green-50 border-green-200 text-green-800',
            };
            return classes[type] || 'bg-gray-50 border-gray-200 text-gray-800';
        },

        getAlertIcon(type) {
            const icons = {
                'warning': 'fas fa-exclamation-triangle text-amber-500',
                'danger': 'fas fa-times-circle text-red-500',
                'info': 'fas fa-info-circle text-blue-500',
                'success': 'fas fa-check-circle text-green-500',
            };
            return icons[type] || 'fas fa-bell text-gray-500';
        },

        formatNumber(number, decimals = 2) {
            if (number === null || number === undefined) return '0';
            return parseFloat(number).toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        }
    };
}