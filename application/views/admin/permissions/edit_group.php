<div class="content-header">
    <h1><?= htmlspecialchars($page_title ?? 'Edit Permission Group') ?></h1>
</div>

<div class="content">
    <form action="<?= site_url('permissions/update_group/'.$group->id) ?>" method="post">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Group Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Group Name <span class="text-danger">*</span></label>
                            <input type="text" name="perm_group_name" class="form-control" required
                                   value="<?= htmlspecialchars($group->perm_group_name) ?>"
                                   placeholder="e.g., Users, Posts, Settings">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Group Slug</label>
                            <input type="text" class="form-control"
                                   value="<?= htmlspecialchars($group->perm_group_slug) ?>" disabled>
                            <small class="text-muted">Slug cannot be changed</small>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="<?= site_url('permissions/groups') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Group
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
