<?php
require_once 'includes/header.php';
?>

<style>
/* Compact styles for smaller medical record cards */
.medical-records-grid .column {
    margin-bottom: 1rem;
    padding: 0.5rem;
}

.medical-records-grid .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 6px;
}

.medical-records-grid .card:hover {
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

/* Medical record details text handling */
.medical-record-details {
    font-size: 0.75rem;
    color: #6c757d;
    line-height: 1.2em;
}

/* Status badge styling */
.status-badge {
    text-transform: capitalize;
}

/* Mobile responsiveness for smaller cards */
@media (max-width: 768px) {
    .medical-records-grid .column {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (min-width: 769px) and (max-width: 1023px) {
    .medical-records-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1024px) and (max-width: 1215px) {
    .medical-records-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1216px) {
    .medical-records-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

/* View Modal Styling */
#viewMedicalRecordModal .modal-card {
    width: 90vw;
    max-width: 1200px;
    max-height: 90vh;
    margin: auto;
}

#viewMedicalRecordModal .modal-card-body {
    max-height: 70vh;
    overflow-y: auto;
    padding: 1.5rem;
}

#viewMedicalRecordModal .field {
    margin-bottom: 1rem;
}

#viewMedicalRecordModal .label {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #363636;
}

#viewMedicalRecordModal .box {
    padding: 1rem;
    margin-bottom: 0;
    min-height: 2.5rem;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

#viewMedicalRecordModal .tag.is-large {
    font-size: 1rem;
    padding: 0.5rem 0.75rem;
    height: auto;
    white-space: nowrap;
}

#viewMedicalRecordModal .subtitle {
    margin-bottom: 1rem !important;
    border-bottom: 1px solid #dbdbdb;
    padding-bottom: 0.5rem;
}

#viewMedicalRecordModal .column {
    padding: 0.5rem;
}

#viewMedicalRecordModal p {
    word-wrap: break-word;
    overflow-wrap: break-word;
    line-height: 1.5;
}

/* Responsive adjustments for view modal */
@media (max-width: 768px) {
    #viewMedicalRecordModal .modal-card {
        width: 95vw;
        margin: 1rem auto;
    }
    
    #viewMedicalRecordModal .columns {
        display: block;
    }
    
    #viewMedicalRecordModal .column {
        width: 100% !important;
        flex: none !important;
    }
    
    #viewMedicalRecordModal .card {
        margin-top: 1rem;
    }
}

/* Print Styles - Optimized for Bond Paper */
@media print {
    body * {
        visibility: hidden;
    }
    
    #medicalRecordPrintArea,
    #medicalRecordPrintArea * {
        visibility: visible;
    }
    
    #medicalRecordPrintArea {
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
    
    .print-medical-records {
        margin-bottom: 8px;
        page-break-inside: avoid;
    }
    
    .print-medical-records h3 {
        margin: 0 0 5px 0 !important;
        font-size: 13px;
        background-color: #f0f0f0;
        padding: 4px 6px;
        border: 1px solid #000;
        page-break-after: avoid;
        font-weight: bold;
    }
    
    .print-info-item {
        padding: 2px 4px;
        border-bottom: 1px solid #eee;
        line-height: 1.2;
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
    
    .print-footer-content {
        margin-bottom: 2px;
    }
    
    /* Optimize for bond paper */
    @page {
        margin: 0.4in;
        size: letter;
    }
    
    /* Prevent page breaks within sections */
    .print-medical-records,
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
                    <i class="fas fa-notes-medical"></i>
                </span>
                Medical Records Management
            </h1>
            <p class="subtitle">Manage patient medical records and clinical documentation</p>
        </div>
        <div class="column is-narrow">
            <button class="button is-primary" id="addMedicalRecordBtn">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>Add New Record</span>
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-content">
            <div class="columns">
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Search Records</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="searchInput" placeholder="Search by patient name, diagnosis..." />
                            <span class="icon is-small is-left">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label class="label">Filter by Patient</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="patientFilter">
                                    <option value="">All Patients</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label class="label">Filter by Doctor</label>
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
                        <label class="label">Filter by Status</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="archived">Archived</option>
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
                                    <option value="record_date">Record Date</option>
                                    <option value="created_at">Date Added</option>
                                    <option value="patient_name">Patient Name</option>
                                    <option value="doctor_name">Doctor Name</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns mt-3">
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

    <!-- Medical Records Grid -->
    <div id="medicalRecordsContainer">
        <div class="columns is-multiline medical-records-grid" id="medicalRecordsGrid">
            <!-- Medical records will be loaded here -->
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="has-text-centered mt-6 mb-6" id="loadingSpinner" style="display: none;">
        <span class="icon is-large">
            <i class="fas fa-spinner fa-pulse fa-2x"></i>
        </span>
        <p class="mt-2">Loading medical records...</p>
    </div>

    <!-- Empty State -->
    <div class="has-text-centered mt-6 mb-6" id="emptyState" style="display: none;">
        <span class="icon is-large has-text-grey-light">
            <i class="fas fa-notes-medical fa-3x"></i>
        </span>
        <p class="title is-5 has-text-grey mt-3">No medical records found</p>
        <p class="subtitle is-6 has-text-grey">Try adjusting your search criteria or add a new medical record</p>
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

<!-- Add/Edit Medical Record Modal -->
<div class="modal" id="medicalRecordModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title" id="modalTitle">Add New Medical Record</p>
            <button class="delete" aria-label="close" id="closeModal"></button>
        </header>
        <section class="modal-card-body">
            <form id="medicalRecordForm">
                <input type="hidden" id="recordId" />
                
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
                            <label class="label">Record Date *</label>
                            <div class="control">
                                <input class="input" type="datetime-local" id="recordDate" required />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Status *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="status" required>
                                        <option value="active">Active</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vital Signs Section -->
                <div class="field mt-5">
                    <h5 class="subtitle is-6">
                        <span class="icon">
                            <i class="fas fa-heartbeat"></i>
                        </span>
                        Vital Signs
                    </h5>
                </div>

                <div class="columns">
                    <div class="column is-3">
                        <div class="field">
                            <label class="label">Height (cm)</label>
                            <div class="control">
                                <input class="input" type="number" id="heightCm" step="0.01" min="0" max="300" placeholder="Height in cm" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label class="label">Weight (kg)</label>
                            <div class="control">
                                <input class="input" type="number" id="weightKg" step="0.01" min="0" max="500" placeholder="Weight in kg" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label class="label">BMI</label>
                            <div class="control">
                                <input class="input" type="number" id="bmi" step="0.01" min="0" max="100" placeholder="BMI" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label class="label">Blood Pressure</label>
                            <div class="control">
                                <input class="input" type="text" id="bloodPressure" placeholder="e.g., 120/80" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Heart Rate (bpm)</label>
                            <div class="control">
                                <input class="input" type="number" id="heartRate" min="0" max="300" placeholder="Heart rate" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Temperature (°C)</label>
                            <div class="control">
                                <input class="input" type="number" id="temperatureC" step="0.1" min="30" max="45" placeholder="Temperature" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Respiratory Rate</label>
                            <div class="control">
                                <input class="input" type="number" id="respiratoryRate" min="0" max="100" placeholder="Breaths per minute" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SOAP Notes Section -->
                <div class="field mt-5">
                    <h5 class="subtitle is-6">
                        <span class="icon">
                            <i class="fas fa-sticky-note"></i>
                        </span>
                        SOAP Notes
                    </h5>
                </div>

                <div class="field">
                    <label class="label">Subjective</label>
                    <div class="control">
                        <textarea class="textarea" id="subjective" placeholder="Patient's symptoms and complaints" rows="3"></textarea>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Objective</label>
                    <div class="control">
                        <textarea class="textarea" id="objective" placeholder="Physical examination findings, lab results, etc." rows="3"></textarea>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Assessment</label>
                    <div class="control">
                        <textarea class="textarea" id="assessment" placeholder="Diagnosis and clinical impression" rows="3"></textarea>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Plan</label>
                    <div class="control">
                        <textarea class="textarea" id="plan" placeholder="Treatment plan, medications, follow-up" rows="3"></textarea>
                    </div>
                </div>

                <!-- Diagnosis and Treatment Section -->
                <div class="field mt-5">
                    <h5 class="subtitle is-6">
                        <span class="icon">
                            <i class="fas fa-diagnoses"></i>
                        </span>
                        Diagnosis & Treatment
                    </h5>
                </div>

                <div class="field">
                    <label class="label">Diagnosis</label>
                    <div class="control">
                        <textarea class="textarea" id="diagnosis" placeholder="Primary and secondary diagnoses" rows="2"></textarea>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Treatment</label>
                    <div class="control">
                        <textarea class="textarea" id="treatment" placeholder="Treatment provided, medications prescribed, procedures performed" rows="3"></textarea>
                    </div>
                </div>

                <!-- Lab Images Section -->
                <div class="field mt-5">
                    <h5 class="subtitle is-6">
                        <span class="icon">
                            <i class="fas fa-images"></i>
                        </span>
                        Lab Images & Results
                    </h5>
                </div>

                <div class="field">
                    <label class="label">Upload Lab Images</label>
                    <div class="control">
                        <div class="file has-name is-fullwidth" id="labImagesContainer">
                            <label class="file-label">
                                <input class="file-input" type="file" id="labImages" multiple accept="image/*,.pdf" />
                                <span class="file-cta">
                                    <span class="file-icon">
                                        <i class="fas fa-upload"></i>
                                    </span>
                                    <span class="file-label">
                                        Choose files...
                                    </span>
                                </span>
                                <span class="file-name" id="labImagesFileName">
                                    No files selected
                                </span>
                            </label>
                        </div>
                        <p class="help">Supported formats: JPG, PNG, GIF, PDF. Maximum 5 files, 5MB each.</p>
                    </div>
                </div>

                <div class="field" id="uploadedImagesContainer" style="display: none;">
                    <label class="label">Uploaded Images</label>
                    <div class="control">
                        <div class="columns is-multiline" id="uploadedImagesGrid">
                            <!-- Uploaded images will be displayed here -->
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="saveMedicalRecord">Save Record</button>
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
                <p>Are you sure you want to delete this medical record?</p>
                <p><strong id="deleteRecordInfo"></strong></p>
                <p class="has-text-danger">This action cannot be undone and will permanently remove this medical record.</p>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-danger" id="confirmDelete">Delete Record</button>
            <button class="button" id="cancelDelete">Cancel</button>
        </footer>
    </div>
</div>

<!-- View Medical Record Details Modal -->
<div class="modal" id="viewMedicalRecordModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">
                <span class="icon">
                    <i class="fas fa-notes-medical"></i>
                </span>
                Medical Record Details
            </p>
            <button class="delete" aria-label="close" id="closeViewModal"></button>
        </header>
        <section class="modal-card-body">
            <div class="columns">
                <div class="column is-9">
                    <!-- Medical Record Information -->
                    <div class="content">
                        <!-- Record Header with Key Info -->
                        <div class="box mb-4">
                            <div class="level">
                                <div class="level-left">
                                    <div class="level-item">
                                        <div>
                                            <p class="title is-4" id="viewPatientName"></p>
                                            <p class="subtitle is-6">
                                                <span class="icon">
                                                    <i class="fas fa-user-md"></i>
                                                </span>
                                                Doctor: <strong id="viewDoctorName"></strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="level-right">
                                    <div class="level-item">
                                        <div class="tags has-addons">
                                            <span class="tag is-large" id="viewRecordDate"></span>
                                            <span class="tag is-large" id="viewStatus"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vital Signs Section -->
                        <div class="mb-5">
                            <h4 class="subtitle is-5">
                                <span class="icon">
                                    <i class="fas fa-heartbeat"></i>
                                </span>
                                Vital Signs
                            </h4>
                            <div class="columns">
                                <div class="column is-3">
                                    <div class="field">
                                        <label class="label">Height</label>
                                        <div class="content">
                                            <p id="viewHeight"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-3">
                                    <div class="field">
                                        <label class="label">Weight</label>
                                        <div class="content">
                                            <p id="viewWeight"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-3">
                                    <div class="field">
                                        <label class="label">BMI</label>
                                        <div class="content">
                                            <p id="viewBmi"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-3">
                                    <div class="field">
                                        <label class="label">Blood Pressure</label>
                                        <div class="content">
                                            <p id="viewBloodPressure"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="columns">
                                <div class="column is-4">
                                    <div class="field">
                                        <label class="label">Heart Rate</label>
                                        <div class="content">
                                            <p id="viewHeartRate"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-4">
                                    <div class="field">
                                        <label class="label">Temperature</label>
                                        <div class="content">
                                            <p id="viewTemperature"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-4">
                                    <div class="field">
                                        <label class="label">Respiratory Rate</label>
                                        <div class="content">
                                            <p id="viewRespiratoryRate"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SOAP Notes Section -->
                        <div class="mb-5">
                            <h4 class="subtitle is-5">
                                <span class="icon">
                                    <i class="fas fa-sticky-note"></i>
                                </span>
                                SOAP Notes
                            </h4>
                            <div class="field">
                                <label class="label">Subjective</label>
                                <div class="box" id="viewSubjective">
                                    <!-- Subjective content -->
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Objective</label>
                                <div class="box" id="viewObjective">
                                    <!-- Objective content -->
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Assessment</label>
                                <div class="box" id="viewAssessment">
                                    <!-- Assessment content -->
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Plan</label>
                                <div class="box" id="viewPlan">
                                    <!-- Plan content -->
                                </div>
                            </div>
                        </div>

                        <!-- Diagnosis and Treatment Section -->
                        <div class="mb-5">
                            <h4 class="subtitle is-5">
                                <span class="icon">
                                    <i class="fas fa-diagnoses"></i>
                                </span>
                                Diagnosis & Treatment
                            </h4>
                            <div class="field">
                                <label class="label">Diagnosis</label>
                                <div class="box" id="viewDiagnosis">
                                    <!-- Diagnosis content -->
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Treatment</label>
                                <div class="box" id="viewTreatment">
                                    <!-- Treatment content -->
                                </div>
                            </div>
                        </div>

                        <!-- Lab Images Section -->
                        <div class="mb-5" id="viewLabImagesSection" style="display: none;">
                            <h4 class="subtitle is-5">
                                <span class="icon">
                                    <i class="fas fa-images"></i>
                                </span>
                                Lab Images & Results
                            </h4>
                            <div class="field">
                                <div class="columns is-multiline" id="viewLabImagesGrid">
                                    <!-- Lab images will be displayed here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="column is-3">
                    <!-- Medical Record Summary Card -->
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
                                    <label class="label is-small">Record ID</label>
                                    <p id="viewRecordId"></p>
                                </div>
                                <hr>
                                <div class="field">
                                    <label class="label is-small">Record Date</label>
                                    <p id="viewRecordDateSummary"></p>
                                </div>
                                <div class="field">
                                    <label class="label is-small">Created</label>
                                    <p id="viewCreatedDate"></p>
                                </div>
                                <div class="field">
                                    <label class="label is-small">Last Updated</label>
                                    <p id="viewLastUpdated"></p>
                                </div>
                                <div class="field">
                                    <label class="label is-small">Status</label>
                                    <div class="content">
                                        <span class="tag is-medium" id="viewStatusSummary"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button" id="closeViewMedicalRecord">
                <span class="icon">
                    <i class="fas fa-times"></i>
                </span>
                <span>Close</span>
            </button>
        </footer>
    </div>
</div>

<!-- Hidden Print Area for Medical Record Info -->
<div id="medicalRecordPrintArea" style="display: none;">
    <!-- Medical record print content will be generated here -->
</div>

<!-- JavaScript -->
<script src="js/medical_records_new.js?v=<?php echo time(); ?>&cache=<?php echo uniqid(); ?>"></script>

    </div> <!-- End of container from header.php --> 