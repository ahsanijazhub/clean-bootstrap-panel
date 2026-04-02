<div class="content-header">
    <h1><?= htmlspecialchars($page_title ?? 'Permissions') ?></h1>
</div>

<div class="content">
    <!-- Action Buttons -->
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="<?= site_url('permissions/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Permission
        </a>
        <a href="<?= site_url('permissions/groups') ?>" class="btn btn-info">
            <i class="fas fa-folder"></i> Manage Groups
        </a>
    </div>

    <!-- Permissions by Group -->
    <?php if (!empty($permissions_grouped)): ?>
        <?php foreach ($permissions_grouped as $group_name => $permissions): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-folder text-primary"></i> <?= htmlspecialchars($group_name) ?>
                        <span class="badge" style="background: #6c757d; color: #fff; padding: 0.25rem 0.5rem; border-radius: 0.25rem; margin-left: 0.5rem;">
                            <?= count($permissions) ?>
                        </span>
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Permission Name</th>
                                <th>Slug</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($permissions as $perm): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($perm->perm_name) ?></strong></td>
                                    <td><code><?= htmlspecialchars($perm->perm_slug) ?></code></td>
                                    <td>
                                        <a href="<?= site_url('permissions/edit/'.$perm->id) ?>" class="btn btn-sm btn-info" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" title="Delete"
                                            onclick="Confirm.delete('Are you sure you want to delete this permission? This will also remove it from all roles.', () => {
                                                window.location.href='<?= site_url('permissions/delete/'.$perm->id) ?>';
                                            })">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center" style="padding: 3rem;">
                <i class="fas fa-key" style="font-size: 3rem; color: #6c757d; margin-bottom: 1rem;"></i>
                <h5>No Permissions Found</h5>
                <p class="text-muted">Start by creating permission groups, then add permissions.</p>
                <a href="<?= site_url('permissions/groups') ?>" class="btn btn-info">
                    <i class="fas fa-folder"></i> Manage Groups
                </a>
                <a href="<?= site_url('permissions/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Permission
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
