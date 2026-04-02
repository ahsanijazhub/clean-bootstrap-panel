<div class="content-header">
    <h1><?= htmlspecialchars($page_title ?? 'Roles') ?></h1>
</div>

<div class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Roles</h3>
            <a href="<?= site_url('roles/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Role
            </a>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Users</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($roles)): ?>
                    <?php foreach ($roles as $role): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($role->role_name) ?></strong>
                            </td>
                            <td>
                                <code><?= htmlspecialchars($role->role_slug) ?></code>
                            </td>
                            <td><?= htmlspecialchars($role->role_description ?? '-') ?></td>
                            <td>
                                <span class="badge" style="background: #6c757d; color: #fff; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">
                                    <?= $role->user_count ?? 0 ?> user(s)
                                </span>
                            </td>
                            <td>
                                <a href="<?= site_url('roles/edit/'.$role->id) ?>" class="btn btn-sm btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if (($role->user_count ?? 0) == 0): ?>
                                <button class="btn btn-sm btn-danger" title="Delete"
                                    onclick="Confirm.delete('Are you sure you want to delete this role?', () => {
                                        window.location.href='<?= site_url('roles/delete/'.$role->id) ?>';
                                    })">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php else: ?>
                                <button class="btn btn-sm btn-secondary" disabled title="Cannot delete - has users">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 2rem; color: #6c757d;">
                            <i class="fas fa-user-shield" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                            No roles found. <a href="<?= site_url('roles/create') ?>">Create one</a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
