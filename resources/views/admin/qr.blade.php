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
        <div class="image-container" >
            @if(request('shift'))
                    {!! QrCode::size(500)->generate(url()->current() . '?shift=' . request('shift')) !!}
            @else
                <p style="text-align:center;">Silakan pilih shift terlebih dahulu</p>
            @endif
        </div>

        @if(request('shift'))
        <p id="time-display" style="margin-top: 15px; font-weight: bold; text-align: center;"></p>
        @endif
    </div>



    <script>
        function selectShift(shift) {
            window.location.href = `?shift=${shift}`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const shift = "{{ request('shift') }}";
            if (shift) {
                // Countdown refresh
                let timeLeft = 5;
                setInterval(() => {
                    if (timeLeft > 0) {
                        document.getElementById("time-display").textContent =
                            "Refresh in " + timeLeft + " second...";
                        timeLeft--;
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            }
        });
    </script>
</body>
</html>
