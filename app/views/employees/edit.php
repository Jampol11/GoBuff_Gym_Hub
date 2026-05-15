<div class="container-fluid px-4 py-3">

    <a href="<?= base_url('/employees/' . $employee['id']) ?>" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i>Back to Profile
    </a>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person-badge-fill me-2 text-warning"></i>
                        Assign Job Role — <?= e($employee['first_name'] . ' ' . $employee['last_name']) ?>
                    </h5>
                    <p class="text-muted small mb-0 mt-1">
                        Update this employee's role, department, and work details. Changing the role will also update their system access.
                    </p>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= base_url('/employees/' . $employee['id'] . '/update') ?>">
                        <?= csrf_field() ?>

                        <!-- Job Role -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Job Role <span class="text-danger">*</span></label>
                            <select name="job_role" class="form-select" required>
                                <option value="">— Select Role —</option>
                                <?php
                                $roles = [
                                    'admin'       => 'Administrative Officer',
                                    'trainer'     => 'Fitness Trainer',
                                    'maintenance' => 'Maintenance Supervisor',
                                    'marketing'   => 'Marketing Officer',
                                    'gym_owner'   => 'Gym Owner',
                                ];
                                foreach ($roles as $val => $label):
                                ?>
                                <option value="<?= $val ?>" <?= $employee['job_role'] === $val ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Changing the role updates the employee's system access immediately after their next login.</div>
                        </div>

                        <!-- Department -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Department</label>
                            <input type="text" name="department" class="form-control"
                                   placeholder="e.g. Fitness, Operations, Marketing"
                                   value="<?= e($employee['department'] ?? '') ?>">
                        </div>

                        <!-- Specialization -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Specialization / Job Description</label>
                            <input type="text" name="specialization" class="form-control"
                                   placeholder="e.g. Strength & Conditioning, Equipment Repair"
                                   value="<?= e($employee['specialization'] ?? '') ?>">
                        </div>

                        <div class="row g-3">
                            <!-- Phone -->
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="phone" class="form-control"
                                       placeholder="09XXXXXXXXX"
                                       value="<?= e($employee['phone'] ?? '') ?>">
                            </div>

                            <!-- Hire Date -->
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Hire Date</label>
                                <input type="date" name="hire_date" class="form-control"
                                       value="<?= e($employee['hire_date'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Salary -->
                        <div class="mb-3 mt-3">
                            <label class="form-label fw-semibold">Monthly Salary (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" name="salary" class="form-control" step="0.01" min="0"
                                       placeholder="0.00"
                                       value="<?= $employee['salary'] !== null ? e($employee['salary']) : '' ?>">
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control" rows="2"
                                      placeholder="Employee's home address"><?= e($employee['address'] ?? '') ?></textarea>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Employment Status</label>
                            <select name="status" class="form-select">
                                <option value="active"   <?= $employee['status'] === 'active'   ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $employee['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="resigned" <?= $employee['status'] === 'resigned' ? 'selected' : '' ?>>Resigned</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-check-lg me-1"></i>Save Changes
                            </button>
                            <a href="<?= base_url('/employees/' . $employee['id']) ?>" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
