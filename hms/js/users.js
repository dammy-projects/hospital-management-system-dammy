class UserManager {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.limit = 8;
        this.isEditing = false;
        this.currentUserId = null;
        this.roles = [];
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadRoles();
        this.loadUsers();
    }
    
    bindEvents() {
        // Modal events
        document.getElementById('addUserBtn').addEventListener('click', () => this.openModal());
        document.getElementById('closeModal').addEventListener('click', () => this.closeModal());
        document.getElementById('cancelModal').addEventListener('click', () => this.closeModal());
        document.getElementById('saveUser').addEventListener('click', () => this.saveUser());
        
        // Delete modal events
        document.getElementById('closeDeleteModal').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('cancelDelete').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('confirmDelete').addEventListener('click', () => this.deleteUser());
        
        // Search and filter events
        document.getElementById('searchInput').addEventListener('input', this.debounce(() => this.searchUsers(), 300));
        document.getElementById('roleFilter').addEventListener('change', () => this.searchUsers());
        document.getElementById('statusFilter').addEventListener('change', () => this.searchUsers());
        document.getElementById('clearFilters').addEventListener('click', () => this.clearFilters());
        
        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => this.goToPage(this.currentPage - 1));
        document.getElementById('nextPage').addEventListener('click', () => this.goToPage(this.currentPage + 1));
        
        // Close modals when clicking background
        document.getElementById('userModal').querySelector('.modal-background').addEventListener('click', () => this.closeModal());
        document.getElementById('deleteModal').querySelector('.modal-background').addEventListener('click', () => this.closeDeleteModal());
        
        // Close modals on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
                this.closeDeleteModal();
            }
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
    
    async loadRoles() {
        try {
            const response = await fetch('controllers/UserController.php?action=roles');
            const result = await response.json();
            
            if (result.success) {
                this.roles = result.data;
                this.populateRoleDropdowns();
            }
        } catch (error) {
            console.error('Error loading roles:', error);
        }
    }
    
    populateRoleDropdowns() {
        const roleSelect = document.getElementById('role');
        const roleFilter = document.getElementById('roleFilter');
        
        // Clear existing options (except the first one)
        roleSelect.innerHTML = '<option value="">Select Role</option>';
        roleFilter.innerHTML = '<option value="">All Roles</option>';
        
        this.roles.forEach(role => {
            roleSelect.innerHTML += `<option value="${role.role_id}">${role.role_name}</option>`;
            roleFilter.innerHTML += `<option value="${role.role_id}">${role.role_name}</option>`;
        });
    }
    
    async loadUsers() {
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                action: 'list',
                page: this.currentPage,
                limit: this.limit,
                search: document.getElementById('searchInput').value,
                role_filter: document.getElementById('roleFilter').value,
                status_filter: document.getElementById('statusFilter').value
            });
            
            const response = await fetch(`controllers/UserController.php?${params}`);
            const result = await response.json();
            
            if (result.success) {
                this.displayUsers(result.data);
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
            console.error('Error loading users:', error);
            this.hideLoading();
            this.showNotification('Error loading users: ' + error.message, 'danger');
        }
    }
    
    displayUsers(users) {
        const container = document.getElementById('usersGrid');
        container.innerHTML = '';
        
        users.forEach(user => {
            const userCard = this.createUserCard(user);
            container.appendChild(userCard);
        });
    }
    
    createUserCard(user) {
        const card = document.createElement('div');
        card.className = 'column is-3';
        
        const statusColor = {
            'active': 'success',
            'inactive': 'warning', 
            'suspended': 'danger'
        }[user.status] || 'info';
        
        card.innerHTML = `
            <div class="card" style="height: 280px;">
                <div class="card-content" style="padding: 1rem;">
                    <div class="has-text-centered mb-3">
                        <div class="mb-2">
                            <span class="icon is-medium has-text-grey-light">
                                <i class="fas fa-user-circle fa-2x"></i>
                            </span>
                        </div>
                        <h6 class="title is-6 mb-1" style="line-height: 1.1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(user.full_name || 'N/A')}">${this.escapeHtml(user.full_name || 'N/A')}</h6>
                        <p class="subtitle is-7 has-text-grey mb-2" style="line-height: 1; margin-top: 0.25rem;">@${this.escapeHtml(user.username)}</p>
                    </div>
                    
                    <div class="content" style="font-size: 0.8rem;">
                        <div class="field mb-2">
                            <span class="tag is-${statusColor} is-light is-small">${user.status}</span>
                            <span class="tag is-info is-light is-small ml-1" style="font-size: 0.7rem;">${this.escapeHtml(user.role_name || 'N/A')}</span>
                        </div>
                        
                        ${user.phone_number ? `<p class="mb-1 is-size-7" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(user.phone_number)}"><strong>Phone:</strong> ${this.escapeHtml(user.phone_number)}</p>` : ''}
                        
                        ${user.address ? `<p class="mb-1 is-size-7" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(user.address)}"><strong>Address:</strong> ${this.escapeHtml(user.address.length > 25 ? user.address.substring(0, 25) + '...' : user.address)}</p>` : ''}
                        
                        <p class="is-size-7 has-text-grey mt-2">
                            ${new Date(user.created_at).toLocaleDateString()}
                        </p>
                    </div>
                </div>
                
                <footer class="card-footer" style="border-top: 1px solid #dbdbdb;">
                    <a class="card-footer-item has-text-info is-size-7" onclick="userManager.editUser(${user.user_id})" style="padding: 0.5rem;">
                        <span class="icon is-small">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit</span>
                    </a>
                    <a class="card-footer-item has-text-danger is-size-7" onclick="userManager.confirmDeleteUser(${user.user_id}, '${this.escapeHtml(user.full_name || user.username)}')" style="padding: 0.5rem;">
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
            this.loadUsers();
        }
    }
    
    searchUsers() {
        this.currentPage = 1;
        this.loadUsers();
    }
    
    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('roleFilter').value = '';
        document.getElementById('statusFilter').value = '';
        this.searchUsers();
    }
    
    openModal(user = null) {
        this.isEditing = !!user;
        this.currentUserId = user ? user.user_id : null;
        
        const modal = document.getElementById('userModal');
        const title = document.getElementById('modalTitle');
        const passwordRequired = document.getElementById('passwordRequired');
        const passwordField = document.getElementById('password');
        
        title.textContent = this.isEditing ? 'Edit User' : 'Add New User';
        passwordRequired.textContent = this.isEditing ? '(leave blank to keep current)' : '*';
        passwordField.required = !this.isEditing;
        
        if (user) {
            document.getElementById('userId').value = user.user_id;
            document.getElementById('fullName').value = user.full_name || '';
            document.getElementById('username').value = user.username || '';
            document.getElementById('phoneNumber').value = user.phone_number || '';
            document.getElementById('address').value = user.address || '';
            document.getElementById('role').value = user.role_id || '';
            document.getElementById('status').value = user.status || '';
            document.getElementById('password').value = '';
        } else {
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
        }
        
        modal.classList.add('is-active');
    }
    
    closeModal() {
        document.getElementById('userModal').classList.remove('is-active');
        document.getElementById('userForm').reset();
    }
    
    async editUser(userId) {
        try {
            const response = await fetch(`controllers/UserController.php?action=get&id=${userId}`);
            const result = await response.json();
            
            if (result.success) {
                this.openModal(result.data);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading user:', error);
            this.showNotification('Error loading user: ' + error.message, 'danger');
        }
    }
    
    async saveUser() {
        const form = document.getElementById('userForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const userData = {
            full_name: document.getElementById('fullName').value,
            username: document.getElementById('username').value,
            phone_number: document.getElementById('phoneNumber').value,
            address: document.getElementById('address').value,
            role_id: document.getElementById('role').value,
            status: document.getElementById('status').value
        };
        
        const password = document.getElementById('password').value;
        if (password) {
            userData.password = password;
        }
        
        try {
            let url = 'controllers/UserController.php?action=';
            let method = 'POST';
            
            if (this.isEditing) {
                url += `update&id=${this.currentUserId}`;
                method = 'PUT';
            } else {
                url += 'create';
            }
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.closeModal();
                this.loadUsers();
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error saving user:', error);
            this.showNotification('Error saving user: ' + error.message, 'danger');
        }
    }
    
    confirmDeleteUser(userId, userName) {
        this.currentUserId = userId;
        document.getElementById('deleteUserName').textContent = userName;
        document.getElementById('deleteModal').classList.add('is-active');
    }
    
    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
        this.currentUserId = null;
    }
    
    async deleteUser() {
        if (!this.currentUserId) return;
        
        try {
            const response = await fetch(`controllers/UserController.php?action=delete&id=${this.currentUserId}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.closeDeleteModal();
                this.loadUsers();
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error deleting user:', error);
            this.showNotification('Error deleting user: ' + error.message, 'danger');
        }
    }
    
    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('usersContainer').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';
    }
    
    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('usersContainer').style.display = 'block';
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
            ${this.escapeHtml(message)}
        `;
        
        // Add to page
        const container = document.querySelector('.container');
        container.insertBefore(notification, container.firstChild);
        
        // Add close functionality
        notification.querySelector('.delete').addEventListener('click', () => {
            notification.remove();
        });
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
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
        return text.replace(/[&<>"']/g, (m) => map[m]);
    }
}

// Initialize the user manager when the page loads
let userManager;
document.addEventListener('DOMContentLoaded', () => {
    userManager = new UserManager();
}); 