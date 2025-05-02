<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kepegawaian Perwakilan Owner</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/admin/data.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="header">
        <h2>DATA PEGAWAI</h2>
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>
    <div class="divider"></div>

    <!-- Opsi Radio Button -->
    <div id="adminOptions" class="action-selection" style="display: flex;">
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
        <button class="btn-done" id="doneButton">DONE</button>
        <button class="btn-cancel" id="cancelButton">CANCEL</button>
    </div>

    <div id="chooseTargetModal" class="modal" style="display:none;">
        <div class="modal-content">
            <h3>Pilih Jenis Data:</h3>
            <div class="button-group">
                <button onclick="handleTargetChoice('pegawai')">Pegawai</button>
                <button onclick="handleTargetChoice('shift')">Shift</button>
            </div>
        </div>
    </div>

    <div class="container">
        <form method="POST" action="{{ route('data_py.edit') }}" id="editForm">
            @csrf
            <div class="employee-list" id="employeeList">
                @foreach($employees as $employee)
                <div class="employee-card {{ $employee['status'] === 'resign' ? 'resign' : 'active' }}" data-id="{{ $employee['id'] }}">

                    <div class="employee-info">
                        <h2 class="employee-name">{{ strtoupper($employee['name']) }}</h2>
                        <p class="employee-role">{{ $employee['role'] }}</p>
                    </div>

                    <!-- Status -->
                    <div class="status-box {{ $employee['status'] === 'active' ? 'status-active' : 'status-resign' }}">
                        {{ strtoupper($employee['status']) }}
                    </div>
                    <select class="edit-status" name="edit_statuses[]" style="display: none;">
                        <option value="active" {{ $employee['status'] == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="resign" {{ $employee['status'] == 'resign' ? 'selected' : '' }}>Resign</option>
                    </select>

                    <!-- Shift -->
                    <div class="shift-box">
                        {{ strtoupper($employee['shift'] ?? '-') }}
                    </div>
                    <select class="edit-shift" name="edit_shifts[]" style="display: none;">
                        <option value="pagi" {{ $employee['shift'] == 'pagi' ? 'selected' : '' }}>Pagi</option>
                        <option value="middle" {{ $employee['shift'] == 'middle' ? 'selected' : '' }}>Middle</option>
                        <option value="malam" {{ $employee['shift'] == 'malam' ? 'selected' : '' }}>Malam</option>
                    </select>

                    <i class="fa-solid fa-trash delete-icon" style="display: none;" onclick="deleteEmployee(this)"></i>
                </div>
                @endforeach
            </div>
        </form>
    </div>

    <!-- popup add -->
    <div id="employeeModal" class="modal">
        <div class="modal-content">
            <h2>Tambah Karyawan</h2>
            <input type="number" id="jumlahInput" min="1" placeholder="Masukkan jumlah">
            <div class="button-group">
                <button class="done-btn" onclick="addNewEmployees()">DONE</button>
                <button class="cancel-btn" onclick="closeEmployeeForm()">CANCEL</button>
            </div>
        </div>
    </div>

    <!-- Tambah Shift -->
    <div id="shiftModal" class="modal">
        <div class="modal-content">
            <h2>Tambah Shift</h2>
            <input type="number" id="jumlahShiftInput" min="1" placeholder="Masukkan jumlah shift">
            <div class="button-group">
                <button class="done-btn" onclick="generateShiftCards()">DONE</button>
                <button class="cancel-btn" onclick="cancelShiftAction()">CANCEL</button>
            </div>
        </div>
    </div>

    <div id="shiftFormContainer" style="display: none; margin-top: 20px;"></div>

    <script>
        let deletedIds = [];

        function deleteEmployee(icon) {
            const card = icon.closest('.employee-card');
            const id = card.getAttribute('data-id');
            if (id) {
                deletedIds.push(id);
                card.remove();
                console.log(deletedIds);
            }
        }

        function applyAction() {
            let selectedAction = document.querySelector('input[name="action"]:checked');
            if (!selectedAction) {
                alert("Silakan pilih aksi terlebih dahulu.");
                return;
            }

            let actionValue = selectedAction.value;
            window.selectedActionValue = selectedAction.value;

            // Show target selection dialog
            document.getElementById("chooseTargetModal").style.display = "flex";

            document.querySelectorAll('input[name="target"]').forEach(input => {
                input.addEventListener("change", function () {
                    const isShift = this.value === 'shift';

                    document.querySelector(".container").style.display = isShift ? "none" : "block";
                    document.getElementById("employeeModal").style.display = "none";
                    document.getElementById("shiftFormContainer").style.display = isShift ? "block" : "none";

                    if (isShift) {
                        closeEmployeeForm();
                        generateShiftForm(actionValue);
                    } else {
                        if (actionValue === "add") {
                            openEmployeeForm();
                        } else {
                            closeEmployeeForm();
                        }
                    }
                });
            });
        }

        function handleTargetChoice(target) {
            const isShift = target === 'shift';
            const actionValue = window.selectedActionValue;

            // Menutup modal pilih target
            document.getElementById("chooseTargetModal").style.display = "none";
            document.querySelector(".container").style.display = isShift ? "none" : "block";
            document.getElementById("shiftFormContainer").style.display = isShift ? "block" : "none";

            if (isShift) {
                closeEmployeeForm();

                // Tampilkan modal untuk meminta jumlah shift
                document.getElementById("shiftModal").style.display = "block";

                // Tombol DONE untuk memasukkan jumlah shift
                document.querySelector("#shiftModal .done-btn").onclick = () => {
                    const jumlah = parseInt(document.getElementById("jumlahShiftInput").value);
                    if (isNaN(jumlah) || jumlah <= 0) {
                        alert("Jumlah shift tidak valid.");
                        return;
                    }

                    // Tutup modal dan tampilkan shift card
                    document.getElementById("shiftModal").style.display = "none";
                    generateShiftForm("add", jumlah);
                };

                document.querySelector("#shiftModal .cancel-btn").onclick = () => {
                    // Reset semua kembali ke halaman awal
                    document.getElementById("shiftModal").style.display = "none";
                    document.getElementById("shiftFormContainer").innerHTML = "";
                    document.getElementById("shiftFormContainer").style.display = "none";
                    document.querySelectorAll('input[name="action"]').forEach(r => r.checked = false);
                    document.getElementById("chooseTargetModal").style.display = "none";
                };


            } else {
                if (actionValue === "add") {
                    openEmployeeForm();
                } else {
                    closeEmployeeForm();
                }
                updateEmployeeCardUI(actionValue);
            }
        }

        function generateShiftForm(action, jumlahShift) {
            const container = document.getElementById("shiftFormContainer");
            container.innerHTML = "";
            container.style.display = "grid";
            container.className = "shift-container"; // agar grid layout diterapkan

            // Validasi jumlah shift
            if (isNaN(jumlahShift) || jumlahShift <= 0) {
                alert("Jumlah shift tidak valid.");
                return;
            }

            if (action === "add") {
                for (let i = 0; i < jumlahShift; i++) {
                    const card = document.createElement("div");
                    card.className = "employee-card shift-card new";

                    card.innerHTML = `
                        <div class="employee-info">
                            <h2 class="employee-name" contenteditable="true">Nama Shift</h2>
                            <p class="employee-role">
                                <input type="time" class="jam-masuk"> -
                                <input type="time" class="jam-keluar">
                            </p>
                        </div>
                    `;

                    container.appendChild(card);
                }

                // Tombol CANCEL dan DONE
                const buttonWrapper = document.createElement("div");
                buttonWrapper.style.gridColumn = "1 / -1";
                buttonWrapper.style.display = "flex";
                buttonWrapper.style.justifyContent = "center";
                buttonWrapper.style.gap = "20px";
                buttonWrapper.style.marginTop = "20px";

                const cancelButton = document.createElement("button");
                cancelButton.textContent = "CANCEL";
                cancelButton.className = "btn-cancel";
                cancelButton.onclick = () => {
                    document.getElementById("jumlahShiftInput").value = ""; // Reset input jumlah shift
                    document.getElementById("shiftModal").style.display = "none"; // Tutup modal
                    container.innerHTML = ""; // Hapus semua shift cards yang sudah dibuat
                };

                const doneButton = document.createElement("button");
                doneButton.textContent = "DONE";
                doneButton.className = "btn-done";
                doneButton.onclick = () => {
                    const shifts = [];
                    container.querySelectorAll(".shift-card").forEach(card => {
                        const nama = card.querySelector(".employee-name").innerText.trim();
                        const jamMasuk = card.querySelector(".jam-masuk").value;
                        const jamKeluar = card.querySelector(".jam-keluar").value;
                        if (nama && jamMasuk && jamKeluar) {
                            shifts.push({
                                nama_shift: nama,
                                jam_masuk: jamMasuk,
                                jam_keluar: jamKeluar
                            });
                        }
                    });

                    if (shifts.length === 0) {
                        alert("Data shift tidak lengkap.");
                        return;
                    }

                    // Kirim ke backend Laravel
                    fetch("/shifts/store-multiple", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                        },
                        body: JSON.stringify({ shifts })
                    })
                    .then(res => {
                        if (!res.ok) throw new Error("Gagal menyimpan shift.");
                        return res.json();
                    })
                    .then(response => {
                        alert("Data shift berhasil disimpan.");
                        document.getElementById("shiftModal").style.display = "none"; // Tutup modal setelah sukses
                        container.innerHTML = ""; // Hapus shift form
                    })
                    .catch(error => {
                        console.error(error);
                        alert("Terjadi kesalahan saat menyimpan shift.");
                    });
                };

                buttonWrapper.appendChild(cancelButton);
                buttonWrapper.appendChild(doneButton);
                container.appendChild(buttonWrapper);
            } else {
                // Load data dari backend
                fetch("/shifts/all")
                    .then(res => res.json())
                    .then(shifts => {
                        let html = "";
                        shifts.forEach(shift => {
                            html += `
                                <div class="employee-card shift-card" data-id="${shift.id_shift}">
                                    <div class="employee-info">
                                        <h2 class="employee-name" contenteditable="${action === 'edit'}">${shift.nama_shift}</h2>
                                        <p class="employee-role">
                                            <input type="time" class="jam-masuk" value="${shift.jam_masuk}" ${action === 'delete' ? 'readonly' : ''}>
                                            -
                                            <input type="time" class="jam-keluar" value="${shift.jam_keluar}" ${action === 'delete' ? 'readonly' : ''}>
                                        </p>
                                    </div>
                                    <div class="status-box ${action === 'delete' ? 'status-resign' : 'status-active'}">
                                        ${action.toUpperCase()}
                                    </div>
                                </div>
                            `;
                        });
                        container.innerHTML = html;
                    });
            }
        }

        function openEmployeeForm() {
            document.getElementById("employeeModal").style.display = "block";
        }

        function closeEmployeeForm() {
            document.getElementById("employeeModal").style.display = "none";
        }

        function addNewEmployees() {
            let jumlah = parseInt(document.getElementById("jumlahInput").value);
            if (isNaN(jumlah) || jumlah <= 0) {
                alert("Jumlah tidak valid.");
                return;
            }

            let employeeList = document.getElementById("employeeList");
            for (let i = 0; i < jumlah; i++) {
                let newCard = document.createElement("div");
                newCard.className = "employee-card new";

                newCard.innerHTML = `
                    <div class="upload-container">
                        <span class="plus-icon"><i class="fa-solid fa-plus"></i></span>
                        <input type="file" class="file-input" accept="image/*">
                        <img class="employee-photo employee-photo-preview" src="#" alt="Preview" style="display:none;">
                    </div>
                    <div class="employee-info">
                        <h2 class="employee-name" contenteditable="true">Nama Lengkap</h2>
                        <p class="employee-role" contenteditable="true">Jabatan</p>
                    </div>
                    <div class="status-box status-active" contenteditable="true">ACTIVE</div>
                    <div class="shift-box" contenteditable="true">SHIFT</div>
                `;
                employeeList.appendChild(newCard);

                const fileInput = newCard.querySelector(".file-input");
                const previewImg = newCard.querySelector(".employee-photo-preview");
                const plusIcon = newCard.querySelector(".plus-icon");

                plusIcon.addEventListener("click", () => fileInput.click());
                fileInput.addEventListener("change", () => {
                    if (fileInput.files && fileInput.files[0]) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            previewImg.src = e.target.result;
                            previewImg.style.display = "block";
                            plusIcon.style.display = "none";
                        };
                        reader.readAsDataURL(fileInput.files[0]);
                    }
                });
            }

            closeEmployeeForm();
        }

        // Aktifkan applyAction saat radio ADD/EDIT/DELETE dipilih
        document.querySelectorAll('input[name="action"]').forEach(radio =>
            radio.addEventListener("change", applyAction)
        );

        // Tombol DONE
        document.getElementById("doneButton").addEventListener("click", submitData);

        // Tombol CANCEL
        document.getElementById("cancelButton").addEventListener("click", () => {
            document.querySelectorAll('input[name="action"]').forEach(r => r.checked = false);
            document.querySelectorAll('input[name="target"]').forEach(r => r.checked = false);

            // Hapus shift card baru & sembunyikan container
            document.getElementById("shiftFormContainer").innerHTML = "";
            document.getElementById("shiftFormContainer").style.display = "none";


            document.querySelectorAll(".employee-card.new").forEach(card => card.remove());
            document.getElementById("targetOptions").style.display = "none";
            document.getElementById("shiftFormContainer").style.display = "none";

            document.querySelectorAll(".employee-card").forEach(card => {
                let fullName = card.querySelector(".employee-name");
                let jobTitle = card.querySelector(".employee-role");
                let status = card.querySelector(".status-box");
                let shift = card.querySelector(".shift-box");
                let deleteIcon = card.querySelector(".delete-icon");

                fullName.contentEditable = "false";
                jobTitle.contentEditable = "false";
                status.contentEditable = "false";
                shift.contentEditable = "false";
                status.style.backgroundColor = "";
                shift.style.backgroundColor = "";

                if (deleteIcon) deleteIcon.style.display = "none";
            });

            deletedIds = [];
            closeEmployeeForm();
        });
    </script>
</body>
</html>
