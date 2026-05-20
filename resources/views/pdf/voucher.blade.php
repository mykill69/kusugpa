@php
    function convertToWords($amount)
    {
        // Handle negative values
        $isNegative = $amount < 0;
        $amount = abs($amount);

        $pesos = floor($amount);
        $centavos = round(($amount - $pesos) * 100);

        $words = [
            '',
            'One',
            'Two',
            'Three',
            'Four',
            'Five',
            'Six',
            'Seven',
            'Eight',
            'Nine',
            'Ten',
            'Eleven',
            'Twelve',
            'Thirteen',
            'Fourteen',
            'Fifteen',
            'Sixteen',
            'Seventeen',
            'Eighteen',
            'Nineteen',
            'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety',
        ];

        $convert = function ($n) use (&$words, &$convert) {
            if ($n < 21) {
                return $words[$n];
            }
            if ($n < 100) {
                return $words[intval($n / 10) * 10] . ($n % 10 ? '-' . $words[$n % 10] : '');
            }
            if ($n < 1000) {
                return $words[intval($n / 100)] . ' Hundred' . ($n % 100 ? ' ' . $convert($n % 100) : '');
            }
            if ($n < 1000000) {
                return $convert(intval($n / 1000)) . ' Thousand' . ($n % 1000 ? ' ' . $convert($n % 1000) : '');
            }
            if ($n < 1000000000) {
                return $convert(intval($n / 1000000)) . ' Million' . ($n % 1000000 ? ' ' . $convert($n % 1000000) : '');
            }
            return '';
        };

        $pesoWords = $convert($pesos);
        $centavos = str_pad($centavos, 2, '0', STR_PAD_LEFT);

        $finalText = ($isNegative ? 'Negative ' : '') . "{$pesoWords} Pesos and {$centavos}/100";

        return ucfirst($finalText);
    }
@endphp

<!DOCTYPE html>
<html>

<head>
    <title>Check Voucher</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .page {
            page-break-after: always;
            padding: 40px 50px;
            font-size: 12px;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .header {
            margin-bottom: 20px;
        }

        .header p {
            margin: 2px 0;
            font-size: 13px;
        }

        .company-name {
            font-weight: bold;
            font-size: 14px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 3px 0;
            font-size: 12px;
        }

        .voucher-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .voucher-table th,
        .voucher-table td {
            padding: 6px 8px;
            font-size: 11px;
            text-align: left;
            vertical-align: top;
        }

        .voucher-table th {
            font-weight: bold;
            font-size: 10px;
        }

        .totals-section {
            margin-top: 30px;
            width: 100%;
        }

        .totals-section table {
            width: 60%;
            float: right;
        }

        .totals-section td {
            padding: 4px 8px;
            font-size: 12px;
        }

        .amount-words {
            margin-top: 30px;
            padding: 10px 0;
            font-size: 12px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 30%;
        }

        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 11px;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    @foreach ($summaryData as $record)
        @php
            // Quedan Type A calculations
            $quedan_a_lkg = $record['quedan_a_lkg'] ?? 0;
            $quedan_a_price = $record['quedan_a_price'] ?? 0;
            $quedan_a_gross = $quedan_a_lkg * $quedan_a_price;

            // Quedan Type B calculations
            $quedan_b_lkg = $record['quedan_b_lkg'] ?? 0;
            $quedan_b_price = $record['quedan_b_price'] ?? 0;
            $quedan_b_gross = $quedan_b_lkg * $quedan_b_price;
            $quedan_b_liens = $record['quedan_b_liens'] ?? 0;
            $quedan_b_service = $record['quedan_b_service_charge'] ?? 0;
            $quedan_b_insurance = $record['quedan_b_insurance'] ?? 0;
            $quedan_b_tax = $record['quedan_b_tax'] ?? 0;
            $quedan_b_total_deductions = $quedan_b_service + $quedan_b_insurance + $quedan_b_tax;
            $quedan_b_net = $quedan_b_gross + $quedan_b_liens - $quedan_b_total_deductions;

            // Quedan Type D calculations
            $quedan_d_lkg = $record['quedan_d_lkg'] ?? 0;
            $quedan_d_price = $record['quedan_d_price'] ?? 0;
            $quedan_d_gross = $quedan_d_lkg * $quedan_d_price;
            $quedan_d_liens = $record['quedan_d_liens'] ?? 0;
            $quedan_d_service = $record['quedan_d_service_charge'] ?? 0;
            $quedan_d_insurance = $record['quedan_d_insurance'] ?? 0;
            $quedan_d_tax = $record['quedan_d_tax'] ?? 0;
            $quedan_d_total_deductions = $quedan_d_liens + $quedan_d_service + $quedan_d_insurance + $quedan_d_tax;
            $quedan_d_net = $quedan_d_gross - $quedan_d_total_deductions;

            // Quedan totals
            $quedanGrossProceeds = $quedan_a_gross + $quedan_b_gross + $quedan_d_gross;
            $quedanTotalLiens = $quedan_b_liens + $quedan_d_liens;
            $quedanTotalServiceCharge = $quedan_b_service + $quedan_d_service;
            $quedanTotalInsurance = $quedan_b_insurance + $quedan_d_insurance;
            $quedanTotalTax = $quedan_b_tax + $quedan_d_tax;
            $quedanTotalDeductions = $quedanTotalServiceCharge + $quedanTotalInsurance + $quedanTotalTax;
            $quedanNetProceeds = $quedanGrossProceeds + $quedanTotalLiens - $quedanTotalDeductions;

            // Molasses calculations
            $mol_net = $record['mol_net'] ?? 0;
            $mol_price = $record['mol_price'] ?? 0;
            $molasses_gross = $mol_net * $mol_price;
            $molasses_liens = $record['molasses_liens'] ?? 0;
            $molasses_service = $record['molasses_service_charge'] ?? 0;
            $molasses_insurance = $record['molasses_insurance'] ?? 0;
            $molasses_tax = $record['molasses_tax'] ?? 0;
            $molasses_total_deductions = $molasses_liens + $molasses_service + $molasses_insurance + $molasses_tax;
            $molasses_net = $molasses_gross - $molasses_total_deductions;

            // Consolidated Upload total
            $consolidated_ta_wt = $record['consolidated_ta_wt'] ?? 0;
            $consolidated_total = $record['consolidated_total'] ?? 0;

            // Grand totals (including consolidated)
            $totalGrossProceeds = $quedanGrossProceeds + $molasses_gross + $consolidated_total;
            $totalDeductions = $quedanTotalDeductions + $molasses_total_deductions;
            $totalNetProceeds = $quedanNetProceeds + $molasses_net + $consolidated_total - $totalDeductions;
        @endphp

        <div class="page">
            <!-- Header -->
            <div class="header">
                <p class="company-name">Kabankalan United Sugar Planter Association</p>
                <p>Repullo St., Kabankalan City</p>
            </div>

            <!-- Voucher Info -->
            <table class="info-table">
                <tr>
                    <td class="text-center" width="70%">
                        {{ $record['planter_name'] }}
                    </td>
                    <td width="30%" class="text-right">
                        <strong>Date:</strong> {{ now()->format('F d, Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="text-center">
                        {{ $record['week_no'] }}
                    </td>
                    <td class="text-right">

                    </td>
                </tr>
            </table>

            <p style="margin: 15px 0; font-size: 13px;">
                <strong>QUEDAN AND MOLASSES LIQUIDATION WEEK ENDING:
                    {{ $record['week_end_date'] ? date('m-d-Y', strtotime($record['week_end_date'])) : '' }}</strong>
            </p>

            <!-- Main Table -->
            <table class="voucher-table">
                <thead>
                    <tr>
                        <th>DOC TYPE.</th>
                        <th>QTY IN LKG/MT</th>
                        <th>PRICE</th>
                        <th>GROSS PROCEEDS</th>
                        <th>QUEDAN LIENS</th>
                        <th>SERVICE CHARGE</th>
                        <th>INSURANCE HANDLING</th>
                        <th>W/HOLDING TAX</th>
                        <th>TOTAL NET DEDUCTION</th>
                        <th>TOTAL NET PROCEEDS</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row A - Quedan Type A -->
                    <tr>
                        <td><strong>A</strong></td>
                        <td>{{ $quedan_a_lkg ? number_format($quedan_a_lkg, 3) : '' }}</td>
                        <td>{{ $quedan_a_price ? number_format($quedan_a_price, 2) : '' }}</td>
                        <td>{{ $quedan_a_gross ? number_format($quedan_a_gross, 2) : '' }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $quedan_a_gross ? number_format($quedan_a_gross, 2) : '' }}</td>
                    </tr>

                    <!-- Row B - Quedan Type B -->
                    <tr>
                        <td><strong>B</strong></td>
                        <td>{{ $quedan_b_lkg ? number_format($quedan_b_lkg, 3) : '' }}</td>
                        <td>{{ $quedan_b_price ? number_format($quedan_b_price, 2) : '' }}</td>
                        <td>{{ $quedan_b_gross ? number_format($quedan_b_gross, 2) : '' }}</td>
                        <td>{{ $quedan_b_liens ? number_format($quedan_b_liens, 2) : '' }}</td>
                        <td>{{ $quedan_b_service ? number_format($quedan_b_service, 2) : '' }}</td>
                        <td>{{ $quedan_b_insurance ? number_format($quedan_b_insurance, 2) : '' }}</td>
                        <td>{{ $quedan_b_tax ? number_format($quedan_b_tax, 2) : '' }}</td>
                        <td>{{ $quedan_b_total_deductions ? number_format($quedan_b_total_deductions, 2) : '' }}</td>
                        <td>{{ $quedan_b_net ? number_format($quedan_b_net, 2) : '' }}</td>
                    </tr>

                    <!-- Row D - Quedan Type D -->
                    <tr>
                        <td><strong>D</strong></td>
                        <td>{{ $quedan_d_lkg ? number_format($quedan_d_lkg, 3) : '' }}</td>
                        <td>{{ $quedan_d_price ? number_format($quedan_d_price, 2) : '' }}</td>
                        <td>{{ $quedan_d_gross ? number_format($quedan_d_gross, 2) : '' }}</td>
                        <td>{{ $quedan_d_liens ? number_format($quedan_d_liens, 2) : '' }}</td>
                        <td>{{ $quedan_d_service ? number_format($quedan_d_service, 2) : '' }}</td>
                        <td>{{ $quedan_d_insurance ? number_format($quedan_d_insurance, 2) : '' }}</td>
                        <td>{{ $quedan_d_tax ? number_format($quedan_d_tax, 2) : '' }}</td>
                        <td>{{ $quedan_d_total_deductions ? number_format($quedan_d_total_deductions, 2) : '' }}</td>
                        <td>{{ $quedan_d_net ? number_format($quedan_d_net, 2) : '' }}</td>
                    </tr>

                    <!-- Row M - Molasses -->
                    <tr>
                        <td><strong>M</strong></td>
                        <td>{{ $mol_net ? number_format($mol_net, 3) : '' }}</td>
                        <td>{{ $mol_price ? number_format($mol_price, 2) : '' }}</td>
                        <td>{{ $molasses_gross ? number_format($molasses_gross, 2) : '' }}</td>
                        <td>{{ $molasses_liens ? number_format($molasses_liens, 2) : '' }}</td>
                        <td>{{ $molasses_service ? number_format($molasses_service, 2) : '' }}</td>
                        <td>{{ $molasses_insurance ? number_format($molasses_insurance, 2) : '' }}</td>
                        <td>{{ $molasses_tax ? number_format($molasses_tax, 2) : '' }}</td>
                        <td>{{ $molasses_total_deductions ? number_format($molasses_total_deductions, 2) : '' }}</td>
                        <td>{{ $molasses_net ? number_format($molasses_net, 2) : '' }}</td>
                    </tr>

                    <!-- Row T - Consolidated Upload -->
                    <tr>
                        <td><strong>T</strong></td>
                        <td>{{ $consolidated_ta_wt ? number_format($consolidated_ta_wt, 3) : '' }}</td>
                        <td></td>
                        <td>{{ $consolidated_total ? number_format($consolidated_total, 2) : '' }}</td>
                        <td colspan="5"></td>
                        <td>{{ $consolidated_total ? number_format($consolidated_total, 2) : '' }}</td>
                    </tr>

                    <!-- Grand Total -->
                    <tr style="border-top: 2px solid #000; border-bottom: 2px solid #000;">
                        <td></td>
                        <td><strong>TOTAL >>>>>>></strong></td>
                        <td></td>
                        <td class="font-bold">{{ $totalGrossProceeds ? number_format($totalGrossProceeds, 2) : '' }}
                        </td>
                        <td class="font-bold">
                            {{ $quedanTotalLiens + $molasses_liens ? number_format($quedanTotalLiens + $molasses_liens, 2) : '' }}
                        </td>
                        <td class="font-bold">
                            {{ $quedanTotalServiceCharge + $molasses_service ? number_format($quedanTotalServiceCharge + $molasses_service, 2) : '' }}
                        </td>
                        <td class="font-bold">
                            {{ $quedanTotalInsurance + $molasses_insurance ? number_format($quedanTotalInsurance + $molasses_insurance, 2) : '' }}
                        </td>
                        <td class="font-bold">
                            {{ $quedanTotalTax + $molasses_tax ? number_format($quedanTotalTax + $molasses_tax, 2) : '' }}
                        </td>
                        <td class="font-bold">{{ $totalDeductions ? number_format($totalDeductions, 2) : '' }}</td>
                        <td class="font-bold">{{ $totalNetProceeds ? number_format($totalNetProceeds, 2) : '' }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Amount in Words -->
            <div class="amount-words">
                <strong>Amount in Words:</strong> {{ convertToWords($totalNetProceeds) }}
            </div>

            <!-- Totals Section -->
            <div class="totals-section clearfix">
                <table style="float: right; width: 50%; margin-top: 20px;">
                    <tr>
                        <td><strong>Gross Proceeds:</strong></td>
                        <td class="text-right">{{ number_format($totalGrossProceeds, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Less: Total Deductions:</strong></td>
                        <td class="text-right">{{ number_format($totalDeductions, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Net Amount Due:</strong></td>
                        <td class="text-right font-bold" style="font-size: 14px;">
                            {{ number_format($totalNetProceeds, 2) }}</td>
                    </tr>
                </table>
            </div>

            <div style="clear: both;"></div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">Prepared by</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Checked by</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Approved by</div>
                </div>
            </div>
        </div>
    @endforeach
</body>

</html>
