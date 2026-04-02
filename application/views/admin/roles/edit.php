<div class="content-header">
    <h1><?= htmlspecialchars($page_title ?? 'Edit Role') ?></h1>
</div>

<div class="content">
    <form action="<?= site_url('roles/update/'.$role->id) ?>" method="post">
        <div class="row">
            <!-- Role Details -->
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Role Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="role_name" class="form-control" required
                                   value="<?= htmlspecialchars($role->role_name) ?>"
                                   placeholder="e.g., Manager, Editor, Viewer">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($role->role_slug) ?>" disabled>
                            <small class="text-muted">Slug cannot be changed</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="role_description" class="form-control" rows="3"
                                      placeholder="Brief description of this role's responsibilities"><?= htmlspecialchars($role->role_description ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions -->
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Permissions</h3>
                        <div style="margin-left: auto;">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPermissions()">
                                Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllPermissions()">
                                Deselect All
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($permissions_grouped)): ?>
                            <div class="row">
                                <?php foreach ($permissions_grouped as $group_name => $permissions): ?>
                                    <div class="col-12 col-md-6 mb-4">
                                        <div class="permission-group">
                                            <h6 style="font-weight: 600; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid #e9ecef;">
                                                <i class="fas fa-folder text-primary"></i> <?= htmlspecialchars($group_name) ?>
                                                <button type="button" class="btn btn-sm" style="padding: 0; margin-left: 0.5rem; color: #6c757d;"
                                                        onclick="toggleGroup(this)">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            </h6>
                                            <?php foreach ($permissions as $perm): ?>
                                                <?php $is_checked = in_array($perm->id, $role_permissions ?? []); ?>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" class="form-check-input perm-checkbox"
                                                           name="permissions[]"
                                                           value="<?= $perm->id ?>"
                                                           id="perm_<?= $perm->id ?>"
                                                           <?= $is_checked ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="perm_<?= $perm->id ?>">
                                                        <?= htmlspecialchars($perm->perm_name) ?>
                                                        <small class="text-muted d-block"><?= htmlspecialchars($perm->perm_slug) ?></small>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center">
                                No permissions available. <a href="<?= site_url('permissions/create') ?>">Create some first</a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-footer d-flex justify-content-between">
                <a href="<?= site_url('roles') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Role
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function selectAllPermissions() {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
}

function deselectAllPermissions() {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
}

function toggleGroup(btn) {
    const group = btn.closest('.permission-group');
    const checkboxes = group.querySelectorAll('.perm-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
</script>
