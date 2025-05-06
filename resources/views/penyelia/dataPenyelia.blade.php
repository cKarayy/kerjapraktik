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

        function applyAction() {
            const selectedAction = document.querySelector('input[name="action"]:checked');
            if (!selectedAction) {
                alert("Silakan pilih aksi terlebih dahulu.");
                return;
            }

            window.selectedActionValue = selectedAction.value;
            updateEmployeeCardUI(window.selectedActionValue);

            if (window.selectedActionValue === 'edit') {
                loadShiftFromDatabase(true); // tampilkan input shift
            } else {
                loadShiftFromDatabase(false);
            }
        }

        function updateEmployeeCardUI(actionValue) {
            const cards = document.querySelectorAll('.employee-card:not(.shift-card)');
            cards.forEach(card => {
                const statusBox = card.querySelector('.status-box');
                const shiftBox = card.querySelector('.shift-box');
                const cardActions = card.querySelector('.card-actions'); // Tombol aksi

                if (actionValue === 'edit') {
                    // Tampilkan input untuk status dan shift
                    cards.forEach(card => {
                        const currentStatus = card.querySelector('.status-box')?.textContent.trim().toLowerCase();
                        const currentShift = card.querySelector('.shift-box')?.textContent.trim();
                        const currentRole = card.querySelector('.employee-role')?.textContent.trim();
                        const shiftCards = document.querySelectorAll('.shift-card');

                        // Ganti status-box jadi dropdown
                        const statusBox = card.querySelector('.status-box');
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

                        // Ganti shift-box jadi dropdown
                        const shiftBox = card.querySelector('.shift-box');
                        if (shiftBox) {
                            const shiftSelect = document.createElement('select');
                            shiftSelect.classList.add('edit-shift');

                            shiftList.forEach(shift => {
                                const opt = document.createElement('option');
                                opt.value = shift.nama_shift;
                                opt.textContent = shift.nama_shift.toUpperCase();
                                if (shift.nama_shift.toUpperCase() === currentShift) opt.selected = true;
                                shiftSelect.appendChild(opt);
                            });
                            shiftBox.replaceWith(shiftSelect);
                        }

                        const rolePara = card.querySelector('.employee-role');
                        if (rolePara) {
                            const roleInput = document.createElement('input');
                            roleInput.type = 'text';
                            roleInput.classList.add('edit-role');
                            roleInput.value = currentRole;
                            rolePara.replaceWith(roleInput);
                        }

                        shiftCards.forEach(card => {
                            card.querySelector('.shift-text').style.display = 'none';
                            card.querySelector('.edit-shift-nama').style.display = 'inline';
                            card.querySelector('.edit-shift-masuk').style.display = 'inline';
                            card.querySelector('.edit-shift-keluar').style.display = 'inline';
                        });
                    });
                }

                // Menambahkan atau menghapus ikon sampah berdasarkan pilihan aksi
                if (actionValue === 'delete') {
                    if (!cardActions) {
                        // Jika tidak ada tombol aksi, buat dan tambahkan tombol sampah
                        const actionsDiv = document.createElement('div');
                        actionsDiv.classList.add('card-actions');
                        const deleteIcon = document.createElement('i');
                        deleteIcon.classList.add('fas', 'fa-trash', 'delete-icon');
                        // Menambahkan event listener ke delete icon
                        deleteIcon.addEventListener('click', function() { deleteEmployee(card); });
                        actionsDiv.appendChild(deleteIcon);
                        card.appendChild(actionsDiv);
                    }
                } else {
                    // Jika aksi bukan delete, sembunyikan ikon sampah
                    if (cardActions) {
                        cardActions.remove();
                    }
                }
            });
        }

        function selectCardForDeletion(card) {
            card.classList.toggle('selected');  // Menambahkan atau menghapus kelas 'selected'
        }

        function deleteEmployee(card) {
            const employeeId = card.getAttribute('data-id');

            if (employeeId) {
                // Cek apakah ID valid
                console.log(`Menghapus pegawai dengan ID: ${employeeId}`);

                fetch(`/pegawai/delete/${employeeId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (response.ok) {
                        // Jika penghapusan sukses, hapus card dari tampilan
                        card.remove();
                        alert("Pegawai berhasil dihapus.");

                        // Setelah penghapusan, reset tampilan
                        resetUIAfterDeletion();
                    } else {
                        // Menangani kesalahan dari server
                        alert(`Gagal menghapus pegawai. Status: ${response.status}`);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Terjadi kesalahan saat menghapus pegawai.");
                });
            } else {
                alert("ID pegawai tidak ditemukan.");
            }
        }

        function resetUIAfterDeletion() {
            // Mengembalikan aksi ke keadaan semula (tanpa tombol sampah)
            const selectedAction = document.querySelector('input[name="action"]:checked');
            if (selectedAction) {
                selectedAction.checked = false;
            }
            // Sembunyikan tombol sampah di setiap card pegawai
            const allCards = document.querySelectorAll('.employee-card');
            allCards.forEach(card => {
                const actionsDiv = card.querySelector('.card-actions');
                if (actionsDiv) {
                    actionsDiv.remove();
                }
            });
            // Reset status tampilan dan shift (misalnya, kembali ke data awal)
            loadEmployeeData();  // Memanggil kembali data pegawai dari server
        }

        function loadEmployeeData() {
            fetch('/pegawai/all')  // Ubah dengan URL yang sesuai untuk mengambil semua pegawai
                .then(response => response.json())
                .then(data => {
                    const employeeListContainer = document.getElementById('employeeList');
                    employeeListContainer.innerHTML = '';  // Menghapus semua card pegawai yang ada
                    data.forEach(employee => {
                        const card = document.createElement('div');
                        card.classList.add('employee-card');
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
                            <div class="shift-box">${employee.shift || '-'}</div>
                        `;
                        employeeListContainer.appendChild(card);
                    });
                })
                .catch(error => console.error('Error loading employee data:', error));
        }

        function loadShiftFromDatabase() {
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
                                    <h2 class="shift-name">
                                        <span class="shift-text">${shift.nama_shift}</span>
                                        <input class="edit-shift-nama" type="text" value="${shift.nama_shift}" style="display:none;">
                                    </h2>
                                    <p class="shift-time">
                                        <span class="shift-text">${shift.jam_masuk} - ${shift.jam_keluar}</span>
                                        <input class="edit-shift-masuk" type="time" value="${shift.jam_masuk}" style="display:none;">
                                        <input class="edit-shift-keluar" type="time" value="${shift.jam_keluar}" style="display:none;">
                                    </p>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                })
                .catch(error => console.error("Gagal memuat data shift:", error));
        }

        // Event Listener radio
        document.querySelectorAll('input[name="action"]').forEach(radio =>
            radio.addEventListener("change", applyAction)
        );

        document.getElementById("doneButton").onclick = function () {
            const actionValue = document.querySelector('input[name="action"]:checked')?.value;

            if (actionValue === 'edit') {
                const cards = document.querySelectorAll('.employee-card:not(.shift-card)');
                const updates = [];

                cards.forEach(card => {
                    const id = card.getAttribute('data-id');
                    const status = card.querySelector('.edit-status')?.value;
                    const shift = card.querySelector('.edit-shift')?.value;
                    const role = card.querySelector('.edit-role')?.value;

                    if (id && status && shift) {
                        updates.push({ id, status, shift, role});
                    }
                });

                updates.forEach(emp => {
                    fetch(`/pegawai/update/${emp.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            status: emp.status,
                            shift: emp.shift,
                            role: emp.role
                        }),
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Gagal memperbarui data.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(`Pegawai ID ${emp.id} berhasil diperbarui.`);

                        // Setelah update berhasil, perbarui semua employee card
                        const allCards = document.querySelectorAll('.employee-card:not(.shift-card)');
                        allCards.forEach(card => {
                            const cardId = card.getAttribute('data-id');

                            if (cardId === emp.id) {
                                // Perbarui tampilan status
                                const statusBox = card.querySelector('.status-box');
                                statusBox.innerHTML = `<span class="${emp.status === 'active' ? 'status-active' : 'status-resign'}">${emp.status.toUpperCase()}</span>`;
                                statusBox.style.display = 'block';

                                // Perbarui tampilan shift
                                const shiftBox = card.querySelector('.shift-box');
                                shiftBox.textContent = emp.shift.toUpperCase();
                                shiftBox.style.display = 'block';

                                // Sembunyikan input dropdown (edit mode)
                                if (statusSelect) {
                                    const newStatusDiv = document.createElement('div');
                                    newStatusDiv.className = `status-box ${emp.status === 'active' ? 'status-active' : 'status-resign'}`;
                                    newStatusDiv.textContent = emp.status.toUpperCase();
                                    statusSelect.replaceWith(newStatusDiv);
                                }

                                // Ganti dropdown shift menjadi div shift-box
                                if (shiftSelect) {
                                    const newShiftDiv = document.createElement('div');
                                    newShiftDiv.className = 'shift-box';
                                    newShiftDiv.textContent = emp.shift.toUpperCase();
                                    shiftSelect.replaceWith(newShiftDiv);
                                }

                                // Ganti input role menjadi p.role kembali
                                const roleInput = card.querySelector('.edit-role');
                                if (roleInput) {
                                    const newRoleP = document.createElement('p');
                                    newRoleP.className = 'employee-role';
                                    newRoleP.textContent = emp.role;
                                    roleInput.replaceWith(newRoleP);
                                }
                            }
                        });

                        // Sembunyikan semua elemen input di mode edit untuk semua card
                        const statusInputs = document.querySelectorAll('.edit-status');
                        const shiftInputs = document.querySelectorAll('.edit-shift');
                        statusInputs.forEach(input => input.style.display = 'none');
                        shiftInputs.forEach(input => input.style.display = 'none');

                        // Tampilkan elemen status dan shift yang baru di semua card
                        const statusElements = document.querySelectorAll('.status-box span');
                        statusElements.forEach(statusElement => {
                            statusElement.style.display = 'block';
                        });

                        const shiftElements = document.querySelectorAll('.shift-box');
                        shiftElements.forEach(shiftElement => {
                            shiftElement.style.display = 'block';
                        });

                    })
                    .catch(error => {
                        console.error(`Error update pegawai ID ${emp.id}:`, error);
                    });
                });

                alert('Semua data berhasil diproses.');

                 // Ambil data shift yang diedit
                 const shiftCards = document.querySelectorAll('.shift-card');
                shiftCards.forEach(card => {
                    const id = card.getAttribute('data-id');
                    const nama_shift = card.querySelector('.edit-shift-nama').value;
                    const jam_masuk = card.querySelector('.edit-shift-masuk').value;
                    const jam_keluar = card.querySelector('.edit-shift-keluar').value;

                    fetch(`/shifts/update/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ nama_shift, jam_masuk, jam_keluar })
                    })
                    .then(res => {
                        if (!res.ok) throw new Error("Gagal update shift");
                        return res.json();
                    })
                    .then(() => {
                        console.log(`Shift ${id} berhasil diperbarui`);
                    })
                    .catch(err => console.error(err));
                });
            }
            else if (actionValue === 'delete') {
            // Ambil semua card yang telah dipilih untuk dihapus
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
                        // Hapus card yang dipilih dari tampilan UI
                        selectedCards.forEach(card => {
                            card.remove();
                        });
                        alert("Data berhasil dihapus.");
                    } else {
                        alert("Gagal menghapus data.");
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert("Terjadi kesalahan saat menghapus data.");
                });
            } else {
                alert("Pilih pegawai yang ingin dihapus.");
            }
            }


        };

        // Tombol CANCEL
        document.getElementById("cancelButton").onclick = function () {
            location.reload();
        };

        // Inisialisasi saat halaman load
        window.addEventListener('DOMContentLoaded', () => {
            document.getElementById("mainWrapper").style.display = "flex";
            loadShiftFromDatabase();
        });
    </script>

</body>
</html>
