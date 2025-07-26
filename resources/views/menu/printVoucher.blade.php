@extends('layout.main')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" action="{{ route('printVoucher') }}" class="form-inline mb-3">
                    <div class="form-group mr-2 pr-2 ">
                        <label for="crop_year" class="mr-2">Crop Year:</label>
                        <select name="crop_year" id="crop_year" class="form-control" required>
                        <option value="">Select Crop Year</option>
                        @foreach ($cropYear as $year)
                            <option value="{{ $year }}" {{ $selectedCropYear == $year ? 'selected' : '' }}>
                                {{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-2">
                    <label for="week_from" class="mr-2">Week From:</label>
                    <select name="week_from" id="week_from" class="form-control" required>
                        <option value="">From</option>
                        @foreach ($weekNos as $week)
                            <option value="{{ $week }}" {{ $weekFrom == $week ? 'selected' : '' }}>
                                {{ $week }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-2">
                    <label for="week_to" class="mr-2">Week To:</label>
                    <select name="week_to" id="week_to" class="form-control" required>
                        <option value="">To</option>
                        @foreach ($weekNos as $week)
                            <option value="{{ $week }}" {{ $weekTo == $week ? 'selected' : '' }}>{{ $week }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

            <!-- PDF Iframe Preview -->
            @if ($selectedCropYear && $weekFrom && $weekTo)
                <div class="card">
                    <div class="card-body">
                        <iframe
                            src="{{ route('voucher.pdf', ['crop_year' => $selectedCropYear, 'week_from' => $weekFrom, 'week_to' => $weekTo]) }}"
                            width="100%" height="600px" frameborder="0"></iframe>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
