<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Pegawai</title>
    <link rel="stylesheet" href="{{ asset('css/pegawai/registerpg.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<body>

    <div class="container">
        <div class="logo-box">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Perusahaan" class="logo-img">
        </div>

        <div class="form-box">
  <h1>REGISTER</h1>
  <div class="line"></div>

  <div class="input-row">
    <label class="label-text" for="full_name">Nama Lengkap</label>
    <input type="text" id="full_name" name="full_name" class="input-style" required>
  </div>

  <div class="input-row">
    <label class="label-text" for="shift">Shift</label>
    <select id="shift" name="shift" class="input-style" required>
      <option value="">Pilih Shift Anda</option>
      <option value="pagi">Pagi</option>
      <option value="middle">Middle</option>
      <option value="malam">Malam</option>
    </select>
  </div>

  <div class="input-row">
    <label class="label-text" for="status">Status</label>
    <select id="status" name="status" class="input-style" required>
    <option value="">Pilih Status Anda</option>
      <option value="active">Active</option>
      <option value="inactive">Inactive</option>
    </select>
  </div>

  <div class="input-row">
    <label class="label-text" for="password">Password</label>
    <div class="password-container">
      <input type="password" id="password" name="password" class="input-style" required>
      <i id="toggleEyePassword" class="fa-solid fa-eye-slash toggle-password"
         onclick="togglePassword('password', 'toggleEyePassword')"></i>
    </div>
  </div>

  <div class="input-row">
    <label class="label-text" for="password_confirmation">Confirm Password</label>
    <div class="password-container">
      <input type="password" id="password_confirmation" name="password_confirmation" class="input-style" required>
      <i id="toggleEyeConfirm" class="fa-solid fa-eye-slash toggle-password"
         onclick="togglePassword('password_confirmation', 'toggleEyeConfirm')"></i>
    </div>
  </div>

  <button type="submit" class="register-btn">REGISTER</button>
</div>



                <!-- <label for="full_name">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" required>
                @error('full_name') <p class="error">{{ $message }}</p> @enderror -->
                <!-- <div class="input-container">
                    <label class="label-text" for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama">
                </div> -->

                <!-- <label for="shift">Shift</label>
                    <select id="shift" name="shift" class="input-style" required>
                    <option value="">Pilih Shift Anda</option>
                    <option value="pagi">Pagi</option>
                    <option value="middle">Middle</option>
                    <option value="malam">Malam</option>
                    </select> -->
                
                <!-- <div class="input-container">
                    <label class="label-text" for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama">
                </div> -->

            <!-- <div class="input-container">
                <label for="jabatan">Jabatan</label>
                <select id="jabatan" name="jabatan" class="input-style" required>
                    <option value="pegawai">Pegawai</option>
                    </select>
            </div> -->

            <!-- <div class="input-container">
                <label for="shift">Shift</label>
                <select id="shift" name="shift" class="input-style" required>
                    <option value="pagi">Pagi</option>
                    <option value="middle">Midle</option>
                    <option value="malam">Malam</option>
                </select>
            </div> -->

            <!-- <div class="input-container">
                <label for="status">Status</label>
                <select id="status" name="status" class="input-style" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>


                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required>
                    <i id="toggleEyePassword" class="fa-solid fa-eye-slash" onclick="togglePassword('password', 'toggleEyePassword')"></i>
                </div>
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror -->

                <!-- <div class="input-container">
                    <label class="label-text" for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password">
                        <i id="toggleEye" class="fa-solid fa-eye-slash toggle-eye" onclick="togglePassword()"></i>
                    </div>
                </div> -->

                <!-- <label for="password_confirmation">Confirm Password</label>
                <div class="password-container">
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                    <i id="toggleEyeConfirm" class="fa-solid fa-eye-slash" onclick="togglePassword('password_confirmation', 'toggleEyeConfirm')"></i>
                </div> -->

                <!-- <div class="input-container">
                    <label class="label-text" for="confirm_password">Confirm Password</label>
                    <div class="confirm_password-container">
                        <input type="password" id="confirm_password" name="confirm_password">
                        <i id="toggleConfirmEye" class="fa-solid fa-eye-slash toggle-eye" onclick="toggleConfirmPassword()"></i>
                    </div>
                </div> -->

            
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("toggleEye");

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

        function toggleConfirmPassword() {
            const input = document.getElementById("confirm_password");
            const icon = document.getElementById("toggleConfirmEye");

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
