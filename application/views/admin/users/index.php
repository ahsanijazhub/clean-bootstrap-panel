<div class="content-header">
    <h1><?= htmlspecialchars($page_title ?? 'Users') ?></h1>
</div>

<div class="content">
    <!-- Filters -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filters</h3>
        </div>
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <select name="role_id" class="form-control">
                        <option value="">All Roles</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role->id ?>" <?= ($filters['role_id'] ?? '') == $role->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role->role_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($is_superadmin): ?>
                    <div class="col-md-4">
                        <select name="company_id" class="form-control">
                            <option value="">All Companies</option>
                            <?php foreach ($companies as $company): ?>
                                <option value="<?= $company->id ?>" <?= ($filters['company_id'] ?? '') == $company->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($company->company_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="col-md-<?= $is_superadmin ? '12' : '4' ?> mt-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="<?= site_url('users') ?>" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Users (<?= $total_users ?>)</h3>
            <?php if ($this->auth_lib->has_permission('users.create')): ?>
                <a href="<?= site_url('users/create') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add User
                </a>
            <?php endif; ?>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($user->first_name . ' ' . $user->last_name) ?></strong>
                                <?php if ($user->is_superadmin == 1): ?>
                                    <span class="badge badge-warning ml-1">Super Admin</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($user->email) ?>">
                                    <?= htmlspecialchars($user->email) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge" style="background: #6c757d; color: #fff; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">
                                    <?= htmlspecialchars($user->role_name ?? 'No Role') ?>
                                </span>
                            </td>
                            <td>
                                <?= htmlspecialchars($user->company_name ?? 'No Company') ?>
                            </td>
                            <td>
                                <?php if ($user->is_active == 1): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $user->last_login ? date('M d, Y H:i', strtotime($user->last_login)) : 'Never' ?>
                            </td>
                            <td>
                                <?php if ($this->auth_lib->has_permission('users.edit')): ?>
                                    <a href="<?= site_url('users/edit/'.$user->id) ?>" class="btn btn-sm btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if ($this->auth_lib->has_permission('users.delete') && $user->id != $this->session->userdata('user_id') && $user->is_superadmin != 1): ?>
                                    <button class="btn btn-sm btn-danger" title="Delete"
                                        onclick="Confirm.delete('Are you sure you want to delete this user?', () => {
                                            window.location.href='<?= site_url('users/delete/'.$user->id) ?>';
                                        })">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php elseif ($user->id == $this->session->userdata('user_id') || $user->is_superadmin == 1): ?>
                                    <button class="btn btn-sm btn-secondary" disabled title="Cannot delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 2rem; color: #6c757d;">
                            <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                            No users found. <a href="<?= site_url('users/create') ?>">Create one</a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="card-footer">
                <nav aria-label="Users pagination">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= site_url('users?page=' . $i . '&' . http_build_query($filters)) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>