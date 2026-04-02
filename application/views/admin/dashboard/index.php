    <!-- Content Header -->
    <div class="content-header">
        <h1><?= htmlspecialchars($page_title ?? 'Dashboard') ?></h1>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- DASHBOARD -->
        <!-- Stats Row -->
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="<?= site_url('customers') ?>" class="info-box">
                    <span class="info-box-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Customers</span>
                        <span class="info-box-number"><?= number_format($stats['total_customers'] ?? 0) ?></span>
                    </div>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <a href="<?= site_url('rental-agreements/all?status=active') ?>" class="info-box">
                    <span class="info-box-icon bg-danger">
                        <i class="fas fa-car"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Rented Cars</span>
                        <span class="info-box-number"><?= number_format($stats['rented_cars'] ?? 0) ?></span>
                    </div>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <a href="<?= site_url('vehicles?is_available=1') ?>" class="info-box">
                    <span class="info-box-icon bg-success">
                        <i class="fas fa-car"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Available Cars</span>
                        <span class="info-box-number"><?= number_format($stats['available_cars'] ?? 0) ?></span>
                    </div>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <a href="<?= site_url('vehicles?vehicle_type=repair') ?>" class="info-box">
                    <span class="info-box-icon bg-warning">
                        <i class="fas fa-wrench"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Repair/Accident Cars</span>
                        <span class="info-box-number"><?= number_format($stats['repair_accident_cars'] ?? 0) ?></span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Stats Row 2 -->
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="<?= site_url('vehicles') ?>" class="info-box">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-car"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Cars</span>
                        <span class="info-box-number"><?= number_format($stats['total_cars'] ?? 0) ?></span>
                    </div>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <a href="<?= site_url('rental-agreements/all') ?>" class="info-box">
                    <span class="info-box-icon bg-secondary">
                        <i class="fas fa-file-invoice"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Invoices</span>
                        <span class="info-box-number"><?= number_format($stats['total_invoices'] ?? 0) ?></span>
                    </div>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <a href="<?= site_url('rental-agreements/all?status=pending') ?>" class="info-box">
                    <span class="info-box-icon" style="background-color: #fd7e14;">
                        <i class="fas fa-exclamation-circle"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Outstanding Invoices</span>
                        <span class="info-box-number"><?= number_format($stats['outstanding_invoices'] ?? 0) ?></span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Welcome Card (shown for both Super Admin and Company Admin) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Welcome, <?= htmlspecialchars($current_user->first_name ?? 'Admin') ?>!</h3>
                    </div>
                    <div class="card-body">
                        <p>Welcome to your company dashboard. Use the sidebar to manage your company's resources.</p>

                        <div class="row mt-4">
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="card" style="border: 1px solid #e9ecef;">
                                    <div class="card-body">
                                        <h5 class="card-title" style="font-size: 1rem;">
                                            <i class="fas fa-users text-primary"></i> Customers
                                        </h5>
                                        <p class="text-muted" style="font-size: 0.875rem;">Manage your customers and their profiles.</p>
                                        <a href="<?= site_url('customers') ?>" class="btn btn-sm btn-outline-primary">Manage Customers</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="card" style="border: 1px solid #e9ecef;">
                                    <div class="card-body">
                                        <h5 class="card-title" style="font-size: 1rem;">
                                            <i class="fas fa-car text-success"></i> Vehicles
                                        </h5>
                                        <p class="text-muted" style="font-size: 0.875rem;">Manage your fleet of vehicles.</p>
                                        <a href="<?= site_url('vehicles') ?>" class="btn btn-sm btn-outline-success">Manage Vehicles</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="card" style="border: 1px solid #e9ecef;">
                                    <div class="card-body">
                                        <h5 class="card-title" style="font-size: 1rem;">
                                            <i class="fas fa-file-contract text-info"></i> Rental Agreements
                                        </h5>
                                        <p class="text-muted" style="font-size: 0.875rem;">View and manage rental agreements.</p>
                                        <a href="<?= site_url('rental-agreements/all') ?>" class="btn btn-sm btn-outline-info">Manage Agreements</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="card" style="border: 1px solid #e9ecef;">
                                    <div class="card-body">
                                        <h5 class="card-title" style="font-size: 1rem;">
                                            <i class="fas fa-tasks text-warning"></i> Agreement Templates
                                        </h5>
                                        <p class="text-muted" style="font-size: 0.875rem;">Create and manage agreement templates.</p>
                                        <a href="<?= site_url('agreement-templates') ?>" class="btn btn-sm btn-outline-warning">Manage Templates</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Account Info (for all users) -->
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= site_url('customers/create') ?>" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Add Customer
                            </a>
                            <a href="<?= site_url('vehicles/create') ?>" class="btn btn-success">
                                <i class="fas fa-car"></i> Add Vehicle
                            </a>
                            <a href="<?= site_url('rental-agreements/create') ?>" class="btn btn-info">
                                <i class="fas fa-file-contract"></i> Create Agreement
                            </a>
                            <a href="<?= site_url('reports') ?>" class="btn btn-secondary">
                                <i class="fas fa-chart-line"></i> View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>

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
                                    <td>
                                        <?php if (isset($current_user) && $current_user->is_superadmin == 1): ?>
                                            <span style="background: #28a745; color: #fff; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">Super Admin</span>
                                        <?php else: ?>
                                            <span style="background: #6c757d; color: #fff; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;"><?= htmlspecialchars($current_user->role_name ?? 'N/A') ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if (isset($current_user) && $current_user->is_superadmin != 1): ?>
                                <tr>
                                    <td><strong>Company</strong></td>
                                    <td><?= htmlspecialchars($current_user->company_name ?? 'N/A') ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td><strong>Last Login</strong></td>
                                    <td><?= $current_user->last_login ? date('M d, Y H:i', strtotime($current_user->last_login)) : 'N/A' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Vehicles Section (for both superadmin and admin) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title"><i class="fas fa-car"></i> Fleet Overview - All Vehicles</h3>
                        <span class="badge badge-light"><?= count($all_vehicles ?? []) ?></span>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <?php if (!empty($all_vehicles)): ?>
                            <table class="table table-bordered table-striped table-sm" id="vehiclesTable">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Vehicle Name</th>
                                        <th>License Plate</th>
                                        <th>Make & Model</th>
                                        <th>Type</th>
                                        <?php if (is_superadmin()): ?>
                                        <th>Company</th>
                                        <?php endif; ?>
                                        <th>Assigned Customer</th>
                                        <th>Agreement #</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_vehicles as $v): ?>
                                    <tr>
                                        <td>
                                            <?php if ($v->is_available): ?>
                                                <span class="badge badge-success">Available</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Rented</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($v->vehicle_name) ?></td>
                                        <td><span class="badge badge-info"><?= htmlspecialchars($v->license_plate) ?></span></td>
                                        <td><?= htmlspecialchars($v->vehicle_make . ' ' . $v->vehicle_model) ?></td>
                                        <td><?= htmlspecialchars($v->vehicle_type) ?></td>
                                        <?php if (is_superadmin()): ?>
                                        <td><?= htmlspecialchars($v->company_name ?? '-') ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <?php if ($v->customer_name): ?>
                                                <a href="javascript:void(0)" onclick="viewCustomerDetails(<?= $v->customer_id ?>)">
                                                    <?= htmlspecialchars($v->customer_name) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($v->agreement_number ?? '-') ?></td>
                                        <td>
                                            <?php if ($v->customer_id): ?>
                                                <button class="btn btn-sm btn-info" onclick="viewCustomerDetails(<?= $v->customer_id ?>)">
                                                    <i class="fas fa-eye"></i> View Customer
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted text-center">No vehicles found</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Details Modal -->
    <div class="modal fade" id="customerDetailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="customerModalContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Loading...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function viewCustomerDetails(customerId) {
        if (!customerId) {
            Toast.error("Customer not found");
            return;
        }
        
        $('#customerDetailsModal').modal('show');
        
        fetch('<?= site_url('dashboard/get_customer_details') ?>?customer_id=' + customerId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let content = '';
                    
                    // Customer Info
                    content += '<div class="row">' +
                        '<div class="col-md-6">' +
                        '<h5>Customer Information</h5>' +
                        '<table class="table table-bordered">' +
                        '<tr><td><strong>Name</strong></td><td>' + (data.customer.customer_name || '-') + '</td></tr>' +
                        '<tr><td><strong>Email</strong></td><td>' + (data.customer.email || '-') + '</td></tr>' +
                        '<tr><td><strong>Phone</strong></td><td>' + (data.customer.phone || '-') + '</td></tr>' +
                        '<tr><td><strong>Company</strong></td><td>' + (data.customer.company_name || '-') + '</td></tr>' +
                        '<tr><td><strong>Profile Status</strong></td><td>' + (data.customer.is_profile_completed ? '<span class="badge badge-success">Completed</span>' : '<span class="badge badge-warning">Pending</span>') + '</td></tr>' +
                        '</table>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                        '<h5>Driving License</h5>' +
                        '<div class="row">';
                    
                    if (data.customer.driving_license_front) {
                        content += '<div class="col-6">' +
                            '<p><strong>Front</strong></p>' +
                            '<img src="<?= base_url("") ?>' + data.customer.driving_license_front + '" class="img-fluid" style="max-height: 150px;">' +
                            '</div>';
                    }
                    
                    if (data.customer.driving_license_back) {
                        content += '<div class="col-6">' +
                            '<p><strong>Back</strong></p>' +
                            '<img src="<?= base_url("") ?>' + data.customer.driving_license_back + '" class="img-fluid" style="max-height: 150px;">' +
                            '</div>';
                    }
                    
                    if (!data.customer.driving_license_front && !data.customer.driving_license_back) {
                        content += '<div class="col-12"><p class="text-muted">No driving license uploaded</p></div>';
                    }
                    
                    content += '</div></div></div>';
                    
                    // Action Buttons
                    content += '<div class="row mt-3">' +
                        '<div class="col-12">' +
                        '<a href="<?= site_url('customers/view/') ?>' + customerId + '" class="btn btn-primary">' +
                        '<i class="fas fa-eye"></i> View Full Details' +
                        '</a> ' +
                        '<a href="<?= site_url('customers/edit/') ?>' + customerId + '" class="btn btn-warning">' +
                        '<i class="fas fa-edit"></i> Edit Customer' +
                        '</a>' +
                        '</div></div>';
                    
                    // Rental Agreements
                    if (data.agreements && data.agreements.length > 0) {
                        content += '<div class="row mt-3">' +
                            '<div class="col-12">' +
                            '<h5>Rental Agreements</h5>' +
                            '<table class="table table-bordered table-sm">' +
                            '<thead><tr><th>Agreement #</th><th>Vehicle</th><th>Status</th><th>Signed At</th><th>Actions</th></tr></thead>' +
                            '<tbody>';
                        
                        data.agreements.forEach(function(agg) {
                            let statusBadge = '';
                            switch(agg.status) {
                                case 'pending': statusBadge = '<span class="badge badge-warning">Pending</span>'; break;
                                case 'active': statusBadge = '<span class="badge badge-success">Active</span>'; break;
                                case 'rejected': statusBadge = '<span class="badge badge-danger">Rejected</span>'; break;
                                case 'expired': statusBadge = '<span class="badge badge-secondary">Expired</span>'; break;
                                default: statusBadge = '<span class="badge badge-info">' + agg.status + '</span>';
                            }
                            
                            content += '<tr>' +
                                '<td>' + (agg.agreement_number || '-') + '</td>' +
                                '<td>' + (agg.vehicle_name || '-') + ' (' + (agg.license_plate || '-') + ')</td>' +
                                '<td>' + statusBadge + '</td>' +
                                '<td>' + (agg.signed_at ? agg.signed_at : '-') + '</td>' +
                                '<td>';
                            
                            if (agg.id) {
                                content += '<a href="<?= site_url("rental-agreements/view/") ?>' + agg.id + '" class="btn btn-sm btn-info">View Full Agreement</a>';
                            }
                            
                            content += '</td></tr>';
                        });
                        
                        content += '</tbody></table></div></div>';
                    }
                    
                    $('#customerModalContent').html(content);
                } else {
                    $('#customerModalContent').html('<div class="alert alert-danger">' + data.message + '</div>');
                }
            })
            .catch(error => {
                $('#customerModalContent').html('<div class="alert alert-danger">Error loading customer details</div>');
            });
    }
    </script>
