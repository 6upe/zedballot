@extends('layouts.app')

@section('title', 'Create Poll')

@push('styles')
<style>
    .step-card {
        display: none;
        transition: all 0.3s ease;
    }
    .step-card.active {
        display: block;
    }
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }
    .step-item {
        flex: 1;
        text-align: center;
        position: relative;
    }
    .step-item::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #e0e0e0;
        z-index: -1;
    }
    .step-item:last-child::after {
        display: none;
    }
    .step-item.completed::after {
        background: #28a745;
    }
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-weight: bold;
    }
    .step-item.active .step-number {
        background: #007bff;
        color: white;
    }
    .step-item.completed .step-number {
        background: #28a745;
        color: white;
    }
    .media-preview {
        max-width: 100%;
        max-height: 200px;
        margin-top: 10px;
        border-radius: 8px;
    }
    .category-item, .nominee-item, .voter-item {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: #f9f9f9;
    }
    .registration-link-box {
        background: #f8f9fa;
        border: 1px dashed #007bff;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }
</style>
@endpush

@section('content')
<div class="section-header">
    <h1>Create Poll</h1>
</div>
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step-item active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Poll Details</div>
                </div>
                <div class="step-item" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Categories & Nominees</div>
                </div>
                <div class="step-item" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Voting & Eligibility</div>
                </div>
                <div class="step-item" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label">Save or Publish</div>
                </div>
            </div>

            <!-- Step 1: Poll Details -->
            <div class="card shadow-sm step-card active" id="step1">
                <div class="card-header">
                    <h5 class="mb-0">Step 1: Poll Details</h5>
                </div>
                <div class="card-body">
                    <form id="step1Form">
                        <div class="form-group">
                            <label for="name">Poll Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="start_at">Start Date & Time</label>
                                <input type="datetime-local" id="start_at" name="start_at" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_at">End Date & Time</label>
                                <input type="datetime-local" id="end_at" name="end_at" class="form-control">
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">Media Uploads</h6>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="cover_image">Cover Image</label>
                                <input type="file" id="cover_image" name="cover_image" class="form-control-file" accept="image/*">
                                <img id="cover_preview" class="media-preview" style="display: none;">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="banner_image">Banner Image</label>
                                <input type="file" id="banner_image" name="banner_image" class="form-control-file" accept="image/*">
                                <img id="banner_preview" class="media-preview" style="display: none;">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="video">Intro Video</label>
                                <input type="file" id="video" name="video" class="form-control-file" accept="video/*">
                                <video id="video_preview" class="media-preview" controls style="display: none;"></video>
                            </div>
                        </div>

                        <div class="text-right mt-4">
                            <button type="button" id="saveStep1Btn" class="btn btn-primary" onclick="saveStep1(event)">
                                <i class="fas fa-save mr-1"></i> Save & Continue
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Step 2: Categories & Nominees -->
            <div class="card shadow-sm step-card" id="step2">
                <div class="card-header">
                    <h5 class="mb-0">Step 2: Categories & Nominees</h5>
                </div>
                <div class="card-body">
                    <!-- Categories Section -->
                    <h6 class="mb-3">Categories</h6>
                    <div id="categoriesList"></div>
                    
                    <!-- Add Category Form (hidden by default) -->
                    <div id="addCategoryForm" style="display: none;" class="mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6>Add Category</h6>
                                <form id="categoryForm">
                                    <div class="form-group">
                                        <label for="category_name">Category Name <span class="text-danger">*</span></label>
                                        <input type="text" id="category_name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="category_description">Description</label>
                                        <textarea id="category_description" class="form-control" rows="2"></textarea>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="saveCategory()">Add Category</button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="cancelCategoryForm()">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-sm btn-outline-primary mb-4" onclick="showAddCategoryForm()">
                        <i class="fas fa-plus mr-1"></i> Add Category
                    </button>

                    <hr>

                    <!-- Nominees Section -->
                    <h6 class="mb-3">Nominees</h6>
                            <div class="mb-3">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="showManualNomineeForm()">
                                        <i class="fas fa-user-plus mr-1"></i> Manual Entry
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="showCSVImport()">
                                        <i class="fas fa-file-csv mr-1"></i> Import CSV
                                    </button>
                                    <a href="{{ route('polls.nominees.csv-template') }}" class="btn btn-sm btn-outline-info" download>
                                        <i class="fas fa-download mr-1"></i> CSV Template
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateNomineeLink()">
                                        <i class="fas fa-link mr-1"></i> Self-Registration Link
                                    </button>
                                </div>
                            </div>

                    <div id="nomineesList"></div>

                    <!-- Manual Nominee Form (hidden by default) -->
                    <div id="manualNomineeForm" style="display: none;" class="mt-3">
                        <div class="card">
                            <div class="card-body">
                                <h6>Add Nominee</h6>
                                <form id="nomineeForm">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select id="nominee_category_id" class="form-control" required>
                                            <option value="">Select Category</option>
                                        </select>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>Name <span class="text-danger">*</span></label>
                                            <input type="text" id="nominee_name" class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Email</label>
                                            <input type="email" id="nominee_email" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>Phone</label>
                                            <input type="text" id="nominee_phone" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Social Link</label>
                                            <input type="url" id="nominee_social_link" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Bio</label>
                                        <textarea id="nominee_bio" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Photo</label>
                                        <input type="file" id="nominee_photo" class="form-control-file" accept="image/*">
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="saveNominee()">Add Nominee</button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="cancelNomineeForm()">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- CSV Import Form (hidden by default) -->
                    <div id="csvImportForm" style="display: none;" class="mt-3">
                        <div class="card">
                            <div class="card-body">
                                <h6>Import Nominees from CSV</h6>
                                <p class="text-muted small">CSV format: name, email, phone, social_link, bio</p>
                                <form id="csvNomineeForm">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select id="csv_category_id" class="form-control" required>
                                            <option value="">Select Category</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>CSV File</label>
                                        <input type="file" id="csv_file" class="form-control-file" accept=".csv,.txt" required>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="importNomineesCSV()">Import</button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="cancelCSVForm()">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <button type="button" class="btn btn-light mr-2" onclick="previousStep()">Previous</button>
                        <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Voting Methods & Eligibility -->
            <div class="card shadow-sm step-card" id="step3">
                <div class="card-header">
                    <h5 class="mb-0">Step 3: Voting Methods & Eligibility</h5>
                </div>
                <div class="card-body">
                    <form id="step3Form">
                        <h6 class="mb-3">Voting Methods <span class="text-danger">*</span></h6>
                        <div class="form-group" id="votingMethodsGroup">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input voting-method-checkbox" id="vm_email" name="voting_methods[]" value="email">
                                <label class="custom-control-label" for="vm_email">Email</label>
                            </div>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input voting-method-checkbox" id="vm_phone" name="voting_methods[]" value="phone">
                                <label class="custom-control-label" for="vm_phone">Phone</label>
                            </div>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input voting-method-checkbox" id="vm_nrc" name="voting_methods[]" value="nrc">
                                <label class="custom-control-label" for="vm_nrc">NRC/ID</label>
                            </div>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input voting-method-checkbox" id="vm_passport" name="voting_methods[]" value="passport">
                                <label class="custom-control-label" for="vm_passport">Passport</label>
                            </div>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input voting-method-checkbox" id="vm_biometric_facial" name="voting_methods[]" value="biometric_facial">
                                <label class="custom-control-label" for="vm_biometric_facial">Biometric (Facial)</label>
                            </div>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input voting-method-checkbox" id="vm_biometric_finger" name="voting_methods[]" value="biometric_finger">
                                <label class="custom-control-label" for="vm_biometric_finger">Biometric (Fingerprint)</label>
                            </div>
                            <small class="form-text text-muted">Select at least one voting method</small>
                            <div id="votingMethodsError" class="invalid-feedback" style="display: none;">Please select at least one voting method</div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="allow_vote_edit" name="allow_vote_edit">
                                <label class="custom-control-label" for="allow_vote_edit">Allow voters to edit votes before poll ends</label>
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">Eligibility</h6>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_public" name="is_public" checked onchange="toggleEligibilityOptions()">
                                <label class="custom-control-label" for="is_public">Public poll (anyone can vote)</label>
                            </div>
                        </div>

                        <div id="eligibilityOptions" style="display: none;">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="email_domain">Email Domain</label>
                                    <input type="text" id="email_domain" name="email_domain" class="form-control" placeholder="e.g. company.com">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="country">Country</label>
                                    <input type="text" id="country" name="country" class="form-control" placeholder="e.g. Zambia">
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3">Eligible Voters Management</h6>
                            <div class="mb-3">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="showManualVoterForm()">
                                        <i class="fas fa-user-plus mr-1"></i> Manual Entry
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="showVoterCSVImport()">
                                        <i class="fas fa-file-csv mr-1"></i> Import CSV
                                    </button>
                                    <a href="{{ route('polls.eligible-voters.csv-template') }}" class="btn btn-sm btn-outline-info" download>
                                        <i class="fas fa-download mr-1"></i> CSV Template
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateVoterLink()">
                                        <i class="fas fa-link mr-1"></i> Self-Registration Link
                                    </button>
                                </div>
                            </div>

                            <div id="eligibleVotersList"></div>

                            <!-- Manual Voter Form -->
                            <div id="manualVoterForm" style="display: none;" class="mt-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Add Eligible Voter</h6>
                                        <form id="voterForm">
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Email</label>
                                                    <input type="email" id="voter_email" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Phone</label>
                                                    <input type="text" id="voter_phone" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Name</label>
                                                    <input type="text" id="voter_name" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Identifier Type</label>
                                                    <select id="voter_identifier_type" class="form-control">
                                                        <option value="">Select</option>
                                                        <option value="email">Email</option>
                                                        <option value="phone">Phone</option>
                                                        <option value="nrc">NRC</option>
                                                        <option value="passport">Passport</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Identifier Value</label>
                                                    <input type="text" id="voter_identifier_value" class="form-control">
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="saveVoter()">Add Voter</button>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="cancelVoterForm()">Cancel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Voter CSV Import Form -->
                            <div id="voterCSVImportForm" style="display: none;" class="mt-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Import Eligible Voters from CSV</h6>
                                        <p class="text-muted small">
                                            CSV format: email, phone, name, identifier_type, identifier_value<br>
                                            <a href="{{ route('polls.eligible-voters.csv-template') }}" class="text-primary" download>
                                                <i class="fas fa-download mr-1"></i>Download CSV Template
                                            </a>
                                        </p>
                                        <form id="csvVoterForm">
                                            <div class="form-group">
                                                <label>CSV File</label>
                                                <input type="file" id="csv_voter_file" class="form-control-file" accept=".csv,.txt" required>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="importVotersCSV()">Import</button>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="cancelVoterCSVForm()">Cancel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-right mt-4">
                            <button type="button" class="btn btn-light mr-2" onclick="previousStep()">Previous</button>
                            <button type="button" class="btn btn-primary" onclick="saveStep3()">Save & Continue</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Step 4: Save or Publish -->
            <div class="card shadow-sm step-card" id="step4">
                <div class="card-header">
                    <h5 class="mb-0">Step 4: Save or Publish</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6>Review Your Poll</h6>
                        <p class="mb-0">Review all the information before publishing. You can save as draft and come back later to make changes.</p>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="notify_nominees" name="notify_nominees">
                            <label class="custom-control-label" for="notify_nominees">Send email notification to nominees when published</label>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <button type="button" class="btn btn-light mr-2" onclick="previousStep()">Previous</button>
                        <button type="button" class="btn btn-secondary mr-2" onclick="saveAsDraft()">
                            <i class="fas fa-save mr-1"></i> Save as Draft
                        </button>
                        <button type="button" class="btn btn-success" onclick="publishPoll()">
                            <i class="fas fa-rocket mr-1"></i> Publish Poll
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPoll = null;
let currentStep = 1;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Media preview handlers
    document.getElementById('cover_image').addEventListener('change', function(e) {
        previewImage(e.target, 'cover_preview');
    });
    document.getElementById('banner_image').addEventListener('change', function(e) {
        previewImage(e.target, 'banner_preview');
    });
    document.getElementById('video').addEventListener('change', function(e) {
        previewVideo(e.target, 'video_preview');
    });
    
    // Load existing poll data if editing
    @if(isset($poll))
        currentPoll = @json($poll);
        loadPollData();
    @endif
});

function loadPollData() {
    if (!currentPoll) return;
    
    // Load step 1 data
    document.getElementById('name').value = currentPoll.name || '';
    document.getElementById('description').value = currentPoll.description || '';
    if (currentPoll.start_at) {
        const startDate = new Date(currentPoll.start_at);
        document.getElementById('start_at').value = startDate.toISOString().slice(0, 16);
    }
    if (currentPoll.end_at) {
        const endDate = new Date(currentPoll.end_at);
        document.getElementById('end_at').value = endDate.toISOString().slice(0, 16);
    }
    
    // Load step 3 data
    if (currentPoll.voting_methods) {
        const methods = currentPoll.voting_methods.split(',');
        methods.forEach(method => {
            const methodTrimmed = method.trim();
            const checkbox = document.getElementById('vm_' + methodTrimmed);
            if (checkbox) {
                checkbox.checked = true;
            } else {
                console.warn('Checkbox not found for method:', methodTrimmed);
            }
        });
    }
    document.getElementById('is_public').checked = currentPoll.is_public !== false;
    document.getElementById('email_domain').value = currentPoll.email_domain || '';
    document.getElementById('country').value = currentPoll.country || '';
    document.getElementById('allow_vote_edit').checked = currentPoll.allow_vote_edit || false;
    toggleEligibilityOptions();
    
    // Determine which step to show based on poll completion
    if (currentPoll.status === 'draft') {
        // Show appropriate step based on what's filled
        if (currentPoll.voting_methods) {
            showStep(3);
            loadEligibleVoters();
        } else if (currentPoll.categories && currentPoll.categories.length > 0) {
            showStep(2);
            loadStep2Data();
        } else {
            showStep(1);
        }
    }
}

function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewVideo(input, previewId) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const preview = document.getElementById(previewId);
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
}

// Step Navigation
function showStep(step) {
    document.querySelectorAll('.step-card').forEach(card => card.classList.remove('active'));
    document.querySelectorAll('.step-item').forEach(item => item.classList.remove('active'));
    
    document.getElementById('step' + step).classList.add('active');
    document.querySelector(`.step-item[data-step="${step}"]`).classList.add('active');
    
    // Mark previous steps as completed
    for (let i = 1; i < step; i++) {
        document.querySelector(`.step-item[data-step="${i}"]`).classList.add('completed');
    }
    
    currentStep = step;
    
    // Load data when showing specific steps
    if (step === 2 && currentPoll) {
        loadStep2Data();
    } else if (step === 3 && currentPoll) {
        loadEligibleVoters();
    }
}

function nextStep() {
    if (currentStep < 4) {
        showStep(currentStep + 1);
    }
}

function previousStep() {
    if (currentStep > 1) {
        showStep(currentStep - 1);
    }
}

// Step 1: Save Poll Details
function saveStep1() {
    const form = document.getElementById('step1Form');
    const formData = new FormData(form);
    
    // Remove empty file inputs to avoid validation issues
    const coverImage = document.getElementById('cover_image');
    const bannerImage = document.getElementById('banner_image');
    const video = document.getElementById('video');
    
    // Only include files if they actually have a file selected
    if (coverImage && coverImage.files.length === 0) {
        formData.delete('cover_image');
    }
    if (bannerImage && bannerImage.files.length === 0) {
        formData.delete('banner_image');
    }
    if (video && video.files.length === 0) {
        formData.delete('video');
    }
    
    // Validate required fields
    const name = document.getElementById('name').value.trim();
    if (!name) {
        showNotification('Poll name is required', 'error');
        document.getElementById('name').focus();
        return;
    }
    
    // Validate dates if provided
    const startAt = document.getElementById('start_at').value;
    const endAt = document.getElementById('end_at').value;
    if (startAt && endAt) {
        if (new Date(startAt) >= new Date(endAt)) {
            showNotification('End date must be after start date', 'error');
            return;
        }
    }
    
    const url = currentPoll 
        ? `/polls/${currentPoll.uuid}/step1`
        : '/polls';
    const method = currentPoll ? 'PUT' : 'POST';
    
    // Show loading state
    const saveBtn = document.getElementById('saveStep1Btn');
    const originalText = saveBtn ? saveBtn.innerHTML : '';
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';
    }
    
    // Debug: Log what's being sent
    console.log('Sending Step 1 data:', {
        url: url,
        method: method,
        hasCoverImage: coverImage && coverImage.files.length > 0,
        hasBannerImage: bannerImage && bannerImage.files.length > 0,
        hasVideo: video && video.files.length > 0,
    });
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            // Don't set Content-Type for FormData - browser will set it with boundary
        },
        body: formData
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // If not JSON, might be validation error
            return response.text().then(text => {
                console.error('Non-JSON response:', text);
                throw new Error('Server returned non-JSON response');
            });
        }
    })
    .then(data => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
        
        if (data.success) {
            currentPoll = data.poll;
            showNotification('Poll details saved successfully!', 'success');
            nextStep();
        } else {
            // Handle validation errors
            let errorMsg = data.message || 'Error saving poll details';
            if (data.errors) {
                const errorList = Object.values(data.errors).flat().join(', ');
                errorMsg = errorList || errorMsg;
            }
            showNotification(errorMsg, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
        
        let errorMsg = 'Error saving poll details';
        if (error.message) {
            errorMsg += ': ' + error.message;
        }
        showNotification(errorMsg, 'error');
    });
}

// Step 2: Categories & Nominees
function loadStep2Data() {
    if (!currentPoll) return;
    
    fetch(`/polls/${currentPoll.uuid}/step2-data`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Step 2 data loaded:', data); // Debug
        if (data.success) {
            renderCategories(data.poll.categories || []);
            // Get all nominees from poll, not just from categories
            const allNominees = data.poll.nominees || [];
            console.log('All nominees:', allNominees); // Debug
            renderNominees(allNominees);
            populateCategorySelects();
        }
    })
    .catch(error => {
        console.error('Error loading step 2 data:', error);
    });
}

function renderCategories(categories) {
    const container = document.getElementById('categoriesList');
    if (categories.length === 0) {
        container.innerHTML = '<p class="text-muted">No categories yet. Add your first category!</p>';
        return;
    }
    
    container.innerHTML = categories.map(cat => `
        <div class="category-item" data-id="${cat.id}">
            <h6>${cat.name}</h6>
            <p class="text-muted small">${cat.description || 'No description'}</p>
            <button class="btn btn-sm btn-danger" onclick="deleteCategory(${cat.id})">Delete</button>
        </div>
    `).join('');
}

function renderNominees(nominees) {
    const container = document.getElementById('nomineesList');
    
    // Clear any existing registration link boxes
    const existingLinks = container.querySelectorAll('.registration-link-box');
    existingLinks.forEach(link => link.remove());
    
    console.log('Rendering nominees:', nominees); // Debug
    
    if (!nominees || nominees.length === 0) {
        container.innerHTML = '<p class="text-muted">No nominees yet.</p>';
        return;
    }
    
    container.innerHTML = nominees.map(nom => {
        const categoryName = nom.category ? nom.category.name : (nom.category_id ? 'Category ID: ' + nom.category_id : 'No category');
        return `
        <div class="nominee-item" data-id="${nom.id}">
            <div class="d-flex justify-content-between">
                <div>
                    <h6>${nom.name || 'Unnamed'}</h6>
                    <p class="text-muted small mb-1">
                        ${nom.email ? 'Email: ' + nom.email + '<br>' : ''}
                        ${nom.phone ? 'Phone: ' + nom.phone + '<br>' : ''}
                        Category: ${categoryName}
                    </p>
                    <span class="badge badge-${nom.status === 'approved' ? 'success' : 'warning'}">${nom.status || 'pending'}</span>
                </div>
                <div>
                    ${nom.status !== 'approved' ? `<button class="btn btn-sm btn-primary" onclick="approveNominee(${nom.id})">Approve</button>` : ''}
                    <button class="btn btn-sm btn-danger" onclick="deleteNominee(${nom.id})">Delete</button>
                </div>
            </div>
        </div>
    `;
    }).join('');
}

function showAddCategoryForm() {
    document.getElementById('addCategoryForm').style.display = 'block';
}

function cancelCategoryForm() {
    document.getElementById('addCategoryForm').style.display = 'none';
    document.getElementById('categoryForm').reset();
}

function saveCategory() {
    const name = document.getElementById('category_name').value.trim();
    if (!name) {
        showNotification('Category name is required', 'error');
        return;
    }
    
    const description = document.getElementById('category_description').value.trim();
    
    fetch(`/polls/${currentPoll.uuid}/categories`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name, description })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadStep2Data();
            cancelCategoryForm();
            showNotification('Category added!', 'success');
        } else {
            showNotification(data.message || 'Error adding category', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding category', 'error');
    });
}

function deleteCategory(id) {
    if (!confirm('Delete this category?')) return;
    
    fetch(`/polls/categories/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadStep2Data();
            showNotification('Category deleted!', 'success');
        }
    });
}

function populateCategorySelects() {
    fetch(`/polls/${currentPoll.uuid}/step2`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        const categories = data.poll.categories || [];
        const selects = ['nominee_category_id', 'csv_category_id'];
        selects.forEach(selectId => {
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">Select Category</option>' + 
                categories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
        });
    });
}

function showManualNomineeForm() {
    document.getElementById('manualNomineeForm').style.display = 'block';
    document.getElementById('csvImportForm').style.display = 'none';
    populateCategorySelects();
}

function showCSVImport() {
    document.getElementById('csvImportForm').style.display = 'block';
    document.getElementById('manualNomineeForm').style.display = 'none';
    populateCategorySelects();
}

function cancelNomineeForm() {
    document.getElementById('manualNomineeForm').style.display = 'none';
    document.getElementById('nomineeForm').reset();
}

function cancelCSVForm() {
    document.getElementById('csvImportForm').style.display = 'none';
    document.getElementById('csvNomineeForm').reset();
}

function saveNominee() {
    const formData = new FormData();
    formData.append('category_id', document.getElementById('nominee_category_id').value);
    formData.append('name', document.getElementById('nominee_name').value);
    formData.append('email', document.getElementById('nominee_email').value);
    formData.append('phone', document.getElementById('nominee_phone').value);
    formData.append('social_link', document.getElementById('nominee_social_link').value);
    formData.append('bio', document.getElementById('nominee_bio').value);
    
    const photo = document.getElementById('nominee_photo').files[0];
    if (photo) formData.append('photo', photo);
    
    fetch(`/polls/${currentPoll.uuid}/nominees`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadStep2Data();
            cancelNomineeForm();
            showNotification('Nominee added!', 'success');
        }
    });
}

function importNomineesCSV() {
    const formData = new FormData();
    formData.append('category_id', document.getElementById('csv_category_id').value);
    formData.append('csv_file', document.getElementById('csv_file').files[0]);
    
    fetch(`/polls/${currentPoll.uuid}/nominees/import`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadStep2Data();
            cancelCSVForm();
            showNotification(`Imported ${data.imported} nominees!`, 'success');
            if (data.errors && data.errors.length > 0) {
                console.warn('Import errors:', data.errors);
            }
        }
    });
}

function generateNomineeLink() {
    fetch(`/polls/${currentPoll.uuid}/nominees/registration-link`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const linkBox = document.createElement('div');
            linkBox.className = 'registration-link-box';
            linkBox.innerHTML = `
                <h6>Self-Registration Link for Nominees</h6>
                <div class="input-group">
                    <input type="text" class="form-control" value="${data.url}" readonly>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('${data.url}')">Copy</button>
                    </div>
                </div>
            `;
            document.getElementById('nomineesList').appendChild(linkBox);
            showNotification('Registration link generated!', 'success');
        }
    });
}

function approveNominee(id) {
    fetch(`/polls/nominees/${id}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadStep2Data();
            showNotification('Nominee approved!', 'success');
        }
    });
}

function deleteNominee(id) {
    if (!confirm('Delete this nominee?')) return;
    
    fetch(`/polls/nominees/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadStep2Data();
            showNotification('Nominee deleted!', 'success');
        }
    });
}

// Step 3: Voting & Eligibility
function toggleEligibilityOptions() {
    const isPublic = document.getElementById('is_public').checked;
    document.getElementById('eligibilityOptions').style.display = isPublic ? 'none' : 'block';
}

function saveStep3() {
    // Get all voting method checkboxes - use class selector as fallback
    let checkboxes = document.querySelectorAll('#step3 input[name="voting_methods[]"]');
    
    // If not found, try alternative selectors
    if (checkboxes.length === 0) {
        checkboxes = document.querySelectorAll('.voting-method-checkbox');
    }
    if (checkboxes.length === 0) {
        checkboxes = document.querySelectorAll('input[name="voting_methods[]"]');
    }
    
    console.log('Found checkboxes:', checkboxes.length);
    
    const votingMethods = [];
    checkboxes.forEach(cb => {
        console.log('Checkbox:', cb.id, 'Checked:', cb.checked, 'Value:', cb.value);
        if (cb.checked && cb.value) {
            votingMethods.push(cb.value);
        }
    });
    
    console.log('Selected voting methods:', votingMethods);
    
    if (votingMethods.length === 0) {
        showNotification('Please select at least one voting method', 'error');
        // Show error message
        const errorDiv = document.getElementById('votingMethodsError');
        if (errorDiv) {
            errorDiv.style.display = 'block';
        }
        // Highlight checkboxes to show error
        checkboxes.forEach(cb => {
            cb.classList.add('is-invalid');
        });
        return;
    }
    
    // Remove error highlighting
    const errorDiv = document.getElementById('votingMethodsError');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
    checkboxes.forEach(cb => {
        cb.classList.remove('is-invalid');
    });
    
    const formData = new FormData();
    formData.append('voting_methods', JSON.stringify(votingMethods));
    formData.append('is_public', document.getElementById('is_public').checked ? '1' : '0');
    formData.append('allow_vote_edit', document.getElementById('allow_vote_edit').checked ? '1' : '0');
    formData.append('email_domain', document.getElementById('email_domain') ? document.getElementById('email_domain').value || '' : '');
    formData.append('country', document.getElementById('country') ? document.getElementById('country').value || '' : '');
    
    console.log('Sending voting_methods:', JSON.stringify(votingMethods));
    console.log('FormData entries:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    fetch(`/polls/${currentPoll.uuid}/step3`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showNotification('Voting methods saved!', 'success');
            nextStep();
        } else {
            showNotification(data.message || 'Error saving voting methods', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving voting methods', 'error');
    });
}

function showManualVoterForm() {
    document.getElementById('manualVoterForm').style.display = 'block';
    document.getElementById('voterCSVImportForm').style.display = 'none';
}

function showVoterCSVImport() {
    document.getElementById('voterCSVImportForm').style.display = 'block';
    document.getElementById('manualVoterForm').style.display = 'none';
}

function cancelVoterForm() {
    document.getElementById('manualVoterForm').style.display = 'none';
    document.getElementById('voterForm').reset();
}

function cancelVoterCSVForm() {
    document.getElementById('voterCSVImportForm').style.display = 'none';
    document.getElementById('csvVoterForm').reset();
}

function saveVoter() {
    const formData = new FormData();
    formData.append('email', document.getElementById('voter_email').value);
    formData.append('phone', document.getElementById('voter_phone').value);
    formData.append('name', document.getElementById('voter_name').value);
    formData.append('identifier_type', document.getElementById('voter_identifier_type').value);
    formData.append('identifier_value', document.getElementById('voter_identifier_value').value);
    
    fetch(`/polls/${currentPoll.uuid}/eligible-voters`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadEligibleVoters();
            cancelVoterForm();
            showNotification('Eligible voter added!', 'success');
        }
    });
}

function importVotersCSV() {
    const formData = new FormData();
    formData.append('csv_file', document.getElementById('csv_voter_file').files[0]);
    
    fetch(`/polls/${currentPoll.uuid}/eligible-voters/import`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadEligibleVoters();
            cancelVoterCSVForm();
            showNotification(`Imported ${data.imported} voters!`, 'success');
        }
    });
}

function generateVoterLink() {
    fetch(`/polls/${currentPoll.uuid}/eligible-voters/registration-link`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const linkBox = document.createElement('div');
            linkBox.className = 'registration-link-box';
            linkBox.innerHTML = `
                <h6>Self-Registration Link for Voters</h6>
                <div class="input-group">
                    <input type="text" class="form-control" value="${data.url}" readonly>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('${data.url}')">Copy</button>
                    </div>
                </div>
            `;
            document.getElementById('eligibleVotersList').appendChild(linkBox);
            showNotification('Registration link generated!', 'success');
        }
    });
}

function loadEligibleVoters() {
    if (!currentPoll) return;
    
    fetch(`/polls/${currentPoll.uuid}/step3-data`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderEligibleVoters(data.poll.eligible_voters || []);
        }
    })
    .catch(error => {
        console.error('Error loading eligible voters:', error);
    });
}

function renderEligibleVoters(voters) {
    const container = document.getElementById('eligibleVotersList');
    if (voters.length === 0) {
        container.innerHTML = '<p class="text-muted">No eligible voters yet.</p>';
        return;
    }
    
    container.innerHTML = voters.map(voter => `
        <div class="voter-item" data-id="${voter.id}">
            <div class="d-flex justify-content-between">
                <div>
                    <h6>${voter.name || 'Unnamed Voter'}</h6>
                    <p class="text-muted small mb-1">
                        ${voter.email ? 'Email: ' + voter.email + '<br>' : ''}
                        ${voter.phone ? 'Phone: ' + voter.phone + '<br>' : ''}
                        ${voter.identifier_type && voter.identifier_value ? voter.identifier_type + ': ' + voter.identifier_value : ''}
                    </p>
                    ${voter.registered_at ? '<span class="badge badge-success">Registered</span>' : '<span class="badge badge-warning">Pending</span>'}
                </div>
                <div>
                    <button class="btn btn-sm btn-danger" onclick="deleteEligibleVoter(${voter.id})">Delete</button>
                </div>
            </div>
        </div>
    `).join('');
}

function deleteEligibleVoter(id) {
    if (!confirm('Delete this eligible voter?')) return;
    
    fetch(`/polls/eligible-voters/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadEligibleVoters();
            showNotification('Eligible voter deleted!', 'success');
        }
    });
}

// Step 4: Finalize
function saveAsDraft() {
    finalizePoll('draft');
}

function publishPoll() {
    if (!confirm('Publish this poll? It will become active immediately.')) return;
    finalizePoll('publish');
}

function finalizePoll(action) {
    const notifyNominees = document.getElementById('notify_nominees').checked;
    
    fetch(`/polls/${currentPoll.uuid}/finalize`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: action,
            notify_nominees: notifyNominees
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                window.location.href = '/polls';
            }, 2000);
        } else {
            showNotification(data.message || 'Error finalizing poll', 'error');
        }
    });
}

// Utility functions
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Link copied to clipboard!', 'success');
    });
}

function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    document.querySelector('.section-body').insertBefore(alert, document.querySelector('.section-body').firstChild);
    setTimeout(() => alert.remove(), 5000);
}
</script>
@endpush
