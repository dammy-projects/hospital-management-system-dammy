class ReportsManager {
    constructor() {
        this.dateRange = {
            from: null,
            to: null
        };
        this.init();
    }

    init() {
        this.bindEvents();
        this.setDefaultDates();
        this.loadOverviewStats();
    }

    bindEvents() {
        // Refresh dashboard
        document.getElementById('refreshDashboard').addEventListener('click', () => {
            this.loadOverviewStats();
        });

        // Export summary
        document.getElementById('exportSummary').addEventListener('click', () => {
            this.exportSummary();
        });

        // Apply date filter
        document.getElementById('applyDateFilter').addEventListener('click', () => {
            this.applyDateFilter();
        });

        // Date input changes
        document.getElementById('fromDate').addEventListener('change', (e) => {
            this.dateRange.from = e.target.value;
        });

        document.getElementById('toDate').addEventListener('change', (e) => {
            this.dateRange.to = e.target.value;
        });
    }

    setDefaultDates() {
        const today = new Date();
        const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
        
        const todayStr = today.toISOString().split('T')[0];
        const thirtyDaysAgoStr = thirtyDaysAgo.toISOString().split('T')[0];
        
        document.getElementById('fromDate').value = thirtyDaysAgoStr;
        document.getElementById('toDate').value = todayStr;
        
        this.dateRange.from = thirtyDaysAgoStr;
        this.dateRange.to = todayStr;
    }

    async loadOverviewStats() {
        try {
            this.showLoading();
            
            const params = new URLSearchParams({
                action: 'overview',
                from_date: this.dateRange.from,
                to_date: this.dateRange.to
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            this.hideLoading();

            if (data.success) {
                this.renderOverviewStats(data.data);
            } else {
                this.showNotification('Error loading overview: ' + data.error, 'is-danger');
            }
        } catch (error) {
            this.hideLoading();
            console.error('Error loading overview:', error);
            this.showNotification('Failed to load overview statistics', 'is-danger');
        }
    }

    renderOverviewStats(stats) {
        const container = document.getElementById('overviewStats');
        
        container.innerHTML = `
            <div class="column is-2">
                <div class="stat-box">
                    <span class="stat-number">${stats.total_patients || 0}</span>
                    <div class="stat-label">Total Patients</div>
                </div>
            </div>
            <div class="column is-2">
                <div class="stat-box">
                    <span class="stat-number">${stats.total_doctors || 0}</span>
                    <div class="stat-label">Active Doctors</div>
                </div>
            </div>
            <div class="column is-2">
                <div class="stat-box">
                    <span class="stat-number">${stats.total_appointments || 0}</span>
                    <div class="stat-label">Appointments</div>
                </div>
            </div>
            <div class="column is-2">
                <div class="stat-box">
                    <span class="stat-number">₱${this.formatAmount(stats.total_revenue || 0)}</span>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
            <div class="column is-2">
                <div class="stat-box">
                    <span class="stat-number">${stats.total_prescriptions || 0}</span>
                    <div class="stat-label">Prescriptions</div>
                </div>
            </div>
            <div class="column is-2">
                <div class="stat-box">
                    <span class="stat-number">${stats.low_stock_items || 0}</span>
                    <div class="stat-label">Low Stock Items</div>
                </div>
            </div>
        `;
    }

    applyDateFilter() {
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;

        if (!fromDate || !toDate) {
            this.showNotification('Please select both from and to dates', 'is-warning');
            return;
        }

        if (new Date(fromDate) > new Date(toDate)) {
            this.showNotification('From date cannot be later than to date', 'is-warning');
            return;
        }

        this.dateRange.from = fromDate;
        this.dateRange.to = toDate;
        
        this.loadOverviewStats();
        this.showNotification('Date filter applied successfully', 'is-success');
    }

    async exportSummary() {
        try {
            const params = new URLSearchParams({
                action: 'export_summary',
                from_date: this.dateRange.from,
                to_date: this.dateRange.to,
                format: 'pdf'
            });

            this.showNotification('Generating export...', 'is-info');
            
            // Create a temporary link to download the file
            const link = document.createElement('a');
            link.href = `controllers/ReportsController.php?${params}`;
            link.download = `hospital_summary_${this.dateRange.from}_to_${this.dateRange.to}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            this.showNotification('Export completed successfully', 'is-success');
        } catch (error) {
            console.error('Error exporting summary:', error);
            this.showNotification('Failed to export summary', 'is-danger');
        }
    }

    formatAmount(amount) {
        return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
    }

    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
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

// Global function for module report navigation
function openModuleReport(module) {
    // Navigate to specific module report page
    window.location.href = `report_${module}.php`;
}

// Initialize the reports manager when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.reportsManager = new ReportsManager();
}); 