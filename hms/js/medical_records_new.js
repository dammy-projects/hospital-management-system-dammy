// Medical Records Management - NEW VERSION
console.log('[DEBUG] Loading medical_records_new.js');

// Simple, robust medical records manager
class MedicalRecordManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 5;
        this.totalPages = 1;
        this.searchTimer = null;
        console.log('[DEBUG] MedicalRecordManager constructor called');
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                console.log('[DEBUG] DOM loaded, initializing...');
                this.init();
            });
        } else {
            console.log('[DEBUG] DOM already ready, initializing immediately...');
            this.init();
        }
    }

    init() {
        console.log('[DEBUG] Initializing MedicalRecordManager...');
        
        // Get DOM elements with null checks
        this.grid = document.getElementById('medicalRecordsGrid');
        this.addBtn = document.getElementById('addMedicalRecordBtn');
        this.modal = document.getElementById('medicalRecordModal');
        this.closeModalBtn = document.getElementById('closeModal');
        this.saveBtn = document.getElementById('saveMedicalRecord');
        this.form = document.getElementById('medicalRecordForm');
        this.loadingSpinner = document.getElementById('loadingSpinner');
        this.emptyState = document.getElementById('emptyState');

        // Add search/filter/pagination event listeners
        this.searchInput = document.getElementById('searchInput');
        this.patientFilter = document.getElementById('patientFilter');
        this.doctorFilter = document.getElementById('doctorFilter');
        this.statusFilter = document.getElementById('statusFilter');
        this.sortBy = document.getElementById('sortBy');
        this.clearFiltersBtn = document.getElementById('clearFilters');
        this.prevPageBtn = document.getElementById('prevPage');
        this.nextPageBtn = document.getElementById('nextPage');
        this.paginationList = document.getElementById('paginationList');

        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimer);
                this.searchTimer = setTimeout(() => {
                    this.currentPage = 1;
                    this.loadMedicalRecords();
                }, 500);
            });
        }
        if (this.patientFilter) {
            this.patientFilter.addEventListener('change', () => {
                this.currentPage = 1;
                this.loadMedicalRecords();
            });
        }
        if (this.doctorFilter) {
            this.doctorFilter.addEventListener('change', () => {
                this.currentPage = 1;
                this.loadMedicalRecords();
            });
        }
        if (this.statusFilter) {
            this.statusFilter.addEventListener('change', () => {
                this.currentPage = 1;
                this.loadMedicalRecords();
            });
        }
        if (this.sortBy) {
            this.sortBy.addEventListener('change', () => {
                this.currentPage = 1;
                this.loadMedicalRecords();
            });
        }
        if (this.clearFiltersBtn) {
            this.clearFiltersBtn.addEventListener('click', () => {
                if (this.searchInput) this.searchInput.value = '';
                if (this.patientFilter) this.patientFilter.value = '';
                if (this.doctorFilter) this.doctorFilter.value = '';
                if (this.statusFilter) this.statusFilter.value = '';
                if (this.sortBy) this.sortBy.value = 'record_date';
                this.currentPage = 1;
                this.loadMedicalRecords();
            });
        }
        if (this.prevPageBtn) {
            this.prevPageBtn.addEventListener('click', () => {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.loadMedicalRecords();
                }
            });
        }
        if (this.nextPageBtn) {
            this.nextPageBtn.addEventListener('click', () => {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                    this.loadMedicalRecords();
                }
            });
        }

        console.log('[DEBUG] DOM elements found:', {
            grid: !!this.grid,
            addBtn: !!this.addBtn,
            modal: !!this.modal,
            closeModalBtn: !!this.closeModalBtn,
            saveBtn: !!this.saveBtn,
            form: !!this.form,
            loadingSpinner: !!this.loadingSpinner,
            emptyState: !!this.emptyState
        });

        // Bind events only if elements exist
        if (this.addBtn) {
            this.addBtn.addEventListener('click', () => {
                console.log('[DEBUG] Add button clicked');
                this.openModal();
            });
        }

        if (this.closeModalBtn) {
            this.closeModalBtn.addEventListener('click', () => {
                console.log('[DEBUG] Close modal button clicked');
                this.closeModal();
            });
        }

        if (this.saveBtn) {
            this.saveBtn.addEventListener('click', () => {
                console.log('[DEBUG] Save button clicked');
                this.saveMedicalRecord();
            });
        }

        if (this.form) {
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                console.log('[DEBUG] Form submitted');
                this.saveMedicalRecord();
            });
        }

        // Bind view modal close buttons
        const closeViewModal = document.getElementById('closeViewModal');
        if (closeViewModal) {
            closeViewModal.addEventListener('click', () => {
                console.log('[DEBUG] Close view modal clicked');
                this.closeViewModal();
            });
        }

        const closeViewMedicalRecord = document.getElementById('closeViewMedicalRecord');
        if (closeViewMedicalRecord) {
            closeViewMedicalRecord.addEventListener('click', () => {
                console.log('[DEBUG] Close view medical record clicked');
                this.closeViewModal();
            });
        }

        // Load initial data
        this.loadMedicalRecords();
        this.loadPatients();
        this.loadDoctors();

        // Add BMI calculation listeners
        const heightCmField = document.getElementById('heightCm');
        const weightKgField = document.getElementById('weightKg');
        
        if (heightCmField) {
            heightCmField.addEventListener('input', () => {
                this.calculateBMI();
            });
        }
        
        if (weightKgField) {
            weightKgField.addEventListener('input', () => {
                this.calculateBMI();
            });
        }

        // Add lab images upload listener
        const labImagesInput = document.getElementById('labImages');
        if (labImagesInput) {
            labImagesInput.addEventListener('change', (e) => {
                this.handleLabImagesUpload(e);
            });
        }
    }

    async loadMedicalRecords() {
        console.log('[DEBUG] Loading medical records...');
        this.showLoading();
        
        try {
            const search = this.searchInput ? this.searchInput.value : '';
            const patientId = this.patientFilter ? this.patientFilter.value : '';
            const doctorId = this.doctorFilter ? this.doctorFilter.value : '';
            const status = this.statusFilter ? this.statusFilter.value : '';
            const sort = this.sortBy ? this.sortBy.value : 'record_date';
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.itemsPerPage,
                search,
                patient_id: patientId,
                doctor_id: doctorId,
                status,
                sort
            });
            const url = `controllers/MedicalRecordController.php?action=list&${params}`;
            console.log('[DEBUG] Fetching from:', url);
            
            const response = await fetch(url);
            const data = await response.json();
            
            console.log('[DEBUG] Backend response:', data);
            
            if (data.success && data.data && Array.isArray(data.data.medical_records)) {
                console.log(`[DEBUG] Received ${data.data.medical_records.length} records`);
                this.renderMedicalRecords(data.data.medical_records);
                this.renderPagination(data.data.pagination);
                this.hideLoading();
                
                if (data.data.medical_records.length === 0) {
                    this.showEmptyState();
                } else {
                    this.hideEmptyState();
                }
            } else {
                console.error('[ERROR] Backend returned error:', data.message);
                this.showEmptyState();
                this.hideLoading();
            }
        } catch (error) {
            console.error('[ERROR] Exception loading records:', error);
            this.showEmptyState();
            this.hideLoading();
        }
    }

    renderMedicalRecords(records) {
        console.log('[DEBUG] Rendering records:', records);
        
        if (!this.grid) {
            console.error('[ERROR] Grid element not found');
            return;
        }
        
        this.grid.innerHTML = '';
        
        if (!records || records.length === 0) {
            console.log('[DEBUG] No records to render');
            this.showEmptyState();
            return;
        }
        
        records.forEach((record, index) => {
            const card = document.createElement('div');
            card.className = 'column is-4';
            card.innerHTML = `
                <div class="card">
                    <div class="card-content">
                        <p class="title is-6">${record.patient_name || 'Unknown Patient'}</p>
                        <p class="subtitle is-7">${record.diagnosis || 'No diagnosis'}</p>
                        <div class="medical-record-details">
                            <strong>Date:</strong> ${record.record_date || ''}<br>
                            <strong>Status:</strong> ${record.status || ''}
                        </div>
                    </div>
                    <footer class="card-footer">
                        <a class="card-footer-item" onclick="medicalRecordManager.viewMedicalRecord(${record.record_id})">
                            <span class="icon">
                                <i class="fas fa-eye"></i>
                            </span>
                            <span>View</span>
                        </a>
                        <a class="card-footer-item" onclick="medicalRecordManager.editMedicalRecord(${record.record_id})">
                            <span class="icon">
                                <i class="fas fa-edit"></i>
                            </span>
                            <span>Edit</span>
                        </a>
                        <a class="card-footer-item has-text-success" onclick="medicalRecordManager.printMedicalRecord(${record.record_id})">
                            <span class="icon">
                                <i class="fas fa-print"></i>
                            </span>
                            <span>Print</span>
                        </a>
                        <a class="card-footer-item has-text-danger" onclick="medicalRecordManager.deleteMedicalRecord(${record.record_id}, '${record.patient_name || 'Unknown Patient'}')">
                            <span class="icon">
                                <i class="fas fa-trash"></i>
                            </span>
                            <span>Delete</span>
                        </a>
                    </footer>
                </div>
            `;
            this.grid.appendChild(card);
        });
        
        console.log(`[DEBUG] Rendered ${records.length} records`);
    }

    renderPagination(pagination) {
        if (!pagination) return;
        this.totalPages = pagination.total_pages || 1;
        const paginationList = this.paginationList;
        if (!paginationList) return;
        paginationList.innerHTML = '';
        for (let i = 1; i <= this.totalPages; i++) {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.className = 'pagination-link' + (i === this.currentPage ? ' is-current' : '');
            a.textContent = i;
            a.addEventListener('click', () => {
                if (i !== this.currentPage) {
                    this.currentPage = i;
                    this.loadMedicalRecords();
                }
            });
            li.appendChild(a);
            paginationList.appendChild(li);
        }
        // Show/hide pagination controls
        const paginationNav = document.getElementById('pagination');
        if (paginationNav) {
            paginationNav.style.display = this.totalPages > 1 ? '' : 'none';
        }
    }

    openModal(resetForm = true) {
        console.log('[DEBUG] Opening modal, resetForm:', resetForm);
        
        // Clear form for new record only if resetForm is true
        if (this.form && resetForm) {
            this.form.reset();
            
            // Set default values for new record
            const recordDateField = document.getElementById('recordDate');
            if (recordDateField) {
                const now = new Date();
                recordDateField.value = now.toISOString().slice(0, 16);
            }
            
            const statusField = document.getElementById('status');
            if (statusField) statusField.value = 'active';
            
            // Clear record ID for new record
            const recordIdField = document.getElementById('recordId');
            if (recordIdField) recordIdField.value = '';
            
            // Update modal title
            const modalTitle = document.getElementById('modalTitle');
            if (modalTitle) modalTitle.textContent = 'Add New Medical Record';
        }
        
        if (this.modal) this.modal.classList.add('is-active');
    }

    closeModal() {
        console.log('[DEBUG] Closing modal');
        if (this.modal) {
            this.modal.classList.remove('is-active');
        }
    }

    showLoading() {
        console.log('[DEBUG] Showing loading');
        if (this.loadingSpinner) this.loadingSpinner.style.display = 'block';
        if (this.grid) this.grid.style.display = 'none';
        if (this.emptyState) this.emptyState.style.display = 'none';
    }

    hideLoading() {
        console.log('[DEBUG] Hiding loading');
        if (this.loadingSpinner) this.loadingSpinner.style.display = 'none';
        if (this.grid) this.grid.style.display = 'flex';
    }

    showEmptyState() {
        console.log('[DEBUG] Showing empty state');
        if (this.emptyState) this.emptyState.style.display = 'block';
        if (this.grid) this.grid.style.display = 'none';
    }

    hideEmptyState() {
        console.log('[DEBUG] Hiding empty state');
        if (this.emptyState) this.emptyState.style.display = 'none';
        if (this.grid) this.grid.style.display = 'flex';
    }

    saveMedicalRecord() {
        console.log('[DEBUG] Save medical record called');
        
        // Get form data
        const formData = new FormData();
        const recordId = document.getElementById('recordId')?.value;
        const patientId = document.getElementById('patientId')?.value;
        const doctorId = document.getElementById('doctorId')?.value;
        const recordDate = document.getElementById('recordDate')?.value;
        const status = document.getElementById('status')?.value;
        
        console.log('[DEBUG] Form field values:');
        console.log('[DEBUG] recordId:', recordId);
        console.log('[DEBUG] patientId:', patientId);
        console.log('[DEBUG] doctorId:', doctorId);
        console.log('[DEBUG] recordDate:', recordDate);
        console.log('[DEBUG] status:', status);
        
        // Basic validation
        if (!patientId || !doctorId || !recordDate || !status) {
            this.showError('Please fill in all required fields (Patient, Doctor, Record Date, Status)');
            return;
        }
        
        // Prepare form data
        const action = recordId ? 'update' : 'create';
        formData.append('action', action);
        console.log('[DEBUG] Action:', action);
        
        if (recordId) formData.append('record_id', recordId);
        formData.append('patient_id', patientId);
        formData.append('doctor_id', doctorId);
        formData.append('record_date', recordDate);
        formData.append('status', status);
        
        // Add optional fields
        const heightCm = document.getElementById('heightCm')?.value;
        const weightKg = document.getElementById('weightKg')?.value;
        const bmi = document.getElementById('bmi')?.value;
        const bloodPressure = document.getElementById('bloodPressure')?.value;
        const heartRate = document.getElementById('heartRate')?.value;
        const temperatureC = document.getElementById('temperatureC')?.value;
        const respiratoryRate = document.getElementById('respiratoryRate')?.value;
        const subjective = document.getElementById('subjective')?.value;
        const objective = document.getElementById('objective')?.value;
        const assessment = document.getElementById('assessment')?.value;
        const plan = document.getElementById('plan')?.value;
        const diagnosis = document.getElementById('diagnosis')?.value;
        const treatment = document.getElementById('treatment')?.value;
        
        console.log('[DEBUG] Optional field values:');
        console.log('[DEBUG] heightCm:', heightCm);
        console.log('[DEBUG] weightKg:', weightKg);
        console.log('[DEBUG] bmi:', bmi);
        console.log('[DEBUG] bloodPressure:', bloodPressure);
        console.log('[DEBUG] heartRate:', heartRate);
        console.log('[DEBUG] temperatureC:', temperatureC);
        console.log('[DEBUG] respiratoryRate:', respiratoryRate);
        console.log('[DEBUG] subjective:', subjective);
        console.log('[DEBUG] objective:', objective);
        console.log('[DEBUG] assessment:', assessment);
        console.log('[DEBUG] plan:', plan);
        console.log('[DEBUG] diagnosis:', diagnosis);
        console.log('[DEBUG] treatment:', treatment);
        
        if (heightCm) formData.append('height_cm', heightCm);
        if (weightKg) formData.append('weight_kg', weightKg);
        if (bmi) formData.append('bmi', bmi);
        if (bloodPressure) formData.append('blood_pressure', bloodPressure);
        if (heartRate) formData.append('heart_rate', heartRate);
        if (temperatureC) formData.append('temperature_c', temperatureC);
        if (respiratoryRate) formData.append('respiratory_rate', respiratoryRate);
        if (subjective) formData.append('subjective', subjective);
        if (objective) formData.append('objective', objective);
        if (assessment) formData.append('assessment', assessment);
        if (plan) formData.append('plan', plan);
        if (diagnosis) formData.append('diagnosis', diagnosis);
        if (treatment) formData.append('treatment', treatment);
        
        // Log all form data being sent
        console.log('[DEBUG] FormData contents:');
        for (let [key, value] of formData.entries()) {
            console.log('[DEBUG]', key + ':', value);
        }
        
        // Send request
        this.saveMedicalRecordRequest(formData);
    }

    async saveMedicalRecordRequest(formData) {
        try {
            console.log('[DEBUG] Sending save request...');
            
            const response = await fetch('controllers/MedicalRecordController.php', {
                method: 'POST',
                body: formData
            });
            
            console.log('[DEBUG] Response status:', response.status);
            console.log('[DEBUG] Response headers:', response.headers);
            
            const responseText = await response.text();
            console.log('[DEBUG] Raw response:', responseText);
            
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (parseError) {
                console.error('[ERROR] JSON parse error:', parseError);
                console.error('[ERROR] Response text:', responseText);
                throw new Error('Invalid JSON response from server: ' + responseText);
            }
            console.log('[DEBUG] Save response:', data);
            
            if (data.success) {
                // Upload lab images if any are selected
                if (this.selectedLabImages && this.selectedLabImages.length > 0) {
                    const recordId = data.record_id || formData.get('record_id');
                    console.log('[DEBUG] Uploading lab images for record:', recordId);
                    
                    const uploadedFiles = await this.uploadLabImages(recordId);
                    console.log('[DEBUG] Uploaded files:', uploadedFiles);
                    
                    if (uploadedFiles.length > 0) {
                        // Update the record with lab images information
                        const updateFormData = new FormData();
                        updateFormData.append('action', 'update');
                        updateFormData.append('record_id', recordId);
                        updateFormData.append('lab_images', JSON.stringify(uploadedFiles));
                        
                        try {
                            const updateResponse = await fetch('controllers/MedicalRecordController.php', {
                                method: 'POST',
                                body: updateFormData
                            });
                            
                            const updateData = await updateResponse.json();
                            if (updateData.success) {
                                console.log('[DEBUG] Lab images updated successfully');
                            } else {
                                console.error('[ERROR] Failed to update lab images:', updateData.message);
                            }
                        } catch (updateError) {
                            console.error('[ERROR] Error updating lab images:', updateError);
                        }
                    }
                }
                
                this.showSuccess(data.message || 'Medical record saved successfully!');
                this.closeModal();
                this.loadMedicalRecords(); // Reload the list
            } else {
                this.showError('Failed to save medical record: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('[ERROR] Error saving medical record:', error);
            console.error('[ERROR] Error details:', error.message);
            this.showError('Error saving medical record: ' + error.message);
        }
    }

    async viewMedicalRecord(recordId) {
        console.log('[DEBUG] Viewing medical record:', recordId);
        try {
            const response = await fetch(`controllers/MedicalRecordController.php?action=get&id=${recordId}`);
            const data = await response.json();
            console.log('[DEBUG] View response:', data);
            
            if (data.success && data.data) {
                this.showMedicalRecordDetails(data.data);
            } else if (data.success && data.message) {
                // Handle case where data is directly in the response
                this.showMedicalRecordDetails(data);
            } else {
                this.showError('Failed to load medical record: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('[ERROR] Error viewing medical record:', error);
            this.showError('Error loading medical record. Please try again.');
        }
    }

    async editMedicalRecord(recordId) {
        console.log('[DEBUG] Editing medical record:', recordId);
        try {
            const response = await fetch(`controllers/MedicalRecordController.php?action=get&id=${recordId}`);
            const data = await response.json();
            console.log('[DEBUG] Edit response:', data);
            
            if (data.success && data.data) {
                this.openEditModal(data.data);
            } else if (data.success && data.message) {
                // Handle case where data is directly in the response
                this.openEditModal(data);
            } else {
                this.showError('Failed to load medical record: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('[ERROR] Error editing medical record:', error);
            this.showError('Error loading medical record. Please try again.');
        }
    }

    async deleteMedicalRecord(recordId, patientName) {
        if (!confirm(`Are you sure you want to delete the medical record for ${patientName}?`)) {
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('record_id', recordId);
            
            const response = await fetch('controllers/MedicalRecordController.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess('Medical record deleted successfully!');
                this.loadMedicalRecords(); // Reload the list
            } else {
                this.showError('Failed to delete medical record: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('[ERROR] Error deleting medical record:', error);
            this.showError('Error deleting medical record. Please try again.');
        }
    }

    openEditModal(record) {
        console.log('[DEBUG] Opening edit modal for record:', record);
        console.log('[DEBUG] Edit record keys:', Object.keys(record));
        
        // Handle nested data structure
        if (record.data && typeof record.data === 'object') {
            record = record.data;
            console.log('[DEBUG] Using nested data for edit:', record);
        }
        
        // First open the modal without resetting the form
        console.log('[DEBUG] About to open modal without reset...');
        this.openModal(false); // Don't reset the form
        
        // Wait a moment for the modal to be visible, then populate fields
        setTimeout(() => {
            console.log('[DEBUG] Modal should be visible now, populating fields...');
            
            // Populate form fields
            if (this.form) {
                console.log('[DEBUG] Form found, getting form fields...');
                
                const recordIdField = document.getElementById('recordId');
                const patientIdField = document.getElementById('patientId');
                const doctorIdField = document.getElementById('doctorId');
                const recordDateField = document.getElementById('recordDate');
                const statusField = document.getElementById('status');
                const heightCmField = document.getElementById('heightCm');
                const weightKgField = document.getElementById('weightKg');
                const bmiField = document.getElementById('bmi');
                const bloodPressureField = document.getElementById('bloodPressure');
                const heartRateField = document.getElementById('heartRate');
                const temperatureCField = document.getElementById('temperatureC');
                const respiratoryRateField = document.getElementById('respiratoryRate');
                const subjectiveField = document.getElementById('subjective');
                const objectiveField = document.getElementById('objective');
                const assessmentField = document.getElementById('assessment');
                const planField = document.getElementById('plan');
                const diagnosisField = document.getElementById('diagnosis');
                const treatmentField = document.getElementById('treatment');
                
                console.log('[DEBUG] Form field checks:');
                console.log('[DEBUG] recordIdField found:', !!recordIdField);
                console.log('[DEBUG] patientIdField found:', !!patientIdField);
                console.log('[DEBUG] doctorIdField found:', !!doctorIdField);
                console.log('[DEBUG] recordDateField found:', !!recordDateField);
                console.log('[DEBUG] statusField found:', !!statusField);
                console.log('[DEBUG] heightCmField found:', !!heightCmField);
                console.log('[DEBUG] weightKgField found:', !!weightKgField);
                console.log('[DEBUG] bmiField found:', !!bmiField);
                console.log('[DEBUG] bloodPressureField found:', !!bloodPressureField);
                console.log('[DEBUG] heartRateField found:', !!heartRateField);
                console.log('[DEBUG] temperatureCField found:', !!temperatureCField);
                console.log('[DEBUG] respiratoryRateField found:', !!respiratoryRateField);
                console.log('[DEBUG] subjectiveField found:', !!subjectiveField);
                console.log('[DEBUG] objectiveField found:', !!objectiveField);
                console.log('[DEBUG] assessmentField found:', !!assessmentField);
                console.log('[DEBUG] planField found:', !!planField);
                console.log('[DEBUG] diagnosisField found:', !!diagnosisField);
                console.log('[DEBUG] treatmentField found:', !!treatmentField);
                
                console.log('[DEBUG] Setting form fields...');
                console.log('[DEBUG] record_id:', record.record_id);
                console.log('[DEBUG] patient_id:', record.patient_id);
                console.log('[DEBUG] doctor_id:', record.doctor_id);
                console.log('[DEBUG] record_date:', record.record_date);
                console.log('[DEBUG] diagnosis:', record.diagnosis);
                console.log('[DEBUG] treatment:', record.treatment);
                
                if (recordIdField) {
                    recordIdField.value = record.record_id || '';
                    console.log('[DEBUG] Set recordIdField to:', recordIdField.value);
                } else {
                    console.log('[DEBUG] recordIdField not found');
                }
                
                if (patientIdField) {
                    patientIdField.value = record.patient_id || '';
                    console.log('[DEBUG] Set patientIdField to:', patientIdField.value);
                } else {
                    console.log('[DEBUG] patientIdField not found');
                }
                
                if (doctorIdField) {
                    doctorIdField.value = record.doctor_id || '';
                    console.log('[DEBUG] Set doctorIdField to:', doctorIdField.value);
                } else {
                    console.log('[DEBUG] doctorIdField not found');
                }
                
                if (recordDateField) {
                    const formattedDate = record.record_date ? record.record_date.replace(' ', 'T') : '';
                    recordDateField.value = formattedDate;
                    console.log('[DEBUG] Set recordDateField to:', recordDateField.value);
                } else {
                    console.log('[DEBUG] recordDateField not found');
                }
                
                if (statusField) {
                    statusField.value = record.status || 'active';
                    console.log('[DEBUG] Set statusField to:', statusField.value);
                } else {
                    console.log('[DEBUG] statusField not found');
                }
                
                if (heightCmField) {
                    heightCmField.value = record.height_cm || '';
                    console.log('[DEBUG] Set heightCmField to:', heightCmField.value);
                } else {
                    console.log('[DEBUG] heightCmField not found');
                }
                
                if (weightKgField) {
                    weightKgField.value = record.weight_kg || '';
                    console.log('[DEBUG] Set weightKgField to:', weightKgField.value);
                } else {
                    console.log('[DEBUG] weightKgField not found');
                }
                
                if (bmiField) {
                    bmiField.value = record.bmi || '';
                    console.log('[DEBUG] Set bmiField to:', bmiField.value);
                } else {
                    console.log('[DEBUG] bmiField not found');
                }
                
                if (bloodPressureField) {
                    bloodPressureField.value = record.blood_pressure || '';
                    console.log('[DEBUG] Set bloodPressureField to:', bloodPressureField.value);
                } else {
                    console.log('[DEBUG] bloodPressureField not found');
                }
                
                if (heartRateField) {
                    heartRateField.value = record.heart_rate || '';
                    console.log('[DEBUG] Set heartRateField to:', heartRateField.value);
                } else {
                    console.log('[DEBUG] heartRateField not found');
                }
                
                if (temperatureCField) {
                    temperatureCField.value = record.temperature_c || '';
                    console.log('[DEBUG] Set temperatureCField to:', temperatureCField.value);
                } else {
                    console.log('[DEBUG] temperatureCField not found');
                }
                
                if (respiratoryRateField) {
                    respiratoryRateField.value = record.respiratory_rate || '';
                    console.log('[DEBUG] Set respiratoryRateField to:', respiratoryRateField.value);
                } else {
                    console.log('[DEBUG] respiratoryRateField not found');
                }
                
                if (subjectiveField) {
                    subjectiveField.value = record.subjective || '';
                    console.log('[DEBUG] Set subjectiveField to:', subjectiveField.value);
                } else {
                    console.log('[DEBUG] subjectiveField not found');
                }
                
                if (objectiveField) {
                    objectiveField.value = record.objective || '';
                    console.log('[DEBUG] Set objectiveField to:', objectiveField.value);
                } else {
                    console.log('[DEBUG] objectiveField not found');
                }
                
                if (assessmentField) {
                    assessmentField.value = record.assessment || '';
                    console.log('[DEBUG] Set assessmentField to:', assessmentField.value);
                } else {
                    console.log('[DEBUG] assessmentField not found');
                }
                
                if (planField) {
                    planField.value = record.plan || '';
                    console.log('[DEBUG] Set planField to:', planField.value);
                } else {
                    console.log('[DEBUG] planField not found');
                }
                
                if (diagnosisField) {
                    diagnosisField.value = record.diagnosis || '';
                    console.log('[DEBUG] Set diagnosisField to:', diagnosisField.value);
                } else {
                    console.log('[DEBUG] diagnosisField not found');
                }
                
                if (treatmentField) {
                    treatmentField.value = record.treatment || '';
                    console.log('[DEBUG] Set treatmentField to:', treatmentField.value);
                } else {
                    console.log('[DEBUG] treatmentField not found');
                }
                
                // Update modal title
                const modalTitle = document.getElementById('modalTitle');
                if (modalTitle) modalTitle.textContent = 'Edit Medical Record';
            } else {
                console.log('[DEBUG] Form not found!');
            }
            
            console.log('[DEBUG] Modal opened, checking if visible...');
            
            // Check if modal is visible after opening
            const modal = document.getElementById('medicalRecordModal');
            if (modal) {
                console.log('[DEBUG] Modal element found:', !!modal);
                console.log('[DEBUG] Modal classes:', modal.className);
                console.log('[DEBUG] Modal is visible:', modal.classList.contains('is-active'));
            } else {
                console.log('[DEBUG] Modal element not found!');
            }
        }, 100);
    }

    showMedicalRecordDetails(record) {
        console.log('[DEBUG] Showing medical record details:', record);
        console.log('[DEBUG] Record keys:', Object.keys(record));
        
        // Handle nested data structure
        if (record.data && typeof record.data === 'object') {
            record = record.data;
            console.log('[DEBUG] Using nested data:', record);
        }
        
        // Populate view modal fields
        const viewPatientName = document.getElementById('viewPatientName');
        const viewDoctorName = document.getElementById('viewDoctorName');
        const viewRecordDate = document.getElementById('viewRecordDate');
        const viewStatus = document.getElementById('viewStatus');
        
        if (viewPatientName) viewPatientName.textContent = record.patient_name || 'Unknown Patient';
        if (viewDoctorName) viewDoctorName.textContent = record.doctor_name || 'Unknown Doctor';
        if (viewRecordDate) viewRecordDate.textContent = record.record_date || 'Not available';
        if (viewStatus) viewStatus.textContent = record.status || 'Unknown';
        
        // Populate vital signs
        const viewHeight = document.getElementById('viewHeight');
        const viewWeight = document.getElementById('viewWeight');
        const viewBmi = document.getElementById('viewBmi');
        const viewBloodPressure = document.getElementById('viewBloodPressure');
        const viewHeartRate = document.getElementById('viewHeartRate');
        const viewTemperature = document.getElementById('viewTemperature');
        const viewRespiratoryRate = document.getElementById('viewRespiratoryRate');
        
        if (viewHeight) viewHeight.textContent = record.height_cm ? `${record.height_cm} cm` : 'Not recorded';
        if (viewWeight) viewWeight.textContent = record.weight_kg ? `${record.weight_kg} kg` : 'Not recorded';
        if (viewBmi) viewBmi.textContent = record.bmi ? record.bmi : 'Not calculated';
        if (viewBloodPressure) viewBloodPressure.textContent = record.blood_pressure || 'Not recorded';
        if (viewHeartRate) viewHeartRate.textContent = record.heart_rate ? `${record.heart_rate} bpm` : 'Not recorded';
        if (viewTemperature) viewTemperature.textContent = record.temperature_c ? `${record.temperature_c}°C` : 'Not recorded';
        if (viewRespiratoryRate) viewRespiratoryRate.textContent = record.respiratory_rate ? `${record.respiratory_rate} breaths/min` : 'Not recorded';
        
        // Populate SOAP notes
        const viewSubjective = document.getElementById('viewSubjective');
        const viewObjective = document.getElementById('viewObjective');
        const viewAssessment = document.getElementById('viewAssessment');
        const viewPlan = document.getElementById('viewPlan');
        
        if (viewSubjective) viewSubjective.textContent = record.subjective || 'No subjective notes recorded';
        if (viewObjective) viewObjective.textContent = record.objective || 'No objective notes recorded';
        if (viewAssessment) viewAssessment.textContent = record.assessment || 'No assessment recorded';
        if (viewPlan) viewPlan.textContent = record.plan || 'No plan recorded';
        
        // Populate diagnosis and treatment
        const viewDiagnosis = document.getElementById('viewDiagnosis');
        const viewTreatment = document.getElementById('viewTreatment');
        
        if (viewDiagnosis) viewDiagnosis.textContent = record.diagnosis || 'No diagnosis recorded';
        if (viewTreatment) viewTreatment.textContent = record.treatment || 'No treatment recorded';
        
        // Display lab images if available
        if (record.lab_images) {
            try {
                const labImages = JSON.parse(record.lab_images);
                if (Array.isArray(labImages) && labImages.length > 0) {
                    this.displayLabImages(labImages);
                } else {
                    // Hide lab images section if no images
                    const viewLabImagesSection = document.getElementById('viewLabImagesSection');
                    if (viewLabImagesSection) viewLabImagesSection.style.display = 'none';
                }
            } catch (parseError) {
                console.error('[ERROR] Error parsing lab images:', parseError);
                const viewLabImagesSection = document.getElementById('viewLabImagesSection');
                if (viewLabImagesSection) viewLabImagesSection.style.display = 'none';
            }
        } else {
            // Hide lab images section if no images
            const viewLabImagesSection = document.getElementById('viewLabImagesSection');
            if (viewLabImagesSection) viewLabImagesSection.style.display = 'none';
        }
        
        // Show the view modal
        const viewModal = document.getElementById('viewMedicalRecordModal');
        if (viewModal) viewModal.classList.add('is-active');
    }

    async loadPatients() {
        try {
            console.log('[DEBUG] Loading patients...');
            const response = await fetch('controllers/PatientController.php?action=list_all');
            const data = await response.json();
            
            if (data.success && data.data) {
                // Populate form dropdown
                const patientSelect = document.getElementById('patientId');
                if (patientSelect) {
                    patientSelect.innerHTML = '<option value="">Select Patient</option>';
                    data.data.forEach(patient => {
                        const fullName = `${patient.first_name} ${patient.middle_name ? patient.middle_name + ' ' : ''}${patient.last_name}`;
                        const option = `<option value="${patient.patient_id}">${fullName}</option>`;
                        patientSelect.innerHTML += option;
                    });
                }
                
                // Populate filter dropdown
                const patientFilter = document.getElementById('patientFilter');
                if (patientFilter) {
                    patientFilter.innerHTML = '<option value="">All Patients</option>';
                    data.data.forEach(patient => {
                        const fullName = `${patient.first_name} ${patient.middle_name ? patient.middle_name + ' ' : ''}${patient.last_name}`;
                        const option = `<option value="${patient.patient_id}">${fullName}</option>`;
                        patientFilter.innerHTML += option;
                    });
                }
                
                console.log(`[DEBUG] Loaded ${data.data.length} patients`);
            }
        } catch (error) {
            console.error('[ERROR] Error loading patients:', error);
        }
    }

    async loadDoctors() {
        try {
            console.log('[DEBUG] Loading doctors...');
            const response = await fetch('controllers/DoctorController.php?action=list_all');
            const data = await response.json();
            
            if (data.success && data.data) {
                // Populate form dropdown
                const doctorSelect = document.getElementById('doctorId');
                if (doctorSelect) {
                    doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
                    data.data.forEach(doctor => {
                        const fullName = `Dr. ${doctor.first_name} ${doctor.middle_name ? doctor.middle_name + ' ' : ''}${doctor.last_name}`;
                        const option = `<option value="${doctor.doctor_id}">${fullName}</option>`;
                        doctorSelect.innerHTML += option;
                    });
                }
                
                // Populate filter dropdown
                const doctorFilter = document.getElementById('doctorFilter');
                if (doctorFilter) {
                    doctorFilter.innerHTML = '<option value="">All Doctors</option>';
                    data.data.forEach(doctor => {
                        const fullName = `Dr. ${doctor.first_name} ${doctor.middle_name ? doctor.middle_name + ' ' : ''}${doctor.last_name}`;
                        const option = `<option value="${doctor.doctor_id}">${fullName}</option>`;
                        doctorFilter.innerHTML += option;
                    });
                }
                
                console.log(`[DEBUG] Loaded ${data.data.length} doctors`);
            }
        } catch (error) {
            console.error('[ERROR] Error loading doctors:', error);
        }
    }

    closeViewModal() {
        console.log('[DEBUG] Closing view modal');
        const viewModal = document.getElementById('viewMedicalRecordModal');
        if (viewModal) {
            viewModal.classList.remove('is-active');
        }
    }

    calculateBMI() {
        const heightCm = parseFloat(document.getElementById('heightCm')?.value);
        const weightKg = parseFloat(document.getElementById('weightKg')?.value);
        const bmiField = document.getElementById('bmi');
        
        if (heightCm > 0 && weightKg > 0 && bmiField) {
            const heightInMeters = heightCm / 100;
            const bmi = weightKg / (heightInMeters * heightInMeters);
            bmiField.value = bmi.toFixed(2);
            console.log('[DEBUG] BMI calculated:', bmi.toFixed(2));
        } else if (bmiField) {
            bmiField.value = '';
        }
    }

    handleLabImagesUpload(event) {
        const files = event.target.files;
        const maxFiles = 5;
        const maxFileSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'];
        
        console.log('[DEBUG] Lab images upload event:', files.length, 'files');
        
        if (files.length > maxFiles) {
            this.showError(`Maximum ${maxFiles} files allowed. Please select fewer files.`);
            event.target.value = '';
            return;
        }
        
        // Validate files
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            if (file.size > maxFileSize) {
                this.showError(`File "${file.name}" is too large. Maximum size is 5MB.`);
                event.target.value = '';
                return;
            }
            
            if (!allowedTypes.includes(file.type)) {
                this.showError(`File "${file.name}" is not a supported format. Please use JPG, PNG, GIF, or PDF.`);
                event.target.value = '';
                return;
            }
        }
        
        // Update file name display
        const fileNameDisplay = document.getElementById('labImagesFileName');
        if (fileNameDisplay) {
            if (files.length === 0) {
                fileNameDisplay.textContent = 'No files selected';
            } else if (files.length === 1) {
                fileNameDisplay.textContent = files[0].name;
            } else {
                fileNameDisplay.textContent = `${files.length} files selected`;
            }
        }
        
        // Store files for later upload
        this.selectedLabImages = Array.from(files);
        console.log('[DEBUG] Selected lab images:', this.selectedLabImages.length, 'files');
    }

    async uploadLabImages(recordId) {
        if (!this.selectedLabImages || this.selectedLabImages.length === 0) {
            console.log('[DEBUG] No lab images to upload');
            return [];
        }
        
        console.log('[DEBUG] Uploading lab images for record:', recordId);
        
        const uploadedFiles = [];
        
        for (let i = 0; i < this.selectedLabImages.length; i++) {
            const file = this.selectedLabImages[i];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('record_id', recordId);
            formData.append('action', 'upload_lab_image');
            
            try {
                const response = await fetch('controllers/MedicalRecordController.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    uploadedFiles.push({
                        filename: data.filename,
                        original_name: file.name,
                        file_path: data.file_path
                    });
                    console.log('[DEBUG] Uploaded file:', data.filename);
                } else {
                    console.error('[ERROR] Failed to upload file:', file.name, data.message);
                }
            } catch (error) {
                console.error('[ERROR] Upload error for file:', file.name, error);
            }
        }
        
        return uploadedFiles;
    }

    displayLabImages(images) {
        if (!images || images.length === 0) {
            console.log('[DEBUG] No lab images to display');
            return;
        }
        
        console.log('[DEBUG] Displaying lab images:', images);
        
        // Display in form (for edit mode)
        const uploadedImagesContainer = document.getElementById('uploadedImagesContainer');
        const uploadedImagesGrid = document.getElementById('uploadedImagesGrid');
        
        if (uploadedImagesContainer && uploadedImagesGrid) {
            uploadedImagesContainer.style.display = 'block';
            uploadedImagesGrid.innerHTML = '';
            
            images.forEach((image, index) => {
                const column = document.createElement('div');
                column.className = 'column is-3';
                
                const isImage = /\.(jpg|jpeg|png|gif)$/i.test(image.filename);
                const isPdf = /\.pdf$/i.test(image.filename);
                
                if (isImage) {
                    column.innerHTML = `
                        <div class="card">
                            <div class="card-image">
                                <figure class="image is-4by3">
                                    <img src="uploads/lab_results/${image.filename}" alt="Lab Image" />
                                </figure>
                            </div>
                            <div class="card-content">
                                <p class="title is-6">${image.original_name}</p>
                                <button class="button is-small is-danger" onclick="medicalRecordManager.removeLabImage(${index})">
                                    <span class="icon">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                    `;
                } else if (isPdf) {
                    column.innerHTML = `
                        <div class="card">
                            <div class="card-content">
                                <div class="content">
                                    <span class="icon is-large">
                                        <i class="fas fa-file-pdf"></i>
                                    </span>
                                    <p class="title is-6">${image.original_name}</p>
                                    <a href="uploads/lab_results/${image.filename}" target="_blank" class="button is-small">
                                        <span class="icon">
                                            <i class="fas fa-download"></i>
                                        </span>
                                        <span>View</span>
                                    </a>
                                    <button class="button is-small is-danger" onclick="medicalRecordManager.removeLabImage(${index})">
                                        <span class="icon">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                uploadedImagesGrid.appendChild(column);
            });
        }
        
        // Display in view modal
        const viewLabImagesSection = document.getElementById('viewLabImagesSection');
        const viewLabImagesGrid = document.getElementById('viewLabImagesGrid');
        
        if (viewLabImagesSection && viewLabImagesGrid) {
            viewLabImagesSection.style.display = 'block';
            viewLabImagesGrid.innerHTML = '';
            
            images.forEach((image) => {
                const column = document.createElement('div');
                column.className = 'column is-3';
                
                const isImage = /\.(jpg|jpeg|png|gif)$/i.test(image.filename);
                const isPdf = /\.pdf$/i.test(image.filename);
                
                if (isImage) {
                    column.innerHTML = `
                        <div class="card">
                            <div class="card-image">
                                <figure class="image is-4by3">
                                    <img src="uploads/lab_results/${image.filename}" alt="Lab Image" />
                                </figure>
                            </div>
                            <div class="card-content">
                                <p class="title is-6">${image.original_name}</p>
                            </div>
                        </div>
                    `;
                } else if (isPdf) {
                    column.innerHTML = `
                        <div class="card">
                            <div class="card-content">
                                <div class="content">
                                    <span class="icon is-large">
                                        <i class="fas fa-file-pdf"></i>
                                    </span>
                                    <p class="title is-6">${image.original_name}</p>
                                    <a href="uploads/lab_results/${image.filename}" target="_blank" class="button is-small">
                                        <span class="icon">
                                            <i class="fas fa-download"></i>
                                        </span>
                                        <span>View</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                viewLabImagesGrid.appendChild(column);
            });
        }
    }

    removeLabImage(index) {
        if (this.selectedLabImages && this.selectedLabImages[index]) {
            this.selectedLabImages.splice(index, 1);
            this.displayLabImages(this.selectedLabImages);
            
            // Update file input
            const labImagesInput = document.getElementById('labImages');
            if (labImagesInput) {
                labImagesInput.value = '';
            }
            
            const fileNameDisplay = document.getElementById('labImagesFileName');
            if (fileNameDisplay) {
                if (this.selectedLabImages.length === 0) {
                    fileNameDisplay.textContent = 'No files selected';
                } else {
                    fileNameDisplay.textContent = `${this.selectedLabImages.length} files selected`;
                }
            }
        }
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification is-${type} is-light`;
        notification.innerHTML = `
            <button class="delete"></button>
            ${message}
        `;

        // Add to page
        const container = document.querySelector('.container');
        container.insertBefore(notification, container.firstChild);

        // Add delete functionality
        notification.querySelector('.delete').addEventListener('click', () => {
            notification.remove();
        });

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'danger');
    }

    async printMedicalRecord(recordId) {
        console.log('[DEBUG] Printing medical record:', recordId);
        try {
            const response = await fetch(`controllers/MedicalRecordController.php?action=get&id=${recordId}`);
            const data = await response.json();
            
            if (data.success && data.data) {
                this.generateMedicalRecordPrintContent(data.data);
                window.print();
            } else {
                this.showError('Failed to load medical record data for printing: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('[ERROR] Error loading medical record data for printing:', error);
            this.showError('Error loading medical record data for printing.');
        }
    }

    generateMedicalRecordPrintContent(record) {
        const printArea = document.getElementById('medicalRecordPrintArea');
        if (!printArea) {
            console.error('[ERROR] Print area not found');
            return;
        }

        const currentDateTime = new Date().toLocaleString();
        
        // Format the record date
        const recordDate = record.record_date ? new Date(record.record_date).toLocaleDateString() : 'Not available';
        
        // Format lab images if available
        let labImagesHTML = '';
        if (record.lab_images) {
            try {
                const labImages = JSON.parse(record.lab_images);
                if (Array.isArray(labImages) && labImages.length > 0) {
                    labImagesHTML = `
                        <div class="print-medical-records">
                            <h3>Lab Images & Results</h3>
                            <div class="print-info-grid">
                                ${labImages.map(img => `
                                    <div class="print-info-item">
                                        <strong>File:</strong> ${img.original_name || img.filename}<br>
                                        <strong>Type:</strong> ${img.filename.split('.').pop().toUpperCase()}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }
            } catch (e) {
                console.error('[ERROR] Error parsing lab images:', e);
            }
        }

        const finalHTML = `
            <div class="print-header">
                <div class="print-title">Hospital Management System</div>
                <div class="print-subtitle">Medical Record Information</div>
            </div>
            
            <div class="print-info">
                <h3>Record Details</h3>
                <div class="print-info-grid">
                    <div class="print-info-item">
                        <strong>Record ID:</strong> ${record.record_id}
                    </div>
                    <div class="print-info-item">
                        <strong>Patient:</strong> ${record.patient_name || 'Unknown'}
                    </div>
                    <div class="print-info-item">
                        <strong>Doctor:</strong> ${record.doctor_name || 'Unknown'}
                    </div>
                    <div class="print-info-item">
                        <strong>Record Date:</strong> ${recordDate}
                    </div>
                    <div class="print-info-item">
                        <strong>Status:</strong> ${record.status || 'Unknown'}
                    </div>
                    <div class="print-info-item">
                        <strong>Created:</strong> ${record.created_at ? new Date(record.created_at).toLocaleDateString() : 'Not available'}
                    </div>
                </div>
            </div>

            <div class="print-medical-records">
                <h3>Vital Signs</h3>
                <div class="print-info-grid">
                    <div class="print-info-item">
                        <strong>Height:</strong> ${record.height_cm ? record.height_cm + ' cm' : 'Not recorded'}
                    </div>
                    <div class="print-info-item">
                        <strong>Weight:</strong> ${record.weight_kg ? record.weight_kg + ' kg' : 'Not recorded'}
                    </div>
                    <div class="print-info-item">
                        <strong>BMI:</strong> ${record.bmi || 'Not calculated'}
                    </div>
                    <div class="print-info-item">
                        <strong>Blood Pressure:</strong> ${record.blood_pressure || 'Not recorded'}
                    </div>
                    <div class="print-info-item">
                        <strong>Heart Rate:</strong> ${record.heart_rate ? record.heart_rate + ' bpm' : 'Not recorded'}
                    </div>
                    <div class="print-info-item">
                        <strong>Temperature:</strong> ${record.temperature_c ? record.temperature_c + '°C' : 'Not recorded'}
                    </div>
                    <div class="print-info-item">
                        <strong>Respiratory Rate:</strong> ${record.respiratory_rate ? record.respiratory_rate + ' breaths/min' : 'Not recorded'}
                    </div>
                </div>
            </div>

            <div class="print-medical-records">
                <h3>SOAP Notes</h3>
                <div class="print-info-grid">
                    <div class="print-info-item">
                        <strong>Subjective:</strong> ${record.subjective || 'Not recorded'}
                    </div>
                    <div class="print-info-item">
                        <strong>Objective:</strong> ${record.objective || 'Not recorded'}
                    </div>
                    <div class="print-info-item">
                        <strong>Assessment:</strong> ${record.assessment || 'Not recorded'}
                    </div>
                    <div class="print-info-item">
                        <strong>Plan:</strong> ${record.plan || 'Not recorded'}
                    </div>
                </div>
            </div>

            <div class="print-medical-records">
                <h3>Diagnosis & Treatment</h3>
                <div class="print-info-grid">
                    <div class="print-info-item">
                        <strong>Diagnosis:</strong> ${record.diagnosis || 'Not recorded'}
                    </div>
                    <div class="print-info-item">
                        <strong>Treatment:</strong> ${record.treatment || 'Not recorded'}
                    </div>
                </div>
            </div>

            ${labImagesHTML}

            <div class="print-footer">
                <div class="print-footer-content">
                    <div>Printed on: ${currentDateTime} | Hospital Management System</div>
                </div>
            </div>
        `;

        printArea.innerHTML = finalHTML;
    }
}

// Initialize when script loads
console.log('[DEBUG] Creating MedicalRecordManager instance');
window.medicalRecordManager = new MedicalRecordManager(); 