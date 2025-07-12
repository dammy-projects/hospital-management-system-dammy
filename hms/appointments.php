<?php
require_once 'includes/header.php';
?>

<style>
/* Compact styles for smaller appointment cards */
.appointments-grid .column {
    margin-bottom: 1rem;
    padding: 0.5rem;
}

.appointments-grid .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 6px;
}

.appointments-grid .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.card-footer-item {
    padding: 0.5rem;
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

/* Status badge styles */
.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-scheduled {
    background-color: #3273dc;
    color: white;
}

.status-completed {
    background-color: #23d160;
    color: white;
}

.status-cancelled {
    background-color: #ff3860;
    color: white;
}

/* Priority indicator */
.priority-urgent {
    border-left: 4px solid #ff3860;
    animation: pulseUrgent 2s infinite;
}

.priority-normal {
    border-left: 4px solid #3273dc;
}

/* Pulse animation for urgent appointments */
@keyframes pulseUrgent {
    0% { box-shadow: 0 0 0 0 rgba(255, 56, 96, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(255, 56, 96, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 56, 96, 0); }
}

/* Ensure proper spacing */
.card .title.is-6 {
    margin-bottom: 0.25rem !important;
}

.card .subtitle.is-7 {
    margin-top: 0 !important;
    margin-bottom: 0.5rem !important;
}

/* Mobile responsiveness for smaller cards */
@media (max-width: 768px) {
    .appointments-grid .column {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (min-width: 769px) and (max-width: 1023px) {
    .appointments-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1024px) and (max-width: 1215px) {
    .appointments-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1216px) {
    .appointments-grid .column {
        flex: 0 0 25%;
        max-width: 25%;
    }
}

/* View Modal Styling */
#viewModal .modal-card-body p {
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

#viewModal .label {
    font-weight: 600;
    color: #363636;
    margin-bottom: 0.25rem;
}

#viewModal .tag {
    font-weight: 600;
}

#viewModal .has-text-weight-semibold {
    color: #2563eb;
    font-size: 1.1rem;
}
</style>

<div class="page-transition mt-4">
    <!-- Page Header -->
    <div class="columns is-vcentered mb-4">
        <div class="column">
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                Appointment Management
            </h1>
            <p class="subtitle">Schedule and manage patient appointments</p>
        </div>
        <div class="column is-narrow">
            <button class="button is-primary" id="addAppointmentBtn">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>Schedule Appointment</span>
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-content">
            <div class="columns">
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Search Appointments</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="searchInput" placeholder="Search by patient, doctor, purpose..." />
                            <span class="icon is-small is-left">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label class="label">Status</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Doctor</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="doctorFilter">
                                    <option value="">All Doctors</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label class="label">Date Range</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="dateFilter">
                                    <option value="">All Dates</option>
                                    <option value="today">Today</option>
                                    <option value="tomorrow">Tomorrow</option>
                                    <option value="this_week">This Week</option>
                                    <option value="next_week">Next Week</option>
                                    <option value="this_month">This Month</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-1">
                    <div class="field">
                        <label class="label">&nbsp;</label>
                        <div class="control">
                            <button class="button is-info is-fullwidth" id="clearFilters">
                                <span class="icon">
                                    <i class="fas fa-redo"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments Grid -->
    <div id="appointmentsContainer">
        <div class="columns is-multiline appointments-grid" id="appointmentsGrid">
            <!-- Appointments will be loaded here -->
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="has-text-centered mt-6 mb-6" id="loadingSpinner" style="display: none;">
        <span class="icon is-large">
            <i class="fas fa-spinner fa-pulse fa-2x"></i>
        </span>
        <p class="mt-2">Loading appointments...</p>
    </div>

    <!-- Empty State -->
    <div class="has-text-centered mt-6 mb-6" id="emptyState" style="display: none;">
        <span class="icon is-large has-text-grey-light">
            <i class="fas fa-calendar-times fa-3x"></i>
        </span>
        <p class="title is-5 has-text-grey mt-3">No appointments found</p>
        <p class="subtitle is-6 has-text-grey">Try adjusting your search criteria or schedule a new appointment</p>
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

<!-- Add/Edit Appointment Modal -->
<div class="modal" id="appointmentModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title" id="modalTitle">Schedule New Appointment</p>
            <button class="delete" aria-label="close" id="closeModal"></button>
        </header>
        <section class="modal-card-body">
            <form id="appointmentForm">
                <input type="hidden" id="appointmentId" />
                
                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Patient *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="patientId" required>
                                        <option value="">Select Patient</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Doctor *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="doctorId" required>
                                        <option value="">Select Doctor</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Appointment Date *</label>
                            <div class="control">
                                <input class="input" type="date" id="appointmentDate" required />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Appointment Time *</label>
                            <div class="control">
                                <input class="input" type="time" id="appointmentTime" required />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-8">
                        <div class="field">
                            <label class="label">Purpose *</label>
                            <div class="control">
                                <textarea class="textarea" id="purpose" placeholder="Reason for appointment" required rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Status *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="status" required>
                                        <option value="scheduled">Scheduled</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="saveAppointment">Save Appointment</button>
            <button class="button" id="cancelModal">Cancel</button>
        </footer>
    </div>
</div>

<!-- View Appointment Modal -->
<div class="modal" id="viewModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Appointment Details</p>
            <button class="delete" aria-label="close" id="closeViewModal"></button>
        </header>
        <section class="modal-card-body">
            <div class="content">
                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Patient Name</label>
                            <div class="control">
                                <p class="has-text-weight-semibold" id="viewPatientName">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Doctor</label>
                            <div class="control">
                                <p class="has-text-weight-semibold" id="viewDoctorName">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Appointment Date</label>
                            <div class="control">
                                <p id="viewAppointmentDate">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Appointment Time</label>
                            <div class="control">
                                <p id="viewAppointmentTime">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-8">
                        <div class="field">
                            <label class="label">Purpose</label>
                            <div class="control">
                                <p id="viewPurpose">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Status</label>
                            <div class="control">
                                <span class="tag" id="viewStatus">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Specialty</label>
                            <div class="control">
                                <p id="viewSpecialty">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Created</label>
                            <div class="control">
                                <p id="viewCreatedAt">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-primary" id="cancelView">Close</button>
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
                <p>Are you sure you want to delete this appointment?</p>
                <p><strong id="deleteAppointmentInfo"></strong></p>
                <p class="has-text-danger">This action cannot be undone.</p>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-danger" id="confirmDelete">Delete Appointment</button>
            <button class="button" id="cancelDelete">Cancel</button>
        </footer>
    </div>
</div>

<!-- JavaScript -->
<script src="js/appointments.js"></script>

    </div> <!-- End of container from header.php -->
</body>
</html> 