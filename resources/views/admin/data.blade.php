<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kepegawaian</title>
    <link rel="stylesheet" href="{{ asset('css/data.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <div class="header">
        <h2>DATA PEGAWAI</h2>
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>
    <div class="divider"></div>

    <!-- Opsi Radio Button -->
    <div id="adminOptions" style="display: none;"class="action-selection">
        <label class="custom-radio">
            <input type="radio" id="add" name="action" value="add">
            <span class="checkmark"></span> ADD
        </label>
        <label class="custom-radio">
            <input type="radio" id="edit" name="action" value="edit">
            <span class="checkmark"></span> EDIT
        </label>
        <label class="custom-radio">
            <input type="radio" id="delete" name="action" value="delete">
            <span class="checkmark"></span> DELETE
        </label>
        <button class="btn-container" onclick="applyAction()">DONE</button>
    </div>


    <div class="container">
        <div class="employee-list" id="employeeList">
            @foreach($employees as $employee)
                <div class="employee-card {{ $employee['status'] === 'resign' ? 'resign' : 'active' }}">
                    <img src="{{ asset('images/logo.png') }}" alt="employee-photo" class="employee-photo">

                    <div class="employee-info">
                        <h2 class="employee-name">{{ strtoupper($employee['name']) }}</h2>
                        <p class="employee-role">{{ $employee['role'] }}</p>
                    </div>

                    <div class="status-box {{ $employee['status'] === 'active' ? 'status-active' : 'status-resign' }}">
                        {{ strtoupper($employee['status']) }}
                    </div>

                    <i class="fa-solid fa-trash delete-icon" style="display: none;" onclick="deleteEmployee(this)"></i>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Ikon Edit & Kunci -->
    <div class="action-icons">
        <img src="{{ asset('images/edit.png') }}" alt="Edit" class="icon-edit" onclick="openDialog()">
        <img src="{{ asset('images/lock.png') }}" alt="Lock" class="icon-lock" onclick="openDialog()">
    </div>

    <!-- Dialog Password -->
    <div id="dialog">
        <p>Enter Password:</p>
        <div class="password-container">
            <input type="password" id="password">
            <i id="toggleEye" class="fa-solid fa-eye-slash" onclick="togglePassword()"></i>
        </div>
        <div class="dialog-buttons">
            <button class="btn-done" onclick="handleSubmit()">DONE</button>
            <button class="btn-cancel" onclick="closeDialog()">CANCEL</button>
        </div>
    </div>

    <!-- Notifikasi -->
    <div id="notification">
        <img id="notifImage" src="" alt="Status">
        <p id="notifText"></p>
    </div>

    <script>
        let passwordInput = document.getElementById("password");
        let adminOptions = document.getElementById("adminOptions");
        let notification = document.getElementById("notification");
        let notifImage = document.getElementById("notifImage");
        let notifText = document.getElementById("notifText");
        let eyeIcon = document.getElementById("toggleEye");

        let successImage = "{{ asset('images/success.png') }}";
        let failedImage = "{{ asset('images/failed.png') }}";

        function openDialog() {
            document.getElementById("dialog").style.display = "block";
        }

        function closeDialog() {
            document.getElementById("dialog").style.display = "none";
        }

        function togglePassword() {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            }
        }

        function handleSubmit() {
            let password = passwordInput.value;
            document.getElementById("dialog").style.display = "none";

            setTimeout(() => {
                if (password === "1234") {
                    notifImage.src = successImage;
                    notifText.innerText = "BERHASIL!";
                    notification.style.display = "flex";

                    setTimeout(() => {
                        notification.style.display = "none";

                        // Menampilkan radio button setelah sukses
                        adminOptions.style.display = "block";
                    }, 2000);
                } else {
                    notifImage.src = failedImage;
                    notifText.innerText = "GAGAL!";
                    notification.style.display = "flex";

                    setTimeout(() => {
                        notification.style.display = "none";
                    }, 2000);
                }
            }, 100);
        }

        function applyAction() {
            let selectedAction = document.querySelector('input[name="action"]:checked');
            if (!selectedAction) {
                alert("Silakan pilih aksi terlebih dahulu.");
                return;
            }

            let actionValue = selectedAction.value;
            let employeeCards = document.querySelectorAll(".employee-card");

            // Hapus kotak tambahan jika bukan "Add"
            if (actionValue !== "add") {
                document.querySelectorAll(".employee-card.new").forEach(card => card.remove());
            }

            employeeCards.forEach(card => {
                let name = card.querySelector(".employee-name");
                let role = card.querySelector(".employee-role");
                let status = card.querySelector(".status-box");
                let deleteIcon = card.querySelector(".delete-icon");

                let isResign = status.innerText.trim().toLowerCase() === "resign";

                if (actionValue === "delete") {
                    deleteIcon.style.display = "block";
                } else {
                    deleteIcon.style.display = "none";
                }

                if (actionValue === "edit") {
                    if (isResign) {
                        name.contentEditable = "false";
                        role.contentEditable = "false";
                        status.contentEditable = "false";
                        status.style.backgroundColor = "";
                    } else {
                        name.contentEditable = "true";
                        role.contentEditable = "true";
                        status.contentEditable = "true";
                        status.style.backgroundColor = "yellow";
                    }
                } else {
                    name.contentEditable = "false";
                    role.contentEditable = "false";
                    status.contentEditable = "false";
                    status.style.backgroundColor = "";
                }
            });

            if (actionValue === "add") {
                addNewEmployees();
            }
        }

        function deleteEmployee(icon) {
            let card = icon.closest(".employee-card");
            card.remove();
        }

        function addNewEmployees() {
            let employeeList = document.getElementById("employeeList");

            for (let i = 0; i < 3; i++) {
                let newCard = document.createElement("div");
                newCard.className = "employee-card new";
                newCard.innerHTML = `
                    <label for="file-input-${i}" class="upload-placeholder">
                        <i class="fa-solid fa-plus"></i>
                    </label>
                    <input type="file" id="file-input-${i}" class="file-input" style="display:none;">
                    <div class="employee-info">
                        <h2 class="employee-name" contenteditable="true">Nama</h2>
                        <p class="employee-role" contenteditable="true">Jabatan</p>
                    </div>
                    <div class="status-box status-active" contenteditable="true">ACTIVE</div>
                `;
                employeeList.appendChild(newCard);
            }
        }

        // **PERUBAHAN OTOMATIS SAAT RADIO BUTTON DIPILIH**
        document.querySelectorAll('input[name="action"]').forEach(radio => {
            radio.addEventListener("change", applyAction);
        });

    </script>

</body>
</html>
