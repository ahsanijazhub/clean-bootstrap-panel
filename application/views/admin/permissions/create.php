<div class="content-header">
    <h1><?= htmlspecialchars($page_title ?? 'Add Permission') ?></h1>
</div>

<div class="content">
    <form action="<?= site_url('permissions/store') ?>" method="post">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Permission Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Permission Group <span class="text-danger">*</span></label>
                            <?php if (!empty($groups)): ?>
                                <select name="permission_group_id" class="form-control" required>
                                    <option value="">-- Select Group --</option>
                                    <?php foreach ($groups as $group): ?>
                                        <option value="<?= $group->id ?>">
                                            <?= htmlspecialchars($group->perm_group_name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <p class="text-danger">
                                    No groups available. <a href="<?= site_url('permissions/create_group') ?>">Create a group first</a>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Permission Name <span class="text-danger">*</span></label>
                            <input type="text" name="perm_name" class="form-control" required
                                   placeholder="e.g., View Users, Create Posts">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Permission Slug <span class="text-danger">*</span></label>
                            <input type="text" name="perm_slug" class="form-control" required
                                   placeholder="e.g., users.view, posts.create"
                                   pattern="[a-z0-9\.\-_]+"
                                   title="Only lowercase letters, numbers, dots, dashes, and underscores">
                            <small class="text-muted">Format: group.action (e.g., users.view, users.create)</small>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="<?= site_url('permissions') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary" <?= empty($groups) ? 'disabled' : '' ?>>
                            <i class="fas fa-save"></i> Create Permission
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
