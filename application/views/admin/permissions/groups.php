<div class="content-header">
    <h1><?= htmlspecialchars($page_title ?? 'Permission Groups') ?></h1>
</div>

<div class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Permission Groups</h3>
            <div style="margin-left: auto; display: flex; gap: 0.5rem;">
                <a href="<?= site_url('permissions') ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Permissions
                </a>
                <a href="<?= site_url('permissions/create_group') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Group
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Group Name</th>
                        <th>Slug</th>
                        <th>Permissions</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($groups)): ?>
                    <?php foreach ($groups as $group): ?>
                        <tr>
                            <td>
                                <strong><i class="fas fa-folder text-primary"></i> <?= htmlspecialchars($group->perm_group_name) ?></strong>
                            </td>
                            <td><code><?= htmlspecialchars($group->perm_group_slug) ?></code></td>
                            <td>
                                <span class="badge" style="background: #17a2b8; color: #fff; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">
                                    <?= $group->perm_count ?? 0 ?> permission(s)
                                </span>
                            </td>
                            <td>
                                <a href="<?= site_url('permissions/edit_group/'.$group->id) ?>" class="btn btn-sm btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if (($group->perm_count ?? 0) == 0): ?>
                                <button class="btn btn-sm btn-danger" title="Delete"
                                    onclick="Confirm.delete('Are you sure you want to delete this group?', () => {
                                        window.location.href='<?= site_url('permissions/delete_group/'.$group->id) ?>';
                                    })">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php else: ?>
                                <button class="btn btn-sm btn-secondary" disabled title="Cannot delete - has permissions">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 2rem; color: #6c757d;">
                            <i class="fas fa-folder-open" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                            No groups found. <a href="<?= site_url('permissions/create_group') ?>">Create one</a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
