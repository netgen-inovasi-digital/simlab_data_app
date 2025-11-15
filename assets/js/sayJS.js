(function (window) {
    'use strict';
    class $ {
        constructor(selector) {
            if (typeof selector === 'function') {
                document.addEventListener('DOMContentLoaded', selector);
            } else this.elements = document.querySelectorAll(selector);
        } on(event, handler) {
            this.elements.forEach(el => {
                if (el) el.addEventListener(event, handler);
            }); return this;
        } val(value) {
            if (value !== undefined) {
                this.elements.forEach(el => {
                    if (el) el.value = value;
                }); return this;
            } else return this.elements.length > 0 ? this.elements[0].value : undefined;
        } text(newText) {
            if (newText !== undefined) {
                this.elements.forEach(element => {
                    element.textContent = newText;
                });
            } else return this.elements.length > 0 ? this.elements[0].textContent : null;
        } validate() {
            let isValid = true;
            this.elements.forEach(form => {
                if (!(form instanceof HTMLFormElement)) return;
                const inputs = Array.from(form.elements);
                inputs.forEach(el => {
                    if (!el) return; const value = el.value.trim();
                    if (el.hasAttribute('required') && value === '') {
                        isValid = false; this.showError(el, `* ${el.placeholder || el.name || el.id} tidak boleh kosong!`);
                    } if (el.type === 'email' && value !== '' && !this.isValidEmail(value)) {
                        isValid = false; this.showError(el, '* alamat email tidak sesuai!');
                    } if (el.hasAttribute('minlength') && value.length < el.getAttribute('minlength')) {
                        isValid = false; this.showError(el, `* minimum adalah ${el.getAttribute('minlength')}`);
                    } if (el.hasAttribute('maxlength') && value.length > el.getAttribute('maxlength')) {
                        isValid = false; this.showError(el, `* maksimum adalah ${el.getAttribute('maxlength')}`);
                    } if (el.type === 'number') {
                        const numberValue = parseFloat(value);
                        const min = el.hasAttribute('min') ? parseFloat(el.getAttribute('min')) : null;
                        const max = el.hasAttribute('max') ? parseFloat(el.getAttribute('max')) : null;
                        if (isNaN(numberValue)) {
                            isValid = false; this.showError(el, '* tidak boleh kosong! harus angka!');
                        } else {
                            if (min !== null && numberValue < min) {
                                isValid = false; this.showError(el, `* harus lebih besar atau sama dengan ${min}`);
                            }
                            if (max !== null && numberValue > max) {
                                isValid = false; this.showError(el, `* harus kurang dari atau sama dengan ${max}`);
                            }
                        }
                    } if (el.hasAttribute('equalto')) {
                        const equalToSelector = el.getAttribute('equalto');
                        const equalToElement = document.querySelector(equalToSelector);
                        if (equalToElement && equalToElement.value.trim() !== value) {
                            isValid = false;
                            this.showError(el, `* harus sama dengan ${equalToElement.placeholder || equalToElement.name || equalToElement.id}`);
                        }
                    }
                    if (isValid) this.clearError(el);
                });
            }); return isValid;
        } showError(el, message) {
            let errorElement = el.nextElementSibling;
            if (!errorElement || !errorElement.classList.contains('error')) {
                errorElement = document.createElement('div');
                errorElement.classList.add('error');
                errorElement.style.color = 'red';
                errorElement.style.fontSize = '12px';
                el.parentNode.insertBefore(errorElement, el.nextSibling);
            } errorElement.textContent = message;
        } clearError(el) {
            const errorElement = el.nextElementSibling;
            if (errorElement && errorElement.classList.contains('error')) {
                errorElement.remove();
            }
        } isValidEmail(email) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        }
        submit(handler) {
            this.elements.forEach(form => {
                if (form instanceof HTMLFormElement) {
                    form.addEventListener('submit', (event) => {
                        event.preventDefault();
                        const isValid = this.validate();
                        if (isValid) {
                            //handler(); 
                            save(this.elements[0]);
                        }
                        Array.from(form.elements).forEach(input => {
                            input.addEventListener('input', () => {
                                this.clearError(input);
                            });
                        });
                    });
                }
            }); return this;
        }
        modal(action) {
            if (this.elements.length > 0) {
                const modalElement = this.elements[0];
                let modal = bootstrap.Modal.getInstance(modalElement);
                if (!modal) modal = new bootstrap.Modal(modalElement);
                action === 'show' ? modal.show() : modal.hide();
            }
        }
        hasClass(className) {
            if (this.elements.length === 0) return false;
            return this.elements[0].classList.contains(className);
        }
    }
    window.$ = (el) => new $(el);
})(window);

function addAction() {
    $('#add').on('click', () => {
        const form = document.getElementById('myform');
        const errorDivs = form.querySelectorAll('.error');
        errorDivs.forEach(errorDiv => { errorDiv.remove(); });
        form.reset();
        // Kosongkan input file (jika ada)
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(fileInput => fileInput.value = '');
        // Kosongkan selectSearch (jika ada)
        document.querySelectorAll('select').forEach(el => {
            if (el.id != "items-per-page") el.value = "";
            const wrapper = el.parentElement.querySelector('.selected');
            if (wrapper) wrapper.textContent = "-- pilih data --";
        });
        document.querySelector('[name="id"]').value = '';
        $('.modal-title').text('Tambah Data');
        $('#modalForm').modal('show');
    })
    $('#myform').submit();
}

function save(form) {
    showLoading();
    const formData = new FormData(form);
    const url = form.getAttribute('action');
    fetch(url, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            $('[name=' + data.xname + ']').val(data.xhash);
            if ($('#modalForm').hasClass('show')) $('#modalForm').modal('hide');
            if (data.res == true) {
                if (table) table.fetchData({ reload: true });
                sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
            } else if (data.res == 'reload') {
                sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
            } else if (data.res == 'refresh') {
                loadContent(data.link);
                sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
            } else if (data.res == 'redirect') {
                window.location.href = data.link;
            } else if (data.res == 'check') {
                sayAlert('errorModal', 'Error', data.link, 'warning');
            } else if (data.res == 'refresh-print') {
                loadContent(data.link);
                window.open(data.print, "_blank");
            }
            else sayAlert('errorModal', 'Error', 'Data gagal disimpan.', 'warning');
        })
        .catch(error => {
            sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
        }).finally(() => {
            hideLoading();
        });
}

function editItem(event) {
    const closest = event.target.closest('div');
    if (closest) {
        showLoading();
        const id = closest.getAttribute('id');
        const fullURL = window.location.href;
        const baseURL = fullURL.substring(0, fullURL.lastIndexOf('/') + 1) + currentUrl;
        const url = baseURL + '/edit/' + id;
        fetch(url, {
            method: 'GET',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    $('.modal-title').text('Ubah Data');
                    $('#modalForm').modal('show');
                    // console.log(data);
                    Object.entries(data).forEach(([key, value]) => {
                        const elements = document.querySelectorAll(`[name="${key}"],[name="${key}[]"]`);
                        if (elements.length > 0) {
                            elements.forEach(el => {
                                if (el.type === "checkbox" || el.type === "radio") {
                                    if (el.type === "checkbox") {
                                        if (Array.isArray(value)) {
                                            el.checked = value.includes(el.value);
                                        } else el.checked = value === "true" || value === "1" || value === true || value === el.value;
                                    } else if (el.type === "radio") el.checked = el.value === value;
                                } else if (el.tagName === "SELECT") {
                                    el.value = value || "";
                                    const wrapper = el.parentElement.querySelector('.selected');
                                    if (wrapper) {
                                        const option = Array.from(el.options).find(opt => opt.value === value);
                                        wrapper.textContent = option ? option.text : "-- pilih data --";
                                    }
                                } else el.value = value || "";
                            });
                        }
                    });
                }
            }).catch(error => {
                sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
            }).finally(() => {
                setTimeout(() => { hideLoading(); }, 300);
            });
    }
}

function deleteItem(event, msg = "") {
    const closest = event.target.closest('div');
    if (msg != "") msg = '<br><strong>' + msg + '</strong>';
    if (closest) {
        sayAlert('confirmModal', 'Confirm!', 'Apakah yakin menghapus data ini?' + msg, 'danger', true, () => {
            showLoading();
            const id = closest.getAttribute('id');
            const fullURL = window.location.href;
            const baseURL = fullURL.substring(0, fullURL.lastIndexOf('/') + 1) + currentUrl;
            const url = baseURL + '/delete/' + id;
            fetch(url, {
                method: 'GET',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.res == 'refresh') {
                        loadContent(data.link);
                        sayAlert('successModal', 'Success', 'Data berhasil dihapus.', 'success');
                    } else if (data.res == true) {
                        table.fetchData({ reload: true });
                        $('[name=' + data.xname + ']').val(data.xhash);
                        sayAlert('successModal', 'Success', 'Data berhasil dihapus.', 'success');
                    } else sayAlert('errorModal', 'Error', 'Data gagal dihapus.', 'warning');
                }).catch(error => {
                    sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.' + error.message, 'warning');
                }).finally(() => {
                    hideLoading();
                });
        });
    }
}

function showLoading() {
    let overlay = document.getElementById('loadingOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.style.display = 'flex';
        overlay.innerHTML = `
                <div class="spinner"></div>
            `;
        document.body.appendChild(overlay);
    }
    overlay.style.display = 'flex';
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

// GLOBAL LISTENER - KEYDOWN AGAR TIDAK BENTROK
let activeKeydownListener = null;
function setKeydownListener(listener) {
    // Hapus listener aktif sebelumnya
    if (activeKeydownListener) {
        document.removeEventListener("keydown", activeKeydownListener);
    }
    // Tambahkan listener baru
    document.addEventListener("keydown", listener);
    activeKeydownListener = listener;
}

function formatCurrency(input, useDecimal) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value.charAt(0) === '0')
        value = value.slice(1); // Hapus '0' pertama
    if (value) {
        if (useDecimal) {
            value = (parseFloat(value) / 100).toFixed(2).replace('.', ',')
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        } else value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    if (!value) {
        value = useDecimal ? '0,00' : '0';
    } input.value = value ? value : '';
}

function formatDecimal(input) {
    let value = input.value.replace(/[^0-9,]/g, '');
    if (!value) return (input.value = '0');
    if (value.startsWith('0,') || value === '0,') {
        input.value = value; return;
    }
    if (value.startsWith('0') && value.length > 1 && value[1] !== ',')
        value = value.replace(/^0+/, '');
    let [num, dec] = value.split(',');
    num = num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    input.value = dec !== undefined ? `${num},${dec.substring(0, 2)}` : num;
}

//dropdown dengan search
function selectSearch(selector) {
    const select = document.querySelector(selector);
    if (!select) return;

    // Buat wrapper untuk dropdown
    const wrapper = document.createElement("div");
    wrapper.className = "position-relative w-100";
    // Sembunyikan elemen select asli
    select.style.display = "none";
    // Buat elemen baru untuk meniru dropdown
    const customSelect = document.createElement("div");
    customSelect.className = "form-select position-relative";
    customSelect.style.cursor = "pointer";
    // Buat elemen yang menampilkan pilihan saat ini
    const selected = document.createElement("div");
    selected.className = "selected";
    selected.textContent = "-- pilih data --";
    // Buat elemen container dropdown
    const dropdownContainer = document.createElement("div");
    dropdownContainer.className = "dropdown-menu w-100 p-2";
    dropdownContainer.style.position = "absolute";
    dropdownContainer.style.top = "100%";
    dropdownContainer.style.left = "0";
    dropdownContainer.style.zIndex = "1050";
    dropdownContainer.style.display = "none";
    dropdownContainer.style.maxHeight = "250px";  // Batas tinggi maksimum (5 baris)
    dropdownContainer.style.overflowY = "auto";  // Tambahkan scroll jika melebihi batas tinggi
    dropdownContainer.style.fontSize = "0.9rem";

    // Buat input pencarian
    const searchInput = document.createElement("input");
    searchInput.type = "text";
    searchInput.className = "form-control mb-2";
    searchInput.placeholder = "Search...";

    // Buat dropdown opsi
    const dropdown = document.createElement("ul");
    dropdown.className = "list-unstyled m-0";

    // Masukkan opsi ke dropdown
    const options = Array.from(select.options);
    options.forEach((option) => {
        const li = document.createElement("li");
        li.className = "dropdown-item text-wrap";
        li.textContent = option.text;
        li.dataset.value = option.value;
        li.style.cursor = "pointer";
        li.addEventListener("click", () => {
            select.value = option.value;  // Perbarui nilai select asli
            selected.textContent = option.text;
            dropdownContainer.style.display = "none";
            // Memastikan form mendeteksi perubahan nilai
            select.dispatchEvent(new Event("change"));  // Trigger perubahan
        });
        dropdown.appendChild(li);
    });

    // Event pencarian
    searchInput.addEventListener("input", (e) => {
        const searchTerm = e.target.value.toLowerCase();
        dropdown.innerHTML = ""; // Hapus isi lama
        // Filter opsi dengan pencarian
        const filteredOptions = options.filter((option) =>
            option.value !== "" && option.text.toLowerCase().includes(searchTerm) // Mengabaikan value=""
        );
        // Menambahkan opsi yang sudah difilter ke dropdown
        filteredOptions.forEach((option) => {
            const li = document.createElement("li");
            li.className = "dropdown-item text-wrap";
            li.textContent = option.text;
            li.dataset.value = option.value;
            li.style.cursor = "pointer";
            li.addEventListener("click", () => {
                select.value = option.value;  // Perbarui nilai select asli
                selected.textContent = option.text;
                dropdownContainer.style.display = "none";
                select.dispatchEvent(new Event("change"));  // Trigger perubahan
            });
            dropdown.appendChild(li);
        });
        // Jika tidak ada hasil pencarian, tampilkan opsi dengan value=""
        if (filteredOptions.length === 0 && searchTerm !== "") {
            const li = document.createElement("li");
            li.className = "dropdown-item disabled";
            li.textContent = "No results found";
            dropdown.appendChild(li);
        }
    });

    // Event untuk membuka dan menutup dropdown
    selected.addEventListener("click", () => {
        dropdownContainer.style.display =
            dropdownContainer.style.display === "block" ? "none" : "block";
        searchInput.value = ""; // Reset input pencarian
        dropdown.innerHTML = ""; // Reset dropdown
        options.forEach((option) => {
            const li = document.createElement("li");
            li.className = "dropdown-item text-wrap";
            li.textContent = option.text;
            li.dataset.value = option.value;
            li.style.cursor = "pointer";
            li.addEventListener("click", () => {
                select.value = option.value;  // Perbarui nilai select asli
                selected.textContent = option.text;
                dropdownContainer.style.display = "none";
                select.dispatchEvent(new Event("change"));  // Trigger perubahan

                //hapus pesan error
                const errorElement = select.nextElementSibling;
                if (errorElement && errorElement.classList.contains('error'))
                    errorElement.remove();
            });
            dropdown.appendChild(li);
        });
    });

    // Event untuk menutup dropdown saat klik di luar
    document.addEventListener("click", (e) => {
        if (!wrapper.contains(e.target)) {
            dropdownContainer.style.display = "none";
        }
    });

    // Susun elemen ke dalam wrapper
    dropdownContainer.appendChild(searchInput);
    dropdownContainer.appendChild(dropdown);
    customSelect.appendChild(selected);
    wrapper.appendChild(customSelect);
    wrapper.appendChild(dropdownContainer);
    // Ganti select asli dengan wrapper
    select.parentNode.insertBefore(wrapper, select);

    // Memastikan elemen select asli memiliki nilai saat form submit
    if (select.form) {
        select.form.addEventListener("submit", function () {
            // Cek apakah elemen select memiliki nilai, jika tidak beri peringatan
            if (!select.value && select.hasAttribute("required")) return false;
        });
    }
}

function sayAlert(modalId, title, message, type, isConfirm = false, confirmCallback = null, confirmButtonText = 'Ya') {
    let modalElement = document.getElementById(modalId);
    if (!modalElement) {
        const modalHtml = `
              <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
                <div class="modal-dialog" style="margin: 5% auto">
                  <div class="modal-content">
                    <div class="modal-header bg-${type} text-white">
                      <h5 class="modal-title" id="${modalId}Label">${title}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="sayalert d-flex align-items-stretch">
                            <div class="me-3 d-flex align-items-center text-success">
                                ${type === 'success' ? `<i class="bi bi-check-circle icon"></i>`
                : (type === 'danger') ? `<i class="bi bi-patch-question icon"></i>`
                    : `<i class="bi bi-x-circle icon"></i>`
            }
                            </div>
                            <div class="flex-grow-1 align-self-center">
                                <div class="alert-title">${title}</div>
                                <div class="alert-subtitle">${message}</div>
                            </div>
                            <div class="button-container justify-content-center">
                            ${isConfirm
                ? `<button type="button" class="btn btn-sm btn-${type} ms-4 me-2" id="${modalId}ConfirmButton">${confirmButtonText}</button>
                                <div class="button-divider"></div>
                                <button type="button" class="btn btn-sm btn-light ms-4 me-2" data-bs-dismiss="modal">Batal</button>`
                : `<button type="button" class="btn btn-sm btn-${type} ms-4 me-2" data-bs-dismiss="modal">(<span id="${modalId}Timer">5</span>)</button>`
            }
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        modalElement = document.getElementById(modalId);
    }

    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalInstance.show();
    modalElement.addEventListener('hidden.bs.modal', () => {
        modalElement.remove();
    });

    if (!isConfirm) {
        startTimer(`${modalId}Timer`, modalInstance);
    } else {
        const confirmButton = document.getElementById(`${modalId}ConfirmButton`);
        if (confirmButton && confirmCallback) {
            confirmButton.onclick = () => {
                confirmCallback();
                modalInstance.hide();
            };
        }
    }
}

function startTimer(timerId, modalInstance) {
    let countdown = 5;
    const timerElement = document.getElementById(timerId);
    timerElement.innerText = countdown;
    const interval = setInterval(() => {
        countdown--;
        timerElement.innerText = countdown;
        if (countdown <= 0) {
            clearInterval(interval);
            modalInstance.hide();
        }
    }, 1000);

    document.addEventListener('keydown', function onKeyPress(event) {
        if (event.key === 'Enter') {
            clearInterval(interval);
            modalInstance.hide();
            document.removeEventListener('keydown', onKeyPress);
        }
    });
}