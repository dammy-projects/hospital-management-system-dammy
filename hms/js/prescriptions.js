class PrescriptionManager {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.limit = 6;
        this.isEditing = false;
        this.currentPrescriptionId = null;
        this.currentPrescriptionData = null; // For storing prescription data for printing
        this.patients = [];
        this.doctors = [];
        this.medicines = [];
        this.medicineItemCount = 0;
        
        this.init();
    }
    
    init() {
        this.loadDropdownData();
        this.loadPrescriptions();
        this.bindEvents();
    }
    
    bindEvents() {
        // Modal events
        document.getElementById('addPrescriptionBtn').addEventListener('click', () => this.openModal());
        document.getElementById('closeModal').addEventListener('click', () => this.closeModal());
        document.getElementById('cancelModal').addEventListener('click', () => this.closeModal());
        document.getElementById('savePrescription').addEventListener('click', () => this.savePrescription());
        
        // View modal events
        document.getElementById('closeViewModal').addEventListener('click', () => this.closeViewModal());
        document.getElementById('closeViewModalBtn').addEventListener('click', () => this.closeViewModal());
        
        // Delete modal events
        document.getElementById('closeDeleteModal').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('cancelDelete').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('confirmDelete').addEventListener('click', () => this.deletePrescription());
        
        // Search and filter events
        document.getElementById('searchInput').addEventListener('input', this.debounce(() => this.searchPrescriptions(), 300));
        document.getElementById('statusFilter').addEventListener('change', () => this.searchPrescriptions());
        document.getElementById('patientFilter').addEventListener('change', () => this.searchPrescriptions());
        document.getElementById('clearFilters').addEventListener('click', () => this.clearFilters());
        
        // Medicine management
        document.getElementById('addMedicineBtn').addEventListener('click', () => this.addMedicineItem());
        
        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => this.goToPage(this.currentPage - 1));
        document.getElementById('nextPage').addEventListener('click', () => this.goToPage(this.currentPage + 1));
        
        // Close modals when clicking background
        document.getElementById('prescriptionModal').querySelector('.modal-background').addEventListener('click', () => this.closeModal());
        document.getElementById('viewModal').querySelector('.modal-background').addEventListener('click', () => this.closeViewModal());
        document.getElementById('deleteModal').querySelector('.modal-background').addEventListener('click', () => this.closeDeleteModal());
        
        // Notification close
        document.getElementById('closeNotification').addEventListener('click', () => this.hideNotification());
    }
    
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    async loadDropdownData() {
        try {
            const [patientsResponse, doctorsResponse, medicinesResponse] = await Promise.all([
                fetch('controllers/PrescriptionController.php?action=patients'),
                fetch('controllers/PrescriptionController.php?action=doctors'),
                fetch('controllers/PrescriptionController.php?action=medicines')
            ]);
            
            const [patientsResult, doctorsResult, medicinesResult] = await Promise.all([
                patientsResponse.json(),
                doctorsResponse.json(),
                medicinesResponse.json()
            ]);
            
            if (patientsResult.success) {
                this.patients = patientsResult.data;
                this.populatePatientDropdowns();
            }
            
            if (doctorsResult.success) {
                this.doctors = doctorsResult.data;
                this.populateDoctorDropdowns();
            }
            
            if (medicinesResult.success) {
                this.medicines = medicinesResult.data;
            }
            
        } catch (error) {
            console.error('Error loading dropdown data:', error);
        }
    }
    
    populatePatientDropdowns() {
        // Populate main patient dropdown
        const patientSelect = document.getElementById('patientId');
        patientSelect.innerHTML = '<option value="">Select Patient</option>';
        
        // Populate filter dropdown
        const filterSelect = document.getElementById('patientFilter');
        filterSelect.innerHTML = '<option value="">All Patients</option>';
        
        this.patients.forEach(patient => {
            const option1 = document.createElement('option');
            option1.value = patient.patient_id;
            option1.textContent = patient.patient_name;
            patientSelect.appendChild(option1);
            
            const option2 = document.createElement('option');
            option2.value = patient.patient_id;
            option2.textContent = patient.patient_name;
            filterSelect.appendChild(option2);
        });
    }
    
    populateDoctorDropdowns() {
        const doctorSelect = document.getElementById('doctorId');
        doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
        
        this.doctors.forEach(doctor => {
            const option = document.createElement('option');
            option.value = doctor.doctor_id;
            option.textContent = `${doctor.doctor_name} (${doctor.specialty})`;
            doctorSelect.appendChild(option);
        });
    }
    
    populateMedicineSelect(selectElement) {
        selectElement.innerHTML = '<option value="">Select Medicine</option>';
        
        this.medicines.forEach(medicine => {
            const option = document.createElement('option');
            option.value = medicine.medicine_id;
            option.textContent = `${medicine.medicine_name} (${medicine.dosage_form} - ${medicine.strength})`;
            selectElement.appendChild(option);
        });
    }
    
    async loadPrescriptions() {
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                action: 'list',
                page: this.currentPage,
                limit: this.limit,
                search: document.getElementById('searchInput').value,
                status_filter: document.getElementById('statusFilter').value,
                patient_filter: document.getElementById('patientFilter').value
            });
            
            const response = await fetch(`controllers/PrescriptionController.php?${params}`);
            const result = await response.json();
            
            if (result.success) {
                this.displayPrescriptions(result.data);
                this.updatePagination(result.pagination);
                this.hideLoading();
                
                if (result.data.length === 0) {
                    this.showEmptyState();
                } else {
                    this.hideEmptyState();
                }
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading prescriptions:', error);
            this.hideLoading();
            this.showNotification('Error loading prescriptions: ' + error.message, 'danger');
        }
    }
    
    displayPrescriptions(prescriptions) {
        const grid = document.getElementById('prescriptionsGrid');
        grid.innerHTML = '';
        
        prescriptions.forEach(prescription => {
            const prescriptionCard = this.createPrescriptionCard(prescription);
            grid.appendChild(prescriptionCard);
        });
    }
    
    createPrescriptionCard(prescription) {
        const column = document.createElement('div');
        column.className = 'column is-4';
        
        const statusBadgeClass = this.getStatusBadgeClass(prescription.status);
        const formattedDate = new Date(prescription.prescription_date).toLocaleDateString();
        
        column.innerHTML = `
            <div class="card">
                <div class="card-content">
                    <div class="content">
                        <div class="level">
                            <div class="level-left">
                                <div class="level-item">
                                    <h6 class="title is-6">${this.escapeHtml(prescription.patient_name || 'Unknown Patient')}</h6>
                                </div>
                            </div>
                            <div class="level-right">
                                <div class="level-item">
                                    <span class="tag ${statusBadgeClass} status-badge">
                                        ${this.escapeHtml(prescription.status || 'Unknown')}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <p class="subtitle is-7">
                            <strong>Dr:</strong> ${this.escapeHtml(prescription.doctor_name || 'Unknown Doctor')}
                            ${prescription.doctor_specialty ? `<br><small>(${this.escapeHtml(prescription.doctor_specialty)})</small>` : ''}
                        </p>
                        <p class="medicine-count">
                            <i class="fas fa-pills"></i> 
                            <strong>${prescription.total_medicines || 0}</strong> medicine(s)
                        </p>
                        <p class="has-text-grey is-size-7">
                            <i class="fas fa-calendar"></i> ${formattedDate}
                        </p>
                        ${prescription.notes ? `<p class="is-size-7 mt-2"><em>"${this.escapeHtml(prescription.notes.substring(0, 60))}${prescription.notes.length > 60 ? '...' : ''}"</em></p>` : ''}
                    </div>
                </div>
                <footer class="card-footer">
                    <a class="card-footer-item has-text-link" onclick="prescriptionManager.viewPrescription(${prescription.prescription_id})">
                        <span class="icon is-small">
                            <i class="fas fa-eye"></i>
                        </span>
                        <span>View</span>
                    </a>
                    <a class="card-footer-item has-text-success" onclick="prescriptionManager.printPrescriptionFromCard(${prescription.prescription_id})">
                        <span class="icon is-small">
                            <i class="fas fa-print"></i>
                        </span>
                        <span>Print</span>
                    </a>
                    <a class="card-footer-item has-text-info" onclick="prescriptionManager.editPrescription(${prescription.prescription_id})">
                        <span class="icon is-small">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit</span>
                    </a>
                    <a class="card-footer-item has-text-danger" onclick="prescriptionManager.confirmDelete(${prescription.prescription_id}, '${this.escapeHtml(prescription.patient_name || 'Unknown Patient')}')">
                        <span class="icon is-small">
                            <i class="fas fa-trash"></i>
                        </span>
                        <span>Delete</span>
                    </a>
                </footer>
            </div>
        `;
        
        return column;
    }
    
    getStatusBadgeClass(status) {
        const statusClassMap = {
            'active': 'is-success',
            'fulfilled': 'is-info',
            'cancelled': 'is-danger'
        };
        
        return statusClassMap[status] || 'is-light';
    }
    
    openModal(prescription = null) {
        const modal = document.getElementById('prescriptionModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('prescriptionForm');
        
        form.reset();
        this.clearMedicineItems();
        
        if (prescription) {
            this.isEditing = true;
            this.currentPrescriptionId = prescription.prescription_id;
            modalTitle.textContent = 'Edit Prescription';
            
            // Populate form
            document.getElementById('prescriptionId').value = prescription.prescription_id;
            document.getElementById('patientId').value = prescription.patient_id;
            document.getElementById('doctorId').value = prescription.doctor_id;
            document.getElementById('prescriptionDate').value = prescription.prescription_date.split(' ')[0];
            document.getElementById('status').value = prescription.status;
            document.getElementById('notes').value = prescription.notes || '';
            
            // Add medicine items
            if (prescription.items && prescription.items.length > 0) {
                prescription.items.forEach(item => {
                    this.addMedicineItem(item);
                });
            } else {
                this.addMedicineItem(); // Add one empty item
            }
        } else {
            this.isEditing = false;
            this.currentPrescriptionId = null;
            modalTitle.textContent = 'New Prescription';
            
            // Set default date to today
            document.getElementById('prescriptionDate').value = new Date().toISOString().split('T')[0];
            
            // Add one empty medicine item
            this.addMedicineItem();
        }
        
        modal.classList.add('is-active');
    }
    
    closeModal() {
        const modal = document.getElementById('prescriptionModal');
        modal.classList.remove('is-active');
        this.isEditing = false;
        this.currentPrescriptionId = null;
        this.clearMedicineItems();
    }
    
    addMedicineItem(itemData = null) {
        const template = document.getElementById('medicineItemTemplate');
        const container = document.getElementById('medicinesContainer');
        const clone = template.content.cloneNode(true);
        
        this.medicineItemCount++;
        
        // Update medicine number
        clone.querySelector('.medicine-number').textContent = this.medicineItemCount;
        
        // Populate medicine dropdown
        const medicineSelect = clone.querySelector('.medicine-select');
        this.populateMedicineSelect(medicineSelect);
        
        // Populate data if editing
        if (itemData) {
            medicineSelect.value = itemData.medicine_id;
            clone.querySelector('.dosage-input').value = itemData.dosage;
            clone.querySelector('.frequency-select').value = itemData.frequency;
            clone.querySelector('.duration-input').value = itemData.duration_days;
            clone.querySelector('.quantity-input').value = itemData.quantity;
            clone.querySelector('.instructions-input').value = itemData.instructions || '';
        }
        
        // Add remove event listener
        clone.querySelector('.remove-medicine-btn').addEventListener('click', (e) => {
            e.target.closest('.medicine-item').remove();
            this.updateMedicineNumbers();
        });
        
        container.appendChild(clone);
    }
    
    clearMedicineItems() {
        document.getElementById('medicinesContainer').innerHTML = '';
        this.medicineItemCount = 0;
    }
    
    updateMedicineNumbers() {
        const medicineItems = document.querySelectorAll('.medicine-item');
        medicineItems.forEach((item, index) => {
            item.querySelector('.medicine-number').textContent = index + 1;
        });
        this.medicineItemCount = medicineItems.length;
    }
    
    collectMedicineItems() {
        const medicineItems = document.querySelectorAll('.medicine-item');
        const items = [];
        
        medicineItems.forEach(item => {
            const medicineId = item.querySelector('.medicine-select').value;
            const dosage = item.querySelector('.dosage-input').value;
            const frequency = item.querySelector('.frequency-select').value;
            const duration = item.querySelector('.duration-input').value;
            const quantity = item.querySelector('.quantity-input').value;
            const instructions = item.querySelector('.instructions-input').value;
            
            if (medicineId && dosage && frequency && duration && quantity) {
                items.push({
                    medicine_id: parseInt(medicineId),
                    dosage: dosage.trim(),
                    frequency: frequency,
                    duration_days: parseInt(duration),
                    quantity: parseInt(quantity),
                    instructions: instructions.trim()
                });
            }
        });
        
        return items;
    }
    
    async savePrescription() {
        const form = document.getElementById('prescriptionForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const items = this.collectMedicineItems();
        if (items.length === 0) {
            this.showNotification('Please add at least one medicine to the prescription', 'warning');
            return;
        }
        
        const prescriptionData = {
            patient_id: parseInt(document.getElementById('patientId').value),
            doctor_id: parseInt(document.getElementById('doctorId').value),
            prescription_date: document.getElementById('prescriptionDate').value,
            status: document.getElementById('status').value,
            notes: document.getElementById('notes').value.trim(),
            items: items
        };
        
        try {
            let url = 'controllers/PrescriptionController.php?action=';
            let method = 'POST';
            
            if (this.isEditing) {
                url += `update&id=${this.currentPrescriptionId}`;
                method = 'PUT';
                prescriptionData.prescription_id = this.currentPrescriptionId;
            } else {
                url += 'create';
            }
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(prescriptionData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.closeModal();
                this.loadPrescriptions();
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error saving prescription:', error);
            this.showNotification('Error saving prescription: ' + error.message, 'danger');
        }
    }
    
    async editPrescription(prescriptionId) {
        try {
            const response = await fetch(`controllers/PrescriptionController.php?action=get&id=${prescriptionId}`);
            const result = await response.json();
            
            if (result.success) {
                this.openModal(result.data);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading prescription:', error);
            this.showNotification('Error loading prescription: ' + error.message, 'danger');
        }
    }
    
    async viewPrescription(prescriptionId) {
        try {
            const response = await fetch(`controllers/PrescriptionController.php?action=get&id=${prescriptionId}`);
            const result = await response.json();
            
            if (result.success) {
                this.currentPrescriptionData = result.data; // Store for printing
                this.displayPrescriptionDetails(result.data);
                document.getElementById('viewModal').classList.add('is-active');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading prescription:', error);
            this.showNotification('Error loading prescription: ' + error.message, 'danger');
        }
    }
    
    displayPrescriptionDetails(prescription) {
        const detailsContainer = document.getElementById('prescriptionDetails');
        const formattedDate = new Date(prescription.prescription_date).toLocaleDateString();
        const statusBadgeClass = this.getStatusBadgeClass(prescription.status);
        
        let medicinesHtml = '';
        if (prescription.items && prescription.items.length > 0) {
            prescription.items.forEach((item, index) => {
                medicinesHtml += `
                    <div class="box">
                        <h6 class="subtitle is-6">${index + 1}. ${this.escapeHtml(item.medicine_name || 'Unknown Medicine')}</h6>
                        <div class="columns is-mobile">
                            <div class="column">
                                <strong>Dosage:</strong> ${this.escapeHtml(item.dosage || 'N/A')}
                            </div>
                            <div class="column">
                                <strong>Frequency:</strong> ${this.escapeHtml(item.frequency || 'N/A')}
                            </div>
                        </div>
                        <div class="columns is-mobile">
                            <div class="column">
                                <strong>Duration:</strong> ${item.duration_days || 'N/A'} days
                            </div>
                            <div class="column">
                                <strong>Quantity:</strong> ${item.quantity || 'N/A'}
                            </div>
                        </div>
                        ${item.instructions ? `<p><strong>Instructions:</strong> ${this.escapeHtml(item.instructions)}</p>` : ''}
                    </div>
                `;
            });
        } else {
            medicinesHtml = '<p class="has-text-grey">No medicines prescribed.</p>';
        }
        
        detailsContainer.innerHTML = `
            <div class="content">
                <div class="level">
                    <div class="level-left">
                        <div class="level-item">
                            <h4 class="title is-4">${this.escapeHtml(prescription.patient_name || 'Unknown Patient')}</h4>
                        </div>
                    </div>
                    <div class="level-right">
                        <div class="level-item">
                            <span class="tag ${statusBadgeClass} is-medium">
                                ${this.escapeHtml(prescription.status || 'Unknown')}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="columns">
                    <div class="column is-6">
                        <p><strong>Doctor:</strong> ${this.escapeHtml(prescription.doctor_name || 'Unknown Doctor')}</p>
                        ${prescription.doctor_specialty ? `<p><strong>Specialty:</strong> ${this.escapeHtml(prescription.doctor_specialty)}</p>` : ''}
                    </div>
                    <div class="column is-6">
                        <p><strong>Date:</strong> ${formattedDate}</p>
                        <p><strong>Total Medicines:</strong> ${prescription.items ? prescription.items.length : 0}</p>
                    </div>
                </div>
                
                ${prescription.notes ? `<div class="notification is-info is-light"><strong>Notes:</strong> ${this.escapeHtml(prescription.notes)}</div>` : ''}
                
                <hr>
                
                <h5 class="title is-5">Prescribed Medicines</h5>
                ${medicinesHtml}
            </div>
        `;
    }
    
    closeViewModal() {
        document.getElementById('viewModal').classList.remove('is-active');
        this.currentPrescriptionData = null; // Clear stored data
    }
    
    async printPrescriptionFromCard(prescriptionId) {
        try {
            const response = await fetch(`controllers/PrescriptionController.php?action=get&id=${prescriptionId}`);
            const result = await response.json();
            
            if (result.success) {
                this.generatePrintContent(result.data);
                window.print();
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading prescription for printing:', error);
            this.showNotification('Error loading prescription: ' + error.message, 'danger');
        }
    }
    
    printPrescription() {
        // Get the prescription data from the currently viewed prescription
        const detailsContainer = document.getElementById('prescriptionDetails');
        const prescriptionData = this.currentPrescriptionData; // We need to store this when viewing
        
        if (!prescriptionData) {
            this.showNotification('No prescription data available for printing', 'warning');
            return;
        }
        
        this.generatePrintContent(prescriptionData);
        window.print();
    }
    
    generatePrintContent(prescription) {
        const printArea = document.getElementById('prescriptionPrintArea');
        const formattedDate = new Date(prescription.prescription_date).toLocaleDateString();
        const currentDate = new Date().toLocaleDateString();
        
        let medicinesHtml = '';
        if (prescription.items && prescription.items.length > 0) {
            medicinesHtml = `
                <table class="medicines-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Medicine Name</th>
                            <th style="width: 15%;">Dosage</th>
                            <th style="width: 15%;">Frequency</th>
                            <th style="width: 10%;">Duration</th>
                            <th style="width: 10%;">Qty</th>
                            <th style="width: 25%;">Instructions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            prescription.items.forEach((item, index) => {
                medicinesHtml += `
                    <tr>
                        <td class="medicine-name">${this.escapeHtml(item.medicine_name || 'Unknown Medicine')}</td>
                        <td>${this.escapeHtml(item.dosage || 'N/A')}</td>
                        <td>${this.escapeHtml(item.frequency || 'N/A')}</td>
                        <td>${item.duration_days || 'N/A'} days</td>
                        <td>${item.quantity || 'N/A'}</td>
                        <td>${this.escapeHtml(item.instructions || '-')}</td>
                    </tr>
                `;
            });
            
            medicinesHtml += `
                    </tbody>
                </table>
            `;
        } else {
            medicinesHtml = '<table class="medicines-table"><tr><td colspan="6" style="text-align: center; font-style: italic;">No medicines prescribed.</td></tr></table>';
        }
        
        printArea.innerHTML = `
            <div class="print-header">
                <div class="print-title">Hospital Management System</div>
                <div class="print-subtitle">Prescription Receipt</div>
            </div>
            
            <div class="print-patient-info">
                <h3>Patient Information</h3>
                <div class="print-patient-grid">
                    <div><strong>Patient:</strong> ${this.escapeHtml(prescription.patient_name || 'Unknown Patient')}</div>
                    <div><strong>Date:</strong> ${formattedDate}</div>
                    <div><strong>Doctor:</strong> ${this.escapeHtml(prescription.doctor_name || 'Unknown Doctor')}</div>
                    <div><strong>Specialty:</strong> ${this.escapeHtml(prescription.doctor_specialty || 'N/A')}</div>
                </div>
            </div>
            
            ${prescription.notes ? `<div class="print-notes"><strong>Doctor's Notes:</strong> ${this.escapeHtml(prescription.notes)}</div>` : ''}
            
            <div class="print-medicines">
                <h3>Prescribed Medicines (${prescription.items ? prescription.items.length : 0} items)</h3>
                ${medicinesHtml}
            </div>
            
            <div class="print-signature">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>Doctor's Signature</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>Pharmacist's Signature</div>
                </div>
            </div>
            
            <div class="print-footer">
                <div>Prescription ID: ${prescription.prescription_id} | Status: ${prescription.status.toUpperCase()} | Total Medicines: ${prescription.items ? prescription.items.length : 0}</div>
                <div>Printed on: ${currentDate} | Please take medicines as prescribed | Valid for 30 days</div>
                <div style="margin-top: 4px; font-weight: bold;">⚠️ Keep this prescription safe - Present to pharmacist when collecting medicines</div>
            </div>
        `;
    }
    
    confirmDelete(prescriptionId, patientName) {
        this.currentPrescriptionId = prescriptionId;
        document.getElementById('deletePrescriptionInfo').textContent = `Prescription for ${patientName}`;
        document.getElementById('deleteModal').classList.add('is-active');
    }
    
    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
        this.currentPrescriptionId = null;
    }
    
    async deletePrescription() {
        if (!this.currentPrescriptionId) return;
        
        try {
            const response = await fetch(`controllers/PrescriptionController.php?action=delete&id=${this.currentPrescriptionId}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.closeDeleteModal();
                this.loadPrescriptions();
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error deleting prescription:', error);
            this.showNotification('Error deleting prescription: ' + error.message, 'danger');
        }
    }
    
    searchPrescriptions() {
        this.currentPage = 1;
        this.loadPrescriptions();
    }
    
    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('patientFilter').value = '';
        this.searchPrescriptions();
    }
    
    updatePagination(pagination) {
        this.currentPage = pagination.current_page;
        this.totalPages = pagination.total_pages;
        
        const paginationElement = document.getElementById('pagination');
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');
        const paginationList = document.getElementById('paginationList');
        
        if (this.totalPages <= 1) {
            paginationElement.style.display = 'none';
            return;
        }
        
        paginationElement.style.display = 'flex';
        
        // Update previous/next buttons
        prevButton.disabled = this.currentPage === 1;
        nextButton.disabled = this.currentPage === this.totalPages;
        
        if (this.currentPage === 1) {
            prevButton.classList.add('is-disabled');
        } else {
            prevButton.classList.remove('is-disabled');
        }
        
        if (this.currentPage === this.totalPages) {
            nextButton.classList.add('is-disabled');
        } else {
            nextButton.classList.remove('is-disabled');
        }
        
        // Generate page numbers
        paginationList.innerHTML = '';
        
        const maxVisiblePages = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(this.totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.className = 'pagination-link';
            a.textContent = i;
            
            if (i === this.currentPage) {
                a.classList.add('is-current');
            }
            
            a.addEventListener('click', () => this.goToPage(i));
            li.appendChild(a);
            paginationList.appendChild(li);
        }
    }
    
    goToPage(page) {
        if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
            this.currentPage = page;
            this.loadPrescriptions();
        }
    }
    
    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('prescriptionsContainer').style.opacity = '0.5';
    }
    
    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('prescriptionsContainer').style.opacity = '1';
    }
    
    showEmptyState() {
        document.getElementById('emptyState').style.display = 'block';
        document.getElementById('pagination').style.display = 'none';
    }
    
    hideEmptyState() {
        document.getElementById('emptyState').style.display = 'none';
    }
    
    showNotification(message, type = 'info') {
        const notification = document.getElementById('notification');
        const messageElement = document.getElementById('notificationMessage');
        
        // Remove existing type classes
        notification.classList.remove('is-success', 'is-warning', 'is-danger', 'is-info');
        
        // Add new type class
        notification.classList.add(`is-${type}`);
        
        messageElement.textContent = message;
        notification.style.display = 'block';
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            this.hideNotification();
        }, 5000);
    }
    
    hideNotification() {
        document.getElementById('notification').style.display = 'none';
    }
    
    escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }
}

// Initialize the prescription manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.prescriptionManager = new PrescriptionManager();
}); 