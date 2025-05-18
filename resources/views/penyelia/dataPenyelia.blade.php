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

    <!-- Notifikasi -->
    <div id="notification" class="notification">
        <span id="notification-message">Data berhasil diperbarui</span>
        <button id="notification-button">OK</button>
    </div>

    <!-- Pilihan Aksi -->
    <div id="adminOptions" class="action-selection" style="display: flex;">
        <label class="custom-radio">
            <input type="radio" name="action" value="edit">
            <span class="checkmark"></span> EDIT
        </label>
        <label class="custom-radio">
            <input type="radio" name="action" value="delete">
            <span class="checkmark"></span> DELETE
        </label>
        <button class="btn-done" id="doneButton">DONE</button>
        <button class="btn-cancel" id="cancelButton">CANCEL</button>
    </div>

    <div class="main-wrapper" id="mainWrapper">
        <!-- SHIFT - tetap di atas -->
        <div id="shiftFormContainer" class="shift-container"></div>

        <!-- PEGAWAI - scrollable -->
        <div class="pegawai-scroll-wrapper">
            <div class="employee-list" id="employeeList">
                @foreach($employees as $employee)
                    <div class="employee-card {{ $employee['status'] === 'resign' ? 'resign' : 'active' }}" data-id="{{ $employee['id'] }}">
                        <img src="{{ asset($employee['photo'] ?? 'images/logo.png') }}" alt="employee-photo" class="employee-photo">
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
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        let deletedIds = [];
        let editedEmployees = {};
        let shiftList = [];
        let isEditMode = false;

        function showNotification(message, isError = false) {
            const notification = document.getElementById('notification');
            const messageElement = document.getElementById('notification-message');
            const button = document.getElementById('notification-button');

            messageElement.textContent = message;

            if (isError) {
                notification.classList.add('error');
                button.style.color = '#f44336';
            } else {
                notification.classList.remove('error');
                button.style.color = '#4CAF50';
            }

            notification.style.display = 'block';

            // Auto hide setelah 5 detik
            setTimeout(() => {
                if (notification.style.display === 'block') {
                    notification.style.display = 'none';
                    resetToNormalMode();
                }
            }, 5000);
        }

        document.getElementById('notification-button').addEventListener('click', function() {
            document.getElementById('notification').style.display = 'none';
            resetToNormalMode();
        });

        function resetToNormalMode() {
        // Reset radio button
        document.querySelectorAll('input[name="action"]').forEach(r => r.checked = false);

        // Bersihkan mode edit/delete
        document.querySelectorAll('.employee-card').forEach(card => {
            // Ganti kembali dropdown status menjadi div
            const statusSelect = card.querySelector('.edit-status');
            if (statusSelect) {
                const statusText = statusSelect.value.toUpperCase();
                const div = document.createElement('div');
                div.className = `status-box ${statusText === 'ACTIVE' ? 'status-active' : 'status-resign'}`;
                div.textContent = statusText;
                statusSelect.replaceWith(div);
            }

            // Ganti kembali dropdown shift menjadi div
            const shiftSelect = card.querySelector('.edit-shift');
            if (shiftSelect) {
                const shiftText = shiftSelect.value.toUpperCase() || '-';
                const div = document.createElement('div');
                div.className = 'shift-box';
                div.textContent = shiftText;
                shiftSelect.replaceWith(div);
            }

            // Ganti kembali input role menjadi paragraf
            const roleInput = card.querySelector('.edit-role');
            if (roleInput) {
                const roleText = roleInput.value;
                const p = document.createElement('p');
                p.className = 'employee-role';
                p.textContent = roleText;
                roleInput.replaceWith(p);
            }

            // Hapus tombol aksi (delete icon, dll)
            const actions = card.querySelector('.card-actions');
            if (actions) actions.remove();
        });

        isEditMode = false;
    }


        function exitEditMode() {
            resetToNormalMode();
        }

        function applyAction() {
            const selectedAction = document.querySelector('input[name="action"]:checked');
            if (!selectedAction) {
                alert("Silakan pilih aksi terlebih dahulu.");
                return;
            }

            window.selectedActionValue = selectedAction.value;
            updateEmployeeCardUI(window.selectedActionValue);

            // Aksi edit
            if (window.selectedActionValue === 'edit') {
                isEditMode = true;
                loadShiftFromDatabase(true); // tampilkan input shift
            }
            // Aksi delete
            else if (window.selectedActionValue === 'delete') {
                isEditMode = false;
                loadShiftFromDatabase(false);
                resetToNormalMode(); // Reset tampilan setelah aksi delete
                showDeleteIcons(); // Menampilkan ikon delete

                // Pastikan radio button untuk delete tercentang
                document.querySelector('input[name="action"][value="delete"]').checked = true;
            }
        }


        function updateEmployeeCardUI(actionValue) {
    const cards = document.querySelectorAll('.employee-card:not(.shift-card)');
    cards.forEach(card => {
        const statusBox = card.querySelector('.status-box');
        const shiftBox = card.querySelector('.shift-box');
        const cardActions = card.querySelector('.card-actions');

        // Edit mode
        if (actionValue === 'edit') {
            const currentStatus = card.querySelector('.status-box')?.textContent.trim().toLowerCase();
            const currentShift = card.querySelector('.shift-box')?.textContent.trim();
            const currentRole = card.querySelector('.employee-role')?.textContent.trim();

            // Ganti status-box menjadi dropdown
            if (statusBox) {
                const statusSelect = document.createElement('select');
                statusSelect.classList.add('edit-status');
                ['active', 'resign'].forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option;
                    opt.textContent = option.toUpperCase();
                    if (option === currentStatus) opt.selected = true;
                    statusSelect.appendChild(opt);
                });
                statusBox.replaceWith(statusSelect);
            }

            // Ganti shift-box menjadi dropdown
            if (shiftBox) {
                const shiftSelect = document.createElement('select');
                shiftSelect.classList.add('edit-shift');
                const defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = '-';
                shiftSelect.appendChild(defaultOpt);
                shiftList.forEach(shift => {
                    const opt = document.createElement('option');
                    opt.value = shift.nama_shift;
                    opt.textContent = shift.nama_shift.toUpperCase();
                    if (shift.nama_shift.toUpperCase() === currentShift) opt.selected = true;
                    shiftSelect.appendChild(opt);
                });
                shiftBox.replaceWith(shiftSelect);
            }

            // Ganti role menjadi input
            const rolePara = card.querySelector('.employee-role');
            if (rolePara) {
                const roleInput = document.createElement('input');
                roleInput.type = 'text';
                roleInput.classList.add('edit-role');
                roleInput.value = currentRole;
                rolePara.replaceWith(roleInput);
            }
        }
        // Mode delete
        else if (actionValue === 'delete') {
            if (!cardActions) {
                const actionsDiv = document.createElement('div');
                actionsDiv.classList.add('card-actions');
                const deleteIcon = document.createElement('i');
                deleteIcon.classList.add('fas', 'fa-trash', 'delete-icon');
                deleteIcon.addEventListener('click', function() { deleteEmployee(card); });
                actionsDiv.appendChild(deleteIcon);
                card.appendChild(actionsDiv);
            }
        }
        // Reset tampilan normal
        else {
            if (cardActions) {
                cardActions.remove();
            }
            // Kembalikan status dan shift ke bentuk semula
            if (statusBox && statusBox.tagName === 'SELECT') {
                const statusText = statusBox.value.toUpperCase();
                statusBox.replaceWith(`<div class="status-box">${statusText}</div>`);
            }
            if (shiftBox && shiftBox.tagName === 'SELECT') {
                const shiftText = shiftBox.value.toUpperCase();
                shiftBox.replaceWith(`<div class="shift-box">${shiftText || '-'}</div>`);
            }
            const roleInput = card.querySelector('.edit-role');
            if (roleInput) {
                const roleText = roleInput.value;
                roleInput.replaceWith(`<p class="employee-role">${roleText}</p>`);
            }
        }
    });
}

function showDeleteIcons() {
    const cards = document.querySelectorAll('.employee-card');
    cards.forEach(card => {
        const cardActions = card.querySelector('.card-actions');
        if (!cardActions) {
            const actionsDiv = document.createElement('div');
            actionsDiv.classList.add('card-actions');
            const deleteIcon = document.createElement('i');
            deleteIcon.classList.add('fas', 'fa-trash', 'delete-icon');
            deleteIcon.addEventListener('click', function() { deleteEmployee(card); });
            actionsDiv.appendChild(deleteIcon);
            card.appendChild(actionsDiv);
        }
    });
}

        function selectCardForDeletion(card) {
            card.classList.toggle('selected');
        }

        function deleteEmployee(card) {
            const employeeId = card.getAttribute('data-id');

            if (employeeId) {
                fetch(`/pegawai/delete/${employeeId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                })
                .then(response => {
                    if (response.ok) {
                        card.remove();
                        showNotification("Pegawai berhasil dihapus.");
                        resetUIAfterDeletion();
                    } else {
                        showNotification(`Gagal menghapus pegawai. Status: ${response.status}`, true);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    showNotification("Terjadi kesalahan saat menghapus pegawai.", true);
                });
            } else {
                showNotification("ID pegawai tidak ditemukan.", true);
            }
        }

        function resetUIAfterDeletion() {
            const selectedAction = document.querySelector('input[name="action"]:checked');
            if (selectedAction) {
                selectedAction.checked = false;
            }
            const allCards = document.querySelectorAll('.employee-card');
            allCards.forEach(card => {
                const actionsDiv = card.querySelector('.card-actions');
                if (actionsDiv) {
                    actionsDiv.remove();
                }
            });
        }

        function loadEmployeeData() {
            fetch('/pegawai/all')
                .then(response => response.json())
                .then(data => {
                    const employeeListContainer = document.getElementById('employeeList');
                    employeeListContainer.innerHTML = '';
                    data.forEach(employee => {
                        const card = document.createElement('div');
                        card.classList.add('employee-card', employee.status === 'active' ? 'active' : 'resign');
                        card.setAttribute('data-id', employee.id);
                        card.innerHTML = `
                            <img src="${employee.photo || 'images/logo.png'}" alt="employee-photo" class="employee-photo">
                            <div class="employee-info">
                                <h2 class="employee-name">${employee.name.toUpperCase()}</h2>
                                <p class="employee-role">${employee.role}</p>
                            </div>
                            <div class="status-box ${employee.status === 'active' ? 'status-active' : 'status-resign'}">
                                ${employee.status.toUpperCase()}
                            </div>
                            <div class="shift-box">${(employee.shift || '-').toUpperCase()}</div>
                        `;
                        employeeListContainer.appendChild(card);
                    });
                })
                .catch(error => console.error('Error loading employee data:', error));
        }

   function loadShiftFromDatabase(showEdit = false) {
    const container = document.getElementById("shiftFormContainer");
    container.innerHTML = "";
    container.style.display = "grid";
    container.className = "shift-container";

    fetch("/shifts/all")
        .then(res => res.json())
        .then(shifts => {
            shiftList = shifts;
            let html = "";
            shifts.forEach(shift => {
                html += `
                    <div class="employee-card shift-card" data-id="${shift.id_shift}">
                        <div class="shift-info">
                            <h2 class="shift-name" contenteditable="true">${shift.nama_shift}</h2>
                            <p class="shift-time" contenteditable="true">${shift.jam_masuk} - ${shift.jam_keluar}</p>
                        </div>
                `;

                // Tombol Done untuk menyimpan perubahan
                html += `

                `;

                html += `</div>`;
            });
            container.innerHTML = html;
        })
        .catch(error => console.error("Gagal memuat data shift:", error));
}

        function saveShiftChanges(shiftId) {
            const shiftCard = document.querySelector(`.employee-card[data-id="${shiftId}"]`);
            const newName = shiftCard.querySelector('.shift-name').textContent.trim();
            const newTime = shiftCard.querySelector('.shift-time').textContent.trim();

            return fetch(`/shifts/update/${shiftId}`, { // ← tambahkan return
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    nama_shift: newName,
                    jam_masuk: newTime.split(' - ')[0],
                    jam_keluar: newTime.split(' - ')[1]
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    shiftCard.querySelector('.shift-name').textContent = newName;
                    shiftCard.querySelector('.shift-time').textContent = newTime;
                    return true; // untuk menandai berhasil
                } else {
                    throw new Error('Gagal memperbarui shift');
                }
            })
            .catch(error => {
                console.error('Error:', error);

                throw error;
            });
        }


        function processEmployeeUpdates() {
    return new Promise((resolve, reject) => {
        const cards = document.querySelectorAll('.employee-card:not(.shift-card)');
        const updates = [];
        let updatePromises = [];

        cards.forEach(card => {
            const id = card.getAttribute('data-id');
            const status = card.querySelector('.edit-status')?.value;
            const shift = card.querySelector('.edit-shift')?.value;
            const role = card.querySelector('.edit-role')?.value;

            if (id && status && shift && role) {
                updates.push({ id, status, shift, role });
            }
        });

        if (updates.length === 0) {
            showNotification("Tidak ada data yang diubah.", true);
            resolve();
            return;
        }

        updates.forEach(update => {
            updatePromises.push(
                fetch(`/pegawai/update/${update.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        status: update.status,
                        shift: update.shift,
                        role: update.role
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const card = document.querySelector(`.employee-card[data-id="${update.id}"]`);
                        card.querySelector('.status-box').textContent = update.status.toUpperCase();
                        card.querySelector('.shift-box').textContent = update.shift.toUpperCase();
                        card.querySelector('.employee-role').textContent = update.role;
                    } else {
                        showNotification('Gagal memperbarui data pegawai', true);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                })
            );
        });

        Promise.all(updatePromises)
            .then(() => {
                resetToNormalMode();
                resolve(); // penting agar .then di tombol DONE berjalan
            })
            .catch(error => {
                console.error('Error in update promises:', error);
                reject(error);
            });
    });
}



        // Event Listener radio
        document.querySelectorAll('input[name="action"]').forEach(radio =>
            radio.addEventListener("change", applyAction)
        );

        // Tombol DONE
        document.getElementById("doneButton").onclick = function () {
            const actionValue = document.querySelector('input[name="action"]:checked')?.value;

            if (actionValue === 'edit') {
                processEmployeeUpdates()
                    .then(() => {
                          const shiftCards = document.querySelectorAll('.shift-card');
                            const updateShiftPromises = Array.from(shiftCards).map(card => {
                                const shiftId = card.getAttribute('data-id');
                                return saveShiftChanges(shiftId);
                            });

                            Promise.all(updateShiftPromises)
                                .then(() => {
                                    showNotification("Data berhasil diperbarui.");
                                })
                                .catch(error => {
                                    console.error("Error updating shifts:", error);

                                });

                    })
                    .catch(error => {
                        console.error("Error updating employees:", error);
                        showNotification("Ada kesalahan saat memperbarui data.", false);
                    });
            }
            else if (actionValue === 'delete') {
                const selectedCards = document.querySelectorAll('.employee-card.selected');
                const deletedIds = [];

                selectedCards.forEach(card => {
                    const id = card.getAttribute('data-id');
                    if (id) {
                        deletedIds.push(id);
                    }
                });

                if (deletedIds.length > 0) {
                    fetch("/pegawai/delete-multiple", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ids: deletedIds })
                    }).then(response => {
                        if (response.ok) {
                            selectedCards.forEach(card => {
                                card.remove();
                            });
                            showNotification("Data berhasil dihapus.");
                        } else {
                            showNotification("Gagal menghapus data.", true);
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        showNotification("Terjadi kesalahan saat menghapus data.", true);
                    });
                } else {
                    showNotification("Pilih pegawai yang ingin dihapus.", true);
                }
            }
        };

        // Tombol CANCEL
        document.getElementById("cancelButton").onclick = function () {
            resetToNormalMode();
        };

        // Inisialisasi saat halaman load
        window.addEventListener('DOMContentLoaded', () => {
            document.getElementById("mainWrapper").style.display = "flex";
            loadShiftFromDatabase();
            loadEmployeeData();
        });
    </script>

</body>
</html>
