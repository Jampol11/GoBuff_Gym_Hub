<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-file-earmark-lock2-fill me-2"></i>Legal Documents</h2>
            <p class="page-subtitle">Upload and manage gym legal documents and permits</p>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('/owner/documents/create') ?>" class="btn btn-primary">
                <i class="bi bi-upload me-1"></i>Upload Document
            </a>
        </div>
    </div>

    <?php if (!empty($expiring)): ?>
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>
            <strong><?= count($expiring) ?> document(s)</strong> are expiring within 30 days.
            <a href="#" class="alert-link">Review them now.</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="<?= base_url('/owner/documents') ?>" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Search by title, category, or description..."
                               value="<?= e($search) ?>">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search): ?>
                        <a href="<?= base_url('/owner/documents') ?>" class="btn btn-outline-secondary">Clear</a>
                    <?php endif; ?>
                </div>
                <div class="col-auto ms-auto">
                    <span class="text-muted small"><?= number_format($pagination['total']) ?> document(s)</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>File</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Confidential</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($docs)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                                    No documents found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($docs as $i => $doc): ?>
                            <?php
                                $isExpired = $doc['expiry_date'] && strtotime($doc['expiry_date']) < time();
                                $isExpiring = $doc['expiry_date'] && !$isExpired
                                    && strtotime($doc['expiry_date']) <= strtotime('+30 days');
                            ?>
                            <tr class="<?= $isExpired ? 'table-danger' : ($isExpiring ? 'table-warning' : '') ?>">
                                <td><?= $pagination['offset'] + $i + 1 ?></td>
                                <td>
                                    <div class="fw-semibold"><?= e($doc['title']) ?></div>
                                    <?php if ($doc['description']): ?>
                                        <small class="text-muted"><?= e(mb_strimwidth($doc['description'], 0, 60, '…')) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= e(ucwords(str_replace('_', ' ', $doc['category']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $ext = strtolower(pathinfo($doc['file_original'], PATHINFO_EXTENSION));
                                    $icon = match($ext) {
                                        'pdf'  => 'bi-file-earmark-pdf-fill text-danger',
                                        'doc', 'docx' => 'bi-file-earmark-word-fill text-primary',
                                        'jpg', 'jpeg', 'png' => 'bi-file-earmark-image-fill text-success',
                                        default => 'bi-file-earmark-fill text-secondary',
                                    };
                                    ?>
                                    <i class="bi <?= $icon ?> me-1"></i>
                                    <small><?= e($doc['file_original']) ?></small>
                                    <br><small class="text-muted"><?= number_format($doc['file_size'] / 1024, 1) ?> KB</small>
                                </td>
                                <td>
                                    <?php if ($doc['expiry_date']): ?>
                                        <span class="<?= $isExpired ? 'text-danger fw-semibold' : ($isExpiring ? 'text-warning fw-semibold' : '') ?>">
                                            <?= date('M d, Y', strtotime($doc['expiry_date'])) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">No expiry</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusBadge = match($doc['status']) {
                                        'active'   => 'success',
                                        'archived' => 'secondary',
                                        'expired'  => 'danger',
                                        default    => 'secondary',
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statusBadge ?>"><?= ucfirst($doc['status']) ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($doc['is_confidential']): ?>
                                        <i class="bi bi-lock-fill text-warning" title="Confidential"></i>
                                    <?php else: ?>
                                        <i class="bi bi-unlock text-muted" title="Not confidential"></i>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?= format_date($doc['created_at']) ?></small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('/owner/documents/' . $doc['id']) ?>"
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('/owner/documents/' . $doc['id'] . '/download') ?>"
                                           class="btn btn-outline-success" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <a href="<?= base_url('/owner/documents/' . $doc['id'] . '/edit') ?>"
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="<?= base_url('/owner/documents/' . $doc['id'] . '/delete') ?>"
                                              class="d-inline" onsubmit="return confirm('Delete this document? This cannot be undone.')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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
                of <?= $pagination['total'] ?> documents
            </small>
            <?= pagination_links($pagination, base_url('/owner/documents')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
