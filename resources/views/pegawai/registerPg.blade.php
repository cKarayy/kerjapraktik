<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Pegawai</title>
    <link rel="stylesheet" href="{{ asset('css/pegawai/registerpg.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="container">
    <div class="logo-box">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Perusahaan" class="logo-img">
    </div>

    <form method="POST" action="{{ route('karyawans.store') }}">
        @csrf
        <div class="form-box">
            <h1>REGISTER</h1>
            <div class="line"></div>

            <div class="input-row">
                <label class="label-text" for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="input-style" required>
            </div>

            <div class="input-row">
                <label class="label-text" for="username">Username</label>
                <input type="text" id="username" name="username" class="input-style" required>
            </div>


            <div class="input-row">
                <label class="label-text" for="jabatan">Jabatan</label>
                <input type="text" id="jabatan" name="jabatan" class="input-style" required>
            </div>

            <div class="input-row">
                <label class="label-text" for="shift">Shift</label>
                <select id="shift" name="shift" class="input-style" required>
                    <option value="">Pilih Shift Anda</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->nama_shift }}">{{ $shift->nama_shift }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-row">
                <label class="label-text" for="role">Role</label>
                <select id="role" name="role" class="input-style" required>
                    <option value="">Pilih Role Anda</option>
                    <option value="pegawai">Pegawai</option>
                    <option value="admin">Admin</option>
                    <option value="penyelia">Penyelia</option>
                </select>
            </div>

            <div class="input-row">
                <label class="label-text" for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" class="input-style" required>
                    <i id="toggleEyePassword" class="fa-solid fa-eye-slash toggle-password"
                       onclick="togglePassword('password', 'toggleEyePassword')"></i>
                </div>
                @error('password')
                    @if (!str_contains($message, 'confirmation'))
                        <div class="error-message" style="color:red; font-size: 14px;">{{ $message }}</div>
                    @endif
                @enderror
            </div>

            <div class="input-row">
                <label class="label-text" for="password_confirmation">Confirm Password</label>
                <div class="password-container">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="input-style" required>
                    <i id="toggleEyeConfirm" class="fa-solid fa-eye-slash toggle-password"
                       onclick="togglePassword('password_confirmation', 'toggleEyeConfirm')"></i>
                </div>
                @error('password_confirmation')
                    <div class="error-message" style="color:red; font-size: 14px;">{{ $message }}</div>
                @else
                    @error('password')
                        @if (str_contains($message, 'confirmation'))
                            <div class="error-message" style="color:red; font-size: 14px;">{{ $message }}</div>
                        @endif
                    @enderror
                @enderror
            </div>

            <button type="submit" class="register-btn">REGISTER</button>
        </div>
    </form>

</div>

<script>
    function togglePassword(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(eyeId);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    }
</script>

</body>
</html>
