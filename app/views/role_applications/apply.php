<?php
/**
 * Role Application Form — for users with role='user'
 * Member role shows a full membership form; other roles show a reason textarea.
 */
?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- Page Header -->
            <div class="d-flex align-items-center mb-4">
                <div class="me-3">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-person-badge-fill text-primary fs-4"></i>
                    </div>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold">Apply for a Role</h4>
                    <p class="text-muted mb-0 small">Submit your role application for review.</p>
                </div>
            </div>

            <?php if ($hasPending): ?>
            <!-- Pending Application Notice -->
            <div class="alert alert-warning d-flex align-items-start gap-3 mb-4">
                <i class="bi bi-hourglass-split fs-5 mt-1"></i>
                <div>
                    <strong>Application Pending</strong><br>
                    You have a pending application for <strong><?= role_label($hasPending['requested_role']) ?></strong>.
                    Please wait for it to be reviewed before submitting a new one.
                </div>
            </div>
            <?php else: ?>
            <!-- Application Form -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-send-fill me-2 text-primary"></i>New Application</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('/role-application/apply') ?>" id="roleApplicationForm" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <!-- Role Selector -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Requested Role <span class="text-danger">*</span></label>
                            <select name="requested_role" id="requestedRole" class="form-select" required>
                                <option value="">— Select a role —</option>
                                <?php foreach ($availableRoles as $value => $label): ?>
                                <option value="<?= e($value) ?>"><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Choose the role that best describes your purpose at GoBuff.</div>
                        </div>

                        <!-- GYM SELECTOR (shown only for employee roles) -->
                        <div id="gymSelectorSection" style="display:none;">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Select Gym <span class="text-danger">*</span></label>
                                <?php if (!empty($approvedGyms)): ?>
                                <select name="gym_id" id="gymSelect" class="form-select">
                                    <option value="">— Select a gym —</option>
                                    <?php foreach ($approvedGyms as $gym): ?>
                                    <option value="<?= e($gym['id']) ?>">
                                        <?= e($gym['business_name']) ?>
                                        <?php if (!empty($gym['address'])): ?> — <?= e($gym['address']) ?><?php endif; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Choose the gym you are applying to work at.
                                </div>
                                <?php else: ?>
                                <div class="alert alert-warning py-2 mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    No registered gyms are available at this time. Please check back later.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- ══════════════════════════════════════════════════════ -->
                        <!-- MEMBERSHIP FORM (shown only when "member" is selected) -->
                        <!-- ══════════════════════════════════════════════════════ -->
                        <div id="membershipFormSection" style="display:none;">

                            <div class="alert alert-info d-flex align-items-start gap-2 mb-4 py-2">
                                <i class="bi bi-info-circle-fill mt-1"></i>
                                <div class="small">
                                    Membership applications are reviewed by the <strong>Administrative Office</strong>.
                                    Please fill in all required fields accurately.
                                </div>
                            </div>

                            <!-- Personal Information -->
                            <div class="card border-0 bg-light rounded-3 mb-4">
                                <div class="card-body p-3">
                                    <h6 class="fw-semibold mb-3 text-primary">
                                        <i class="bi bi-person-fill me-2"></i>Personal Information
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" class="form-control form-control-sm"
                                                   placeholder="e.g. Juan">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" class="form-control form-control-sm"
                                                   placeholder="e.g. dela Cruz">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" name="date_of_birth" class="form-control form-control-sm"
                                                   max="<?= date('Y-m-d', strtotime('-16 years')) ?>">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-semibold">Gender <span class="text-danger">*</span></label>
                                            <select name="gender" class="form-select form-select-sm">
                                                <option value="">— Select —</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other / Prefer not to say</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                            <input type="tel" name="phone" class="form-control form-control-sm"
                                                   placeholder="e.g. 09XX-XXX-XXXX">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">Home Address <span class="text-danger">*</span></label>
                                            <textarea name="address" class="form-control form-control-sm" rows="2"
                                                      placeholder="Street, Barangay, City, Province"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Emergency Contact -->
                            <div class="card border-0 bg-light rounded-3 mb-4">
                                <div class="card-body p-3">
                                    <h6 class="fw-semibold mb-3 text-danger">
                                        <i class="bi bi-telephone-fill me-2"></i>Emergency Contact
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-semibold">Contact Name <span class="text-danger">*</span></label>
                                            <input type="text" name="emergency_name" class="form-control form-control-sm"
                                                   placeholder="Full name">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-semibold">Contact Phone <span class="text-danger">*</span></label>
                                            <input type="tel" name="emergency_phone" class="form-control form-control-sm"
                                                   placeholder="e.g. 09XX-XXX-XXXX">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-semibold">Relationship <span class="text-danger">*</span></label>
                                            <input type="text" name="emergency_relation" class="form-control form-control-sm"
                                                   placeholder="e.g. Parent, Spouse, Sibling">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Health & Fitness -->
                            <div class="card border-0 bg-light rounded-3 mb-4">
                                <div class="card-body p-3">
                                    <h6 class="fw-semibold mb-3 text-success">
                                        <i class="bi bi-heart-pulse-fill me-2"></i>Health & Fitness
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">Known Health Conditions</label>
                                            <textarea name="health_conditions" class="form-control form-control-sm" rows="2"
                                                      placeholder="e.g. asthma, hypertension, diabetes — or leave blank if none"></textarea>
                                            <div class="form-text">Optional. This helps our trainers provide safe guidance.</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">Fitness Goals</label>
                                            <textarea name="fitness_goals" class="form-control form-control-sm" rows="2"
                                                      placeholder="e.g. lose weight, build muscle, improve endurance"></textarea>
                                            <div class="form-text">Optional. Tell us what you want to achieve.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Membership Plan Preference -->
                            <div class="card border-0 bg-light rounded-3 mb-4">
                                <div class="card-body p-3">
                                    <h6 class="fw-semibold mb-3 text-warning">
                                        <i class="bi bi-card-checklist me-2"></i>Membership Plan Preference
                                    </h6>
                                    <div class="row g-3">
                                        <?php
                                        $plans = [
                                            'monthly'     => ['Monthly',      '1 Month',   'bi-calendar-month',    'primary'],
                                            'quarterly'   => ['Quarterly',    '3 Months',  'bi-calendar3',         'success'],
                                            'semi_annual' => ['Semi-Annual',  '6 Months',  'bi-calendar-range',    'warning'],
                                            'annual'      => ['Annual',       '12 Months', 'bi-calendar-check',    'danger'],
                                        ];
                                        foreach ($plans as $val => [$label, $duration, $icon, $color]):
                                        ?>
                                        <div class="col-sm-6 col-lg-3">
                                            <label class="plan-card d-block cursor-pointer">
                                                <input type="radio" name="plan_preference" value="<?= $val ?>"
                                                       class="d-none plan-radio">
                                                <div class="card border-2 text-center p-3 h-100 plan-option">
                                                    <i class="bi <?= $icon ?> fs-3 text-<?= $color ?> mb-2"></i>
                                                    <div class="fw-semibold"><?= $label ?></div>
                                                    <div class="text-muted small"><?= $duration ?></div>
                                                </div>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Final pricing and start date will be confirmed by the Administrative Office upon approval.
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- END MEMBERSHIP FORM SECTION -->

                        <!-- REASON FIELD (shown for non-member roles) -->
                        <div id="reasonSection" style="display:none;">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Reason / Background <span class="text-danger">*</span></label>
                                <textarea name="reason" id="reasonField" class="form-control" rows="5" minlength="10"
                                    placeholder="Briefly explain why you are applying for this role (e.g. I am a certified fitness trainer with 3 years of experience...)"></textarea>
                                <div class="form-text">Minimum 10 characters.</div>
                            </div>

                            <!-- DOCUMENT UPLOADS -->
                            <div class="card border-0 bg-light rounded-3 mb-4">
                                <div class="card-body p-3">
                                    <h6 class="fw-semibold mb-1 text-primary">
                                        <i class="bi bi-paperclip me-2"></i>Supporting Documents
                                        <span class="text-danger">*</span>
                                    </h6>
                                    <p class="text-muted small mb-3">
                                        Upload at least one document. Accepted: PDF, DOC, DOCX, JPG, PNG, WEBP (max 10 MB each).
                                    </p>

                                    <div id="docRows">
                                        <!-- Row 1 — required -->
                                        <div class="doc-row row g-2 align-items-end mb-3">
                                            <div class="col-sm-5">
                                                <label class="form-label small fw-semibold">Document Type <span class="text-danger">*</span></label>
                                                <select name="document_type[]" class="form-select form-select-sm doc-type-select" required>
                                                    <option value="">— Select type —</option>
                                                    <option value="resume">Resume / CV</option>
                                                    <option value="biodata">Biodata (Philippine Format)</option>
                                                    <option value="birth_certificate">Birth Certificate (PSA)</option>
                                                    <option value="government_id">Government-Issued ID</option>
                                                    <option value="certificate">Certificate / Diploma</option>
                                                    <option value="other">Other Supporting Document</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label small fw-semibold">File <span class="text-danger">*</span></label>
                                                <input type="file" name="documents[]" class="form-control form-control-sm doc-file-input"
                                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" required>
                                            </div>
                                            <div class="col-sm-1 d-flex align-items-end justify-content-end">
                                                <!-- placeholder for remove button alignment -->
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" id="addDocBtn" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-plus-circle me-1"></i>Add Another Document
                                    </button>
                                    <div class="form-text mt-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Philippine applicants: a <strong>Biodata</strong> or <strong>PSA Birth Certificate</strong> is commonly required.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2" id="submitSection" style="display:none !important;">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-send me-2"></i>Submit Application
                            </button>
                            <a href="<?= base_url('/dashboard') ?>" class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Application History -->
            <?php if (!empty($myApplications)): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-secondary"></i>My Application History</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Role Applied</th>
                                    <th>Gym</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Review Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myApplications as $app): ?>
                                <tr>
                                    <td class="fw-semibold"><?= e($app['role_label'] ?? role_label($app['requested_role'])) ?></td>
                                    <td class="text-muted small">
                                        <?php if (!empty($app['gym_name'])): ?>
                                        <i class="bi bi-building me-1"></i><?= e($app['gym_name']) ?>
                                        <?php else: ?>
                                        <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = match($app['status']) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default    => 'warning',
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($app['status']) ?></span>
                                    </td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($app['created_at'])) ?></td>
                                    <td class="text-muted small"><?= e($app['review_notes'] ?? '—') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
.plan-option {
    border-color: #dee2e6 !important;
    transition: border-color .15s, box-shadow .15s;
    cursor: pointer;
}
.plan-radio:checked + .plan-option {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13,110,253,.15);
    background: rgba(13,110,253,.04);
}
.plan-option:hover {
    border-color: #86b7fe !important;
}
</style>

<script>
(function () {
    const roleSelect       = document.getElementById('requestedRole');
    const memberSection    = document.getElementById('membershipFormSection');
    const gymSection       = document.getElementById('gymSelectorSection');
    const gymSelect        = document.getElementById('gymSelect');
    const reasonSection    = document.getElementById('reasonSection');
    const submitSection    = document.getElementById('submitSection');
    const reasonField      = document.getElementById('reasonField');

    function toggleSections() {
        const val = roleSelect.value;
        if (!val) {
            memberSection.style.display = 'none';
            gymSection.style.display    = 'none';
            reasonSection.style.display = 'none';
            submitSection.style.display = 'none';
            if (gymSelect) gymSelect.removeAttribute('required');
            setDocRequired(false);
            return;
        }

        submitSection.style.display = '';

        if (val === 'member') {
            memberSection.style.display = '';
            gymSection.style.display    = 'none';
            reasonSection.style.display = 'none';
            reasonField.removeAttribute('required');
            if (gymSelect) gymSelect.removeAttribute('required');
            setMembershipRequired(true);
            setDocRequired(false);
        } else {
            memberSection.style.display = 'none';
            gymSection.style.display    = '';
            reasonSection.style.display = '';
            reasonField.setAttribute('required', 'required');
            if (gymSelect) gymSelect.setAttribute('required', 'required');
            setMembershipRequired(false);
            setDocRequired(true);
        }
    }

    function setDocRequired(required) {
        const firstType = document.querySelector('.doc-type-select');
        const firstFile = document.querySelector('.doc-file-input');
        if (firstType) {
            if (required) firstType.setAttribute('required', 'required');
            else firstType.removeAttribute('required');
        }
        if (firstFile) {
            if (required) firstFile.setAttribute('required', 'required');
            else firstFile.removeAttribute('required');
        }
    }

    function setMembershipRequired(required) {
        const fields = ['first_name','last_name','date_of_birth','gender','phone','address',
                        'emergency_name','emergency_phone','emergency_relation'];
        fields.forEach(name => {
            const el = document.querySelector('[name="' + name + '"]');
            if (el) {
                if (required) el.setAttribute('required', 'required');
                else el.removeAttribute('required');
            }
        });
        const planRadios = document.querySelectorAll('.plan-radio');
        planRadios.forEach(r => {
            if (required) r.setAttribute('required', 'required');
            else r.removeAttribute('required');
        });
    }

    // Plan card visual selection
    document.querySelectorAll('.plan-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.plan-option').forEach(c => c.classList.remove('selected'));
            if (this.checked) {
                this.nextElementSibling.classList.add('selected');
            }
        });
    });

    // Add another document row
    document.getElementById('addDocBtn')?.addEventListener('click', function () {
        const container = document.getElementById('docRows');
        const row = document.createElement('div');
        row.className = 'doc-row row g-2 align-items-end mb-3';
        row.innerHTML = `
            <div class="col-sm-5">
                <label class="form-label small fw-semibold">Document Type</label>
                <select name="document_type[]" class="form-select form-select-sm">
                    <option value="">— Select type —</option>
                    <option value="resume">Resume / CV</option>
                    <option value="biodata">Biodata (Philippine Format)</option>
                    <option value="birth_certificate">Birth Certificate (PSA)</option>
                    <option value="government_id">Government-Issued ID</option>
                    <option value="certificate">Certificate / Diploma</option>
                    <option value="other">Other Supporting Document</option>
                </select>
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-semibold">File</label>
                <input type="file" name="documents[]" class="form-control form-control-sm"
                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
            </div>
            <div class="col-sm-1 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-outline-danger remove-doc-btn" title="Remove">
                    <i class="bi bi-trash"></i>
                </button>
            </div>`;
        container.appendChild(row);

        row.querySelector('.remove-doc-btn').addEventListener('click', function () {
            row.remove();
        });
    });

    roleSelect.addEventListener('change', toggleSections);
    toggleSections();
})();
</script>
