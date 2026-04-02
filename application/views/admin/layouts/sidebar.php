<!-- Main Sidebar -->
<aside class="main-sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <!-- <div class="sidebar-brand-icon">S</div> -->
        <img src="<?= base_url('assets/images/sozo-logo.webp') ?>" alt="Sozo Rent a Car : Comapanies Logo"
            class="sidebar-brand-logo" />
        <span class="sidebar-brand-text">SOZO Manager</span>
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
            <?php if (is_superadmin()): ?>
                <li class="nav-header">MANAGEMENT</li>

                <!-- Companies -->
                <li class="nav-item">
                    <a href="<?= site_url('companies') ?>"
                        class="nav-link <?= ($this->uri->segment(1) == 'companies' && $this->uri->segment(2) != 'my-company') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-building"></i></span>
                        <span class="nav-link-text">Companies</span>
                    </a>
                </li>
            <?php else: ?>
                <!-- My Company (for company admins) -->
                <li class="nav-header">MANAGEMENT</li>

                <li class="nav-item">
                    <a href="<?= site_url('companies/my-company') ?>"
                        class="nav-link <?= ($this->uri->segment(2) == 'my-company') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-building"></i></span>
                        <span class="nav-link-text">My Company</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Vehicles (for both superadmin and company admin) -->
            <li class="nav-item">
                <a href="<?= site_url('vehicles') ?>"
                    class="nav-link <?= ($this->uri->segment(1) == 'vehicles') ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-car"></i></span>
                    <span class="nav-link-text">Vehicles</span>
                </a>
            </li>

            <!-- Customers (for both superadmin and company admin) -->
            <li class="nav-item">
                <a href="<?= site_url('customers') ?>"
                    class="nav-link <?= ($this->uri->segment(1) == 'customers') ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-users"></i></span>
                    <span class="nav-link-text">Customers</span>
                </a>
            </li>

            <!-- Invoices -->
            <li class="nav-item">
                <a href="<?= site_url('invoices') ?>"
                    class="nav-link <?= ($this->uri->segment(1) == 'invoices') ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-file-invoice"></i></span>
                    <span class="nav-link-text">Invoices</span>
                </a>
            </li>

            <!-- Agreement Templates -->
            <?php if (has_permission('agreement-templates.view')): ?>
            <li class="nav-item">
                <a href="<?= site_url('agreement-templates') ?>"
                    class="nav-link <?= ($this->uri->segment(1) == 'agreement-templates') ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-file-contract"></i></span>
                    <span class="nav-link-text">Agreement Templates</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Accidents -->
            <?php if (has_permission('accidents.view')): ?>
            <li class="nav-item">
                <a href="<?= site_url('accidents') ?>"
                    class="nav-link <?= ($this->uri->segment(1) == 'accidents') ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-car-crash"></i></span>
                    <span class="nav-link-text">Accidents</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Rental Agreements -->
            <?php if (has_permission('rental-agreements.view')): ?>
            <li class="nav-item <?= ($this->uri->segment(1) == 'rental-agreements' && in_array($this->uri->segment(2), ['index','all', 'view', 'create', 'store', 'edit', 'update', 'delete'])) ? 'menu-open' : '' ?>">
                <a href="#" class="nav-link" data-toggle="submenu">
                    <span class="nav-icon"><i class="fas fa-file-signature"></i></span>
                    <span class="nav-link-text">Rental Agreements</span>
                    <span class="nav-arrow"><i class="fas fa-angle-right"></i></span>
                    <?php
                    // Show badge with pending review count
                    $CI =& get_instance();
                    $CI->load->model('Rental_agreement_model');
                    $company_id = is_superadmin() ? null : (current_user()->company_id ?? null);
                    $signed_count = $CI->Rental_agreement_model->count_by_status('signed', $company_id);
                    if ($signed_count > 0):
                    ?>
                    <span class="nav-badge" style="background: #ffc107; color: #000;"><?= $signed_count ?></span>
                    <?php endif; ?>
                </a>

                <ul class="nav-treeview">

                    <!-- View All Rental Agreements -->
                    <li class="nav-item">
                        <a href="<?= site_url('rental-agreements/all') ?>"
                            class="nav-link <?= ($this->uri->segment(1) == 'rental-agreements' && ($this->uri->segment(2) == 'all' || $this->uri->segment(2) == 'index' || $this->uri->segment(2) == 'view')) ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-list"></i></span>
                            <span class="nav-link-text">View All Agreements</span>
                        </a>
                    </li>

                    <!-- Create Agreement -->
                    <?php if (has_permission('rental-agreements.create')): ?>
                    <li class="nav-item">
                        <a href="<?= site_url('rental-agreements/create') ?>"
                            class="nav-link <?= ($this->uri->segment(1) == 'rental-agreements' && ($this->uri->segment(2) == 'create' || $this->uri->segment(2) == 'store')) ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-plus-circle"></i></span>
                            <span class="nav-link-text">Create Agreement</span>
                        </a>
                    </li>
                    <?php endif; ?>

                </ul>
            </li>
            <?php endif; ?>

            <!-- COMMENTED OUT: Vehicle Assignment Section -->

            <!-- COMMENTED OUT: User Management Section -->

            <!-- COMMENTED OUT: Reports Section -->

            <!-- COMMENTED OUT: Settings Section -->

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