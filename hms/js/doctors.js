class DoctorManager {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.limit = 6;
        this.isEditing = false;
        this.currentDoctorId = null;
        this.departments = [];
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadDoctors();
        this.loadDepartments();
        this.loadDepartmentsForFilter();
    }
    
    bindEvents() {
        // Modal events
        document.getElementById('addDoctorBtn').addEventListener('click', () => this.openModal());
        document.getElementById('closeModal').addEventListener('click', () => this.closeModal());
        document.getElementById('cancelModal').addEventListener('click', () => this.closeModal());
        document.getElementById('saveDoctor').addEventListener('click', () => this.saveDoctor());
        
        // View modal events
        document.getElementById('closeViewModal').addEventListener('click', () => this.closeViewModal());
        document.getElementById('cancelView').addEventListener('click', () => this.closeViewModal());
        
        // Delete modal events
        document.getElementById('closeDeleteModal').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('cancelDelete').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('confirmDelete').addEventListener('click', () => this.deleteDoctor());
        
        // Search and filter events
        document.getElementById('searchInput').addEventListener('input', this.debounce(() => this.searchDoctors(), 300));
        document.getElementById('departmentFilter').addEventListener('change', () => this.searchDoctors());
        document.getElementById('statusFilter').addEventListener('change', () => this.searchDoctors());
        document.getElementById('clearFilters').addEventListener('click', () => this.clearFilters());
        
        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => this.goToPage(this.currentPage - 1));
        document.getElementById('nextPage').addEventListener('click', () => this.goToPage(this.currentPage + 1));
        
        // Close modals when clicking background
        document.getElementById('doctorModal').querySelector('.modal-background').addEventListener('click', () => this.closeModal());
        document.getElementById('viewModal').querySelector('.modal-background').addEventListener('click', () => this.closeViewModal());
        document.getElementById('deleteModal').querySelector('.modal-background').addEventListener('click', () => this.closeDeleteModal());
        
        // Form submit prevention
        document.getElementById('doctorForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveDoctor();
        });
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
    
    async loadDoctors() {
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                action: 'list',
                page: this.currentPage,
                limit: this.limit,
                search: document.getElementById('searchInput').value,
                department_filter: document.getElementById('departmentFilter').value,
                status_filter: document.getElementById('statusFilter').value
            });
            
            const response = await fetch(`controllers/DoctorController.php?${params}`);
            const result = await response.json();
            
            if (result.success) {
                this.displayDoctors(result.data);
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
            console.error('Error loading doctors:', error);
            this.hideLoading();
            this.showNotification('Error loading doctors: ' + error.message, 'danger');
        }
    }
    
    displayDoctors(doctors) {
        const grid = document.getElementById('doctorsGrid');
        grid.innerHTML = '';
        
        doctors.forEach(doctor => {
            const card = this.createDoctorCard(doctor);
            grid.appendChild(card);
        });
    }
    
    createDoctorCard(doctor) {
        const card = document.createElement('div');
        card.className = 'column is-4';
        
        const statusColor = {
            'active': 'success',
            'inactive': 'warning'
        }[doctor.status] || 'info';
        
        card.innerHTML = `
            <div class="card" style="display: flex; flex-direction: column; height: 220px;">
                <div class="card-content" style="padding: 0.75rem; flex: 1; display: flex; flex-direction: column;">
                    <div class="has-text-centered mb-2">
                        <div class="mb-1">
                            <span class="icon is-small has-text-primary">
                                <i class="fas fa-user-md fa-lg"></i>
                            </span>
                        </div>
                        <h6 class="title is-6 mb-1" style="line-height: 1.1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(doctor.full_name || 'N/A')}">${this.escapeHtml(doctor.full_name || 'N/A')}</h6>
                        <p class="subtitle is-7 has-text-grey mb-2" style="line-height: 1; margin-top: 0.1rem;">${this.escapeHtml(doctor.specialty || 'N/A')}</p>
                    </div>
                    
                    <div class="content" style="font-size: 0.75rem; flex: 1;">
                        <div class="field mb-2">
                            <span class="tag is-${statusColor} is-light is-small">${doctor.status}</span>
                            <span class="tag is-info is-light is-small ml-1" style="font-size: 0.65rem;">${this.escapeHtml(doctor.department_name || 'N/A')}</span>
                        </div>
                        
                        <p class="mb-1 is-size-7" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(doctor.email)}"><strong>Email:</strong> ${this.escapeHtml(doctor.email)}</p>
                        
                        ${doctor.contact_number ? `<p class="mb-1 is-size-7" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(doctor.contact_number)}"><strong>Phone:</strong> ${this.escapeHtml(doctor.contact_number)}</p>` : ''}
                    </div>
                </div>
                
                <footer class="card-footer" style="border-top: 1px solid #dbdbdb; margin-top: auto;">
                    <a class="card-footer-item has-text-primary is-size-7" onclick="doctorManager.viewDoctor(${doctor.doctor_id})" style="padding: 0.4rem;">
                        <span class="icon is-small">
                            <i class="fas fa-eye"></i>
                        </span>
                        <span>View</span>
                    </a>
                    <a class="card-footer-item has-text-info is-size-7" onclick="doctorManager.editDoctor(${doctor.doctor_id})" style="padding: 0.4rem;">
                        <span class="icon is-small">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit</span>
                    </a>
                    <a class="card-footer-item has-text-danger is-size-7" onclick="doctorManager.confirmDeleteDoctor(${doctor.doctor_id}, '${this.escapeHtml(doctor.full_name)}')" style="padding: 0.4rem;">
                        <span class="icon is-small">
                            <i class="fas fa-trash"></i>
                        </span>
                        <span>Delete</span>
                    </a>
                </footer>
            </div>
        `;
        
        return card;
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
            this.loadDoctors();
        }
    }
    
    openModal(doctor = null) {
        this.isEditing = !!doctor;
        this.currentDoctorId = doctor ? doctor.doctor_id : null;
        
        const modal = document.getElementById('doctorModal');
        const title = document.getElementById('modalTitle');
        
        title.textContent = this.isEditing ? 'Edit Doctor' : 'Add New Doctor';
        
        if (doctor) {
            document.getElementById('doctorId').value = doctor.doctor_id;
            document.getElementById('firstName').value = doctor.first_name || '';
            document.getElementById('middleName').value = doctor.middle_name || '';
            document.getElementById('lastName').value = doctor.last_name || '';
            document.getElementById('specialty').value = doctor.specialty || '';
            document.getElementById('contactNumber').value = doctor.contact_number || '';
            document.getElementById('email').value = doctor.email || '';
            document.getElementById('departmentId').value = doctor.department_id || '';
            document.getElementById('schedule').value = doctor.schedule || '';
            document.getElementById('status').value = doctor.status || '';
        } else {
            document.getElementById('doctorForm').reset();
            document.getElementById('doctorId').value = '';
        }
        
        modal.classList.add('is-active');
    }
    
    closeModal() {
        document.getElementById('doctorModal').classList.remove('is-active');
        document.getElementById('doctorForm').reset();
        this.isEditing = false;
        this.currentDoctorId = null;
    }
    
    async saveDoctor() {
        // Validate required fields
        const firstName = document.getElementById('firstName').value;
        const lastName = document.getElementById('lastName').value;
        const specialty = document.getElementById('specialty').value;
        const contactNumber = document.getElementById('contactNumber').value;
        const email = document.getElementById('email').value;
        const departmentId = document.getElementById('departmentId').value;
        const status = document.getElementById('status').value;
        
        if (!firstName || !lastName || !specialty || !contactNumber || !email || !departmentId || !status) {
            this.showNotification('Please fill in all required fields', 'warning');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            this.showNotification('Please enter a valid email address', 'warning');
            return;
        }
        
        const data = {
            first_name: firstName,
            middle_name: document.getElementById('middleName').value,
            last_name: lastName,
            specialty: specialty,
            contact_number: contactNumber,
            email: email,
            department_id: parseInt(departmentId),
            schedule: document.getElementById('schedule').value,
            status: status
        };
        
        if (this.isEditing) {
            data.doctor_id = this.currentDoctorId;
        }
        
        try {
            const url = this.isEditing 
                ? 'controllers/DoctorController.php?action=update'
                : 'controllers/DoctorController.php?action=create';
            
            const method = this.isEditing ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                this.closeModal();
                this.loadDoctors();
            } else {
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error saving doctor:', error);
            this.showNotification('Error saving doctor: ' + error.message, 'danger');
        }
    }
    
    async editDoctor(id) {
        try {
            const response = await fetch(`controllers/DoctorController.php?action=get&id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                this.openModal(result.data);
            } else {
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error loading doctor:', error);
            this.showNotification('Error loading doctor: ' + error.message, 'danger');
        }
    }
    
    async viewDoctor(id) {
        try {
            const response = await fetch(`controllers/DoctorController.php?action=get&id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                this.populateViewModal(result.data);
                document.getElementById('viewModal').classList.add('is-active');
            } else {
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error loading doctor:', error);
            this.showNotification('Error loading doctor: ' + error.message, 'danger');
        }
    }
    
    populateViewModal(doctor) {
        // Basic information
        document.getElementById('viewFullName').value = doctor.full_name || 'N/A';
        document.getElementById('viewSpecialty').value = doctor.specialty || 'N/A';
        document.getElementById('viewDepartment').value = doctor.department_name || 'N/A';
        document.getElementById('viewContactNumber').value = doctor.contact_number || 'N/A';
        document.getElementById('viewEmail').value = doctor.email || 'N/A';
        document.getElementById('viewSchedule').value = doctor.schedule || 'N/A';
        
        // Status with color
        const statusElement = document.getElementById('viewStatus');
        statusElement.textContent = doctor.status || 'N/A';
        statusElement.className = 'tag is-medium';
        
        const statusColor = {
            'active': 'is-success',
            'inactive': 'is-warning'
        }[doctor.status] || 'is-info';
        
        statusElement.classList.add(statusColor);
    }
    
    closeViewModal() {
        document.getElementById('viewModal').classList.remove('is-active');
    }
    
    confirmDeleteDoctor(id, name) {
        this.currentDoctorId = id;
        document.getElementById('deleteDoctorName').textContent = name;
        document.getElementById('deleteModal').classList.add('is-active');
    }
    
    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
        this.currentDoctorId = null;
    }
    
    async deleteDoctor() {
        try {
            const response = await fetch('controllers/DoctorController.php?action=delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: this.currentDoctorId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                this.closeDeleteModal();
                this.loadDoctors();
            } else {
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error deleting doctor:', error);
            this.showNotification('Error deleting doctor: ' + error.message, 'danger');
        }
    }
    
    searchDoctors() {
        this.currentPage = 1;
        this.loadDoctors();
    }
    
    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('departmentFilter').value = '';
        document.getElementById('statusFilter').value = '';
        this.searchDoctors();
    }
    
    async loadDepartments() {
        try {
            const response = await fetch('controllers/DoctorController.php?action=departments');
            const result = await response.json();
            
            if (result.success) {
                this.departments = result.data;
                this.populateDepartmentDropdown();
            }
        } catch (error) {
            console.error('Error loading departments:', error);
        }
    }
    
    populateDepartmentDropdown() {
        const select = document.getElementById('departmentId');
        select.innerHTML = '<option value="">Select Department</option>';
        
        this.departments.forEach(department => {
            const option = document.createElement('option');
            option.value = department.department_id;
            option.textContent = department.department_name;
            select.appendChild(option);
        });
    }
    
    async loadDepartmentsForFilter() {
        try {
            const response = await fetch('controllers/DoctorController.php?action=departments');
            const result = await response.json();
            
            if (result.success) {
                this.populateDepartmentFilterDropdown(result.data);
            }
        } catch (error) {
            console.error('Error loading departments for filter:', error);
        }
    }
    
    populateDepartmentFilterDropdown(departments) {
        const select = document.getElementById('departmentFilter');
        select.innerHTML = '<option value="">All Departments</option>';
        
        departments.forEach(department => {
            const option = document.createElement('option');
            option.value = department.department_id;
            option.textContent = department.department_name;
            select.appendChild(option);
        });
    }
    
    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('doctorsContainer').style.display = 'none';
    }
    
    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('doctorsContainer').style.display = 'block';
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
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.maxWidth = '400px';
        
        notification.innerHTML = `
            <button class="delete"></button>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
        
        // Remove on click
        notification.querySelector('.delete').addEventListener('click', () => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        });
    }
    
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
}

// Initialize the manager when the page loads
document.addEventListener('DOMContentLoaded', function() {
    window.doctorManager = new DoctorManager();
}); 