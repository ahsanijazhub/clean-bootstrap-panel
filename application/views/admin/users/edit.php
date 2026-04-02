<div class="content-header">
    <h1><?= htmlspecialchars($page_title ?? 'Edit User') ?></h1>
</div>

<div class="content">
    <form action="<?= site_url('users/update/' . $user->id) ?>" method="post">
        <div class="row">
            <!-- User Details -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">User Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" required
                                           placeholder="Enter first name" value="<?= htmlspecialchars($user->first_name) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" required
                                           placeholder="Enter last name" value="<?= htmlspecialchars($user->last_name) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required
                                   placeholder="Enter email address" value="<?= htmlspecialchars($user->email) ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                   placeholder="Enter phone number" value="<?= htmlspecialchars($user->phone ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Leave empty to keep current password">
                            <small class="form-text text-muted">Leave empty to keep current password</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control"
                                   placeholder="Confirm new password">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role & Company -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Role & Company</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role_id" class="form-control" required>
                                <option value="">Select Role</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role->id ?>" <?= $user->role_id == $role->id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($role->role_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($is_superadmin): ?>
                            <div class="form-group">
                                <label class="form-label">Company</label>
                                <select name="company_id" class="form-control">
                                    <option value="">No Company (Super Admin)</option>
                                    <?php foreach ($companies as $company): ?>
                                        <option value="<?= $company->id ?>" <?= $user->company_id == $company->id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($company->company_name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Leave empty for super admin users</small>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="company_id" value="<?= $current_user_company_id ?>">
                            <div class="form-group">
                                <label class="form-label">Company</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($companies[0]->company_name ?? 'No Company') ?>" readonly>
                                <small class="form-text text-muted">Users must belong to your company</small>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="is_active" value="1" id="is_active"
                                       <?= $user->is_active == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    Active User
                                </label>
                            </div>
                            <small class="form-text text-muted">Inactive users cannot login</small>
                        </div>

                        <?php if ($user->is_superadmin == 1): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                This is a super admin user. Some restrictions may apply.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-footer d-flex justify-content-between">
                <a href="<?= site_url('users') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update User
                </button>
            </div>
        </div>
    </form>
</div>