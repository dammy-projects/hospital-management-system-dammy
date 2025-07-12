class BillingManager {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.currentFilters = {
            search: '',
            paymentStatus: '',
            insuranceStatus: ''
        };
        this.currentBill = null;
        this.patients = [];
        this.appointments = [];
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadPatients();
        this.loadAppointments();
        this.loadBills();
    }

    bindEvents() {
        // Add new bill button
        document.getElementById('addBillingBtn').addEventListener('click', () => {
            this.showAddModal();
        });

        // Search and filters
        document.getElementById('searchInput').addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.currentFilters.search = e.target.value;
                this.currentPage = 1;
                this.loadBills();
            }, 300);
        });

        document.getElementById('paymentStatusFilter').addEventListener('change', (e) => {
            this.currentFilters.paymentStatus = e.target.value;
            this.currentPage = 1;
            this.loadBills();
        });

        document.getElementById('insuranceStatusFilter').addEventListener('change', (e) => {
            this.currentFilters.insuranceStatus = e.target.value;
            this.currentPage = 1;
            this.loadBills();
        });

        document.getElementById('clearFilters').addEventListener('click', () => {
            this.clearFilters();
        });

        // Modal events
        document.getElementById('closeModal').addEventListener('click', () => {
            this.hideModal();
        });

        document.getElementById('cancelModal').addEventListener('click', () => {
            this.hideModal();
        });

        document.getElementById('saveBilling').addEventListener('click', () => {
            this.saveBill();
        });

        // View modal events
        document.getElementById('closeViewModal').addEventListener('click', () => {
            this.hideViewModal();
        });

        document.getElementById('closeViewModalBtn').addEventListener('click', () => {
            this.hideViewModal();
        });

        document.getElementById('printFromViewBtn').addEventListener('click', () => {
            this.printCurrentBill();
        });

        // Delete modal events
        document.getElementById('closeDeleteModal').addEventListener('click', () => {
            this.hideDeleteModal();
        });

        document.getElementById('cancelDelete').addEventListener('click', () => {
            this.hideDeleteModal();
        });

        document.getElementById('confirmDelete').addEventListener('click', () => {
            this.confirmDeleteBill();
        });

        // Patient selection change event
        document.getElementById('patientId').addEventListener('change', (e) => {
            this.loadPatientAppointments(e.target.value);
        });

        // Modal background clicks
        document.querySelectorAll('.modal-background').forEach(bg => {
            bg.addEventListener('click', (e) => {
                if (e.target === bg) {
                    bg.closest('.modal').classList.remove('is-active');
                }
            });
        });

        // Pagination events
        document.getElementById('prevPage').addEventListener('click', (e) => {
            e.preventDefault();
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadBills();
            }
        });

        document.getElementById('nextPage').addEventListener('click', (e) => {
            e.preventDefault();
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.loadBills();
            }
        });
    }

    async loadBills() {
        try {
            this.showLoading();
            
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: 6,
                search: this.currentFilters.search,
                payment_status: this.currentFilters.paymentStatus,
                insurance_status: this.currentFilters.insuranceStatus
            });

            const response = await fetch(`controllers/BillingController.php?${params}`);
            const data = await response.json();

            this.hideLoading();

            if (data.success) {
                this.renderBills(data.data);
                this.updatePagination(data.pagination);
            } else {
                this.showNotification('Error loading bills: ' + data.error, 'is-danger');
            }
        } catch (error) {
            this.hideLoading();
            console.error('Error loading bills:', error);
            this.showNotification('Failed to load bills', 'is-danger');
        }
    }

    renderBills(bills) {
        const grid = document.getElementById('billingGrid');
        const emptyState = document.getElementById('emptyState');

        if (bills.length === 0) {
            grid.innerHTML = '';
            emptyState.style.display = 'block';
            return;
        }

        emptyState.style.display = 'none';
        
        grid.innerHTML = bills.map(bill => `
            <div class="column">
                <div class="card">
                    <div class="card-content">
                        <div class="media">
                            <div class="media-left">
                                <span class="icon is-large has-text-info">
                                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                                </span>
                            </div>
                            <div class="media-content">
                                <p class="title is-6">Bill #${bill.billing_id}</p>
                                <p class="subtitle is-7 has-text-grey">${bill.patient_name || 'Unknown Patient'}</p>
                            </div>
                        </div>
                        
                        <div class="content">
                            <div class="amount-display mb-2">${bill.formatted_amount}</div>
                            <div class="mb-2">
                                <span class="status-badge ${bill.payment_status_class}">${bill.payment_status}</span>
                                <span class="insurance-status ${bill.insurance_status_class} ml-1">${bill.insurance_claim_status}</span>
                            </div>
                            <div class="has-text-grey-dark">
                                <small>
                                    <i class="fas fa-calendar mr-1"></i>
                                    ${bill.formatted_date}
                                </small>
                            </div>
                            ${bill.appointment_purpose ? `
                                <div class="has-text-grey">
                                    <small>
                                        <i class="fas fa-stethoscope mr-1"></i>
                                        ${bill.appointment_purpose}
                                    </small>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    <footer class="card-footer">
                        <a class="card-footer-item has-text-info" onclick="billingManager.viewBill(${bill.billing_id})">
                            <span class="icon"><i class="fas fa-eye"></i></span>
                            <span>View</span>
                        </a>
                        <a class="card-footer-item has-text-primary" onclick="billingManager.editBill(${bill.billing_id})">
                            <span class="icon"><i class="fas fa-edit"></i></span>
                            <span>Edit</span>
                        </a>
                        <a class="card-footer-item has-text-success" onclick="billingManager.printBillFromCard(${bill.billing_id})">
                            <span class="icon"><i class="fas fa-print"></i></span>
                            <span>Print</span>
                        </a>
                        <a class="card-footer-item has-text-danger" onclick="billingManager.deleteBill(${bill.billing_id}, '${bill.patient_name || 'Unknown'}', '${bill.formatted_amount}')">
                            <span class="icon"><i class="fas fa-trash"></i></span>
                            <span>Delete</span>
                        </a>
                    </footer>
                </div>
            </div>
        `).join('');
    }

    updatePagination(pagination) {
        this.totalPages = pagination.total_pages;
        const paginationEl = document.getElementById('pagination');
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        const paginationList = document.getElementById('paginationList');

        if (pagination.total_pages <= 1) {
            paginationEl.style.display = 'none';
            return;
        }

        paginationEl.style.display = 'flex';
        
        // Update prev/next buttons
        prevBtn.classList.toggle('is-disabled', !pagination.has_prev);
        nextBtn.classList.toggle('is-disabled', !pagination.has_next);

        // Generate page numbers
        let pages = [];
        const current = pagination.current_page;
        const total = pagination.total_pages;

        if (total <= 7) {
            pages = Array.from({length: total}, (_, i) => i + 1);
        } else {
            if (current <= 4) {
                pages = [1, 2, 3, 4, 5, '...', total];
            } else if (current >= total - 3) {
                pages = [1, '...', total - 4, total - 3, total - 2, total - 1, total];
            } else {
                pages = [1, '...', current - 1, current, current + 1, '...', total];
            }
        }

        paginationList.innerHTML = pages.map(page => {
            if (page === '...') {
                return '<li><span class="pagination-ellipsis">&hellip;</span></li>';
            }
            
            return `
                <li>
                    <a class="pagination-link ${page === current ? 'is-current' : ''}" 
                       onclick="billingManager.goToPage(${page})">${page}</a>
                </li>
            `;
        }).join('');
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadBills();
    }

    async loadPatients() {
        try {
            const response = await fetch('controllers/BillingController.php?action=patients');
            const data = await response.json();

            if (data.success) {
                this.patients = data.data;
                this.populatePatientDropdown();
            } else {
                console.error('Failed to load patients:', data.error);
            }
        } catch (error) {
            console.error('Error loading patients:', error);
        }
    }

    async loadAppointments() {
        try {
            const response = await fetch('controllers/BillingController.php?action=appointments');
            const data = await response.json();

            if (data.success) {
                this.appointments = data.data;
            } else {
                console.error('Failed to load appointments:', data.error);
            }
        } catch (error) {
            console.error('Error loading appointments:', error);
        }
    }

    async loadPatientAppointments(patientId) {
        if (!patientId) {
            this.populateAppointmentDropdown([]);
            return;
        }

        try {
            const response = await fetch(`controllers/BillingController.php?action=patient-appointments&patient_id=${patientId}`);
            const data = await response.json();

            if (data.success) {
                this.populateAppointmentDropdown(data.data);
            } else {
                console.error('Failed to load patient appointments:', data.error);
                this.populateAppointmentDropdown([]);
            }
        } catch (error) {
            console.error('Error loading patient appointments:', error);
            this.populateAppointmentDropdown([]);
        }
    }

    populatePatientDropdown() {
        const select = document.getElementById('patientId');
        select.innerHTML = '<option value="">Select Patient</option>' +
            this.patients.map(patient => 
                `<option value="${patient.patient_id}">${patient.patient_name}</option>`
            ).join('');
    }

    populateAppointmentDropdown(appointments = null) {
        const select = document.getElementById('appointmentId');
        const appointmentsToUse = appointments || this.appointments;
        
        select.innerHTML = '<option value="">Select Appointment (Optional)</option>' +
            appointmentsToUse.map(appointment => 
                `<option value="${appointment.appointment_id}">
                    ${appointment.formatted_date} - ${appointment.purpose || 'No purpose specified'}
                    ${appointment.patient_name ? ` (${appointment.patient_name})` : ''}
                    ${appointment.doctor_name ? ` - Dr. ${appointment.doctor_name}` : ''}
                </option>`
            ).join('');
    }

    showAddModal() {
        this.currentBill = null;
        document.getElementById('modalTitle').textContent = 'New Bill';
        this.resetForm();
        this.showModal();
    }

    async editBill(id) {
        try {
            const response = await fetch(`controllers/BillingController.php?action=get&id=${id}`);
            const data = await response.json();

            if (data.success) {
                this.currentBill = data.data;
                document.getElementById('modalTitle').textContent = 'Edit Bill';
                this.populateForm(data.data);
                this.showModal();
            } else {
                this.showNotification('Error loading bill: ' + data.error, 'is-danger');
            }
        } catch (error) {
            console.error('Error loading bill:', error);
            this.showNotification('Failed to load bill', 'is-danger');
        }
    }

    async viewBill(id) {
        try {
            const response = await fetch(`controllers/BillingController.php?action=get&id=${id}`);
            const data = await response.json();

            if (data.success) {
                this.currentBill = data.data;
                this.renderBillDetails(data.data);
                this.showViewModal();
            } else {
                this.showNotification('Error loading bill: ' + data.error, 'is-danger');
            }
        } catch (error) {
            console.error('Error loading bill:', error);
            this.showNotification('Failed to load bill', 'is-danger');
        }
    }

    renderBillDetails(bill) {
        const detailsContainer = document.getElementById('billingDetails');
        
        detailsContainer.innerHTML = `
            <div class="columns">
                <div class="column is-6">
                    <div class="box">
                        <h4 class="title is-5">Bill Information</h4>
                        <div class="content">
                            <p><strong>Bill ID:</strong> #${bill.billing_id}</p>
                            <p><strong>Amount:</strong> <span class="has-text-primary has-text-weight-bold">${bill.formatted_amount}</span></p>
                            <p><strong>Date:</strong> ${bill.formatted_date}</p>
                            <p><strong>Payment Status:</strong> 
                                <span class="tag ${bill.payment_status_class}">${bill.payment_status}</span>
                            </p>
                            <p><strong>Insurance Status:</strong> 
                                <span class="tag ${bill.insurance_status_class}">${bill.insurance_claim_status}</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="box">
                        <h4 class="title is-5">Patient Information</h4>
                        <div class="content">
                            <p><strong>Name:</strong> ${bill.patient_name || 'N/A'}</p>
                            <p><strong>Contact:</strong> ${bill.patient_contact || 'N/A'}</p>
                            <p><strong>Email:</strong> ${bill.patient_email || 'N/A'}</p>
                            <p><strong>Address:</strong> ${bill.patient_address || 'N/A'}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            ${bill.appointment_date ? `
                <div class="box">
                    <h4 class="title is-5">Appointment Information</h4>
                    <div class="content">
                        <div class="columns">
                            <div class="column is-6">
                                <p><strong>Date:</strong> ${bill.formatted_appointment_date}</p>
                                <p><strong>Purpose:</strong> ${bill.appointment_purpose || 'N/A'}</p>
                            </div>
                            <div class="column is-6">
                                <p><strong>Doctor:</strong> ${bill.doctor_name || 'N/A'}</p>
                                <p><strong>Specialty:</strong> ${bill.doctor_specialty || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            ` : ''}
        `;
    }

    deleteBill(id, patientName, amount) {
        this.currentBillToDelete = id;
        document.getElementById('deleteBillingInfo').textContent = 
            `Bill #${id} for ${patientName} (${amount})`;
        this.showDeleteModal();
    }

    async confirmDeleteBill() {
        try {
            const response = await fetch(`controllers/BillingController.php?action=delete&id=${this.currentBillToDelete}`, {
                method: 'DELETE'
            });
            
            const data = await response.json();

            if (data.success) {
                this.showNotification('Bill deleted successfully', 'is-success');
                this.hideDeleteModal();
                this.loadBills();
            } else {
                this.showNotification('Error deleting bill: ' + data.error, 'is-danger');
            }
        } catch (error) {
            console.error('Error deleting bill:', error);
            this.showNotification('Failed to delete bill', 'is-danger');
        }
    }

    async saveBill() {
        try {
            const formData = this.getFormData();
            const isEdit = this.currentBill !== null;
            
            let url, method;
            if (isEdit) {
                url = `controllers/BillingController.php?action=update&id=${this.currentBill.billing_id}`;
                method = 'PUT';
            } else {
                url = 'controllers/BillingController.php?action=create';
                method = 'POST';
            }

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(
                    `Bill ${isEdit ? 'updated' : 'created'} successfully`, 
                    'is-success'
                );
                this.hideModal();
                this.loadBills();
            } else {
                this.showNotification('Error saving bill: ' + data.error, 'is-danger');
            }
        } catch (error) {
            console.error('Error saving bill:', error);
            this.showNotification('Failed to save bill', 'is-danger');
        }
    }

    getFormData() {
        return {
            patient_id: document.getElementById('patientId').value,
            appointment_id: document.getElementById('appointmentId').value || null,
            amount: parseFloat(document.getElementById('amount').value),
            payment_status: document.getElementById('paymentStatus').value,
            insurance_claim_status: document.getElementById('insuranceClaimStatus').value,
            billing_date: document.getElementById('billingDate').value
        };
    }

    populateForm(bill) {
        document.getElementById('billingId').value = bill.billing_id;
        document.getElementById('patientId').value = bill.patient_id;
        document.getElementById('appointmentId').value = bill.appointment_id || '';
        document.getElementById('amount').value = bill.amount;
        document.getElementById('paymentStatus').value = bill.payment_status;
        document.getElementById('insuranceClaimStatus').value = bill.insurance_claim_status;
        
        // Format date for datetime-local input
        const date = new Date(bill.billing_date);
        const formattedDate = date.getFullYear() + '-' + 
            String(date.getMonth() + 1).padStart(2, '0') + '-' + 
            String(date.getDate()).padStart(2, '0') + 'T' + 
            String(date.getHours()).padStart(2, '0') + ':' + 
            String(date.getMinutes()).padStart(2, '0');
        document.getElementById('billingDate').value = formattedDate;

        // Load patient appointments if patient is selected
        if (bill.patient_id) {
            this.loadPatientAppointments(bill.patient_id);
        }
    }

    resetForm() {
        document.getElementById('billingForm').reset();
        document.getElementById('billingId').value = '';
        
        // Set current datetime as default
        const now = new Date();
        const formattedNow = now.getFullYear() + '-' + 
            String(now.getMonth() + 1).padStart(2, '0') + '-' + 
            String(now.getDate()).padStart(2, '0') + 'T' + 
            String(now.getHours()).padStart(2, '0') + ':' + 
            String(now.getMinutes()).padStart(2, '0');
        document.getElementById('billingDate').value = formattedNow;
        
        // Reset appointment dropdown
        this.populateAppointmentDropdown([]);
    }

    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('paymentStatusFilter').value = '';
        document.getElementById('insuranceStatusFilter').value = '';
        
        this.currentFilters = {
            search: '',
            paymentStatus: '',
            insuranceStatus: ''
        };
        
        this.currentPage = 1;
        this.loadBills();
    }

    // Print functionality
    async printBillFromCard(id) {
        try {
            const response = await fetch(`controllers/BillingController.php?action=get&id=${id}`);
            const data = await response.json();

            if (data.success) {
                this.generatePrintContent(data.data);
                window.print();
            } else {
                this.showNotification('Error loading bill for printing: ' + data.error, 'is-danger');
            }
        } catch (error) {
            console.error('Error loading bill for printing:', error);
            this.showNotification('Failed to load bill for printing', 'is-danger');
        }
    }

    printCurrentBill() {
        if (this.currentBill) {
            this.generatePrintContent(this.currentBill);
            window.print();
        }
    }

    generatePrintContent(bill) {
        const printArea = document.getElementById('billingPrintArea');
        
        printArea.innerHTML = `
            <div class="print-header">
                <div class="print-title">Hospital Management System</div>
                <div class="print-subtitle">Billing Receipt</div>
            </div>
            
            <div class="print-bill-info">
                <h3>Bill Information</h3>
                <div class="print-bill-grid">
                    <div><strong>Bill ID:</strong> #${bill.billing_id}</div>
                    <div><strong>Date:</strong> ${bill.formatted_date}</div>
                    <div><strong>Patient:</strong> ${bill.patient_name || 'N/A'}</div>
                    <div><strong>Contact:</strong> ${bill.patient_contact || 'N/A'}</div>
                    <div><strong>Payment Status:</strong> ${bill.payment_status}</div>
                    <div><strong>Insurance Status:</strong> ${bill.insurance_claim_status}</div>
                </div>
            </div>
            
            ${bill.appointment_date ? `
                <div class="print-bill-info">
                    <h3>Appointment Details</h3>
                    <div class="print-bill-grid">
                        <div><strong>Date:</strong> ${bill.formatted_appointment_date}</div>
                        <div><strong>Purpose:</strong> ${bill.appointment_purpose || 'N/A'}</div>
                        <div><strong>Doctor:</strong> ${bill.doctor_name || 'N/A'}</div>
                        <div><strong>Specialty:</strong> ${bill.doctor_specialty || 'N/A'}</div>
                    </div>
                </div>
            ` : ''}
            
            <div class="print-amount">
                <div>Total Amount: ${bill.formatted_amount}</div>
            </div>
            
            <div class="print-footer">
                <div>Generated on: ${new Date().toLocaleString()}</div>
                <div>This is a computer-generated receipt.</div>
                <div>For questions regarding this bill, please contact the billing department.</div>
            </div>
        `;
    }

    // Modal management
    showModal() {
        document.getElementById('billingModal').classList.add('is-active');
    }

    hideModal() {
        document.getElementById('billingModal').classList.remove('is-active');
    }

    showViewModal() {
        document.getElementById('viewModal').classList.add('is-active');
    }

    hideViewModal() {
        document.getElementById('viewModal').classList.remove('is-active');
    }

    showDeleteModal() {
        document.getElementById('deleteModal').classList.add('is-active');
    }

    hideDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
    }

    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('billingContainer').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';
    }

    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('billingContainer').style.display = 'block';
    }

    showNotification(message, type = 'is-info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification ${type} is-light`;
        notification.innerHTML = `
            <button class="delete"></button>
            ${message}
        `;

        // Add to page
        document.body.appendChild(notification);

        // Position it
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.maxWidth = '400px';

        // Add close functionality
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
}

// Initialize the billing manager when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.billingManager = new BillingManager();
}); 