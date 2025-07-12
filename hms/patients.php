<?php
require_once 'includes/header.php';
?>

<style>
/* Compact styles for smaller patient cards */
.patients-grid .column {
    margin-bottom: 1rem;
    padding: 0.5rem;
}

.patients-grid .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 6px;
}

.patients-grid .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.card-footer-item {
    padding: 0.3rem 0.2rem;
    cursor: pointer;
    transition: background-color 0.2s ease;
    font-size: 0.7rem;
    text-align: center;
    border-right: 1px solid #dbdbdb;
    flex: 1;
    min-width: 0;
}

.card-footer-item:last-child {
    border-right: none;
}

.card-footer-item:hover {
    background-color: #f5f5f5;
}

.card-footer-item .icon {
    margin-right: 0.25rem !important;
}

.card-footer-item span:not(.icon) {
    font-size: 0.65rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Text overflow handling */
.card .title,
.card .subtitle {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Ensure proper spacing between name and details */
.card .title.is-6 {
    margin-bottom: 0.25rem !important;
}

.card .subtitle.is-7 {
    margin-top: 0 !important;
    margin-bottom: 0.5rem !important;
}

/* Patient details text handling */
.patient-details {
    font-size: 0.75rem;
    color: #6c757d;
    line-height: 1.2em;
}

/* Gender badge styling */
.gender-badge {
    text-transform: capitalize;
}

/* Mobile responsiveness for smaller cards */
@media (max-width: 768px) {
    .patients-grid .column {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (min-width: 769px) and (max-width: 1023px) {
    .patients-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1024px) and (max-width: 1215px) {
    .patients-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1216px) {
    .patients-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

/* View Modal Styling */
#viewPatientModal .modal-card {
    width: 90vw;
    max-width: 1200px;
    max-height: 90vh;
    margin: auto;
}

#viewPatientModal .modal-card-body {
    max-height: 70vh;
    overflow-y: auto;
    padding: 1.5rem;
}

#viewPatientModal .field {
    margin-bottom: 1rem;
}

#viewPatientModal .label {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #363636;
}

#viewPatientModal .box {
    padding: 1rem;
    margin-bottom: 0;
    min-height: 2.5rem;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

#viewPatientModal .tag.is-large {
    font-size: 1rem;
    padding: 0.5rem 0.75rem;
    height: auto;
    white-space: nowrap;
}

#viewPatientModal .subtitle {
    margin-bottom: 1rem !important;
    border-bottom: 1px solid #dbdbdb;
    padding-bottom: 0.5rem;
}

#viewPatientModal .column {
    padding: 0.5rem;
}

#viewPatientModal p {
    word-wrap: break-word;
    overflow-wrap: break-word;
    line-height: 1.5;
}

/* Responsive adjustments for view modal */
@media (max-width: 768px) {
    #viewPatientModal .modal-card {
        width: 95vw;
        margin: 1rem auto;
    }
    
    #viewPatientModal .columns {
        display: block;
    }
    
    #viewPatientModal .column {
        width: 100% !important;
        flex: none !important;
    }
    
    #viewPatientModal .card {
        margin-top: 1rem;
    }
}

/* Print Styles - Optimized for Bond Paper */
@media print {
    body * {
        visibility: hidden;
    }
    
    #patientPrintArea,
    #patientPrintArea * {
        visibility: visible;
    }
    
    #patientPrintArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        padding: 10px;
        font-family: Arial, sans-serif;
        display: block !important;
        visibility: visible !important;
        font-size: 12px;
        line-height: 1.3;
        page-break-inside: avoid;
        max-height: 100vh;
        overflow: hidden;
    }
    
    .print-header {
        text-align: center;
        border-bottom: 2px solid #000;
        padding-bottom: 6px;
        margin-bottom: 8px;
        page-break-inside: avoid;
    }
    
    .print-title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 3px;
    }
    
    .print-subtitle {
        font-size: 13px;
        color: #666;
        margin: 0;
    }
    
    .print-info {
        margin-bottom: 8px;
        border: 1px solid #000;
        padding: 6px;
        page-break-inside: avoid;
    }
    
    .print-info h3 {
        margin: 0 0 4px 0 !important;
        font-size: 13px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 3px;
        font-weight: bold;
    }
    
    .print-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        font-size: 11px;
        margin-bottom: 4px;
    }
    
    .print-patients {
        margin-bottom: 8px;
        page-break-inside: avoid;
    }
    
    .print-patients h3 {
        margin: 0 0 5px 0 !important;
        font-size: 13px;
        background-color: #f0f0f0;
        padding: 4px 6px;
        border: 1px solid #000;
        page-break-after: avoid;
        font-weight: bold;
    }
    
    .patients-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 10px;
        page-break-inside: avoid;
    }
    
    .patients-table th,
    .patients-table td {
        border: 1px solid #ccc;
        padding: 3px 4px;
        text-align: left;
        vertical-align: top;
        line-height: 1.2;
    }
    
    .patients-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        font-size: 10px;
    }
    
    .print-footer {
        margin-top: 8px;
        border-top: 1px solid #000;
        padding-top: 4px;
        text-align: center;
        font-size: 9px;
        line-height: 1.3;
        page-break-inside: avoid;
    }
    
    .print-footer div {
        margin-bottom: 2px;
    }
    
    /* Optimize for bond paper */
    @page {
        margin: 0.4in;
        size: letter;
    }
    
    /* Prevent page breaks within sections */
    .print-patients,
    .print-info,
    .print-header,
    .print-footer {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    
    /* Better spacing */
    h1, h2, h3, h4, h5, h6 {
        page-break-after: avoid;
        margin-top: 0 !important;
        margin-bottom: 4px !important;
    }
    
    p, div {
        margin: 0 !important;
    }
    
    /* Ensure content fits properly */
    * {
        max-width: 100% !important;
        box-sizing: border-box;
    }
    
    /* Hide elements that shouldn't be printed */
    .navbar, .tabs, .search-controls, .pagination-controls, 
    .button, .modal, .loading-overlay, .card-footer {
        display: none !important;
    }
}

/* Responsive button text */
@media (max-width: 1215px) {
    .card-footer-item span:not(.icon) {
        display: none;
    }
    
    .card-footer-item {
        padding: 0.4rem 0.2rem;
    }
}

@media (min-width: 1216px) {
    .card-footer-item span:not(.icon) {
        display: inline;
    }
}
</style>

<div class="page-transition mt-4">
    <!-- Page Header -->
    <div class="columns is-vcentered mb-4">
        <div class="column">
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-users"></i>
                </span>
                Patient Management
            </h1>
            <p class="subtitle">Manage patients and their information</p>
        </div>
        <div class="column is-narrow">
            <button class="button is-primary" id="addPatientBtn">
                <span class="icon">
                    <i class="fas fa-user-plus"></i>
                </span>
                <span>Add New Patient</span>
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-content">
            <div class="columns">
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Search Patients</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="searchInput" placeholder="Search by name, email, phone..." />
                            <span class="icon is-small is-left">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label class="label">Filter by Gender</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="genderFilter">
                                    <option value="">All Genders</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label class="label">Filter by Status</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="deceased">Deceased</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label class="label">Sort By</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="sortBy">
                                    <option value="first_name">First Name</option>
                                    <option value="last_name">Last Name</option>
                                    <option value="date_of_birth">Date of Birth</option>
                                    <option value="created_at">Date Added</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-2">
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

    <!-- Patients Grid -->
    <div id="patientsContainer">
        <div class="columns is-multiline patients-grid" id="patientsGrid">
            <!-- Patients will be loaded here -->
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="has-text-centered mt-6 mb-6" id="loadingSpinner" style="display: none;">
        <span class="icon is-large">
            <i class="fas fa-spinner fa-pulse fa-2x"></i>
        </span>
        <p class="mt-2">Loading patients...</p>
    </div>

    <!-- Empty State -->
    <div class="has-text-centered mt-6 mb-6" id="emptyState" style="display: none;">
        <span class="icon is-large has-text-grey-light">
            <i class="fas fa-users fa-3x"></i>
        </span>
        <p class="title is-5 has-text-grey mt-3">No patients found</p>
        <p class="subtitle is-6 has-text-grey">Try adjusting your search criteria or add a new patient</p>
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

<!-- Add/Edit Patient Modal -->
<div class="modal" id="patientModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title" id="modalTitle">Add New Patient</p>
            <button class="delete" aria-label="close" id="closeModal"></button>
        </header>
        <section class="modal-card-body">
            <form id="patientForm">
                <input type="hidden" id="patientId" />
                
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
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Date of Birth *</label>
                            <div class="control">
                                <input class="input" type="date" id="dateOfBirth" required />
                            </div>
                        </div>
                    </div>
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Gender *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
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
                                        <option value="deceased">Deceased</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Contact Number</label>
                            <div class="control">
                                <input class="input" type="tel" id="contactNumber" placeholder="Enter contact number" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Email</label>
                            <div class="control">
                                <input class="input" type="email" id="email" placeholder="Enter email address" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Address</label>
                    <div class="control">
                        <textarea class="textarea" id="address" placeholder="Enter complete address" rows="3"></textarea>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Medical History</label>
                    <div class="control">
                        <textarea class="textarea" id="medicalHistory" placeholder="Enter medical history (allergies, conditions, medications, etc.)" rows="4"></textarea>
                    </div>
                    <p class="help">Include any relevant medical conditions, allergies, current medications, or other important health information</p>
                </div>

                <!-- Emergency Contact Information -->
                <div class="field mt-5">
                    <h5 class="subtitle is-6">
                        <span class="icon">
                            <i class="fas fa-phone-alt"></i>
                        </span>
                        Emergency Contact Information
                    </h5>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Emergency Contact Name</label>
                            <div class="control">
                                <input class="input" type="text" id="emergencyContactName" placeholder="Enter emergency contact name" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Relationship</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="emergencyContactRelationship">
                                        <option value="">Select Relationship</option>
                                        <option value="parent">Parent</option>
                                        <option value="spouse">Spouse</option>
                                        <option value="sibling">Sibling</option>
                                        <option value="child">Child</option>
                                        <option value="friend">Friend</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Emergency Contact Phone</label>
                            <div class="control">
                                <input class="input" type="tel" id="emergencyContactPhone" placeholder="Enter emergency contact phone" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Emergency Contact Address</label>
                            <div class="control">
                                <textarea class="textarea" id="emergencyContactAddress" placeholder="Enter emergency contact address" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guardian/Parent Information -->
                <div class="field mt-5">
                    <h5 class="subtitle is-6">
                        <span class="icon">
                            <i class="fas fa-user-shield"></i>
                        </span>
                        Guardian/Parent Information
                        <span class="tag is-light is-small ml-2">For minors or dependents</span>
                    </h5>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Guardian/Parent Name</label>
                            <div class="control">
                                <input class="input" type="text" id="guardianName" placeholder="Enter guardian/parent name" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Guardian Relationship</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="guardianRelationship">
                                        <option value="">Select Relationship</option>
                                        <option value="parent">Parent</option>
                                        <option value="guardian">Legal Guardian</option>
                                        <option value="grandparent">Grandparent</option>
                                        <option value="aunt_uncle">Aunt/Uncle</option>
                                        <option value="foster_parent">Foster Parent</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Guardian Phone</label>
                            <div class="control">
                                <input class="input" type="tel" id="guardianPhone" placeholder="Enter guardian phone number" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Guardian Address</label>
                            <div class="control">
                                <textarea class="textarea" id="guardianAddress" placeholder="Enter guardian address" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="savePatient">Save Patient</button>
            <button class="button" id="cancelModal">Cancel</button>
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
                <p>Are you sure you want to delete this patient?</p>
                <p><strong id="deletePatientName"></strong></p>
                <p class="has-text-danger">This action cannot be undone and will remove all associated medical records, appointments, and billing information.</p>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-danger" id="confirmDelete">Delete Patient</button>
            <button class="button" id="cancelDelete">Cancel</button>
        </footer>
    </div>
</div>

<!-- View Patient Details Modal -->
<div class="modal" id="viewPatientModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">
                <span class="icon">
                    <i class="fas fa-user-circle"></i>
                </span>
                Patient Details
            </p>
            <button class="delete" aria-label="close" id="closeViewModal"></button>
        </header>
        <section class="modal-card-body">
            <div class="columns">
                <div class="column is-9">
                    <!-- Patient Information -->
                    <div class="content">
                        <!-- Patient Header with Key Info -->
                        <div class="box mb-4">
                            <div class="level">
                                <div class="level-left">
                                    <div class="level-item">
                                        <div>
                                            <p class="title is-4" id="viewFullName"></p>
                                            <p class="subtitle is-6">
                                                <span class="icon">
                                                    <i class="fas fa-id-card"></i>
                                                </span>
                                                Patient ID: <strong id="viewPatientId"></strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="level-right">
                                    <div class="level-item">
                                        <div class="tags has-addons">
                                            <span class="tag is-large" id="viewAge"></span>
                                            <span class="tag is-large" id="viewGender"></span>
                                            <span class="tag is-large" id="viewStatus"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information Section -->
                        <div class="mb-5">
                            <h4 class="subtitle is-5">
                                <span class="icon">
                                    <i class="fas fa-user"></i>
                                </span>
                                Personal Information
                            </h4>
                            <div class="columns">
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Date of Birth</label>
                                        <div class="content">
                                            <p id="viewDateOfBirth"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Registration Date</label>
                                        <div class="content">
                                            <p id="viewRegisteredDate"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="mb-5">
                            <h4 class="subtitle is-5">
                                <span class="icon">
                                    <i class="fas fa-phone"></i>
                                </span>
                                Contact Information
                            </h4>
                            <div class="columns">
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Contact Number</label>
                                        <div class="content">
                                            <p id="viewContactNumber"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Email Address</label>
                                        <div class="content">
                                            <p id="viewEmail"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Address</label>
                                <div class="box" id="viewAddress">
                                    <!-- Address content -->
                                </div>
                            </div>
                        </div>

                        <!-- Medical Information Section -->
                        <div class="mb-5">
                            <h4 class="subtitle is-5">
                                <span class="icon">
                                    <i class="fas fa-notes-medical"></i>
                                </span>
                                Medical Information
                            </h4>
                            <div class="field">
                                <label class="label">Medical History</label>
                                <div class="box" id="viewMedicalHistory">
                                    <!-- Medical history content -->
                                </div>
                            </div>
                        </div>

                        <!-- Insurance Information Section -->
                        <div class="mb-5" id="insuranceSection">
                            <h4 class="subtitle is-5">
                                <span class="icon">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                Insurance Information
                            </h4>
                            <div id="viewInsuranceContent">
                                <!-- Insurance content will be populated here -->
                            </div>
                        </div>

                        <!-- Emergency Contact Information Section -->
                        <div class="mb-5">
                            <h4 class="subtitle is-5">
                                <span class="icon">
                                    <i class="fas fa-phone-alt"></i>
                                </span>
                                Emergency Contact Information
                            </h4>
                            <div class="columns">
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Emergency Contact Name</label>
                                        <div class="content">
                                            <p id="viewEmergencyContactName"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Relationship</label>
                                        <div class="content">
                                            <span class="tag is-medium" id="viewEmergencyContactRelationship"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="columns">
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Emergency Contact Phone</label>
                                        <div class="content">
                                            <p id="viewEmergencyContactPhone"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Emergency Contact Address</label>
                                        <div class="box" id="viewEmergencyContactAddress">
                                            <!-- Emergency contact address content -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Guardian/Parent Information Section -->
                        <div class="mb-5" id="guardianSection">
                            <h4 class="subtitle is-5">
                                <span class="icon">
                                    <i class="fas fa-user-shield"></i>
                                </span>
                                Guardian/Parent Information
                            </h4>
                            <div class="columns">
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Guardian/Parent Name</label>
                                        <div class="content">
                                            <p id="viewGuardianName"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Guardian Relationship</label>
                                        <div class="content">
                                            <span class="tag is-medium" id="viewGuardianRelationship"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="columns">
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Guardian Phone</label>
                                        <div class="content">
                                            <p id="viewGuardianPhone"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Guardian Address</label>
                                        <div class="box" id="viewGuardianAddress">
                                            <!-- Guardian address content -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="column is-3">
                    <!-- Patient Summary Card -->
                    <div class="card">
                        <div class="card-header">
                            <p class="card-header-title">
                                <span class="icon">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                                Summary
                            </p>
                        </div>
                        <div class="card-content">
                            <div class="content">
                                <div class="field">
                                    <label class="label is-small">Last Updated</label>
                                    <p id="viewLastUpdated"></p>
                                </div>
                                <hr>
                                <div class="field">
                                    <label class="label is-small">Current Status</label>
                                    <div class="content">
                                        <span class="tag is-medium" id="viewStatusSummary"></span>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label is-small">Age Group</label>
                                    <p id="viewAgeGroup"></p>
                                </div>
                                <div class="field">
                                    <label class="label is-small">Gender</label>
                                    <div class="content">
                                        <span class="tag is-medium" id="viewGenderSummary"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button" id="closeViewPatient">
                <span class="icon">
                    <i class="fas fa-times"></i>
                </span>
                <span>Close</span>
            </button>
        </footer>
    </div>
</div>

<!-- Hidden Print Area for Patient Info -->
<div id="patientPrintArea" style="display: none;">
    <!-- Patient print content will be generated here -->
</div>

<!-- JavaScript -->
<script src="js/patients.js"></script>

    </div> <!-- End of container from header.php -->
</body>
</html> 