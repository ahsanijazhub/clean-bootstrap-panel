<div class="content-header">
    <h1><?= htmlspecialchars($page_title ?? 'My Profile') ?></h1>
</div>

<div class="content">
    <div class="row">
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="user-avatar-large mx-auto">
                            <?= strtoupper(substr($user->first_name ?? 'U', 0, 1)) ?>
                        </div>
                    </div>
                    <h4><?= htmlspecialchars(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?></h4>
                    <p class="text-muted"><?= htmlspecialchars($user->role_name ?? '') ?></p>
                    <?php if ($user->is_superadmin == 1): ?>
                        <span class="badge badge-warning">Super Admin</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profile Information</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td style="width: 150px;"><strong>First Name</strong></td>
                                <td><?= htmlspecialchars($user->first_name ?? '') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Last Name</strong></td>
                                <td><?= htmlspecialchars($user->last_name ?? '') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td><?= htmlspecialchars($user->email ?? '') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Phone</strong></td>
                                <td><?= htmlspecialchars($user->phone ?? 'Not set') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Role</strong></td>
                                <td><?= htmlspecialchars($user->role_name ?? '') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>
                                    <?php if ($user->is_active == 1): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Last Login</strong></td>
                                <td><?= $user->last_login ? date('M d, Y H:i', strtotime($user->last_login)) : 'Never' ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>