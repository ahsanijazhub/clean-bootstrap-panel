<div class="content-header">
    <h1><?= htmlspecialchars($page_title ?? 'Add Permission Group') ?></h1>
</div>

<div class="content">
    <form action="<?= site_url('permissions/store_group') ?>" method="post">
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
                                   placeholder="e.g., Users, Posts, Settings"
                                   id="group_name">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Group Slug</label>
                            <input type="text" name="perm_group_slug" class="form-control"
                                   placeholder="Auto-generated from name"
                                   pattern="[a-z0-9\-_]+"
                                   title="Only lowercase letters, numbers, dashes, and underscores"
                                   id="group_slug">
                            <small class="text-muted">Leave empty to auto-generate from name</small>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="<?= site_url('permissions/groups') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Group
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('group_name').addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[^a-z0-9\s\-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    document.getElementById('group_slug').placeholder = slug || 'Auto-generated from name';
});
</script>
