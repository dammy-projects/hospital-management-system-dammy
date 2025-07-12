<?php
require_once 'includes/header.php';
?>

<style>
/* Compact styles for smaller doctor cards */
.doctors-grid .column {
    margin-bottom: 1rem;
    padding: 0.5rem;
}

.doctors-grid .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 6px;
}

.doctors-grid .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.card-footer-item {
    padding: 0.4rem;
    cursor: pointer;
    transition: background-color 0.2s ease;
    font-size: 0.8rem;
}

.card-footer-item:hover {
    background-color: #f5f5f5;
}

/* Text overflow handling */
.card .title,
.card .subtitle {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Ensure proper spacing between name and specialty */
.card .title.is-6 {
    margin-bottom: 0.25rem !important;
}

.card .subtitle.is-7 {
    margin-top: 0 !important;
    margin-bottom: 0.5rem !important;
}

/* Mobile responsiveness for smaller cards */
@media (max-width: 768px) {
    .doctors-grid .column {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (min-width: 769px) and (max-width: 1023px) {
    .doctors-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1024px) and (max-width: 1215px) {
    .doctors-grid .column {
        flex: 0 0 33.333%;
        max-width: 33.333%;
    }
}

@media (min-width: 1216px) {
    .doctors-grid .column {
        flex: 0 0 25%;
        max-width: 25%;
    }
}
</style>

<div class="page-transition mt-4">
    <!-- Page Header -->
    <div class="columns is-vcentered mb-4">
        <div class="column">
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-user-md"></i>
                </span>
                Doctor Management
            </h1>
            <p class="subtitle">Manage doctors and their information</p>
        </div>
        <div class="column is-narrow">
            <button class="button is-primary" id="addDoctorBtn">
                <span class="icon">
                    <i class="fas fa-user-plus"></i>
                </span>
                <span>Add New Doctor</span>
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-content">
            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Search Doctors</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="searchInput" placeholder="Search by name, specialty, email..." />
                            <span class="icon is-small is-left">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Filter by Department</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="departmentFilter">
                                    <option value="">All Departments</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Filter by Status</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label class="label">&nbsp;</label>
                        <div class="control">
                            <button class="button is-info is-fullwidth" id="clearFilters">
                                <span class="icon">
                                    <i class="fas fa-redo"></i>
                                </span>
                                <span>Reset</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Doctors Grid -->
    <div id="doctorsContainer">
        <div class="columns is-multiline doctors-grid" id="doctorsGrid">
            <!-- Doctors will be loaded here -->
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="has-text-centered mt-6 mb-6" id="loadingSpinner" style="display: none;">
        <span class="icon is-large">
            <i class="fas fa-spinner fa-pulse fa-2x"></i>
        </span>
        <p class="mt-2">Loading doctors...</p>
    </div>

    <!-- Empty State -->
    <div class="has-text-centered mt-6 mb-6" id="emptyState" style="display: none;">
        <span class="icon is-large has-text-grey-light">
            <i class="fas fa-user-md fa-3x"></i>
        </span>
        <p class="title is-5 has-text-grey mt-3">No doctors found</p>
        <p class="subtitle is-6 has-text-grey">Try adjusting your search criteria or add a new doctor</p>
    </div>

    <!-- Pagination -->
    <nav class="pagination is-centered mt-5" role="navigation" aria-label="pagination" id="pagination" style="display: none;">
        <a class="pagination-previous" id="prevPage">Previous</a>
        <a class="pagination-next" id="nextPage">Next page</a>
        <ul class="pagination-list" id="paginationList">
            <!-- Pagination buttons will be generated here -->
        </ul>
    </nav>
</div>

<!-- Add/Edit Doctor Modal -->
<div class="modal" id="doctorModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title" id="modalTitle">Add New Doctor</p>
            <button class="delete" aria-label="close" id="closeModal"></button>
        </header>
        <section class="modal-card-body">
            <form id="doctorForm">
                <input type="hidden" id="doctorId" />
                
                <div class="columns">
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">First Name *</label>
                            <div class="control">
                                <input class="input" type="text" id="firstName" placeholder="Enter first name" required />
                            </div>
                        </div>
                    </div>
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Middle Name</label>
                            <div class="control">
                                <input class="input" type="text" id="middleName" placeholder="Enter middle name" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Last Name *</label>
                            <div class="control">
                                <input class="input" type="text" id="lastName" placeholder="Enter last name" required />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Specialty *</label>
                            <div class="control">
                                <input class="input" type="text" id="specialty" placeholder="e.g., Cardiologist, Neurologist" required />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Department *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="departmentId" required>
                                        <option value="">Select Department</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Contact Number *</label>
                            <div class="control">
                                <input class="input" type="tel" id="contactNumber" placeholder="Enter contact number" required />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Email *</label>
                            <div class="control">
                                <input class="input" type="email" id="email" placeholder="Enter email address" required />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-8">
                        <div class="field">
                            <label class="label">Schedule</label>
                            <div class="control">
                                <input class="input" type="text" id="schedule" placeholder="e.g., Monday-Friday: 9:00 AM - 5:00 PM" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Status *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="saveDoctor">Save Doctor</button>
            <button class="button" id="cancelModal">Cancel</button>
        </footer>
    </div>
</div>

<!-- View Doctor Modal -->
<div class="modal" id="viewModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Doctor Details</p>
            <button class="delete" aria-label="close" id="closeViewModal"></button>
        </header>
        <section class="modal-card-body">
            <div class="columns">
                <div class="column is-6">
                    <div class="field">
                        <label class="label">Full Name</label>
                        <div class="control">
                            <input class="input" type="text" id="viewFullName" readonly />
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="field">
                        <label class="label">Specialty</label>
                        <div class="control">
                            <input class="input" type="text" id="viewSpecialty" readonly />
                        </div>
                    </div>
                </div>
            </div>

            <div class="columns">
                <div class="column is-6">
                    <div class="field">
                        <label class="label">Department</label>
                        <div class="control">
                            <input class="input" type="text" id="viewDepartment" readonly />
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="field">
                        <label class="label">Status</label>
                        <div class="control">
                            <span class="tag is-medium" id="viewStatus"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="columns">
                <div class="column is-6">
                    <div class="field">
                        <label class="label">Contact Number</label>
                        <div class="control">
                            <input class="input" type="text" id="viewContactNumber" readonly />
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="field">
                        <label class="label">Email</label>
                        <div class="control">
                            <input class="input" type="text" id="viewEmail" readonly />
                        </div>
                    </div>
                </div>
            </div>

            <div class="field">
                <label class="label">Schedule</label>
                <div class="control">
                    <textarea class="textarea" id="viewSchedule" readonly rows="2"></textarea>
                </div>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button" id="cancelView">Close</button>
        </footer>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Confirm Delete</p>
            <button class="delete" aria-label="close" id="closeDeleteModal"></button>
        </header>
        <section class="modal-card-body">
            <div class="content">
                <p>Are you sure you want to delete this doctor?</p>
                <p><strong id="deleteDoctorName"></strong></p>
                <p class="has-text-danger">This action cannot be undone.</p>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-danger" id="confirmDelete">Delete Doctor</button>
            <button class="button" id="cancelDelete">Cancel</button>
        </footer>
    </div>
</div>

<!-- JavaScript -->
<script src="js/doctors.js"></script>

    </div> <!-- End of container from header.php -->
</body>
</html> 