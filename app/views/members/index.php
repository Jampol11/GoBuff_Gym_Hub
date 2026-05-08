<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">Members</h2>
            <p class="page-subtitle">Manage all gym members</p>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('/members/export') ?>" class="btn btn-outline-success">
                <i class="bi bi-download me-1"></i>Export CSV
            </a>
            <?php if (has_role(['gym_owner','admin'])): ?>
            <a href="<?= base_url('/members/create') ?>" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-1"></i>Add Member
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="<?= base_url('/members') ?>" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Search by name, email, or membership ID..."
                               value="<?= e($search) ?>">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search): ?>
                        <a href="<?= base_url('/members') ?>" class="btn btn-outline-secondary">Clear</a>
                    <?php endif; ?>
                </div>
                <div class="col-auto ms-auto">
                    <span class="text-muted small">
                        <?= number_format($pagination['total']) ?> member(s) found
                    </span>
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
                        <tr>
                            <th>#</th>
                            <th>Member</th>
                            <th>Membership ID</th>
                            <th>Contact</th>
                            <th>Gender</th>
                            <th>Membership</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    No members found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($members as $i => $m): ?>
                                <tr>
                                    <td><?= $pagination['offset'] + $i + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                                <?= strtoupper(substr($m['first_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></div>
                                                <small class="text-muted"><?= e($m['email'] ?? '') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><code><?= e($m['membership_id']) ?></code></td>
                                    <td><?= e($m['phone'] ?? 'N/A') ?></td>
                                    <td><?= ucfirst(e($m['gender'] ?? '')) ?></td>
                                    <td>
                                        <?php if (!empty($m['plan_name'])): ?>
                                            <span class="badge bg-primary"><?= e($m['plan_name']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= status_badge($m['status'] ?? 'active') ?></td>
                                    <td><small><?= format_date($m['created_at']) ?></small></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('/members/' . $m['id']) ?>"
                                               class="btn btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if (has_role(['gym_owner','admin'])): ?>
                                            <a href="<?= base_url('/members/' . $m['id'] . '/edit') ?>"
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="<?= base_url('/members/' . $m['id'] . '/delete') ?>"
                                                  class="d-inline" onsubmit="return confirm('Delete this member?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
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
            <small class="text-muted">
                Showing <?= $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?>
                of <?= $pagination['total'] ?> members
            </small>
            <?= pagination_links($pagination, base_url('/members')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
