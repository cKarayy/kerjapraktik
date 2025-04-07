<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kepegawaian Perwakilan Owner</title>
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

    <div class="container">
        <div class="employee-list" id="employeeList">
            @foreach($employees as $employee)
            <div class="employee-card {{ $employee['status'] === 'resign' ? 'resign' : 'active' }}" data-id="{{ $employee['id'] }}">
                <div class="upload-container">
                    <img class="employee-photo" src="{{ asset($employee['photo'] ?? 'images/logo.png') }}" alt="Foto Pegawai">
                </div>
                <div class="employee-info">
                    <h2 class="employee-name">{{ strtoupper($employee['name']) }}</h2>
                    <p class="employee-role">{{ $employee['role'] }}</p>
                </div>
                <div class="status-box {{ $employee['status'] === 'active' ? 'status-active' : 'status-resign' }}">
                    {{ strtoupper($employee['status']) }}
                </div>
                <div class="shift-box">
                    {{ strtoupper($employee['shift'] ?? '-') }}
                </div>
                <i class="fa-solid fa-trash delete-icon" style="display: none;" onclick="deleteEmployee(this)"></i>
            </div>
            @endforeach
        </div>
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


    <script>
        let deletedIds = [];

        function deleteEmployee(icon) {
            const card = icon.closest('.employee-card');
            const id = card.getAttribute('data-id');
            if (id) {
                deletedIds.push(id);
                card.remove();
            }
        }

        function applyAction() {
            let selectedAction = document.querySelector('input[name="action"]:checked');
            if (!selectedAction) {
                alert("Silakan pilih aksi terlebih dahulu.");
                return;
            }

            let actionValue = selectedAction.value;
            let employeeCards = document.querySelectorAll(".employee-card");

            // Reset semua kartu
            employeeCards.forEach(card => {
                let name = card.querySelector(".employee-name");
                let role = card.querySelector(".employee-role");
                let status = card.querySelector(".status-box");
                let shift = card.querySelector(".shift-box");
                let deleteIcon = card.querySelector(".delete-icon");

                let isResign = status.innerText.trim().toLowerCase() === "resign";

                if (actionValue === "edit") {
                    if (!isResign) {
                        name.contentEditable = "true";
                        role.contentEditable = "true";
                        shift.contentEditable = "true";
                        status.contentEditable = "true";
                        status.style.backgroundColor = "yellow";
                        shift.style.backgroundColor = "yellow";
                    }
                } else {
                    name.contentEditable = "false";
                    role.contentEditable = "false";
                    shift.contentEditable = "false";
                    status.contentEditable = "false";
                    status.style.backgroundColor = "";
                    shift.style.backgroundColor = "";
                }

                deleteIcon.style.display = actionValue === "delete" ? "block" : "none";
            });

            if (actionValue === "add") {
                openEmployeeForm();
            } else {
                closeEmployeeForm();
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
                        <h2 class="employee-name" contenteditable="true">Nama</h2>
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

        function submitData() {
            document.activeElement.blur();
            let formData = new FormData();

            // Tambah
            document.querySelectorAll(".employee-card.new").forEach(card => {
                formData.append("new_names[]", card.querySelector(".employee-name").innerText.trim());
                formData.append("new_roles[]", card.querySelector(".employee-role").innerText.trim());
                formData.append("new_statuses[]", card.querySelector(".status-box").innerText.trim().toLowerCase());
                formData.append("new_shifts[]", card.querySelector(".shift-box").innerText.trim());

                const fileInput = card.querySelector(".file-input");
                formData.append("new_photos[]", fileInput.files[0] ?? '');
            });

            // Edit
            document.querySelectorAll(".employee-card:not(.new)").forEach(card => {
                const id = card.getAttribute("data-id");
                if (id) {
                    formData.append("edit_ids[]", id);
                    formData.append("edit_names[]", card.querySelector(".employee-name").innerText.trim());
                    formData.append("edit_roles[]", card.querySelector(".employee-role").innerText.trim());
                    formData.append("edit_statuses[]", card.querySelector(".status-box").innerText.trim().toLowerCase());
                    formData.append("edit_shifts[]", card.querySelector(".shift-box").innerText.trim());
                }
            });

            // Hapus
            deletedIds.forEach(id => formData.append("deleted_ids[]", id));

            fetch("{{ route('data_py.saveAll') }}", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert("Data berhasil disimpan!");
                location.reload();
            })
            .catch(err => {
                console.error(err);
                alert("Gagal menyimpan data!");
            });
        }

        document.querySelectorAll('input[name="action"]').forEach(radio =>
            radio.addEventListener("change", applyAction)
        );

        document.getElementById("doneButton").addEventListener("click", submitData);

        document.getElementById("cancelButton").addEventListener("click", () => {
            document.querySelectorAll('input[name="action"]').forEach(r => r.checked = false);
            document.querySelectorAll(".employee-card.new").forEach(card => card.remove());

            document.querySelectorAll(".employee-card").forEach(card => {
                let name = card.querySelector(".employee-name");
                let role = card.querySelector(".employee-role");
                let status = card.querySelector(".status-box");
                let shift = card.querySelector(".shift-box");
                let deleteIcon = card.querySelector(".delete-icon");

                name.contentEditable = "false";
                role.contentEditable = "false";
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
