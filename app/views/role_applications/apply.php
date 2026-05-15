<?php
/**
 * Role Application Form — for users with role='user'
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
                    <p class="text-muted mb-0 small">Submit your role application to the Gym Owner for review.</p>
                </div>
            </div>

            <?php if ($hasPending): ?>
            <!-- Pending Application Notice -->
            <div class="alert alert-warning d-flex align-items-start gap-3 mb-4">
                <i class="bi bi-hourglass-split fs-5 mt-1"></i>
                <div>
                    <strong>Application Pending</strong><br>
                    You have a pending application for <strong><?= role_label($hasPending['requested_role']) ?></strong>.
                    Please wait for the Gym Owner to review it before submitting a new one.
                </div>
            </div>
            <?php else: ?>
            <!-- Application Form -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-send-fill me-2 text-primary"></i>New Application</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('/role-application/apply') ?>">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Requested Role <span class="text-danger">*</span></label>
                            <select name="requested_role" class="form-select" required>
                                <option value="">— Select a role —</option>
                                <?php foreach ($availableRoles as $value => $label): ?>
                                <option value="<?= e($value) ?>"><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Choose the role that best describes your purpose at GoBuff.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Reason / Background <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="5" required minlength="10"
                                placeholder="Briefly explain why you are applying for this role (e.g. I am a gym enthusiast who wants to enroll as a member, or I am a certified fitness trainer...)"></textarea>
                            <div class="form-text">Minimum 10 characters.</div>
                        </div>

                        <div class="d-flex gap-2">
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
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Review Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myApplications as $app): ?>
                                <tr>
                                    <td class="fw-semibold"><?= e($app['role_label'] ?? role_label($app['requested_role'])) ?></td>
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
