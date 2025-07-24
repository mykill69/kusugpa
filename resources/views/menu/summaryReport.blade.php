@extends('layout.main')
<style>
    th.sorting {
        cursor: pointer;
        user-select: none;
    }

    th.sorting i {
        margin-left: 5px;
    }
</style>
@section('content')
    <div class="container-fluid">
        <h4 class="mb-3">Summary Report</h4>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Card container -->
        <div class="card">
            <div class="card-header">
                <div class="dt-buttons btn-group flex-wrap">
                    <form id="summary-filter-form" class="form-inline mb-3">
                        @csrf

                        <!-- Crop Year -->
                        <div class="form-group mr-2">
                            <label for="crop_year" class="mr-2">Crop Year:</label>
                            <select name="crop_year" id="crop_year" class="form-control" required>
                                <option value="">Select Crop Year</option>
                                @foreach ($cropYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Week From -->
                        <div class="form-group mr-2">
                            <label for="week_from" class="mr-2">Week From:</label>
                            <select name="week_from" id="week_from" class="form-control">
                                <option value="">Select Week From</option>
                                @foreach ($weekNos as $week)
                                    <option value="{{ $week }}">{{ $week }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Week To -->
                        <div class="form-group mr-2">
                            <label for="week_to" class="mr-2">Week To:</label>
                            <select name="week_to" id="week_to" class="form-control">
                                <option value="">Select Week To</option>
                                @foreach ($weekNos as $week)
                                    <option value="{{ $week }}">{{ $week }}</option>
                                @endforeach
                            </select>
                        </div>


                        {{-- <!-- View PDF Button -->
                        <button type="button" class="btn btn-primary ml-2" id="preview-pdf-btn" data-toggle="modal"
                            data-target="#summaryPdfModal">
                            <i class="fas fa-eye"></i> View PDF
                        </button> --}}

                       <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#summaryPdfModal" onclick="previewPDF()">
    <i class="fas fa-file-pdf"></i> Preview PDF
</button>
<!-- Download PDF Button -->
<a href="#" class="btn btn-danger ml-2" onclick="downloadPDF()">
    <i class="fas fa-download"></i> Download PDF
</a>

                    </form>




                </div>


                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 300px;">
                        <input type="text" id="summarySearch" class="form-control float-right" placeholder="Search...">
                        <div class="input-group-append">
                            <button class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scrollable table body -->
            <div class="card-body table-responsive p-0" style="height: 700px;">
                <table class="table table-head-fixed" id="summaryTable">
                    <thead class="thead-black">
                        <tr>
                            <th class="sorting" data-index="0">#</th>
                            <th class="sorting" data-index="1">Crop Year <i class="fas fa-sort text-muted"></i></th>
                            <th class="sorting" data-index="2">Week No <i class="fas fa-sort text-muted"></i></th>
                            <th class="sorting" data-index="3">Planter Code <i class="fas fa-sort text-muted"></i></th>
                            <th class="sorting" data-index="4">Planter Name <i class="fas fa-sort text-muted"></i></th>
                            <th class="sorting" data-index="5">Net Cane <i class="fas fa-sort text-muted"></i></th>
                            <th class="sorting" data-index="6">Net Amount <i class="fas fa-sort text-muted"></i></th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summaries as $summary)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $summary->crop_year }}</td>
                                <td>{{ $summary->week_no }}</td>
                                <td>{{ $summary->planter_code }}</td>
                                <td>{{ $summary->planter_name }}</td>
                                <td>{{ number_format($summary->net_cane, 3) }}</td>
                                <td>{{ number_format($summary->net_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No summary data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="template/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="template/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>


    <!-- JS for live search (unchanged) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('summarySearch');
            const rows = document.querySelectorAll('#summaryTable tbody tr');
            const headers = document.querySelectorAll('#summaryTable th.sorting');

            // Live search remains unchanged
            searchInput.addEventListener('keyup', function() {
                const term = this.value.toLowerCase();
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });

            // Column sorting logic
            const getCellValue = (tr, idx) => tr.children[idx]?.innerText || tr.children[idx]?.textContent;

            const comparer = (idx, asc) => (a, b) => {
                const valA = getCellValue(a, idx).replace(/[^0-9.-]+/g, "");
                const valB = getCellValue(b, idx).replace(/[^0-9.-]+/g, "");
                const aFloat = parseFloat(valA);
                const bFloat = parseFloat(valB);
                const isNumeric = !isNaN(aFloat) && !isNaN(bFloat);

                return isNumeric ?
                    (asc ? aFloat - bFloat : bFloat - aFloat) :
                    (asc ?
                        getCellValue(a, idx).localeCompare(getCellValue(b, idx)) :
                        getCellValue(b, idx).localeCompare(getCellValue(a, idx)));
            };

            headers.forEach((th, idx) => {
                th.addEventListener('click', function() {
                    const table = th.closest('table');
                    const tbody = table.querySelector('tbody');

                    const isAsc = th.classList.contains('sorting_asc');
                    headers.forEach(h => {
                        h.classList.remove('sorting_asc', 'sorting_desc');
                        const icon = h.querySelector('i');
                        if (icon) icon.className = 'fas fa-sort text-muted';
                    });

                    th.classList.add(isAsc ? 'sorting_desc' : 'sorting_asc');

                    const icon = th.querySelector('i');
                    if (icon) {
                        icon.className = isAsc ? 'fas fa-sort-down text-dark' :
                            'fas fa-sort-up text-dark';
                    }

                    Array.from(tbody.querySelectorAll('tr'))
                        .sort(comparer(idx, !isAsc))
                        .forEach(tr => tbody.appendChild(tr));
                });
            });
        });
    </script>

    <script>
        $(function() {
            const table = $("#summaryTable").DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                ordering: false, // or true if needed
                searching: false, // disable built-in search since you have custom search
                paging: false, // disable paging if your table scrolls
                info: false,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'csv',
                        className: 'd-none' // hidden, trigger manually
                    },
                    {
                        extend: 'excel',
                        className: 'd-none'
                    },
                    {
                        extend: 'print',
                        className: 'd-none'
                    }
                ]
            });

            // Attach click events to your custom buttons
            $('.buttons-csv').on('click', function() {
                table.button('.buttons-csv').trigger();
            });

            $('.buttons-excel').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

            $('.buttons-print').on('click', function() {
                table.button('.buttons-print').trigger();
            });
        });
    </script>

   

    <script>
function previewPDF() {
    const cropYear = document.getElementById('crop_year').value;
    const weekFrom = document.getElementById('week_from').value;
    const weekTo = document.getElementById('week_to').value;

    if (!cropYear) {
        alert("Please select a Crop Year.");
        return;
    }

    const url = `{{ url('/summary/pdf-preview') }}?crop_year=${cropYear}&week_from=${weekFrom}&week_to=${weekTo}`;
    document.getElementById('pdfFrame').src = url;
}


function downloadPDF() {
    const cropYear = document.getElementById('crop_year').value;
    const weekFrom = document.getElementById('week_from').value;
    const weekTo = document.getElementById('week_to').value;

    if (!cropYear) {
        alert("Please select a Crop Year.");
        return;
    }

    const downloadUrl = `{{ url('/summary/download-pdf') }}?crop_year=${cropYear}&week_from=${weekFrom}&week_to=${weekTo}`;
    window.open(downloadUrl, '_blank');
}
</script>



    {{-- @include('modal.viewSummaryPdf') --}}

<!-- PDF Modal -->
<div class="modal fade" id="summaryPdfModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document" width="90%">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Summary Report PDF Preview</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body" style="height: 90vh;">
        <iframe id="pdfFrame" src="" frameborder="0" style="width:100%; height:100%;"></iframe>
      </div>
    </div>
  </div>
</div>


@endsection
