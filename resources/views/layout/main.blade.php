<!-- resources/views/layout/main.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KUSUG-PA {{ isset($title) ? '| ' . $title : '' }}</title>
    <!-- Google Font: Source Sans Pro -->
    <!-- Bootstrap JS (include this before closing body tag) -->
    <!-- Date Range Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> --}}
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('template/plugins/toastr/toastr.min.css') }}">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('template/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('template/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('template/dist/css/adminlte.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('template/plugins/summernote/summernote-bs4.min.css') }}">
    <!-- Logo  -->
    <link rel="shortcut icon" type="" href="{{ asset('template/img/kusug_logo2.png') }}">

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <a href="{{ route('logout') }}" class="nav-link text-danger">
                    <i class="nav-icon fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </ul>
        </nav>

        <!-- Main Sidebar -->
        @include('layout.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <section class="content pt-3">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer text-sm text-center">
            <strong>KUSUG-PA Check and Voucher &copy; {{ date('Y') }}</strong>
        </footer>

    </div>


    <!-- ./wrapper -->
    <script src="{{ asset('template/plugins/jquery/jquery.min.js') }}"></script>
    
    <!-- Bootstrap 4 -->

    <!-- AdminLTE App -->
    <script src="{{ asset('template/dist/js/adminlte.min.js') }}"></script>

    <!-- Toastr -->
    <script src="{{ asset('template/plugins/toastr/toastr.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('template/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('template/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('template/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ asset('template/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('template/plugins/summernote/summernote-bs4.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('template/plugins/sweetalert2/sweetalert2.min.js') }}"></script>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cropYearDropdown = document.getElementById('crop-year-options').innerHTML;
            const weekNoDropdown = document.getElementById('week-no-options').innerHTML;
            const userId = "{{ auth()->user()->id }}";

            function postData(url, data) {
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    }).then(res => res.json())
                    .then(response => Swal.fire('Success', response.message, 'success'))
                    .catch(error => Swal.fire('Error', 'Something went wrong!', 'error'));
            }

            // FORM GROUP HELPER (mimics Bootstrap form-group row)
            function swalFormGroup(label, input) {
                return `
                <div class="form-group row mb-2" style="text-align:left; display:flex; align-items:center;">
                    <label class="col-sm-4 col-form-label" style="font-weight:bold;">${label}</label>
                    <div class="col-sm-8">${input}</div>
                </div>
            `;
            }

            // === CROP YEAR ===
            document.getElementById('cropyear-alert').addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Add Crop Year',
                    html: `
                    <div class="container text-start">
                        ${swalFormGroup('Crop Year', `<input type="text" id="new_crop_year" class="form-control" placeholder="e.g. 2024-2025">`)}
                        <input type="hidden" id="user_id" value="${userId}">
                    </div>
                `,
                    confirmButtonText: 'Submit',
                    focusConfirm: false,
                    customClass: {
                        popup: 'swal-wide'
                    },
                    preConfirm: () => ({
                        crop_year: document.getElementById('new_crop_year').value,
                        user_id: document.getElementById('user_id').value
                    })
                }).then(result => {
                    if (result.isConfirmed) {
                        postData("{{ route('updates.addCropYear') }}", result.value);
                    }
                });
            });

            // === WEEK NUMBER ===
            document.getElementById('weeknum-alert').addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Add Week Number',
                    html: `
                    <div class="container text-start">
                        ${swalFormGroup('Crop Year', `<select id="crop_year" class="form-control">${cropYearDropdown}</select>`)}
                        ${swalFormGroup('Week Number', `<input type="text" id="week_no" class="form-control" placeholder="e.g. 1">`)}
                        ${swalFormGroup('Week Start', `<input type="date" id="week_start_date" class="form-control">`)}
                        ${swalFormGroup('Week End', `<input type="date" id="week_end_date" class="form-control">`)}
                        <input type="hidden" id="user_id" value="${userId}">
                    </div>
                `,
                    confirmButtonText: 'Submit',
                    focusConfirm: false,
                    customClass: {
                        popup: 'swal-wide'
                    },
                    preConfirm: () => ({
                        crop_year: document.getElementById('crop_year').value,
                        week_no: document.getElementById('week_no').value,
                        week_start_date: document.getElementById('week_start_date').value,
                        week_end_date: document.getElementById('week_end_date').value,
                        user_id: document.getElementById('user_id').value
                    })
                }).then(result => {
                    if (result.isConfirmed) {
                        postData("{{ route('updates.addWeekNumber') }}", result.value);
                    }
                });
            });

            // === QUEDAN PRICE ===
            document.getElementById('quedan-alert').addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Add Quedan Price',
                    html: `
                    <div class="container text-start">
                        ${swalFormGroup('Quedan Type', `<input type="text" id="quedan_type" class="form-control" placeholder="e.g. A">`)}
                        ${swalFormGroup('Quedan Price', `<input type="number" id="quedan_price" class="form-control" placeholder="e.g. 950">`)}
                        ${swalFormGroup('Crop Year', `<select id="crop_year" class="form-control">${cropYearDropdown}</select>`)}
                        ${swalFormGroup('Week Number', `<select id="week_no" class="form-control">${weekNoDropdown}</select>`)}
                        <input type="hidden" id="user_id" value="${userId}">
                    </div>
                `,
                    confirmButtonText: 'Submit',
                    focusConfirm: false,
                    customClass: {
                        popup: 'swal-wide'
                    },
                    preConfirm: () => ({
                        quedan_type: document.getElementById('quedan_type').value,
                        quedan_price: document.getElementById('quedan_price').value,
                        crop_year: document.getElementById('crop_year').value,
                        week_no: document.getElementById('week_no').value,
                        user_id: document.getElementById('user_id').value
                    })
                }).then(result => {
                    if (result.isConfirmed) {
                        postData("{{ route('updates.addQuedanPrice') }}", result.value);
                    }
                });
            });

            // === MOLASSES PRICE ===
            document.getElementById('molasses-alert').addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Add Molasses Price',
                    html: `
                    <div class="container text-start">
                        ${swalFormGroup('Molasses Price', `<input type="number" id="mol_price" class="form-control" placeholder="e.g. 850">`)}
                        ${swalFormGroup('Crop Year', `<select id="crop_year" class="form-control">${cropYearDropdown}</select>`)}
                        ${swalFormGroup('Week Number', `<select id="week_no" class="form-control">${weekNoDropdown}</select>`)}
                        <input type="hidden" id="user_id" value="${userId}">
                    </div>
                `,
                    confirmButtonText: 'Submit',
                    focusConfirm: false,
                    customClass: {
                        popup: 'swal-wide'
                    },
                    preConfirm: () => ({
                        mol_price: document.getElementById('mol_price').value,
                        crop_year: document.getElementById('crop_year').value,
                        week_no: document.getElementById('week_no').value,
                        user_id: document.getElementById('user_id').value
                    })
                }).then(result => {
                    if (result.isConfirmed) {
                        postData("{{ route('updates.addMolassesPrice') }}", result.value);
                    }
                });
            });
        });
    </script>



    <script>
        function openSummaryUpload() {
            Swal.fire({
                title: 'Upload Summary CSV',
                html: `
                <form id="uploadForm" method="POST" action="{{ route('summary.upload') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <input type="file" name="file" accept=".csv" class="swal2-file" required>
                </form>
            `,
                showCancelButton: true,
                confirmButtonText: 'Upload',
                focusConfirm: false,
                preConfirm: () => {
                    document.getElementById('uploadForm').submit();
                }
            });
        }
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonText: 'OK'
            });
        </script>
    @endif



    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": false,
                "lengthChange": true,
                "autoWidth": true,
                //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#about').select2({
                placeholder: "Select About"
            });
        });
    </script>


</body>

</html>
