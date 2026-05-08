<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Membership Details</h2></div>
        <a href="<?= base_url('/memberships') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><?= e($membership['plan_name']) ?></h6>
                    <?= status_badge($membership['status']) ?>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-5">Plan Type</dt><dd class="col-7"><?= ucfirst(e($membership['plan_type'] ?? 'N/A')) ?></dd>
                        <dt class="col-5">Start Date</dt><dd class="col-7"><?= format_date($membership['start_date']) ?></dd>
                        <dt class="col-5">Expiry Date</dt><dd class="col-7"><?= format_date($membership['expiry_date']) ?></dd>
                        <dt class="col-5">Amount</dt><dd class="col-7"><?= format_currency($membership['amount']) ?></dd>
                        <dt class="col-5">Created</dt><dd class="col-7"><?= format_datetime($membership['created_at']) ?></dd>
                    </dl>
                    <?php if ($membership['status'] === 'pending'): ?>
                    <div class="d-flex gap-2 mt-3">
                        <form method="POST" action="<?= base_url('/memberships/' . $membership['id'] . '/approve') ?>">
                            <?= csrf_field() ?>
                            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Approve</button>
                        </form>
                        <form method="POST" action="<?= base_url('/memberships/' . $membership['id'] . '/reject') ?>">
                            <?= csrf_field() ?>
                            <button class="btn btn-danger"><i class="bi bi-x-lg me-1"></i>Reject</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
