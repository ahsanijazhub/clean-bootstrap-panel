<div class="content-header">
    <h1><?= htmlspecialchars($page_title ?? 'Add User') ?></h1>
</div>

<div class="content">
    <form action="<?= site_url('users/store') ?>" method="post">
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
                                           placeholder="Enter first name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" required
                                           placeholder="Enter last name">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required
                                   placeholder="Enter email address">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                   placeholder="Enter phone number">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required
                                   placeholder="Enter password (min 6 characters)">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" class="form-control" required
                                   placeholder="Confirm password">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Role & Status</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role_id" class="form-control" required>
                                <option value="">Select Role</option>
                                <?php foreach ($roles as $role): ?>
                                    <?php if ($role->role_slug == 'admin' && !$is_superadmin): continue; endif; ?>
                                    <option value="<?= $role->id ?>">
                                        <?= htmlspecialchars($role->role_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="is_active" value="1" id="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    Active User
                                </label>
                            </div>
                            <small class="form-text text-muted">Inactive users cannot login</small>
                        </div>
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
                    <i class="fas fa-save"></i> Create User
                </button>
            </div>
        </div>
    </form>
</div>