    <!-- Content Header -->
    <div class="content-header">
        <h1><?= htmlspecialchars($page_title ?? 'Dashboard') ?></h1>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Welcome Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Welcome, <?= htmlspecialchars($current_user->first_name ?? 'Admin') ?>!</h3>
                    </div>
                    <div class="card-body">
                        <p>Welcome to the Admin Panel. Use the sidebar to navigate through the system.</p>

                        <div class="row mt-4">
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="card" style="border: 1px solid #e9ecef;">
                                    <div class="card-body">
                                        <h5 class="card-title" style="font-size: 1rem;">
                                            <i class="fas fa-users text-primary"></i> Users
                                        </h5>
                                        <p class="text-muted" style="font-size: 0.875rem;">Manage system users and their access.</p>
                                        <?php if (has_permission('users.view')): ?>
                                        <a href="<?= site_url('users') ?>" class="btn btn-sm btn-outline-primary">Manage Users</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="card" style="border: 1px solid #e9ecef;">
                                    <div class="card-body">
                                        <h5 class="card-title" style="font-size: 1rem;">
                                            <i class="fas fa-user-tag text-success"></i> Roles
                                        </h5>
                                        <p class="text-muted" style="font-size: 0.875rem;">Define user roles and permissions.</p>
                                        <?php if (has_permission('roles.view')): ?>
                                        <a href="<?= site_url('roles') ?>" class="btn btn-sm btn-outline-success">Manage Roles</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="card" style="border: 1px solid #e9ecef;">
                                    <div class="card-body">
                                        <h5 class="card-title" style="font-size: 1rem;">
                                            <i class="fas fa-shield-alt text-info"></i> Permissions
                                        </h5>
                                        <p class="text-muted" style="font-size: 0.875rem;">Configure fine-grained permissions.</p>
                                        <?php if (has_permission('permissions.view')): ?>
                                        <a href="<?= site_url('permissions') ?>" class="btn btn-sm btn-outline-info">Manage Permissions</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Account Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td><strong>Name</strong></td>
                                    <td><?= htmlspecialchars(($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? '')) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td><?= htmlspecialchars($current_user->email ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Role</strong></td>
                                    <td><span style="background: #6c757d; color: #fff; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;"><?= htmlspecialchars($current_user->role_name ?? 'N/A') ?></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Last Login</strong></td>
                                    <td><?= $current_user->last_login ? date('M d, Y H:i', strtotime($current_user->last_login)) : 'N/A' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <?php if (has_permission('users.create')): ?>
                            <a href="<?= site_url('users/create') ?>" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Add User
                            </a>
                            <?php endif; ?>
                            <?php if (has_permission('roles.create')): ?>
                            <a href="<?= site_url('roles/create') ?>" class="btn btn-success">
                                <i class="fas fa-user-tag"></i> Add Role
                            </a>
                            <?php endif; ?>
                            <?php if (has_permission('settings.view')): ?>
                            <a href="<?= site_url('settings') ?>" class="btn btn-secondary">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>