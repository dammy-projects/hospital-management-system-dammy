// Patient Management JavaScript
class PatientManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.totalPages = 1;
        this.currentPatientId = null;
        this.searchTimer = null;
        
        this.initializeEventListeners();
        this.loadPatients();
    }

    initializeEventListeners() {
        // Add Patient Button
        document.getElementById('addPatientBtn').addEventListener('click', () => {
            this.openModal();
        });

        // Modal Close Buttons
        document.getElementById('closeModal').addEventListener('click', () => {
            this.closeModal();
        });

        document.getElementById('cancelModal').addEventListener('click', () => {
            this.closeModal();
        });

        // Save Patient Button
        document.getElementById('savePatient').addEventListener('click', () => {
            this.savePatient();
        });

        // Delete Modal Close Buttons
        document.getElementById('closeDeleteModal').addEventListener('click', () => {
            this.closeDeleteModal();
        });

        document.getElementById('cancelDelete').addEventListener('click', () => {
            this.closeDeleteModal();
        });

        // Confirm Delete Button
        document.getElementById('confirmDelete').addEventListener('click', () => {
            this.deletePatient();
        });

        // Search Input with Debounce
        document.getElementById('searchInput').addEventListener('input', (e) => {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => {
                this.currentPage = 1;
                this.loadPatients();
            }, 500);
        });

        // Filter Dropdowns
        document.getElementById('genderFilter').addEventListener('change', () => {
            this.currentPage = 1;
            this.loadPatients();
        });

        document.getElementById('statusFilter').addEventListener('change', () => {
            this.currentPage = 1;
            this.loadPatients();
        });

        document.getElementById('sortBy').addEventListener('change', () => {
            this.currentPage = 1;
            this.loadPatients();
        });

        // Clear Filters Button
        document.getElementById('clearFilters').addEventListener('click', () => {
            this.clearFilters();
        });

        // Pagination
        document.getElementById('prevPage').addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadPatients();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.loadPatients();
            }
        });

        // Modal Background Click
        document.querySelector('#patientModal .modal-background').addEventListener('click', () => {
            this.closeModal();
        });

        document.querySelector('#deleteModal .modal-background').addEventListener('click', () => {
            this.closeDeleteModal();
        });

        document.querySelector('#viewPatientModal .modal-background').addEventListener('click', () => {
            this.closeViewModal();
        });

        // View Modal Close Buttons
        document.getElementById('closeViewModal').addEventListener('click', () => {
            this.closeViewModal();
        });

        document.getElementById('closeViewPatient').addEventListener('click', () => {
            this.closeViewModal();
        });

        // Form Submit Prevention
        document.getElementById('patientForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.savePatient();
        });
    }

    async loadPatients() {
        this.showLoading();
        
        try {
            const searchTerm = document.getElementById('searchInput').value;
            const genderFilter = document.getElementById('genderFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const sortBy = document.getElementById('sortBy').value;
            
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.itemsPerPage,
                search: searchTerm,
                gender: genderFilter,
                status: statusFilter,
                sort: sortBy
            });

            const response = await fetch(`controllers/PatientController.php?action=list&${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderPatients(data.patients);
                this.renderPagination(data.pagination);
                this.hideLoading();
                
                if (data.patients.length === 0) {
                    this.showEmptyState();
                } else {
                    this.hideEmptyState();
                }
            } else {
                this.showError('Failed to load patients: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading patients:', error);
            this.showError('Error loading patients. Please try again.');
        }
    }

    renderPatients(patients) {
        const grid = document.getElementById('patientsGrid');
        grid.innerHTML = '';

        patients.forEach(patient => {
            const patientCard = this.createPatientCard(patient);
            grid.appendChild(patientCard);
        });
    }

    createPatientCard(patient) {
        const column = document.createElement('div');
        column.className = 'column is-4';

        const statusClass = patient.status === 'active' ? 'is-success' : 
                           patient.status === 'inactive' ? 'is-warning' : 'is-dark';
        const statusIcon = patient.status === 'active' ? 'fa-check-circle' : 
                          patient.status === 'inactive' ? 'fa-pause-circle' : 'fa-times-circle';

        const genderClass = patient.gender === 'male' ? 'is-info' : 
                           patient.gender === 'female' ? 'is-primary' : 'is-light';

        // Calculate age
        const birthDate = new Date(patient.date_of_birth);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();

        // Full name
        const fullName = `${patient.first_name} ${patient.middle_name ? patient.middle_name + ' ' : ''}${patient.last_name}`;

        // Format contact info
        const contactInfo = patient.contact_number || 'No contact';
        const email = patient.email || 'No email';

        column.innerHTML = `
            <div class="card">
                <div class="card-content">
                    <div class="media">
                        <div class="media-left">
                            <span class="icon is-large has-text-primary">
                                <i class="fas fa-user-circle fa-2x"></i>
                            </span>
                        </div>
                        <div class="media-content">
                            <p class="title is-6">${this.escapeHtml(fullName)}</p>
                            <p class="subtitle is-7">
                                <span class="icon">
                                    <i class="fas fa-birthday-cake"></i>
                                </span>
                                Age ${age} • ${this.formatDate(patient.date_of_birth)}
                            </p>
                            <div class="patient-details mb-2">
                                <div class="mb-1">
                                    <span class="icon is-small">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    ${this.escapeHtml(contactInfo)}
                                </div>
                                <div class="mb-1">
                                    <span class="icon is-small">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    ${this.escapeHtml(email)}
                                </div>
                            </div>
                            <div class="tags">
                                <span class="tag ${genderClass} gender-badge">
                                    <span class="icon is-small">
                                        <i class="fas ${patient.gender === 'male' ? 'fa-mars' : patient.gender === 'female' ? 'fa-venus' : 'fa-genderless'}"></i>
                                    </span>
                                    <span>${patient.gender}</span>
                                </span>
                                <span class="tag ${statusClass}">
                                    <span class="icon is-small">
                                        <i class="fas ${statusIcon}"></i>
                                    </span>
                                    <span>${patient.status.charAt(0).toUpperCase() + patient.status.slice(1)}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="card-footer">
                    <a class="card-footer-item has-text-primary" onclick="patientManager.viewPatient(${patient.patient_id})">
                        <span class="icon">
                            <i class="fas fa-eye"></i>
                        </span>
                        <span>View</span>
                    </a>
                    <a class="card-footer-item has-text-success" onclick="patientManager.printPatient(${patient.patient_id})">
                        <span class="icon">
                            <i class="fas fa-print"></i>
                        </span>
                        <span>Print</span>
                    </a>
                    <a class="card-footer-item has-text-info" onclick="patientManager.editPatient(${patient.patient_id})">
                        <span class="icon">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit</span>
                    </a>
                    <a class="card-footer-item has-text-danger" onclick="patientManager.confirmDelete(${patient.patient_id}, '${this.escapeHtml(fullName)}')">
                        <span class="icon">
                            <i class="fas fa-trash"></i>
                        </span>
                        <span>Delete</span>
                    </a>
                </footer>
            </div>
        `;

        return column;
    }

    renderPagination(pagination) {
        this.totalPages = pagination.total_pages;
        const paginationContainer = document.getElementById('pagination');
        const paginationList = document.getElementById('paginationList');
        
        if (this.totalPages <= 1) {
            paginationContainer.style.display = 'none';
            return;
        }

        paginationContainer.style.display = 'flex';
        paginationList.innerHTML = '';

        // Update Previous/Next buttons
        document.getElementById('prevPage').classList.toggle('is-disabled', this.currentPage === 1);
        document.getElementById('nextPage').classList.toggle('is-disabled', this.currentPage === this.totalPages);

        // Generate page numbers
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(this.totalPages, this.currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.innerHTML = `
                <a class="pagination-link ${i === this.currentPage ? 'is-current' : ''}" 
                   onclick="patientManager.goToPage(${i})">${i}</a>
            `;
            paginationList.appendChild(li);
        }
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadPatients();
    }

    openModal(patient = null) {
        const modal = document.getElementById('patientModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('patientForm');
        
        // Reset form
        form.reset();
        
        if (patient) {
            // Edit mode
            modalTitle.textContent = 'Edit Patient';
            document.getElementById('patientId').value = patient.patient_id;
            document.getElementById('firstName').value = patient.first_name;
            document.getElementById('middleName').value = patient.middle_name || '';
            document.getElementById('lastName').value = patient.last_name;
            document.getElementById('dateOfBirth').value = patient.date_of_birth;
            document.getElementById('gender').value = patient.gender;
            document.getElementById('contactNumber').value = patient.contact_number || '';
            document.getElementById('email').value = patient.email || '';
            document.getElementById('address').value = patient.address || '';
            document.getElementById('medicalHistory').value = patient.medical_history || '';
            document.getElementById('status').value = patient.status;
            
            // Emergency contact fields
            document.getElementById('emergencyContactName').value = patient.emergency_contact_name || '';
            document.getElementById('emergencyContactRelationship').value = patient.emergency_contact_relationship || '';
            document.getElementById('emergencyContactPhone').value = patient.emergency_contact_phone || '';
            document.getElementById('emergencyContactAddress').value = patient.emergency_contact_address || '';
            
            // Guardian/parent fields
            document.getElementById('guardianName').value = patient.guardian_name || '';
            document.getElementById('guardianRelationship').value = patient.guardian_relationship || '';
            document.getElementById('guardianPhone').value = patient.guardian_phone || '';
            document.getElementById('guardianAddress').value = patient.guardian_address || '';
            
            this.currentPatientId = patient.patient_id;
        } else {
            // Add mode
            modalTitle.textContent = 'Add New Patient';
            document.getElementById('patientId').value = '';
            this.currentPatientId = null;
        }
        
        modal.classList.add('is-active');
    }

    closeModal() {
        document.getElementById('patientModal').classList.remove('is-active');
        this.currentPatientId = null;
    }

    async editPatient(patientId) {
        try {
            const response = await fetch(`controllers/PatientController.php?action=get&id=${patientId}`);
            const data = await response.json();
            
            if (data.success) {
                this.openModal(data.patient);
                return Promise.resolve(); // Return resolved promise for chaining
            } else {
                this.showError('Failed to load patient data: ' + data.message);
                return Promise.reject('Failed to load patient data');
            }
        } catch (error) {
            console.error('Error loading patient:', error);
            this.showError('Error loading patient data.');
            return Promise.reject(error);
        }
    }

    async savePatient() {
        const form = document.getElementById('patientForm');
        const saveButton = document.getElementById('savePatient');
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Disable button during save
        saveButton.classList.add('is-loading');
        saveButton.disabled = true;

        try {
            const formData = new FormData();
            formData.append('action', this.currentPatientId ? 'update' : 'create');
            
            if (this.currentPatientId) {
                formData.append('patient_id', this.currentPatientId);
            }
            
            formData.append('first_name', document.getElementById('firstName').value.trim());
            formData.append('middle_name', document.getElementById('middleName').value.trim());
            formData.append('last_name', document.getElementById('lastName').value.trim());
            formData.append('date_of_birth', document.getElementById('dateOfBirth').value);
            formData.append('gender', document.getElementById('gender').value);
            formData.append('contact_number', document.getElementById('contactNumber').value.trim());
            formData.append('email', document.getElementById('email').value.trim());
            formData.append('address', document.getElementById('address').value.trim());
            formData.append('medical_history', document.getElementById('medicalHistory').value.trim());
            formData.append('status', document.getElementById('status').value);

            // Emergency contact fields
            formData.append('emergency_contact_name', document.getElementById('emergencyContactName').value.trim());
            formData.append('emergency_contact_relationship', document.getElementById('emergencyContactRelationship').value);
            formData.append('emergency_contact_phone', document.getElementById('emergencyContactPhone').value.trim());
            formData.append('emergency_contact_address', document.getElementById('emergencyContactAddress').value.trim());
            
            // Guardian/parent fields
            formData.append('guardian_name', document.getElementById('guardianName').value.trim());
            formData.append('guardian_relationship', document.getElementById('guardianRelationship').value);
            formData.append('guardian_phone', document.getElementById('guardianPhone').value.trim());
            formData.append('guardian_address', document.getElementById('guardianAddress').value.trim());

            const response = await fetch('controllers/PatientController.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.closeModal();
                this.loadPatients();
                this.showSuccess(this.currentPatientId ? 'Patient updated successfully!' : 'Patient created successfully!');
            } else {
                this.showError('Failed to save patient: ' + data.message);
            }
        } catch (error) {
            console.error('Error saving patient:', error);
            this.showError('Error saving patient. Please try again.');
        } finally {
            saveButton.classList.remove('is-loading');
            saveButton.disabled = false;
        }
    }

    confirmDelete(patientId, patientName) {
        this.currentPatientId = patientId;
        document.getElementById('deletePatientName').textContent = patientName;
        document.getElementById('deleteModal').classList.add('is-active');
    }

    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
        this.currentPatientId = null;
    }

    async deletePatient() {
        const deleteButton = document.getElementById('confirmDelete');
        
        deleteButton.classList.add('is-loading');
        deleteButton.disabled = true;

        try {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('patient_id', this.currentPatientId);

            const response = await fetch('controllers/PatientController.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.closeDeleteModal();
                this.loadPatients();
                this.showSuccess('Patient deleted successfully!');
            } else {
                this.showError('Failed to delete patient: ' + data.message);
            }
        } catch (error) {
            console.error('Error deleting patient:', error);
            this.showError('Error deleting patient. Please try again.');
        } finally {
            deleteButton.classList.remove('is-loading');
            deleteButton.disabled = false;
        }
    }

    async viewPatient(patientId) {
        if (!patientId || patientId === 'undefined' || patientId === 'null') {
            this.showError('Patient ID is missing or invalid');
            return;
        }
        
        try {
            const response = await fetch(`controllers/PatientController.php?action=get&id=${patientId}`, {
                method: 'GET',
                credentials: 'include'
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showPatientDetails(data.patient, data.insurances || []);
                this.currentViewPatientId = patientId;
            } else {
                this.showError('Failed to load patient data: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading patient:', error);
            this.showError('Error loading patient data.');
        }
    }

    showPatientDetails(patient, insurances = []) {
        // Calculate age
        const birthDate = new Date(patient.date_of_birth);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        // Full name
        const fullName = `${patient.first_name} ${patient.middle_name ? patient.middle_name + ' ' : ''}${patient.last_name}`;

        // Determine age group
        let ageGroup;
        if (age < 2) ageGroup = 'Infant';
        else if (age < 13) ageGroup = 'Child';
        else if (age < 20) ageGroup = 'Teenager';
        else if (age < 60) ageGroup = 'Adult';
        else ageGroup = 'Senior';

        // Header section
        document.getElementById('viewFullName').textContent = fullName;
        document.getElementById('viewPatientId').textContent = `#${patient.patient_id}`;
        
        // Header tags
        document.getElementById('viewAge').textContent = `${age} years old`;
        document.getElementById('viewAge').className = 'tag is-large is-info';
        
        // Gender tag with icon and color
        const genderElement = document.getElementById('viewGender');
        const genderClass = patient.gender === 'male' ? 'is-info' : 
                           patient.gender === 'female' ? 'is-primary' : 'is-light';
        const genderIcon = patient.gender === 'male' ? 'fa-mars' : 
                          patient.gender === 'female' ? 'fa-venus' : 'fa-genderless';
        genderElement.className = `tag is-large ${genderClass}`;
        genderElement.innerHTML = `
            <span class="icon is-small">
                <i class="fas ${genderIcon}"></i>
            </span>
            <span>${patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1)}</span>
        `;

        // Status tag with icon and color
        const statusElement = document.getElementById('viewStatus');
        const statusClass = patient.status === 'active' ? 'is-success' : 
                           patient.status === 'inactive' ? 'is-warning' : 'is-dark';
        const statusIcon = patient.status === 'active' ? 'fa-check-circle' : 
                          patient.status === 'inactive' ? 'fa-pause-circle' : 'fa-times-circle';
        statusElement.className = `tag is-large ${statusClass}`;
        statusElement.innerHTML = `
            <span class="icon is-small">
                <i class="fas ${statusIcon}"></i>
            </span>
            <span>${patient.status.charAt(0).toUpperCase() + patient.status.slice(1)}</span>
        `;

        // Personal Information
        document.getElementById('viewDateOfBirth').textContent = this.formatDate(patient.date_of_birth);
        document.getElementById('viewRegisteredDate').textContent = this.formatDate(patient.created_at);

        // Contact Information
        document.getElementById('viewContactNumber').textContent = patient.contact_number || 'Not provided';
        document.getElementById('viewEmail').textContent = patient.email || 'Not provided';
        
        // Address
        const addressElement = document.getElementById('viewAddress');
        if (patient.address && patient.address.trim()) {
            addressElement.textContent = patient.address;
            addressElement.classList.remove('has-text-grey-light');
        } else {
            addressElement.textContent = 'Address not provided';
            addressElement.classList.add('has-text-grey-light');
        }

        // Medical History
        const medicalHistoryElement = document.getElementById('viewMedicalHistory');
        if (patient.medical_history && patient.medical_history.trim()) {
            medicalHistoryElement.textContent = patient.medical_history;
            medicalHistoryElement.classList.remove('has-text-grey-light');
        } else {
            medicalHistoryElement.textContent = 'No medical history recorded';
            medicalHistoryElement.classList.add('has-text-grey-light');
        }

        // Insurance Information - IMPROVED IMPLEMENTATION
        const insuranceSection = document.getElementById('insuranceSection');
        const insuranceContent = document.getElementById('viewInsuranceContent');
        
        // Always show insurance section
        insuranceSection.style.display = 'block';
        
        if (insurances && Array.isArray(insurances) && insurances.length > 0) {
            let insuranceHTML = '';
            
            insurances.forEach((insurance, index) => {
                const statusClass = insurance.status === 'active' ? 'is-success' : 'is-warning';
                const statusText = insurance.status ? insurance.status.charAt(0).toUpperCase() + insurance.status.slice(1) : 'Unknown';
                
                insuranceHTML += `
                    <div class="box${index > 0 ? ' mt-3' : ''}">
                        <div class="columns is-mobile">
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label is-small">Insurance Provider</label>
                                    <p><strong>${this.escapeHtml(insurance.provider_name || 'N/A')}</strong></p>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label is-small">Status</label>
                                    <span class="tag ${statusClass}">${statusText}</span>
                                </div>
                            </div>
                        </div>
                        <div class="columns is-mobile">
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label is-small">Policy Number</label>
                                    <p>${this.escapeHtml(insurance.insurance_number || 'N/A')}</p>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label is-small">Provider Contact</label>
                                    <p>${this.escapeHtml(insurance.provider_contact || 'N/A')}</p>
                                </div>
                            </div>
                        </div>
                        ${insurance.provider_address ? `
                        <div class="field">
                            <label class="label is-small">Provider Address</label>
                            <p>${this.escapeHtml(insurance.provider_address)}</p>
                        </div>` : ''}
                    </div>
                `;
            });
            
            insuranceContent.innerHTML = insuranceHTML;
        } else {
            insuranceContent.innerHTML = `
                <div class="box">
                    <p class="has-text-grey-light">No insurance information available</p>
                </div>
            `;
        }

        // Summary Sidebar
        document.getElementById('viewLastUpdated').textContent = this.formatDate(patient.updated_at);
        document.getElementById('viewAgeGroup').textContent = ageGroup;

        // Status Summary (duplicate for sidebar)
        const statusSummaryElement = document.getElementById('viewStatusSummary');
        statusSummaryElement.className = `tag is-medium ${statusClass}`;
        statusSummaryElement.innerHTML = `
            <span class="icon is-small">
                <i class="fas ${statusIcon}"></i>
            </span>
            <span>${patient.status.charAt(0).toUpperCase() + patient.status.slice(1)}</span>
        `;

        // Gender Summary (duplicate for sidebar)
        const genderSummaryElement = document.getElementById('viewGenderSummary');
        genderSummaryElement.className = `tag is-medium ${genderClass}`;
        genderSummaryElement.innerHTML = `
            <span class="icon is-small">
                <i class="fas ${genderIcon}"></i>
            </span>
            <span>${patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1)}</span>
        `;

        // Emergency Contact Information
        document.getElementById('viewEmergencyContactName').textContent = patient.emergency_contact_name || 'Not provided';
        document.getElementById('viewEmergencyContactPhone').textContent = patient.emergency_contact_phone || 'Not provided';
        
        // Emergency contact relationship tag
        const emergencyRelationshipElement = document.getElementById('viewEmergencyContactRelationship');
        if (patient.emergency_contact_relationship) {
            emergencyRelationshipElement.textContent = patient.emergency_contact_relationship.charAt(0).toUpperCase() + patient.emergency_contact_relationship.slice(1);
            emergencyRelationshipElement.className = 'tag is-medium is-warning';
        } else {
            emergencyRelationshipElement.textContent = 'Not specified';
            emergencyRelationshipElement.className = 'tag is-medium is-light';
        }
        
        // Emergency contact address
        const emergencyAddressElement = document.getElementById('viewEmergencyContactAddress');
        if (patient.emergency_contact_address && patient.emergency_contact_address.trim()) {
            emergencyAddressElement.textContent = patient.emergency_contact_address;
            emergencyAddressElement.classList.remove('has-text-grey-light');
        } else {
            emergencyAddressElement.textContent = 'Address not provided';
            emergencyAddressElement.classList.add('has-text-grey-light');
        }

        // Guardian/Parent Information
        const guardianSection = document.getElementById('guardianSection');
        if (patient.guardian_name || patient.guardian_phone || patient.guardian_address) {
            guardianSection.style.display = 'block';
            
            document.getElementById('viewGuardianName').textContent = patient.guardian_name || 'Not provided';
            document.getElementById('viewGuardianPhone').textContent = patient.guardian_phone || 'Not provided';
            
            // Guardian relationship tag
            const guardianRelationshipElement = document.getElementById('viewGuardianRelationship');
            if (patient.guardian_relationship) {
                let relationshipText = patient.guardian_relationship.replace('_', '/');
                relationshipText = relationshipText.charAt(0).toUpperCase() + relationshipText.slice(1);
                guardianRelationshipElement.textContent = relationshipText;
                guardianRelationshipElement.className = 'tag is-medium is-info';
            } else {
                guardianRelationshipElement.textContent = 'Not specified';
                guardianRelationshipElement.className = 'tag is-medium is-light';
            }
            
            // Guardian address
            const guardianAddressElement = document.getElementById('viewGuardianAddress');
            if (patient.guardian_address && patient.guardian_address.trim()) {
                guardianAddressElement.textContent = patient.guardian_address;
                guardianAddressElement.classList.remove('has-text-grey-light');
            } else {
                guardianAddressElement.textContent = 'Address not provided';
                guardianAddressElement.classList.add('has-text-grey-light');
            }
        } else {
            guardianSection.style.display = 'none';
        }

        // Show modal
        document.getElementById('viewPatientModal').classList.add('is-active');
    }

    closeViewModal() {
        document.getElementById('viewPatientModal').classList.remove('is-active');
        this.currentViewPatientId = null;
    }

    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('genderFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('sortBy').value = 'first_name';
        this.currentPage = 1;
        this.loadPatients();
    }

    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('patientsContainer').style.display = 'none';
        document.getElementById('pagination').style.display = 'none';
    }

    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('patientsContainer').style.display = 'block';
    }

    showEmptyState() {
        document.getElementById('emptyState').style.display = 'block';
        document.getElementById('patientsContainer').style.display = 'none';
    }

    hideEmptyState() {
        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('patientsContainer').style.display = 'block';
    }

    showSuccess(message) {
        this.showNotification(message, 'is-success');
    }

    showError(message) {
        this.showNotification(message, 'is-danger');
    }

    showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification ${type} is-light`;
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

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
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
    
    async printPatient(patientId) {
        try {
            const response = await fetch(`controllers/PatientController.php?action=get&id=${patientId}`, {
                method: 'GET',
                credentials: 'include'
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.generatePatientPrintContent(data.patient, data.insurances || []);
                window.print();
            } else {
                this.showError('Failed to load patient data for printing: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading patient data for printing:', error);
            this.showError('Error loading patient data for printing.');
        }
    }
    
    generatePatientPrintContent(patient, insurances = []) {
        const printArea = document.getElementById('patientPrintArea');
        const currentDateTime = new Date().toLocaleString();
        const age = patient.date_of_birth ? new Date().getFullYear() - new Date(patient.date_of_birth).getFullYear() : 'N/A';
        const fullName = `${patient.first_name} ${patient.middle_name ? patient.middle_name + ' ' : ''}${patient.last_name}`;
        
        // Generate insurance information HTML - IMPROVED IMPLEMENTATION
        let insuranceHTML = '';
        if (insurances && Array.isArray(insurances) && insurances.length > 0) {
            insuranceHTML = `
            <div class="print-patients">
                <h3>Insurance Information</h3>
                <table class="patients-table">`;
            
            insurances.forEach((insurance, index) => {
                if (index > 0) {
                    insuranceHTML += `<tr><td colspan="4" style="border-top: 2px solid #ccc; padding: 4px;"></td></tr>`;
                }
                insuranceHTML += `
                    <tr>
                        <td style="width: 25%; font-weight: bold;">Insurance Provider</td>
                        <td style="width: 25%;">${this.escapeHtml(insurance.provider_name || 'N/A')}</td>
                        <td style="width: 25%; font-weight: bold;">Policy Number</td>
                        <td style="width: 25%;">${this.escapeHtml(insurance.insurance_number || 'N/A')}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Status</td>
                        <td style="text-transform: capitalize;">${this.escapeHtml(insurance.status || 'N/A')}</td>
                        <td style="font-weight: bold;">Provider Contact</td>
                        <td>${this.escapeHtml(insurance.provider_contact || 'N/A')}</td>
                    </tr>`;
                
                if (insurance.provider_address) {
                    insuranceHTML += `
                    <tr>
                        <td style="font-weight: bold;">Provider Address</td>
                        <td colspan="3">${this.escapeHtml(insurance.provider_address)}</td>
                    </tr>`;
                }
            });
            
            insuranceHTML += `
                </table>
            </div>`;
        } else {
            // Show "No insurance" section for completeness
            insuranceHTML = `
            <div class="print-patients">
                <h3>Insurance Information</h3>
                <table class="patients-table">
                    <tr>
                        <td style="text-align: center; padding: 20px; color: #666;">No insurance information available</td>
                    </tr>
                </table>
            </div>`;
        }
        
        const finalHTML = `
            <div class="print-header">
                <div class="print-title">Hospital Management System</div>
                <div class="print-subtitle">Patient Information Record</div>
            </div>
            
            <div class="print-info">
                <h3>Patient Information</h3>
                <div class="print-info-grid">
                    <div><strong>Patient Name:</strong> ${this.escapeHtml(fullName)}</div>
                    <div><strong>Patient ID:</strong> #${patient.patient_id}</div>
                    <div><strong>Date of Birth:</strong> ${patient.date_of_birth ? new Date(patient.date_of_birth).toLocaleDateString() : 'N/A'}</div>
                    <div><strong>Age:</strong> ${age} years old</div>
                    <div><strong>Gender:</strong> ${patient.gender ? patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : 'N/A'}</div>
                    <div><strong>Status:</strong> ${patient.status ? patient.status.charAt(0).toUpperCase() + patient.status.slice(1) : 'N/A'}</div>
                </div>
            </div>
            
            <div class="print-patients">
                <h3>Contact & Personal Details</h3>
                <table class="patients-table">
                    <tr>
                        <td style="width: 20%; font-weight: bold;">Phone Number</td>
                        <td style="width: 30%;">${this.escapeHtml(patient.contact_number || 'N/A')}</td>
                        <td style="width: 20%; font-weight: bold;">Email Address</td>
                        <td style="width: 30%;">${this.escapeHtml(patient.email || 'N/A')}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Home Address</td>
                        <td colspan="3">${this.escapeHtml(patient.address || 'N/A')}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Registration Date</td>
                        <td>${patient.created_at ? new Date(patient.created_at).toLocaleDateString() : 'N/A'}</td>
                        <td style="font-weight: bold;">Last Updated</td>
                        <td>${patient.updated_at ? new Date(patient.updated_at).toLocaleDateString() : 'N/A'}</td>
                    </tr>
                </table>
            </div>
            
            ${insuranceHTML}
            
            ${patient.medical_history ? `<div class="print-patients">
                <h3>Medical History</h3>
                <table class="patients-table">
                    <tr>
                        <td style="padding: 8px;">${this.escapeHtml(patient.medical_history)}</td>
                    </tr>
                </table>
            </div>` : ''}
            
            ${patient.emergency_contact_name ? `<div class="print-patients">
                <h3>Emergency Contact Information</h3>
                <table class="patients-table">
                    <tr>
                        <td style="width: 25%; font-weight: bold;">Contact Name</td>
                        <td style="width: 25%;">${this.escapeHtml(patient.emergency_contact_name)}</td>
                        <td style="width: 25%; font-weight: bold;">Relationship</td>
                        <td style="width: 25%;">${this.escapeHtml(patient.emergency_contact_relationship || 'N/A')}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Phone Number</td>
                        <td>${this.escapeHtml(patient.emergency_contact_phone || 'N/A')}</td>
                        <td style="font-weight: bold;">Address</td>
                        <td>${this.escapeHtml(patient.emergency_contact_address || 'N/A')}</td>
                    </tr>
                </table>
            </div>` : ''}
            
            ${patient.guardian_name ? `<div class="print-patients">
                <h3>Guardian/Parent Information</h3>
                <table class="patients-table">
                    <tr>
                        <td style="width: 25%; font-weight: bold;">Guardian Name</td>
                        <td style="width: 25%;">${this.escapeHtml(patient.guardian_name)}</td>
                        <td style="width: 25%; font-weight: bold;">Relationship</td>
                        <td style="width: 25%;">${this.escapeHtml(patient.guardian_relationship || 'N/A')}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Phone Number</td>
                        <td>${this.escapeHtml(patient.guardian_phone || 'N/A')}</td>
                        <td style="font-weight: bold;">Address</td>
                        <td>${this.escapeHtml(patient.guardian_address || 'N/A')}</td>
                    </tr>
                </table>
            </div>` : ''}
            
            <div class="print-footer">
                <div>Patient Record ID: PAT-${patient.patient_id}-${Date.now()}</div>
                <div>Printed on: ${currentDateTime} | Hospital Management System</div>
                <div style="margin-top: 4px; font-weight: bold;">🏥 Confidential Patient Information - Handle with Care</div>
            </div>
        `;
        
        printArea.innerHTML = finalHTML;
    }
}

// Initialize the patient manager when the page loads
let patientManager;
document.addEventListener('DOMContentLoaded', () => {
    patientManager = new PatientManager();
});