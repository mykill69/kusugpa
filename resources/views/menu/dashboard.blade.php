<!-- resources/views/menu/dashboard.blade.php -->
@extends('layout.main')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <!-- Box 1: Pending Vouchers -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>150</h3>
                    <p>Pending Vouchers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <a href="#" class="small-box-footer">View More <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Box 2: Approved Vouchers -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>85</h3>
                    <p>Approved Vouchers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="#" class="small-box-footer">View More <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Box 3: Rejected Vouchers -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>12</h3>
                    <p>Rejected Vouchers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <a href="#" class="small-box-footer">View More <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Box 4: Total Users -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>45</h3>
                    <p>KUSUG-PA Members</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">View More <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3 col-6">
            <div class="description-block border-right">
                <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 17%</span>
                <h5 class="description-header">$35,210.43</h5>
                <span class="description-text">TOTAL REVENUE</span>
            </div>
            <!-- /.description-block -->
        </div>
        <!-- /.col -->
        <div class="col-sm-3 col-6">
            <div class="description-block border-right">
                <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i> 0%</span>
                <h5 class="description-header">$10,390.90</h5>
                <span class="description-text">TOTAL COST</span>
            </div>
            <!-- /.description-block -->
        </div>
        <!-- /.col -->
        <div class="col-sm-3 col-6">
            <div class="description-block border-right">
                <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 20%</span>
                <h5 class="description-header">$24,813.53</h5>
                <span class="description-text">TOTAL PROFIT</span>
            </div>
            <!-- /.description-block -->
        </div>
        <!-- /.col -->
        <div class="col-sm-3 col-6">
            <div class="description-block">
                <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i> 18%</span>
                <h5 class="description-header">1200</h5>
                <span class="description-text">GOAL COMPLETIONS</span>
            </div>
            <!-- /.description-block -->
        </div>
    </div>

    <div class="row mt-4">
        <!-- Monthly Crop Production (Single Bar) -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monthly Crop Production</h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Last 5 Years Crop Production (Comparative Bar) -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Last 5 Years Crop Production</h3>
                </div>
                <div class="card-body">
                    <canvas id="yearlyChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>



    <!-- Additional Dashboard Content -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activity</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-user text-primary"></i> User <strong>Juan Dela Cruz</strong> submitted a
                            voucher.</li>
                        <li><i class="fas fa-check text-success"></i> Voucher #102 approved.</li>
                        <li><i class="fas fa-times text-danger"></i> Voucher #97 rejected.</li>
                        <li><i class="fas fa-user-plus text-warning"></i> New member <strong>Maria Clara</strong>
                            registered.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Task Progress -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Task Progress</h3>
                </div>
                <div class="card-body">
                    <div class="progress-group">
                        Voucher Processing
                        <span class="float-right"><b>75</b>/100</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-info" style="width: 75%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        Loans Released
                        <span class="float-right"><b>30</b>/50</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" style="width: 60%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        Reports Generated
                        <span class="float-right"><b>10</b>/20</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning" style="width: 50%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="crop-year-options" class="d-none">
        @foreach ($cropYears as $cy)
            <option value="{{ $cy }}">{{ $cy }}</option>
        @endforeach
    </div>

    <div id="week-no-options" class="d-none">
        @foreach ($weekNos as $wn)
            <option value="{{ $wn }}">{{ $wn }}</option>
        @endforeach
    </div>

    <!-- ChartJS -->
    <script src="template/plugins/chart.js/Chart.min.js"></script>
    <script src="template/plugins/chart.js/Chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Monthly Chart - Single Bar
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: ['January', 'February', 'March', 'April'],
                datasets: [{
                    label: 'Crop Output (tons)',
                    data: [120, 90, 130, 100],
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Yearly Chart - Comparative Bar (Multiple Datasets)
        const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
        new Chart(yearlyCtx, {
            type: 'bar',
            data: {
                labels: ['2021', '2022', '2023', '2024', '2025'],
                datasets: [{
                        label: 'Rice',
                        data: [120, 140, 130, 110, 125],
                        backgroundColor: 'rgba(255, 99, 132, 0.7)'
                    },
                    {
                        label: 'Corn',
                        data: [90, 100, 95, 105, 110],
                        backgroundColor: 'rgba(255, 206, 86, 0.7)'
                    },
                    {
                        label: 'Banana',
                        data: [70, 80, 60, 85, 90],
                        backgroundColor: 'rgba(54, 162, 235, 0.7)'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

@endsection
