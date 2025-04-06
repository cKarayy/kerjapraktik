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
    <div id="adminOptions" style="display: flex;" class="action-selection">
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
        <button class="btn-container" id="doneButton">DONE</button>
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

    <script>
        let deletedIds = [];

        function deleteEmployee(icon) {
            let card = icon.closest(".employee-card");
            let id = card.getAttribute("data-id");
            if (id) deletedIds.push(id);
            card.remove();
        }

        function applyAction() {
            let selectedAction = document.querySelector('input[name="action"]:checked');
            if (!selectedAction) {
                alert("Silakan pilih aksi terlebih dahulu.");
                return;
            }

            let actionValue = selectedAction.value;
            let employeeCards = document.querySelectorAll(".employee-card");

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

        function addNewEmployees() {
            let employeeList = document.getElementById("employeeList");

            for (let i = 0; i < 3; i++) {
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

                let fileInput = newCard.querySelector(".file-input");
                let previewImg = newCard.querySelector(".employee-photo-preview");
                let plusIcon = newCard.querySelector(".plus-icon");

                fileInput.addEventListener("change", function () {
                    if (fileInput.files && fileInput.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            previewImg.src = e.target.result;
                            previewImg.style.display = "block";
                            plusIcon.style.display = "none";
                        };
                        reader.readAsDataURL(fileInput.files[0]);
                    }
                });
            }
        }

        function submitData() {
            let formData = new FormData();

            // ADD
            let newCards = document.querySelectorAll(".employee-card.new");
            newCards.forEach(card => {
                let name = card.querySelector(".employee-name").innerText.trim();
                let role = card.querySelector(".employee-role").innerText.trim();
                let status = card.querySelector(".status-box").innerText.trim().toLowerCase();
                let shift = card.querySelector(".shift-box").innerText.trim();

                formData.append("new_names[]", name);
                formData.append("new_roles[]", role);
                formData.append("new_statuses[]", status);
                formData.append("new_shifts[]", shift);

                let fileInput = card.querySelector(".file-input");
                formData.append("new_photos[]", fileInput && fileInput.files[0] ? fileInput.files[0] : '');
            });

            // EDIT
            let editedCards = document.querySelectorAll(".employee-card:not(.new)");
            editedCards.forEach(card => {
                let id = card.getAttribute("data-id");
                let name = card.querySelector(".employee-name").innerText.trim();
                let role = card.querySelector(".employee-role").innerText.trim();
                let status = card.querySelector(".status-box").innerText.trim().toLowerCase();
                let shift = card.querySelector(".shift-box").innerText.trim();

                formData.append("edit_ids[]", id);
                formData.append("edit_names[]", name);
                formData.append("edit_roles[]", role);
                formData.append("edit_statuses[]", status);
                formData.append("edit_shifts[]", shift);
            });

            // DELETE
            deletedIds.forEach(id => {
                formData.append("deleted_ids[]", id);
            });

            fetch("{{ route('data_py.saveAll') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert("Data berhasil disimpan!");
                location.reload();
            })
            .catch(error => {
                console.error("Gagal menyimpan data:", error);
                alert("Terjadi kesalahan saat menyimpan data.");
            });
        }

        // Event listener
        document.querySelectorAll('input[name="action"]').forEach(radio => {
            radio.addEventListener("change", applyAction);
        });

        document.getElementById("doneButton").addEventListener("click", submitData);
    </script>
</body>
</html>
