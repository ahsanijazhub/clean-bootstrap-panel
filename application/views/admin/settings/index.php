<div class="content-header">
    <h1><?= htmlspecialchars($page_title ?? 'Settings') ?></h1>
</div>

<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">General Settings</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Settings page for the admin panel. Common settings include:</p>
                    <ul>
                        <li>Site name</li>
                        <li>Logo</li>
                        <li>Email settings</li>
                        <li>Timezone</li>
                    </ul>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        To customize settings, edit <code>application/config/config.php</code> and <code>application/config/email.php</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>