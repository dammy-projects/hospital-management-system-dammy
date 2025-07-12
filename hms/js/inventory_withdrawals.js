class InventoryWithdrawalManager {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.limit = 6;
        this.isEditing = false;
        this.currentWithdrawalId = null;
        this.currentWithdrawalData = null; // For storing withdrawal data for printing
        this.users = [];
        this.inventoryItems = [];
        this.itemEntryCount = 0;
        
        this.init();
    }
    
    init() {
        this.loadUsers();
        this.loadInventoryItems();
        this.loadWithdrawals();
        this.bindEvents();
    }
    
    bindEvents() {
        // Modal events
        document.getElementById('addWithdrawalBtn').addEventListener('click', () => this.openModal());
        document.getElementById('closeModal').addEventListener('click', () => this.closeModal());
        document.getElementById('cancelModal').addEventListener('click', () => this.closeModal());
        document.getElementById('saveWithdrawal').addEventListener('click', () => this.saveWithdrawal());
        
        // View modal events
        document.getElementById('closeViewModal').addEventListener('click', () => this.closeViewModal());
        document.getElementById('closeViewModalBtn').addEventListener('click', () => this.closeViewModal());
        document.getElementById('printFromViewBtn').addEventListener('click', () => this.printCurrentWithdrawal());
        
        // Delete modal events
        document.getElementById('closeDeleteModal').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('cancelDelete').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('confirmDelete').addEventListener('click', () => this.deleteWithdrawal());
        
        // Search and filter events
        document.getElementById('searchInput').addEventListener('input', this.debounce(() => this.searchWithdrawals(), 300));
        document.getElementById('statusFilter').addEventListener('change', () => this.searchWithdrawals());
        document.getElementById('userFilter').addEventListener('change', () => this.searchWithdrawals());
        document.getElementById('clearFilters').addEventListener('click', () => this.clearFilters());
        
        // Item management
        document.getElementById('addItemBtn').addEventListener('click', () => this.addItemEntry());
        
        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => this.goToPage(this.currentPage - 1));
        document.getElementById('nextPage').addEventListener('click', () => this.goToPage(this.currentPage + 1));
        
        // Close modals when clicking outside
        document.getElementById('withdrawalModal').addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-background')) {
                this.closeModal();
            }
        });
        
        document.getElementById('viewModal').addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-background')) {
                this.closeViewModal();
            }
        });
        
        document.getElementById('deleteModal').addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-background')) {
                this.closeDeleteModal();
            }
        });
    }
    
    async loadUsers() {
        try {
            const response = await fetch('controllers/InventoryWithdrawalController.php?action=users');
            const result = await response.json();
            
            if (result.success) {
                this.users = result.data;
                this.populateUserFilter();
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }
    
    async loadInventoryItems() {
        try {
            const response = await fetch('controllers/InventoryWithdrawalController.php?action=inventory-items');
            const result = await response.json();
            
            if (result.success) {
                this.inventoryItems = result.data;
            }
        } catch (error) {
            console.error('Error loading inventory items:', error);
        }
    }
    
    populateUserFilter() {
        const userFilter = document.getElementById('userFilter');
        userFilter.innerHTML = '<option value="">All Users</option>';
        
        this.users.forEach(user => {
            const option = document.createElement('option');
            option.value = user.user_id;
            option.textContent = user.full_name;
            userFilter.appendChild(option);
        });
    }
    
    async loadWithdrawals() {
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                action: 'list',
                page: this.currentPage,
                limit: this.limit,
                search: document.getElementById('searchInput').value,
                status_filter: document.getElementById('statusFilter').value,
                user_filter: document.getElementById('userFilter').value
            });
            
            const response = await fetch(`controllers/InventoryWithdrawalController.php?${params}`);
            const result = await response.json();
            
            if (result.success) {
                this.displayWithdrawals(result.data);
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
            console.error('Error loading withdrawals:', error);
            this.hideLoading();
            this.showNotification('Error loading withdrawals: ' + error.message, 'danger');
        }
    }
    
    displayWithdrawals(withdrawals) {
        const grid = document.getElementById('withdrawalsGrid');
        grid.innerHTML = '';
        
        withdrawals.forEach(withdrawal => {
            const withdrawalCard = this.createWithdrawalCard(withdrawal);
            grid.appendChild(withdrawalCard);
        });
    }
    
    createWithdrawalCard(withdrawal) {
        const column = document.createElement('div');
        column.className = 'column is-one-quarter-desktop is-one-third-tablet is-half-mobile';
        
        const statusBadgeClass = this.getStatusBadgeClass(withdrawal.status);
        const withdrawalDate = new Date(withdrawal.withdrawal_date).toLocaleDateString();
        const withdrawalTime = new Date(withdrawal.withdrawal_date).toLocaleTimeString();
        
        column.innerHTML = `
            <div class="card">
                <div class="card-content">
                    <div class="content">
                        <p class="title is-6 mb-2">
                            <span class="icon">
                                <i class="fas fa-box-open"></i>
                            </span>
                            Withdrawal #${withdrawal.withdrawal_id}
                        </p>
                        <p class="subtitle is-7 has-text-grey">
                            ${withdrawalDate} at ${withdrawalTime}
                        </p>
                        
                        <div class="field is-grouped is-grouped-multiline mb-2">
                            <div class="control">
                                <div class="tags has-addons">
                                    <span class="tag">Status</span>
                                    <span class="tag ${statusBadgeClass}">${withdrawal.status}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="field is-grouped is-grouped-multiline mb-2">
                            <div class="control">
                                <div class="tags has-addons">
                                    <span class="tag">Items</span>
                                    <span class="tag is-primary">${withdrawal.total_items}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="has-text-grey is-size-7">
                            <p><strong>Performed by:</strong> ${withdrawal.performed_by_name || 'Unknown'}</p>
                            ${withdrawal.notes ? `<p><strong>Notes:</strong> ${withdrawal.notes.substring(0, 50)}${withdrawal.notes.length > 50 ? '...' : ''}</p>` : ''}
                        </div>
                    </div>
                </div>
                <footer class="card-footer">
                    <a class="card-footer-item" onclick="withdrawalManager.viewWithdrawal(${withdrawal.withdrawal_id})">
                        <span class="icon"><i class="fas fa-eye"></i></span>
                        <span>View</span>
                    </a>
                    <a class="card-footer-item" onclick="withdrawalManager.editWithdrawal(${withdrawal.withdrawal_id})">
                        <span class="icon"><i class="fas fa-edit"></i></span>
                        <span>Edit</span>
                    </a>
                    <a class="card-footer-item has-text-success" onclick="withdrawalManager.printWithdrawalFromCard(${withdrawal.withdrawal_id})">
                        <span class="icon"><i class="fas fa-print"></i></span>
                        <span>Print</span>
                    </a>
                    <a class="card-footer-item has-text-danger" onclick="withdrawalManager.confirmDelete(${withdrawal.withdrawal_id}, '${withdrawal.notes || 'Withdrawal #' + withdrawal.withdrawal_id}')">
                        <span class="icon"><i class="fas fa-trash"></i></span>
                        <span>Delete</span>
                    </a>
                </footer>
            </div>
        `;
        
        return column;
    }
    
    getStatusBadgeClass(status) {
        switch (status) {
            case 'pending': return 'is-warning';
            case 'approved': return 'is-info';
            case 'completed': return 'is-success';
            case 'cancelled': return 'is-danger';
            default: return 'is-light';
        }
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
        
        // Update prev/next buttons
        prevButton.disabled = this.currentPage === 1;
        nextButton.disabled = this.currentPage === this.totalPages;
        
        prevButton.classList.toggle('is-disabled', this.currentPage === 1);
        nextButton.classList.toggle('is-disabled', this.currentPage === this.totalPages);
        
        // Generate page numbers
        paginationList.innerHTML = '';
        
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(this.totalPages, this.currentPage + 2);
        
        if (startPage > 1) {
            paginationList.appendChild(this.createPageButton(1));
            if (startPage > 2) {
                paginationList.appendChild(this.createEllipsis());
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            paginationList.appendChild(this.createPageButton(i));
        }
        
        if (endPage < this.totalPages) {
            if (endPage < this.totalPages - 1) {
                paginationList.appendChild(this.createEllipsis());
            }
            paginationList.appendChild(this.createPageButton(this.totalPages));
        }
    }
    
    createPageButton(pageNumber) {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.className = `pagination-link ${pageNumber === this.currentPage ? 'is-current' : ''}`;
        a.textContent = pageNumber;
        a.addEventListener('click', () => this.goToPage(pageNumber));
        li.appendChild(a);
        return li;
    }
    
    createEllipsis() {
        const li = document.createElement('li');
        const span = document.createElement('span');
        span.className = 'pagination-ellipsis';
        span.innerHTML = '&hellip;';
        li.appendChild(span);
        return li;
    }
    
    goToPage(page) {
        if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
            this.currentPage = page;
            this.loadWithdrawals();
        }
    }
    
    openModal() {
        this.isEditing = false;
        this.currentWithdrawalId = null;
        document.getElementById('modalTitle').textContent = 'New Withdrawal';
        document.getElementById('withdrawalModal').classList.add('is-active');
        this.resetForm();
        this.addItemEntry(); // Add initial item entry
        
        // Set default date to current datetime
        const now = new Date();
        const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
        document.getElementById('withdrawalDate').value = localDateTime;
        
        // Set status to completed and hide the status field for new withdrawals
        document.getElementById('status').value = 'completed';
        const statusField = document.getElementById('status').closest('.field');
        if (statusField) {
            statusField.style.display = 'none';
        }
    }
    
    closeModal() {
        document.getElementById('withdrawalModal').classList.remove('is-active');
        this.resetForm();
    }
    
    resetForm() {
        document.getElementById('withdrawalForm').reset();
        document.getElementById('withdrawalId').value = '';
        document.getElementById('itemsContainer').innerHTML = '';
        this.itemEntryCount = 0;
    }
    
    addItemEntry() {
        this.itemEntryCount++;
        const container = document.getElementById('itemsContainer');
        
        const itemEntry = document.createElement('div');
        itemEntry.className = 'item-entry';
        itemEntry.id = `item-entry-${this.itemEntryCount}`;
        
        itemEntry.innerHTML = `
            <div class="item-entry-header">
                <h6 class="title is-6">Item ${this.itemEntryCount}</h6>
                <button type="button" class="remove-item-btn" onclick="withdrawalManager.removeItemEntry(${this.itemEntryCount})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="columns">
                <div class="column is-8">
                    <div class="field">
                        <label class="label">Inventory Item *</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="itemId-${this.itemEntryCount}" required>
                                    <option value="">Select Item</option>
                                    ${this.inventoryItems.map(item => 
                                        `<option value="${item.item_id}" data-stock="${item.quantity_in_stock}" data-unit="${item.unit}">
                                            ${item.item_name} (Stock: ${item.quantity_in_stock} ${item.unit}) - ${item.category_name}
                                        </option>`
                                    ).join('')}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Quantity *</label>
                        <div class="control">
                            <input class="input" type="number" id="quantity-${this.itemEntryCount}" min="1" required>
                        </div>
                        <p class="help is-success" id="stock-info-${this.itemEntryCount}" style="display: none;"></p>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(itemEntry);
        
        // Add event listener to show stock info when item is selected
        document.getElementById(`itemId-${this.itemEntryCount}`).addEventListener('change', (e) => {
            const option = e.target.selectedOptions[0];
            const stockInfo = document.getElementById(`stock-info-${this.itemEntryCount}`);
            
            if (option && option.value) {
                const stock = option.dataset.stock;
                const unit = option.dataset.unit;
                stockInfo.textContent = `Available stock: ${stock} ${unit}`;
                stockInfo.style.display = 'block';
            } else {
                stockInfo.style.display = 'none';
            }
        });
    }
    
    removeItemEntry(entryId) {
        const entry = document.getElementById(`item-entry-${entryId}`);
        if (entry) {
            entry.remove();
        }
        
        // Renumber remaining entries
        this.renumberItemEntries();
    }
    
    renumberItemEntries() {
        const entries = document.querySelectorAll('.item-entry');
        entries.forEach((entry, index) => {
            const newNumber = index + 1;
            const title = entry.querySelector('.title');
            if (title) {
                title.textContent = `Item ${newNumber}`;
            }
        });
    }
    
    async saveWithdrawal() {
        const formData = this.getFormData();
        
        if (!this.validateForm(formData)) {
            return;
        }
        
        try {
            const url = 'controllers/InventoryWithdrawalController.php';
            const method = this.isEditing ? 'PUT' : 'POST';
            const action = this.isEditing ? 'update' : 'create';
            
            if (this.isEditing) {
                formData.withdrawal_id = this.currentWithdrawalId;
            }
            
            console.log('💾 Saving withdrawal...', formData);
            
            const response = await fetch(`${url}?action=${action}`, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            console.log('📥 Save response:', result);
            
            if (result.success) {
                // Handle stock updates if creating withdrawal (auto-completed)
                if (!this.isEditing && result.stock_updates && result.stock_updates.length > 0) {
                    console.log('📊 Inventory automatically updated:', result.stock_updates);
                    
                    // Show detailed success message with stock updates
                    let message = result.message;
                    message += '\n\nStock Updates:';
                    result.stock_updates.forEach(update => {
                        message += `\n• ${update.item_name}: ${update.old_stock} → ${update.new_stock} (withdrew ${update.withdrawn})`;
                    });
                    
                    this.showNotification(message, 'success');
                } else {
                    this.showNotification(result.message, 'success');
                }
                
                this.closeModal();
                this.loadWithdrawals();
            } else {
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error saving withdrawal:', error);
            this.showNotification('Error saving withdrawal: ' + error.message, 'danger');
        }
    }
    
    getFormData() {
        const withdrawalDate = document.getElementById('withdrawalDate').value;
        const status = document.getElementById('status').value;
        const notes = document.getElementById('notes').value;
        
        const items = [];
        const itemEntries = document.querySelectorAll('.item-entry');
        
        itemEntries.forEach((entry, index) => {
            const itemId = entry.querySelector('[id^="itemId-"]').value;
            const quantity = entry.querySelector('[id^="quantity-"]').value;
            
            if (itemId && quantity) {
                items.push({
                    item_id: parseInt(itemId),
                    quantity: parseInt(quantity)
                });
            }
        });
        
        return {
            withdrawal_date: withdrawalDate,
            status: status,
            notes: notes,
            items: items
        };
    }
    
    validateForm(formData) {
        if (!formData.withdrawal_date) {
            this.showNotification('Withdrawal date is required', 'danger');
            return false;
        }
        
        if (!formData.status) {
            this.showNotification('Status is required', 'danger');
            return false;
        }
        
        if (formData.items.length === 0) {
            this.showNotification('At least one item is required', 'danger');
            return false;
        }
        
        // Validate each item
        for (let i = 0; i < formData.items.length; i++) {
            const item = formData.items[i];
            if (!item.item_id) {
                this.showNotification(`Item is required for entry ${i + 1}`, 'danger');
                return false;
            }
            if (!item.quantity || item.quantity <= 0) {
                this.showNotification(`Valid quantity is required for entry ${i + 1}`, 'danger');
                return false;
            }
        }
        
        return true;
    }
    
    async viewWithdrawal(id) {
        try {
            const response = await fetch(`controllers/InventoryWithdrawalController.php?action=get&id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                this.currentWithdrawalData = result.data; // Store for printing
                this.displayWithdrawalDetails(result.data);
                document.getElementById('viewModal').classList.add('is-active');
            } else {
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error loading withdrawal:', error);
            this.showNotification('Error loading withdrawal details', 'danger');
        }
    }
    
    displayWithdrawalDetails(withdrawal) {
        const details = document.getElementById('withdrawalDetails');
        const withdrawalDate = new Date(withdrawal.withdrawal_date).toLocaleString();
        const statusBadgeClass = this.getStatusBadgeClass(withdrawal.status);
        
        details.innerHTML = `
            <div class="columns">
                <div class="column is-6">
                    <div class="field">
                        <label class="label">Withdrawal ID</label>
                        <div class="control">
                            <input class="input" type="text" value="#${withdrawal.withdrawal_id}" readonly>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="field">
                        <label class="label">Status</label>
                        <div class="control">
                            <span class="tag is-medium ${statusBadgeClass}">${withdrawal.status}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="columns">
                <div class="column is-6">
                    <div class="field">
                        <label class="label">Withdrawal Date</label>
                        <div class="control">
                            <input class="input" type="text" value="${withdrawalDate}" readonly>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="field">
                        <label class="label">Performed By</label>
                        <div class="control">
                            <input class="input" type="text" value="${withdrawal.performed_by_name || 'Unknown'}" readonly>
                        </div>
                    </div>
                </div>
            </div>
            
            ${withdrawal.notes ? `
                <div class="field">
                    <label class="label">Notes</label>
                    <div class="control">
                        <textarea class="textarea" readonly>${withdrawal.notes}</textarea>
                    </div>
                </div>
            ` : ''}
            
            <div class="field">
                <label class="label">Withdrawal Items</label>
                <div class="table-container">
                    <table class="table is-fullwidth is-striped">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Available Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${withdrawal.items.map(item => `
                                <tr>
                                    <td>${item.item_name}</td>
                                    <td>${item.category_name || 'N/A'}</td>
                                    <td>${item.quantity} ${item.unit || ''}</td>
                                    <td>
                                        <span class="tag ${item.quantity_in_stock >= item.quantity ? 'is-success' : 'is-danger'}">
                                            ${item.quantity_in_stock} ${item.unit || ''}
                                        </span>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="field">
                <button class="button is-info is-small" onclick="withdrawalManager.debugWithdrawal(${withdrawal.withdrawal_id})">
                    <span class="icon">
                        <i class="fas fa-bug"></i>
                    </span>
                    <span>Debug Info</span>
                </button>
            </div>
        `;
    }
    
    closeViewModal() {
        document.getElementById('viewModal').classList.remove('is-active');
        this.currentWithdrawalData = null; // Clear stored data
    }
    
    async editWithdrawal(id) {
        try {
            const response = await fetch(`controllers/InventoryWithdrawalController.php?action=get&id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                this.isEditing = true;
                this.currentWithdrawalId = id;
                document.getElementById('modalTitle').textContent = 'Edit Withdrawal';
                this.populateForm(result.data);
                
                // Show status field when editing (hidden for new withdrawals)
                const statusField = document.getElementById('status').closest('.field');
                if (statusField) {
                    statusField.style.display = 'block';
                }
                
                document.getElementById('withdrawalModal').classList.add('is-active');
            } else {
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error loading withdrawal:', error);
            this.showNotification('Error loading withdrawal for editing', 'danger');
        }
    }
    
    populateForm(withdrawal) {
        document.getElementById('withdrawalId').value = withdrawal.withdrawal_id;
        
        // Format date for datetime-local input
        const date = new Date(withdrawal.withdrawal_date);
        const localDateTime = new Date(date.getTime() - date.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
        document.getElementById('withdrawalDate').value = localDateTime;
        
        document.getElementById('status').value = withdrawal.status;
        document.getElementById('notes').value = withdrawal.notes || '';
        
        // Clear existing items
        document.getElementById('itemsContainer').innerHTML = '';
        this.itemEntryCount = 0;
        
        // Add withdrawal items
        withdrawal.items.forEach(item => {
            this.addItemEntry();
            const currentEntry = this.itemEntryCount;
            document.getElementById(`itemId-${currentEntry}`).value = item.item_id;
            document.getElementById(`quantity-${currentEntry}`).value = item.quantity;
            
            // Trigger change event to show stock info
            document.getElementById(`itemId-${currentEntry}`).dispatchEvent(new Event('change'));
        });
    }
    
    confirmDelete(id, description) {
        this.currentWithdrawalId = id;
        document.getElementById('deleteWithdrawalInfo').textContent = description;
        document.getElementById('deleteModal').classList.add('is-active');
    }
    
    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
        this.currentWithdrawalId = null;
    }
    
    async deleteWithdrawal() {
        if (!this.currentWithdrawalId) return;
        
        try {
            const response = await fetch(`controllers/InventoryWithdrawalController.php?action=delete&id=${this.currentWithdrawalId}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                this.closeDeleteModal();
                this.loadWithdrawals();
            } else {
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error deleting withdrawal:', error);
            this.showNotification('Error deleting withdrawal', 'danger');
        }
    }
    
    async completeWithdrawal(id) {
        console.log('🔄 Starting withdrawal completion for ID:', id);
        
        if (!confirm('Are you sure you want to complete this withdrawal? This will update the inventory stock levels.')) {
            console.log('❌ User cancelled withdrawal completion');
            return;
        }
        
        try {
            console.log('📡 Sending completion request...');
            
            const response = await fetch('controllers/InventoryWithdrawalController.php?action=complete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ withdrawal_id: id })
            });
            
            console.log('📥 Response received:', response.status, response.statusText);
            
            // Check if response is ok
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            console.log('📋 Response data:', result);
            
            if (result.success) {
                console.log('✅ Withdrawal completed successfully');
                
                // Show detailed success message with stock updates
                let message = result.message;
                if (result.stock_updates && result.stock_updates.length > 0) {
                    console.log('📊 Stock updates:', result.stock_updates);
                    message += '\n\nStock Updates:';
                    result.stock_updates.forEach(update => {
                        message += `\n• ${update.item_name}: ${update.old_stock} → ${update.new_stock} (withdrew ${update.withdrawn})`;
                    });
                } else {
                    console.warn('⚠️ No stock updates returned');
                }
                
                this.showNotification(message, 'success');
                this.loadWithdrawals();
                
                // Log the completion for debugging
                console.log('🎯 Withdrawal completion summary:', {
                    withdrawal_id: id,
                    stock_updates: result.stock_updates,
                    details: result.details
                });
            } else {
                console.error('❌ Server returned error:', result.message);
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('💥 JavaScript error during completion:', error);
            console.error('Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            this.showNotification('Error completing withdrawal: ' + error.message, 'danger');
        }
    }
    
    searchWithdrawals() {
        this.currentPage = 1;
        this.loadWithdrawals();
    }
    
    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('userFilter').value = '';
        this.searchWithdrawals();
    }
    
    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('withdrawalsContainer').style.display = 'none';
    }
    
    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('withdrawalsContainer').style.display = 'block';
    }
    
    showEmptyState() {
        document.getElementById('emptyState').style.display = 'block';
    }
    
    hideEmptyState() {
        document.getElementById('emptyState').style.display = 'none';
    }
    
    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification-toast');
        existingNotifications.forEach(n => n.remove());
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = `notification is-${type} notification-toast`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideInRight 0.3s ease-out;
        `;
        
        notification.innerHTML = `
            <button class="delete"></button>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        // Add close functionality
        notification.querySelector('.delete').addEventListener('click', () => {
            notification.remove();
        });
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
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
    
    async debugWithdrawal(id) {
        try {
            console.log('🐛 Starting debug for withdrawal ID:', id);
            
            const response = await fetch(`controllers/InventoryWithdrawalController.php?action=debug-withdrawal&id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                console.log('🔍 Debug Information:', result.data);
                
                // Create a detailed debug message
                let debugMessage = `=== WITHDRAWAL DEBUG INFO ===\n`;
                debugMessage += `Withdrawal ID: ${result.data.withdrawal.withdrawal_id}\n`;
                debugMessage += `Status: ${result.data.withdrawal.status}\n`;
                debugMessage += `Can Complete: ${result.data.can_complete ? 'YES' : 'NO'}\n`;
                debugMessage += `Total Items: ${result.data.debug_info.total_items}\n\n`;
                
                debugMessage += `=== ITEMS ANALYSIS ===\n`;
                result.data.items_analysis.forEach((item, index) => {
                    debugMessage += `Item ${index + 1}: ${item.current_stock.item_name}\n`;
                    debugMessage += `  - Withdrawal Qty: ${item.withdrawal_item.quantity}\n`;
                    debugMessage += `  - Current Stock: ${item.current_stock.quantity_in_stock}\n`;
                    debugMessage += `  - Stock Sufficient: ${item.stock_sufficient ? 'YES' : 'NO'}\n`;
                    debugMessage += `  - Stock After: ${item.stock_after_withdrawal}\n`;
                    debugMessage += `  - Last Updated: ${item.current_stock.last_updated}\n\n`;
                });
                
                console.log(debugMessage);
                alert(debugMessage);
                
                // Also show in a more readable format
                this.showNotification('Debug information logged to console. Check browser console for details.', 'info');
            } else {
                console.error('Debug failed:', result.message);
                this.showNotification('Debug failed: ' + result.message, 'danger');
            }
        } catch (error) {
            console.error('Debug error:', error);
            this.showNotification('Debug error: ' + error.message, 'danger');
        }
    }
    
    async printWithdrawalFromCard(withdrawalId) {
        try {
            console.log('🖨️ Printing withdrawal ID:', withdrawalId);
            
            const response = await fetch(`controllers/InventoryWithdrawalController.php?action=get&id=${withdrawalId}`);
            const result = await response.json();
            
            if (result.success) {
                this.generatePrintContent(result.data);
                window.print();
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading withdrawal for printing:', error);
            this.showNotification('Error loading withdrawal: ' + error.message, 'danger');
        }
    }
    
    generatePrintContent(withdrawal) {
        // Get or create print area
        let printArea = document.getElementById('withdrawalPrintArea');
        if (!printArea) {
            printArea = document.createElement('div');
            printArea.id = 'withdrawalPrintArea';
            printArea.style.display = 'none';
            document.body.appendChild(printArea);
        }
        
        const formattedDate = new Date(withdrawal.withdrawal_date).toLocaleDateString();
        const formattedTime = new Date(withdrawal.withdrawal_date).toLocaleTimeString();
        const currentDate = new Date().toLocaleDateString();
        
        let itemsHtml = '';
        if (withdrawal.items && withdrawal.items.length > 0) {
            itemsHtml = `
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Item Name</th>
                            <th style="width: 20%;">Category</th>
                            <th style="width: 15%;">Quantity</th>
                            <th style="width: 15%;">Unit</th>
                            <th style="width: 20%;">Available Stock</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            withdrawal.items.forEach((item, index) => {
                itemsHtml += `
                    <tr>
                        <td class="item-name">${this.escapeHtml(item.item_name || 'Unknown Item')}</td>
                        <td>${this.escapeHtml(item.category_name || 'N/A')}</td>
                        <td style="text-align: center; font-weight: bold;">${item.quantity || 'N/A'}</td>
                        <td style="text-align: center;">${this.escapeHtml(item.unit || '')}</td>
                        <td style="text-align: center;">${item.quantity_in_stock || 'N/A'} ${this.escapeHtml(item.unit || '')}</td>
                    </tr>
                `;
            });
            
            itemsHtml += `
                    </tbody>
                </table>
            `;
        } else {
            itemsHtml = '<table class="items-table"><tr><td colspan="5" style="text-align: center; font-style: italic;">No items in this withdrawal.</td></tr></table>';
        }
        
        printArea.innerHTML = `
            <div class="print-header">
                <div class="print-title">Hospital Management System</div>
                <div class="print-subtitle">Inventory Withdrawal Receipt</div>
            </div>
            
            <div class="print-withdrawal-info">
                <h3>Withdrawal Information</h3>
                <div class="print-withdrawal-grid">
                    <div><strong>Withdrawal ID:</strong> #${withdrawal.withdrawal_id}</div>
                    <div><strong>Date:</strong> ${formattedDate}</div>
                    <div><strong>Time:</strong> ${formattedTime}</div>
                    <div><strong>Status:</strong> ${withdrawal.status.toUpperCase()}</div>
                    <div><strong>Performed By:</strong> ${this.escapeHtml(withdrawal.performed_by_name || 'Unknown User')}</div>
                    <div><strong>Total Items:</strong> ${withdrawal.items ? withdrawal.items.length : 0}</div>
                </div>
            </div>
            
            ${withdrawal.notes ? `<div class="print-notes"><strong>Notes:</strong> ${this.escapeHtml(withdrawal.notes)}</div>` : ''}
            
            <div class="print-items">
                <h3>Withdrawn Items (${withdrawal.items ? withdrawal.items.length : 0} items)</h3>
                ${itemsHtml}
            </div>
            
            <div class="print-signature">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>Authorized By</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>Received By</div>
                </div>
            </div>
            
            <div class="print-footer">
                <div>Withdrawal ID: ${withdrawal.withdrawal_id} | Status: ${withdrawal.status.toUpperCase()} | Total Items: ${withdrawal.items ? withdrawal.items.length : 0}</div>
                <div>Printed on: ${currentDate} | This is an official inventory withdrawal record</div>
                <div style="margin-top: 4px; font-weight: bold;">⚠️ Keep this receipt for your records - Required for inventory audit trail</div>
            </div>
        `;
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
    
    printCurrentWithdrawal() {
        if (!this.currentWithdrawalData) {
            this.showNotification('No withdrawal data available for printing', 'warning');
            return;
        }
        
        this.generatePrintContent(this.currentWithdrawalData);
        window.print();
    }
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);

// Initialize the withdrawal manager
const withdrawalManager = new InventoryWithdrawalManager(); 