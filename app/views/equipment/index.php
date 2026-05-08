<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Equipment</h2><p class="page-subtitle">Gym equipment inventory and status</p></div>
        <div class="page-actions">
            <a href="<?= base_url('/equipment/export') ?>" class="btn btn-outline-success"><i class="bi bi-download me-1"></i>Export</a>
            <?php if (has_role(['gym_owner','admin','maintenance'])): ?>
            <a href="<?= base_url('/equipment/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-1"></i>Add Equipment</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Summary -->
    <div class="row g-3 mb-4">
        <?php foreach ($status_counts as $sc): ?>
            <div class="col-sm-4">
                <div class="card text-center border-<?= $sc['condition_status'] === 'good' ? 'success' : ($sc['condition_status'] === 'needs_repair' ? 'warning' : 'info') ?>">
                    <div class="card-body py-3">
                        <div class="fs-2 fw-bold text-<?= $sc['condition_status'] === 'good' ? 'success' : ($sc['condition_status'] === 'needs_repair' ? 'warning' : 'info') ?>">
                            <?= $sc['count'] ?>
                        </div>
                        <div class="text-muted small"><?= ucwords(str_replace('_', ' ', $sc['condition_status'])) ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="<?= base_url('/equipment') ?>" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Search equipment..." value="<?= e($search) ?>">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search): ?><a href="<?= base_url('/equipment') ?>" class="btn btn-outline-secondary">Clear</a><?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr><th>#</th><th>Name</th><th>Brand/Model</th><th>Category</th><th>Location</th><th>Status</th><th>Purchase Date</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($equipment)): ?>
                            <tr><td colspan="8" class="text-center py-5 text-muted">No equipment found</td></tr>
                        <?php else: ?>
                            <?php foreach ($equipment as $i => $eq): ?>
                                <tr>
                                    <td><?= $pagination['offset'] + $i + 1 ?></td>
                                    <td class="fw-semibold"><?= e($eq['name']) ?></td>
                                    <td><?= e($eq['brand'] ?? '') ?> <?= e($eq['model'] ?? '') ?></td>
                                    <td><?= e($eq['category'] ?? '') ?></td>
                                    <td><?= e($eq['location'] ?? '') ?></td>
                                    <td><?= status_badge($eq['condition_status']) ?></td>
                                    <td><?= format_date($eq['purchase_date']) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('/equipment/' . $eq['id']) ?>" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                            <?php if (has_role(['gym_owner','admin','maintenance'])): ?>
                                            <a href="<?= base_url('/equipment/' . $eq['id'] . '/edit') ?>" class="btn btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                            <form method="POST" action="<?= base_url('/equipment/' . $eq['id'] . '/delete') ?>"
                                                  class="d-inline" onsubmit="return confirm('Delete this equipment?')">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?></small>
            <?= pagination_links($pagination, base_url('/equipment')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
