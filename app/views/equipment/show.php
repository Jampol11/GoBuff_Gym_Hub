<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title"><?= e($equipment['name']) ?></h2></div>
        <div class="page-actions">
            <?php if (has_role(['gym_owner','admin','maintenance'])): ?>
            <a href="<?= base_url('/equipment/' . $equipment['id'] . '/edit') ?>" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
            <a href="<?= base_url('/maintenance/create') ?>" class="btn btn-outline-danger"><i class="bi bi-wrench me-1"></i>Report Issue</a>
            <?php endif; ?>
            <a href="<?= base_url('/equipment') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">Equipment Details</h6>
                    <?= status_badge($equipment['condition_status']) ?>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-5">Category</dt><dd class="col-7"><?= e($equipment['category'] ?? 'N/A') ?></dd>
                        <dt class="col-5">Brand</dt><dd class="col-7"><?= e($equipment['brand'] ?? 'N/A') ?></dd>
                        <dt class="col-5">Model</dt><dd class="col-7"><?= e($equipment['model'] ?? 'N/A') ?></dd>
                        <dt class="col-5">Serial No.</dt><dd class="col-7"><code><?= e($equipment['serial_number'] ?? 'N/A') ?></code></dd>
                        <dt class="col-5">Location</dt><dd class="col-7"><?= e($equipment['location'] ?? 'N/A') ?></dd>
                        <dt class="col-5">Purchase Date</dt><dd class="col-7"><?= format_date($equipment['purchase_date']) ?></dd>
                        <dt class="col-5">Purchase Price</dt><dd class="col-7"><?= format_currency($equipment['purchase_price'] ?? 0) ?></dd>
                        <dt class="col-5">Last Maintenance</dt><dd class="col-7"><?= format_date($equipment['last_maintenance_date']) ?></dd>
                        <dt class="col-5">Notes</dt><dd class="col-7"><?= e($equipment['notes'] ?? 'None') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h6 class="mb-0 fw-semibold">Maintenance History</h6></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light"><tr><th>Issue</th><th>Priority</th><th>Status</th><th>Reported</th></tr></thead>
                            <tbody>
                                <?php if (empty($maintenance)): ?>
                                    <tr><td colspan="4" class="text-center py-3 text-muted">No maintenance records</td></tr>
                                <?php else: ?>
                                    <?php foreach ($maintenance as $m): ?>
                                        <tr>
                                            <td><?= e($m['issue_type']) ?></td>
                                            <td><?= status_badge($m['priority'] ?? 'medium') ?></td>
                                            <td><?= status_badge($m['status']) ?></td>
                                            <td><?= format_date($m['created_at']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
