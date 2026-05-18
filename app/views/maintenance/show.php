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
            <?php endif; ?>
            <?php if (has_role(['gym_owner', 'admin']) && $report['status'] === 'completed'): ?>
                <form method="POST" action="<?= base_url('/maintenance/' . $report['id'] . '/approve') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-success"
                            onclick="return confirm('Approve this report and restore the equipment to service?')">
                        <i class="bi bi-patch-check me-1"></i>Approve
                    </button>
                </form>
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
                ];
                $order  = array_keys($steps);
                $current = array_search($report['status'], $order);
                if ($current === false) $current = -1;
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
                    <form method="POST" action="<?= base_url('/maintenance/' . $report['id'] . '/approve') ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Approve this report and mark the equipment as back in service?')">
                            <i class="bi bi-patch-check me-1"></i>Approve & Close Report
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>

    </div>
</div>
