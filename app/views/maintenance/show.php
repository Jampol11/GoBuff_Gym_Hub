<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">Maintenance Report Details</h2>
            <p class="page-subtitle">Report #<?= (int)$report['id'] ?></p>
        </div>
        <div class="page-actions">
            <?php if (has_role(['gym_owner', 'admin']) && $report['status'] === 'pending'): ?>
                <form method="POST" action="<?= base_url('/maintenance/' . $report['id'] . '/verify') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-info text-white"
                            onclick="return confirm('Verify this report and notify staff to proceed?')">
                        <i class="bi bi-check-circle me-1"></i>Verify
                    </button>
                </form>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#declineModal">
                    <i class="bi bi-x-circle me-1"></i>Decline
                </button>
            <?php endif; ?>
            <?php if (has_role(['gym_owner', 'admin']) && $report['status'] === 'completed'): ?>
                <form method="POST" action="<?= base_url('/maintenance/' . $report['id'] . '/approve') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-success"
                            onclick="return confirm('Approve this report and restore the equipment to service?')">
                        <i class="bi bi-patch-check me-1"></i>Approve
                    </button>
                </form>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#declineModal">
                    <i class="bi bi-x-circle me-1"></i>Decline
                </button>
            <?php endif; ?>
            <a href="<?= base_url('/maintenance') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <!-- Workflow Progress -->
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <?php
                $steps = [
                    'pending'     => ['label' => '1. Reported',    'icon' => 'bi-flag-fill',       'color' => 'warning'],
                    'in_progress' => ['label' => '2. Verified',    'icon' => 'bi-check-circle-fill','color' => 'info'],
                    'completed'   => ['label' => '3. Work Done',   'icon' => 'bi-wrench',           'color' => 'primary'],
                    'approved'    => ['label' => '4. Approved',    'icon' => 'bi-patch-check-fill', 'color' => 'success'],
                    'declined'    => ['label' => 'Declined',       'icon' => 'bi-x-circle-fill',    'color' => 'danger'],
                ];
                // For declined status, show only the declined step as active
                if ($report['status'] === 'declined') {
                    $steps = ['declined' => $steps['declined']];
                    $order   = ['declined'];
                    $current = 0;
                } else {
                    unset($steps['declined']);
                    $order   = array_keys($steps);
                    $current = array_search($report['status'], $order);
                    if ($current === false) $current = -1;
                }
                ?>
                <?php foreach ($steps as $key => $step):
                    $idx    = array_search($key, $order);
                    $done   = $idx <= $current;
                    $active = $idx === $current;
                ?>
                <div class="d-flex align-items-center gap-2 <?= $done ? '' : 'opacity-40' ?>">
                    <div class="rounded-circle d-flex align-items-center justify-content-center
                                bg-<?= $done ? $step['color'] : 'secondary' ?> bg-opacity-<?= $active ? '100' : '25' ?>"
                         style="width:36px;height:36px;">
                        <i class="bi <?= $step['icon'] ?> <?= $done ? 'text-' . ($active ? 'white' : $step['color']) : 'text-secondary' ?>"></i>
                    </div>
                    <span class="fw-<?= $active ? 'bold' : 'normal' ?> small text-<?= $done ? $step['color'] : 'muted' ?>">
                        <?= $step['label'] ?>
                    </span>
                </div>
                <?php if ($idx < count($steps) - 1): ?>
                    <div class="flex-grow-1 border-top border-2 border-<?= $idx < $current ? 'success' : 'secondary' ?> opacity-25 d-none d-md-block"></div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- Report Details -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-wrench me-2 text-danger"></i>Report Information</h6>
                    <?= status_badge($report['status']) ?>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Equipment</dt>
                        <dd class="col-7 fw-semibold">
                            <?php if ($equipment): ?>
                                <a href="<?= base_url('/equipment/' . $equipment['id']) ?>" class="text-decoration-none">
                                    <?= e($equipment['name']) ?>
                                </a>
                            <?php else: ?>N/A<?php endif; ?>
                        </dd>

                        <dt class="col-5 text-muted">Issue Type</dt>
                        <dd class="col-7"><?= e($report['issue_type']) ?></dd>

                        <dt class="col-5 text-muted">Priority</dt>
                        <dd class="col-7"><?= status_badge($report['priority'] ?? 'medium') ?></dd>

                        <dt class="col-5 text-muted">Reported By</dt>
                        <dd class="col-7"><?= e($reporter_name) ?></dd>

                        <dt class="col-5 text-muted">Date Reported</dt>
                        <dd class="col-7"><?= format_date($report['created_at']) ?></dd>

                        <?php if (!empty($report['verified_at'])): ?>
                        <dt class="col-5 text-muted">Verified At</dt>
                        <dd class="col-7"><?= format_date($report['verified_at']) ?></dd>
                        <?php endif; ?>

                        <?php if (!empty($report['completed_at'])): ?>
                        <dt class="col-5 text-muted">Work Completed At</dt>
                        <dd class="col-7"><?= format_date($report['completed_at']) ?></dd>
                        <?php endif; ?>

                        <?php if (!empty($report['approved_at'])): ?>
                        <dt class="col-5 text-muted">Approved At</dt>
                        <dd class="col-7"><?= format_date($report['approved_at']) ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Description, Resolution & Actions -->
        <div class="col-lg-6 d-flex flex-column gap-4">

            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-card-text me-2 text-secondary"></i>Issue Description</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(e($report['description'])) ?></p>
                </div>
            </div>

            <?php if (!empty($report['photo_evidence'])): ?>
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-camera me-2 text-danger"></i>Photo Evidence</h6>
                </div>
                <div class="card-body text-center p-2">
                    <a href="<?= base_url('/assets/uploads/' . e($report['photo_evidence'])) ?>"
                       target="_blank" title="Click to view full size">
                        <img src="<?= base_url('/assets/uploads/' . e($report['photo_evidence'])) ?>"
                             alt="Photo evidence"
                             class="img-fluid rounded shadow-sm"
                             style="max-height:320px; object-fit:contain; cursor:zoom-in;">
                    </a>
                    <p class="text-muted mt-2 mb-0" style="font-size:.75rem;">
                        <i class="bi bi-zoom-in me-1"></i>Click image to view full size
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($report['resolution'])): ?>
            <div class="card shadow-sm border-start border-primary border-3">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-tools me-2 text-primary"></i>Work Done / Resolution</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(e($report['resolution'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($report['decline_reason'])): ?>
            <div class="card shadow-sm border-start border-danger border-3">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-x-circle me-2 text-danger"></i>Decline Reason</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><?= nl2br(e($report['decline_reason'])) ?></p>
                    <?php if (!empty($report['declined_at'])): ?>
                    <p class="text-muted mb-0" style="font-size:.8rem;">
                        <i class="bi bi-clock me-1"></i>Declined on <?= format_date($report['declined_at']) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Step 3: Maintenance staff marks work complete -->
            <?php if (has_role(['gym_owner', 'admin', 'maintenance']) && $report['status'] === 'in_progress'): ?>
            <div class="card shadow-sm border-start border-warning border-3">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-wrench me-2 text-warning"></i>Mark Work as Complete</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= base_url('/maintenance/' . $report['id'] . '/complete') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">What was done? <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="resolution" rows="3" required
                                      placeholder="Describe the repair work performed..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning w-100"
                                onclick="return confirm('Submit work as complete? The owner will be notified for final approval.')">
                            <i class="bi bi-check-lg me-1"></i>Submit as Complete
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Step 4: Owner final approval -->
            <?php if (has_role(['gym_owner', 'admin']) && $report['status'] === 'completed'): ?>
            <div class="card shadow-sm border-start border-success border-3">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-patch-check me-2 text-success"></i>Final Approval</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        The maintenance staff has completed the work. Review the resolution above and approve to restore the equipment to service.
                    </p>
                    <div class="d-flex gap-2">
                        <form method="POST" action="<?= base_url('/maintenance/' . $report['id'] . '/approve') ?>" class="flex-grow-1">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success w-100"
                                    onclick="return confirm('Approve this report and mark the equipment as back in service?')">
                                <i class="bi bi-patch-check me-1"></i>Approve & Close Report
                            </button>
                        </form>
                        <button type="button" class="btn btn-outline-danger flex-grow-1"
                                data-bs-toggle="modal" data-bs-target="#declineModal">
                            <i class="bi bi-x-circle me-1"></i>Decline Work
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

    </div>
</div>

<?php if (has_role(['gym_owner', 'admin']) && in_array($report['status'], ['pending', 'completed'])): ?>
<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?= base_url('/maintenance/' . $report['id'] . '/decline') ?>">
                <?= csrf_field() ?>
                <div class="modal-header border-danger">
                    <h5 class="modal-title text-danger" id="declineModalLabel">
                        <i class="bi bi-x-circle me-2"></i>
                        <?= $report['status'] === 'pending' ? 'Decline Report' : 'Decline Work Done' ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if ($report['status'] === 'pending'): ?>
                    <p class="text-muted small mb-3">
                        Declining this report will mark it as invalid and notify the reporter. The equipment status will be restored.
                    </p>
                    <?php else: ?>
                    <p class="text-muted small mb-3">
                        Declining the work will send it back to the maintenance staff for correction. They will be notified with your reason.
                    </p>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Reason for declining <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" name="decline_reason" rows="4" required
                                  placeholder="Explain why this is being declined..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>Confirm Decline
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
