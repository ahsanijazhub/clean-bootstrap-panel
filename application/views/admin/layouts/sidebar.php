<!-- Main Sidebar -->
<aside class="main-sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <!-- <div class="sidebar-brand-icon">S</div> -->
        <img src="<?= base_url('assets/images/Logo-Transparent.webp') ?>" alt="Admin Logo"
            class="sidebar-brand-logo" />
        <span class="sidebar-brand-text">Admin Panel</span>
    </div>

    <!-- Sidebar Content -->
    <div class="sidebar-content">
        <ul class="nav-sidebar">

            <!-- ================= MAIN ================= -->
            <li class="nav-header">MAIN</li>

            <li class="nav-item">
                <a href="<?= site_url('dashboard') ?>"
                    class="nav-link <?= ($this->uri->segment(1) == 'dashboard' || $this->uri->segment(1) == '') ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>

            <!-- ================= MANAGEMENT ================= -->
            <li class="nav-header">MANAGEMENT</li>

            <!-- Users -->
            <?php if (has_permission('users.view')): ?>
            <li class="nav-item">
                <a href="<?= site_url('users') ?>"
                    class="nav-link <?= ($this->uri->segment(1) == 'users') ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-users"></i></span>
                    <span class="nav-link-text">Users</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Roles -->
            <?php if (has_permission('roles.view')): ?>
            <li class="nav-item">
                <a href="<?= site_url('roles') ?>"
                    class="nav-link <?= ($this->uri->segment(1) == 'roles') ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-user-tag"></i></span>
                    <span class="nav-link-text">Roles</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Permissions -->
            <?php if (has_permission('permissions.view')): ?>
            <li class="nav-item">
                <a href="<?= site_url('permissions') ?>"
                    class="nav-link <?= ($this->uri->segment(1) == 'permissions') ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-shield-alt"></i></span>
                    <span class="nav-link-text">Permissions</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Settings -->
            <?php if (has_permission('settings.view')): ?>
            <li class="nav-item">
                <a href="<?= site_url('settings') ?>"
                    class="nav-link <?= ($this->uri->segment(1) == 'settings') ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-cog"></i></span>
                    <span class="nav-link-text">Settings</span>
                </a>
            </li>
            <?php endif; ?>

        </ul>
    </div>
</aside>

<!-- Content Wrapper -->
<div class="content-wrapper">

    <!-- Header -->
    <header class="main-header">
        <div class="header-left">
            <button type="button" class="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Breadcrumb -->
            <nav class="breadcrumb d-none d-sm-flex">
                <?php if (!empty($breadcrumb)): ?>
                    <?php foreach ($breadcrumb as $index => $crumb): ?>
                        <?php if ($index === count($breadcrumb) - 1): ?>
                            <span class="breadcrumb-item"><?= htmlspecialchars($crumb['title']) ?></span>
                        <?php else: ?>
                            <span class="breadcrumb-item">
                                <a href="<?= $crumb['url'] ?>"><?= htmlspecialchars($crumb['title']) ?></a>
                            </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav>
        </div>

        <!-- User Menu -->
        <div class="header-right">
            <div class="user-menu">
                <button type="button" class="user-menu-toggle">
                    <div class="user-avatar">
                        <?= strtoupper(substr($current_user->first_name ?? 'U', 0, 1)) ?>
                    </div>
                    <span class="user-name">
                        <?= htmlspecialchars(($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? '')) ?>
                    </span>
                    <i class="fas fa-chevron-down" style="font-size: 0.75rem;"></i>
                </button>

                <div class="user-menu-dropdown">
                    <a href="<?= site_url('profile') ?>">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>

                    <a href="<?= site_url('settings') ?>">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>

                    <div class="dropdown-divider"></div>

                    <a href="<?= site_url('logout') ?>" class="text-danger">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </header>