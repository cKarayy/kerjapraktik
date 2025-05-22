<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/admin/qr.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <div class="keterangan-container">
        <span>SHIFT :</span>
        <label class="custom-radio">
            <input type="radio" name="shift" value="pagi" {{ request('shift') == 'pagi' ? 'checked' : '' }} onclick="selectShift('pagi')">
            <span class="checkmark"></span> PAGI
        </label>
        <label class="custom-radio">
            <input type="radio" name="shift" value="middle" {{ request('shift') == 'middle' ? 'checked' : '' }} onclick="selectShift('middle')">
            <span class="checkmark"></span> MIDDLE
        </label>
        <label class="custom-radio">
            <input type="radio" name="shift" value="malam" {{ request('shift') == 'malam' ? 'checked' : '' }} onclick="selectShift('malam')">
            <span class="checkmark"></span> MALAM
        </label>
    </div>

    <div class="card-content">
        <div class="image-container">
            @if($shift && $qrCode)
                {!! $qrCode !!}
            @else
                <p style="text-align:center;">Silakan pilih shift terlebih dahulu</p>
            @endif
        </div>
    </div>

    <script>
        function selectShift(shift) {
            window.location.href = `?shift=${shift}`;
        }
    </script>
</body>
</html>
