class AppointmentManager {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.limit = 8;
        this.isEditing = false;
        this.currentAppointmentId = null;
        this.patients = [];
        this.doctors = [];
        
        this.init();
    }

    async init() {
        await this.loadPatients();
        await this.loadDoctors();
        this.bindEvents();
        this.loadAppointments();
    }
    
    bindEvents() {
        // Modal events
        document.getElementById('addAppointmentBtn').addEventListener('click', () => this.openModal());
        document.getElementById('closeModal').addEventListener('click', () => this.closeModal());
        document.getElementById('cancelModal').addEventListener('click', () => this.closeModal());
        document.getElementById('saveAppointment').addEventListener('click', () => this.saveAppointment());
        
        // Delete modal events
        document.getElementById('closeDeleteModal').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('cancelDelete').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('confirmDelete').addEventListener('click', () => this.deleteAppointment());
        
        // View modal events
        document.getElementById('closeViewModal').addEventListener('click', () => this.closeViewModal());
        document.getElementById('cancelView').addEventListener('click', () => this.closeViewModal());
        
        // Search and filter events
        document.getElementById('searchInput').addEventListener('input', this.debounce(() => this.searchAppointments(), 300));
        document.getElementById('statusFilter').addEventListener('change', () => this.searchAppointments());
        document.getElementById('doctorFilter').addEventListener('change', () => this.searchAppointments());
        document.getElementById('dateFilter').addEventListener('change', () => this.searchAppointments());
        document.getElementById('clearFilters').addEventListener('click', () => this.clearFilters());
        
        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => this.goToPage(this.currentPage - 1));
        document.getElementById('nextPage').addEventListener('click', () => this.goToPage(this.currentPage + 1));
        
        // Close modals when clicking background
        document.getElementById('appointmentModal').querySelector('.modal-background').addEventListener('click', () => this.closeModal());
        document.getElementById('deleteModal').querySelector('.modal-background').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('viewModal').querySelector('.modal-background').addEventListener('click', () => this.closeViewModal());
        
        // Add real-time validation for appointment time
        document.getElementById('appointmentTime').addEventListener('change', () => this.validateAppointmentTime());
        document.getElementById('appointmentDate').addEventListener('change', () => this.validateAppointmentTime());
        document.getElementById('doctorId').addEventListener('change', () => this.validateAppointmentTime());
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

    async loadPatients() {
        try {
            const response = await fetch('controllers/PatientController.php?action=list_all');
            const result = await response.json();
            
            if (result.success) {
                this.patients = result.data;
                this.populatePatientDropdowns();
            }
        } catch (error) {
            console.error('Error loading patients:', error);
        }
    }

    async loadDoctors() {
        try {
            const response = await fetch('controllers/DoctorController.php?action=list_all');
            const result = await response.json();
            
            if (result.success) {
                this.doctors = result.data;
                this.populateDoctorDropdowns();
            }
        } catch (error) {
            console.error('Error loading doctors:', error);
        }
    }

    populatePatientDropdowns() {
        const patientSelect = document.getElementById('patientId');
        patientSelect.innerHTML = '<option value="">Select Patient</option>';
        
        this.patients.forEach(patient => {
            const option = document.createElement('option');
            option.value = patient.patient_id;
            option.textContent = `${patient.first_name} ${patient.last_name}`;
            patientSelect.appendChild(option);
        });
    }

    populateDoctorDropdowns() {
        const doctorSelect = document.getElementById('doctorId');
        const doctorFilter = document.getElementById('doctorFilter');
        
        doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
        doctorFilter.innerHTML = '<option value="">All Doctors</option>';
        
        this.doctors.forEach(doctor => {
            // For appointment form
            const option = document.createElement('option');
            option.value = doctor.doctor_id;
            option.textContent = `Dr. ${doctor.first_name} ${doctor.last_name} - ${doctor.specialty}`;
            doctorSelect.appendChild(option);
            
            // For filter dropdown
            const filterOption = document.createElement('option');
            filterOption.value = doctor.doctor_id;
            filterOption.textContent = `Dr. ${doctor.first_name} ${doctor.last_name}`;
            doctorFilter.appendChild(filterOption);
        });
    }
    
    async loadAppointments() {
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                action: 'list',
                page: this.currentPage,
                limit: this.limit,
                search: document.getElementById('searchInput').value,
                status_filter: document.getElementById('statusFilter').value,
                doctor_filter: document.getElementById('doctorFilter').value,
                date_filter: document.getElementById('dateFilter').value
            });
            
            const response = await fetch(`controllers/AppointmentController.php?${params}`);
            const result = await response.json();
            
            if (result.success) {
                this.displayAppointments(result.data);
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
            console.error('Error loading appointments:', error);
            this.hideLoading();
            this.showNotification('Error loading appointments: ' + error.message, 'danger');
        }
    }
    
    displayAppointments(appointments) {
        const grid = document.getElementById('appointmentsGrid');
        grid.innerHTML = '';
        
        appointments.forEach(appointment => {
            const appointmentCard = this.createAppointmentCard(appointment);
            grid.appendChild(appointmentCard);
        });
    }
    
    createAppointmentCard(appointment) {
        const column = document.createElement('div');
        column.className = 'column';
        
        const statusClass = `status-${appointment.status}`;
        const appointmentDate = new Date(appointment.appointment_date);
        const formattedDate = appointmentDate.toLocaleDateString();
        const formattedTime = appointmentDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        // Check if appointment is today, upcoming, or past
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const appointmentDay = new Date(appointmentDate.getFullYear(), appointmentDate.getMonth(), appointmentDate.getDate());
        
        let priorityClass = 'priority-normal';
        let priorityIndicator = '';
        
        if (appointmentDay.getTime() === today.getTime()) {
            priorityClass = 'priority-urgent';
            priorityIndicator = '<span class="tag is-danger is-small mb-2">TODAY</span>';
        } else if (appointmentDate > now && appointmentDay.getTime() - today.getTime() <= 24 * 60 * 60 * 1000) {
            priorityClass = 'priority-urgent';
            priorityIndicator = '<span class="tag is-warning is-small mb-2">TOMORROW</span>';
        } else if (appointmentDate < now && appointment.status === 'scheduled') {
            priorityIndicator = '<span class="tag is-light is-small mb-2">OVERDUE</span>';
        }
        
        column.innerHTML = `
            <div class="card ${priorityClass}">
                <div class="card-content">
                    ${priorityIndicator}
                    <div class="media">
                        <div class="media-left">
                            <span class="icon is-large">
                                <i class="fas fa-calendar-check fa-2x has-text-primary"></i>
                            </span>
                        </div>
                        <div class="media-content">
                            <p class="title is-6">${appointment.patient_name}</p>
                            <p class="subtitle is-7">Dr. ${appointment.doctor_name}</p>
                            <p class="subtitle is-7">${appointment.specialty}</p>
                        </div>
                        <div class="media-right">
                            <span class="status-badge ${statusClass}">${appointment.status}</span>
                        </div>
                    </div>
                    <div class="content">
                        <p><strong>Date:</strong> ${formattedDate}</p>
                        <p><strong>Time:</strong> ${formattedTime}</p>
                        <p><strong>Purpose:</strong> ${appointment.purpose}</p>
                        <time datetime="${appointment.created_at}" class="is-size-7 has-text-grey">
                            Scheduled: ${new Date(appointment.created_at).toLocaleDateString()}
                        </time>
                    </div>
                </div>
                <footer class="card-footer">
                    <a href="#" class="card-footer-item" onclick="appointmentManager.openModal(${JSON.stringify(appointment).replace(/"/g, '&quot;')})">
                        <span class="icon is-small">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit</span>
                    </a>
                    <a href="#" class="card-footer-item" onclick="appointmentManager.confirmDeleteAppointment(${appointment.appointment_id}, '${appointment.patient_name} - ${formattedDate}')">
                        <span class="icon is-small">
                            <i class="fas fa-trash"></i>
                        </span>
                        <span>Delete</span>
                    </a>
                    <a href="#" class="card-footer-item" onclick="appointmentManager.viewAppointment(${appointment.appointment_id})">
                        <span class="icon is-small">
                            <i class="fas fa-eye"></i>
                        </span>
                        <span>View</span>
                    </a>
                    <a href="#" class="card-footer-item" onclick="appointmentManager.printReceipt(${appointment.appointment_id})">
                        <span class="icon is-small">
                            <i class="fas fa-print"></i>
                        </span>
                        <span>Print</span>
                    </a>
                </footer>
            </div>
        `;
        
        return column;
    }

    updatePagination(pagination) {
        this.currentPage = pagination.current_page;
        this.totalPages = pagination.total_pages;
        
        const paginationElement = document.getElementById('pagination');
        const paginationList = document.getElementById('paginationList');
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');
        
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
        
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(this.totalPages, this.currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.className = `pagination-link ${i === this.currentPage ? 'is-current' : ''}`;
            a.textContent = i;
            a.addEventListener('click', () => this.goToPage(i));
            li.appendChild(a);
            paginationList.appendChild(li);
        }
    }
    
    goToPage(page) {
        if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
            this.currentPage = page;
            this.loadAppointments();
        }
    }
    
    searchAppointments() {
        this.currentPage = 1;
        this.loadAppointments();
    }
    
    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('doctorFilter').value = '';
        document.getElementById('dateFilter').value = '';
        this.searchAppointments();
    }
    
    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('appointmentsContainer').style.display = 'none';
    }
    
    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('appointmentsContainer').style.display = 'block';
    }
    
    openModal(appointment = null) {
        this.isEditing = !!appointment;
        this.currentAppointmentId = appointment ? appointment.appointment_id : null;
        
        const modal = document.getElementById('appointmentModal');
        const title = document.getElementById('modalTitle');
        
        title.textContent = this.isEditing ? 'Edit Appointment' : 'Schedule New Appointment';
        
        if (appointment) {
            document.getElementById('appointmentId').value = appointment.appointment_id;
            document.getElementById('patientId').value = appointment.patient_id;
            document.getElementById('doctorId').value = appointment.doctor_id;
            
            // Split appointment_date into date and time
            const appointmentDate = new Date(appointment.appointment_date);
            document.getElementById('appointmentDate').value = appointmentDate.toISOString().split('T')[0];
            document.getElementById('appointmentTime').value = appointmentDate.toTimeString().split(' ')[0].substring(0, 5);
            
            document.getElementById('purpose').value = appointment.purpose || '';
            document.getElementById('status').value = appointment.status || '';
        } else {
            document.getElementById('appointmentForm').reset();
            document.getElementById('appointmentId').value = '';
            
            // Set default date to today
            const today = new Date();
            document.getElementById('appointmentDate').value = today.toISOString().split('T')[0];
        }
        
        modal.classList.add('is-active');
    }
    
    closeModal() {
        document.getElementById('appointmentModal').classList.remove('is-active');
    }
    
    async saveAppointment() {
        const form = document.getElementById('appointmentForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const appointmentDate = document.getElementById('appointmentDate').value;
        const appointmentTime = document.getElementById('appointmentTime').value;
        const appointmentDateTime = `${appointmentDate} ${appointmentTime}:00`;
        
        // Check if appointment is in the past
        const appointmentDateTimeObj = new Date(appointmentDateTime);
        const now = new Date();
        
        if (appointmentDateTimeObj <= now && document.getElementById('status').value === 'scheduled') {
            this.showNotification('Cannot schedule appointment in the past', 'danger');
            return;
        }
        
        const appointmentData = {
            patient_id: document.getElementById('patientId').value,
            doctor_id: document.getElementById('doctorId').value,
            appointment_date: appointmentDateTime,
            purpose: document.getElementById('purpose').value,
            status: document.getElementById('status').value
        };
        
        try {
            let url = 'controllers/AppointmentController.php?action=';
            let method = 'POST';
            
            if (this.isEditing) {
                url += `update&id=${this.currentAppointmentId}`;
                method = 'PUT';
            } else {
                url += 'create';
            }
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(appointmentData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.closeModal();
                this.loadAppointments();
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error saving appointment:', error);
            
            // Check if it's a double booking error
            if (error.message.includes('Double booking detected')) {
                this.showNotification(error.message, 'warning');
            } else {
                this.showNotification('Error saving appointment: ' + error.message, 'danger');
            }
        }
    }
    
    confirmDeleteAppointment(appointmentId, appointmentInfo) {
        this.currentAppointmentId = appointmentId;
        document.getElementById('deleteAppointmentInfo').textContent = appointmentInfo;
        document.getElementById('deleteModal').classList.add('is-active');
    }
    
    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
    }
    
    async deleteAppointment() {
        try {
            const response = await fetch('controllers/AppointmentController.php?action=delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: this.currentAppointmentId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.closeDeleteModal();
                this.loadAppointments();
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error deleting appointment:', error);
            this.showNotification('Error deleting appointment: ' + error.message, 'danger');
        }
    }
    
    async viewAppointment(appointmentId) {
        try {
            const response = await fetch(`controllers/AppointmentController.php?action=get&id=${appointmentId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showViewModal(result.data);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading appointment:', error);
            this.showNotification('Error loading appointment details: ' + error.message, 'danger');
        }
    }
    
    showViewModal(appointment) {
        // Format date and time
        const appointmentDate = new Date(appointment.appointment_date);
        const formattedDate = appointmentDate.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        const formattedTime = appointmentDate.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
        
        const createdDate = new Date(appointment.created_at);
        const formattedCreated = createdDate.toLocaleDateString('en-US') + ' at ' + createdDate.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
        
        // Populate modal fields
        document.getElementById('viewPatientName').textContent = appointment.patient_name || 'N/A';
        document.getElementById('viewDoctorName').textContent = `Dr. ${appointment.doctor_name}` || 'N/A';
        document.getElementById('viewAppointmentDate').textContent = formattedDate;
        document.getElementById('viewAppointmentTime').textContent = formattedTime;
        document.getElementById('viewPurpose').textContent = appointment.purpose || 'N/A';
        document.getElementById('viewSpecialty').textContent = appointment.specialty || 'N/A';
        document.getElementById('viewCreatedAt').textContent = formattedCreated;
        
        // Set status with appropriate styling
        const statusElement = document.getElementById('viewStatus');
        statusElement.textContent = appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1);
        statusElement.className = 'tag';
        
        switch (appointment.status) {
            case 'scheduled':
                statusElement.classList.add('is-info');
                break;
            case 'completed':
                statusElement.classList.add('is-success');
                break;
            case 'cancelled':
                statusElement.classList.add('is-danger');
                break;
            default:
                statusElement.classList.add('is-light');
        }
        
        // Show modal
        document.getElementById('viewModal').classList.add('is-active');
    }
    
    closeViewModal() {
        document.getElementById('viewModal').classList.remove('is-active');
    }
    
    async printReceipt(appointmentId) {
        try {
            const response = await fetch(`controllers/AppointmentController.php?action=get&id=${appointmentId}`);
            const result = await response.json();
            
            if (result.success) {
                this.generateReceipt(result.data);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading appointment for receipt:', error);
            this.showNotification('Error loading appointment details for receipt: ' + error.message, 'danger');
        }
    }
    
    generateReceipt(appointment) {
        // Format date and time
        const appointmentDate = new Date(appointment.appointment_date);
        const formattedDate = appointmentDate.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        const formattedTime = appointmentDate.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
        
        const printDate = new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const receiptHTML = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Appointment Receipt - ${appointment.patient_name}</title>
                <style>
                    body {
                        font-family: 'Arial', sans-serif;
                        line-height: 1.6;
                        color: #333;
                        max-width: 800px;
                        margin: 0 auto;
                        padding: 20px;
                        background: white;
                    }
                    .header {
                        text-align: center;
                        border-bottom: 3px solid #3273dc;
                        padding-bottom: 20px;
                        margin-bottom: 30px;
                    }
                    .hospital-name {
                        font-size: 28px;
                        font-weight: bold;
                        color: #3273dc;
                        margin-bottom: 5px;
                    }
                    .hospital-subtitle {
                        font-size: 16px;
                        color: #666;
                        margin-bottom: 10px;
                    }
                    .receipt-title {
                        font-size: 24px;
                        font-weight: bold;
                        color: #333;
                        margin-top: 15px;
                    }
                    .receipt-number {
                        font-size: 14px;
                        color: #666;
                        margin-top: 5px;
                    }
                    .appointment-details {
                        background: #f8f9fa;
                        padding: 25px;
                        border-radius: 8px;
                        margin: 20px 0;
                        border-left: 4px solid #3273dc;
                    }
                    .detail-row {
                        display: flex;
                        justify-content: space-between;
                        margin-bottom: 12px;
                        border-bottom: 1px dotted #ddd;
                        padding-bottom: 8px;
                    }
                    .detail-label {
                        font-weight: bold;
                        color: #555;
                        flex: 1;
                    }
                    .detail-value {
                        flex: 2;
                        text-align: right;
                        color: #333;
                    }
                    .status-badge {
                        display: inline-block;
                        padding: 5px 15px;
                        border-radius: 20px;
                        font-weight: bold;
                        text-transform: uppercase;
                        font-size: 12px;
                    }
                    .status-scheduled {
                        background: #3273dc;
                        color: white;
                    }
                    .status-completed {
                        background: #23d160;
                        color: white;
                    }
                    .status-cancelled {
                        background: #ff3860;
                        color: white;
                    }
                    .footer {
                        text-align: center;
                        margin-top: 40px;
                        padding-top: 20px;
                        border-top: 1px solid #ddd;
                        color: #666;
                        font-size: 14px;
                    }
                    .important-note {
                        background: #fff3cd;
                        border: 1px solid #ffeaa7;
                        padding: 15px;
                        border-radius: 5px;
                        margin: 20px 0;
                        font-size: 14px;
                    }
                    @media print {
                        body {
                            margin: 0;
                            padding: 15px;
                            font-size: 14px;
                        }
                        .hospital-name {
                            font-size: 24px;
                        }
                        .receipt-title {
                            font-size: 20px;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="hospital-name">Hospital Management System</div>
                    <div class="hospital-subtitle">Quality Healthcare for Everyone</div>
                    <div class="receipt-title">Appointment Receipt</div>
                    <div class="receipt-number">Receipt #: APT-${appointment.appointment_id.toString().padStart(6, '0')}</div>
                </div>
                
                <div class="appointment-details">
                    <div class="detail-row">
                        <span class="detail-label">Patient Name:</span>
                        <span class="detail-value">${appointment.patient_name}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Doctor:</span>
                        <span class="detail-value">Dr. ${appointment.doctor_name}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Specialty:</span>
                        <span class="detail-value">${appointment.specialty}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Appointment Date:</span>
                        <span class="detail-value">${formattedDate}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Appointment Time:</span>
                        <span class="detail-value">${formattedTime}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Purpose:</span>
                        <span class="detail-value">${appointment.purpose}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">
                            <span class="status-badge status-${appointment.status}">
                                ${appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1)}
                            </span>
                        </span>
                    </div>
                </div>
                
                <div class="important-note">
                    <strong>Important Instructions:</strong>
                    <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                        <li>Please arrive 15 minutes before your scheduled appointment time</li>
                        <li>Bring a valid ID and insurance card (if applicable)</li>
                        <li>Bring any relevant medical records or test results</li>
                        <li>Contact us immediately if you need to reschedule</li>
                    </ul>
                </div>
                
                <div class="footer">
                    <p><strong>Hospital Management System</strong></p>
                    <p>Phone: (555) 123-4567 | Email: info@hospital.com</p>
                    <p>Address: 123 Medical Center Drive, Healthcare City, HC 12345</p>
                    <p style="margin-top: 15px; font-style: italic;">
                        Receipt generated on ${printDate}
                    </p>
                </div>
            </body>
            </html>
        `;
        
        // Open in new window and print
        const printWindow = window.open('', '_blank', 'width=800,height=900');
        printWindow.document.write(receiptHTML);
        printWindow.document.close();
        
        // Wait for content to load then print
        printWindow.onload = function() {
            printWindow.print();
        };
        
        this.showNotification('Receipt opened in new window for printing', 'success');
    }
    
    showEmptyState() {
        document.getElementById('emptyState').style.display = 'block';
        document.getElementById('pagination').style.display = 'none';
    }
    
    hideEmptyState() {
        document.getElementById('emptyState').style.display = 'none';
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
        document.body.appendChild(notification);
        
        // Position notification
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        
        // Add close functionality
        notification.querySelector('.delete').addEventListener('click', () => {
            document.body.removeChild(notification);
        });
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 5000);
    }
    
    validateAppointmentTime() {
        const dateInput = document.getElementById('appointmentDate');
        const timeInput = document.getElementById('appointmentTime');
        const doctorSelect = document.getElementById('doctorId');
        
        if (!dateInput.value || !timeInput.value || !doctorSelect.value) {
            return;
        }
        
        const appointmentDateTime = `${dateInput.value} ${timeInput.value}:00`;
        const appointmentDateTimeObj = new Date(appointmentDateTime);
        const now = new Date();
        
        // Clear previous validation styles
        timeInput.classList.remove('is-danger', 'is-warning');
        
        // Check if appointment is in the past
        if (appointmentDateTimeObj <= now) {
            timeInput.classList.add('is-danger');
            this.showValidationMessage('Cannot schedule appointment in the past', 'danger');
            return;
        }
        
        // Check if it's outside business hours (8 AM - 6 PM)
        const hours = appointmentDateTimeObj.getHours();
        if (hours < 8 || hours >= 18) {
            timeInput.classList.add('is-warning');
            this.showValidationMessage('Appointment scheduled outside typical business hours (8 AM - 6 PM)', 'warning');
        }
        
        // Check if it's on weekend
        const dayOfWeek = appointmentDateTimeObj.getDay();
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            timeInput.classList.add('is-warning');
            this.showValidationMessage('Appointment scheduled on weekend', 'warning');
        }
    }
    
    showValidationMessage(message, type) {
        // Remove any existing validation message
        const existingMessage = document.querySelector('.validation-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Create new validation message
        const timeInput = document.getElementById('appointmentTime');
        const messageEl = document.createElement('p');
        messageEl.className = `help validation-message has-text-${type === 'danger' ? 'danger' : 'warning'}`;
        messageEl.textContent = message;
        
        // Insert after the time input
        timeInput.parentNode.appendChild(messageEl);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.remove();
            }
        }, 5000);
    }
}

// Initialize the appointment manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.appointmentManager = new AppointmentManager();
}); 