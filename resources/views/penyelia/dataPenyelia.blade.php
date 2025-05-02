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

    <div id="initialTargetModal" class="modal" style="display:flex;">
        <div class="modal-content">
            <h3>Tampilkan Data:</h3>
            <div class="button-group">
                <button onclick="initTargetChoice('pegawai')">Pegawai</button>
                <button onclick="initTargetChoice('shift')">Shift</button>
            </div>
        </div>
    </div>

    <!-- Opsi Radio Button -->
    <div id="adminOptions" class="action-selection" style="display: flex;">
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

    <div class="container" style="display: none;">
        <form method="POST" action="{{ route('data_py.edit', $pegawai->id) }}">
            @csrf
            @method('PUT')

            <!-- Nama -->
            <input type="text" name="name" value="{{ $pegawai->nama_lengkap }}" placeholder="Nama Lengkap" required>

            <!-- Jabatan -->
            <input type="text" name="role" value="{{ $pegawai->jabatan }}" placeholder="Jabatan" required>

            <!-- Shift -->
            <select name="shift" required>
                @foreach ($shifts as $shift)
                    <option value="{{ $shift->nama_shift }}"
                        {{ $pegawai->shift->nama_shift == $shift->nama_shift ? 'selected' : '' }}>
                        {{ $shift->nama_shift }}
                    </option>
                @endforeach
            </select>

            <!-- Status -->
            <select name="status" required>
                <option value="active" {{ $pegawai->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $pegawai->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit">Update</button>
        </form>
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
        }

        function handleTargetChoice(target) {
            document.getElementById("chooseTargetModal").style.display = "none";

            if (target === 'pegawai') {
                document.querySelector(".container").style.display = "block";
                document.getElementById("shiftFormContainer").style.display = "none";
                updateEmployeeCardUI(window.selectedActionValue);
            } else if (target === 'shift') {
                document.querySelector(".container").style.display = "none";
                document.getElementById("shiftFormContainer").style.display = "block";
                loadShiftFromDatabase();
            }
        }

        function loadShiftFromDatabase() {
            const container = document.getElementById("shiftFormContainer");
            container.innerHTML = "";
            container.style.display = "grid";
            container.className = "shift-container";

            fetch("/shifts/all")
                .then(res => res.json())
                .then(shifts => {
                    let html = "";
                    shifts.forEach(shift => {
                        html += `
                            <div class="employee-card shift-card" data-id="${shift.id_shift}">
                                <div class="employee-info">
                                    <h2 class="employee-name">${shift.nama_shift}</h2>
                                    <p class="employee-role">
                                        ${shift.jam_masuk} - ${shift.jam_keluar}
                                    </p>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                })
                .catch(error => {
                    console.error("Gagal memuat data shift:", error);
                });
        }

        function openEmployeeForm() {
            document.getElementById("employeeModal").style.display = "block";
        }

        function closeEmployeeForm() {
            document.getElementById("employeeModal").style.display = "none";
        }

        function updateEmployeeCardUI(actionValue) {
            document.querySelectorAll('.edit-status, .edit-shift, .delete-icon').forEach(el => {
                el.style.display = 'none';
            });

            if (actionValue === 'edit') {
                document.querySelectorAll('.edit-status, .edit-shift').forEach(el => {
                    el.style.display = 'inline-block';
                });
            } else if (actionValue === 'delete') {
                document.querySelectorAll('.delete-icon').forEach(icon => {
                    icon.style.display = 'inline-block';
                });
            }
        }

        // Aktifkan applyAction saat radio ADD/EDIT/DELETE dipilih
        document.querySelectorAll('input[name="action"]').forEach(radio =>
            radio.addEventListener("change", applyAction)
        );

        // Tombol DONE
        document.getElementById("doneButton").onclick = function () {
            const actionValue = document.querySelector('input[name="action"]:checked')?.value;
            if (actionValue === 'edit') {
                document.getElementById("editForm").submit();
            } else if (actionValue === 'delete') {
                fetch("/pegawai/delete-multiple", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: deletedIds })
                }).then(response => {
                    if (response.ok) {
                        alert("Data berhasil dihapus.");
                        location.reload();
                    } else {
                        alert("Gagal menghapus data.");
                    }
                });
            }
        };

        document.getElementById("cancelButton").onclick = function () {
            location.reload();
        };

        window.addEventListener('DOMContentLoaded', () => {
            // Modal pemilihan awal tampil
            document.getElementById("initialTargetModal").style.display = "flex";
        });

        function initTargetChoice(target) {
            const isShift = target === 'shift';

            document.getElementById("initialTargetModal").style.display = "none";
            document.querySelector(".container").style.display = isShift ? "none" : "block";
            document.getElementById("shiftFormContainer").style.display = isShift ? "block" : "none";

            if (isShift) {
                loadShiftFromDatabase(); // <-- penting
            }
        }

    </script>
</body>
</html>
