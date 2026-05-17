<?php
/**
 * Membership Application Detail — Admin Officer review
 */
$isPending = $application['status'] === 'pending';
$planLabels = [
    'monthly'     => 'Monthly',
    'quarterly'   => 'Quarterly',
    'semi_annual' => 'Semi-Annual',
    'annual'      => 'Annual',
];
?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- Back -->
            <a href="<?= base_url('/member-applications') ?>" class="btn btn-sm btn-outline-secondary mb-4">
                <i class="bi bi-arrow-left me-1"></i>Back to Applications
            </a>

            <!-- Application Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-card-checklist me-2 text-primary"></i>Membership Application #<?= $application['id'] ?>
                    </h6>
                    <?php
                    $badgeClass = match($application['status']) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'warning',
                    };
                    ?>
                    <span class="badge bg-<?= $badgeClass ?> fs-6 px-3"><?= ucfirst($application['status']) ?></span>
                </div>
                <div class="card-body p-4">

                    <!-- Account Info -->
                    <h6 class="text-muted text-uppercase small fw-semibold mb-3 border-bottom pb-2">Account Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Full Name (Account)</div>
                            <div class="fw-semibold"><?= e($application['user_name']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Email</div>
                            <div><?= e($application['user_email']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Username</div>
                            <div><?= e($application['username']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Submitted</div>
                            <div><?= date('F d, Y g:i A', strtotime($application['created_at'])) ?></div>
                        </div>
                    </div>

                    <?php if (!empty($membershipData)): ?>

                    <!-- Personal Information -->
                    <h6 class="text-muted text-uppercase small fw-semibold mb-3 border-bottom pb-2">Personal Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">First Name</div>
                            <div class="fw-semibold"><?= e($membershipData['first_name'] ?? '—') ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Last Name</div>
                            <div class="fw-semibold"><?= e($membershipData['last_name'] ?? '—') ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Date of Birth</div>
                            <div>
                                <?php
                                $dob = $membershipData['date_of_birth'] ?? '';
                                if ($dob) {
                                    $age = (int)date_diff(date_create($dob), date_create('today'))->y;
                                    echo date('F d, Y', strtotime($dob)) . " <span class='text-muted small'>({$age} yrs)</span>";
                                } else {
                                    echo '—';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Gender</div>
                            <div><?= e(ucfirst($membershipData['gender'] ?? '—')) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Phone Number</div>
                            <div><?= e($membershipData['phone'] ?? '—') ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Home Address</div>
                            <div><?= e($membershipData['address'] ?? '—') ?></div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <h6 class="text-muted text-uppercase small fw-semibold mb-3 border-bottom pb-2">Emergency Contact</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Contact Name</div>
                            <div class="fw-semibold"><?= e($membershipData['emergency_name'] ?? '—') ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Contact Phone</div>
                            <div><?= e($membershipData['emergency_phone'] ?? '—') ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Relationship</div>
                            <div><?= e($membershipData['emergency_relation'] ?? '—') ?></div>
                        </div>
                    </div>

                    <!-- Health & Fitness -->
                    <h6 class="text-muted text-uppercase small fw-semibold mb-3 border-bottom pb-2">Health & Fitness</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="text-muted small mb-1">Known Health Conditions</div>
                            <div class="bg-light rounded p-2 small">
                                <?= nl2br(e($membershipData['health_conditions'] ?? '')) ?: '<span class="text-muted">None declared</span>' ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small mb-1">Fitness Goals</div>
                            <div class="bg-light rounded p-2 small">
                                <?= nl2br(e($membershipData['fitness_goals'] ?? '')) ?: '<span class="text-muted">Not specified</span>' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Plan Preference -->
                    <h6 class="text-muted text-uppercase small fw-semibold mb-3 border-bottom pb-2">Membership Plan Preference</h6>
                    <div class="mb-4">
                        <?php $pref = $membershipData['plan_preference'] ?? ''; ?>
                        <span class="badge bg-primary fs-6 px-3 py-2">
                            <i class="bi bi-card-checklist me-1"></i>
                            <?= e($planLabels[$pref] ?? ucfirst(str_replace('_', '-', $pref))) ?>
                        </span>
                        <div class="text-muted small mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Final pricing and start date will be set when creating the membership record.
                        </div>
                    </div>

                    <?php endif; ?>

                    <?php if (!$isPending && $application['review_notes']): ?>
                    <div class="mb-2">
                        <div class="text-muted small mb-1">Review Notes</div>
                        <div class="bg-light rounded p-3"><?= nl2br(e($application['review_notes'])) ?></div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <?php if ($isPending): ?>
            <!-- Approve / Reject Forms -->
            <div class="row g-3">
                <!-- Approve -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm border-start border-success border-3">
                        <div class="card-body p-4">
                            <h6 class="fw-semibold text-success mb-3">
                                <i class="bi bi-check-circle-fill me-2"></i>Approve Application
                            </h6>
                            <form method="POST" action="<?= base_url('/member-applications/' . $application['id'] . '/approve') ?>">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label class="form-label small">Notes (optional)</label>
                                    <textarea name="review_notes" class="form-control form-control-sm" rows="3"
                                        placeholder="Welcome message or next steps..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success w-100"
                                    onclick="return confirm('Approve this membership application and assign the Member role?')">
                                    <i class="bi bi-check-lg me-1"></i>Approve & Assign Member Role
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Reject -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm border-start border-danger border-3">
                        <div class="card-body p-4">
                            <h6 class="fw-semibold text-danger mb-3">
                                <i class="bi bi-x-circle-fill me-2"></i>Reject Application
                            </h6>
                            <form method="POST" action="<?= base_url('/member-applications/' . $application['id'] . '/reject') ?>">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label class="form-label small">Reason for rejection <span class="text-danger">*</span></label>
                                    <textarea name="review_notes" class="form-control form-control-sm" rows="3" required
                                        placeholder="Explain why the application is rejected..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger w-100"
                                    onclick="return confirm('Reject this membership application?')">
                                    <i class="bi bi-x-lg me-1"></i>Reject Application
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-secondary">
                <i class="bi bi-info-circle me-2"></i>
                This application was <strong><?= $application['status'] ?></strong>
                <?php if ($application['reviewed_at']): ?>
                on <?= date('F d, Y', strtotime($application['reviewed_at'])) ?>
                <?php endif; ?>
                <?php if ($application['reviewer_name']): ?>
                by <strong><?= e($application['reviewer_name']) ?></strong>
                <?php endif; ?>.
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
