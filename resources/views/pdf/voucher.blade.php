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
    <title>Cheque Voucher</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .page {
            page-break-after: always;
            padding: 50px;
        }

        .page:last-child {
            page-break-after: auto;
        }

        h1 {
            text-align: center;
        }

        .info {
            margin-top: 30px;
        }

        p {
            font-size: 16px;
        }
    </style>
</head>

<body>
    @foreach ($summaryData as $record)
        @php
            $computedAmount = $record->net_cane * ($record->qp ?? 0); // fallback to 0 if null
        @endphp

        <div class="page">
            <h1>CHEQUE VOUCHER</h1>
            <div class="info">
                <p>{{ now()->format('d-m-Y  ') }}</p>
                <p>{{ $record->crop_year }}</p>
                <p>{{ $record->week_no }}</p>
                <p>{{ $record->planter_code }}</p>
                <p>{{ $record->planter_name }}</p>
                <p>{{ $record->qp ? number_format($computedAmount, 2) : 'N/A' }}</p>

                <p>{{ convertToWords($computedAmount) }}</p>
            </div>
        </div>
    @endforeach

</body>

</html>
