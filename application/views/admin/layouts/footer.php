    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <strong>&copy; <?= date('Y') ?> <a href="<?= site_url() ?>" style="color: var(--primary-color); text-decoration: none;">SOZO Manager</a>.</strong> All rights reserved.
            </div>
            <div>
                Version 1.0.0
            </div>
        </div>
    </footer>
</div><!-- /.content-wrapper -->

<!-- OverlayScrollbars JS -->
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- Bootstrap JS (for Modals) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?= base_url('assets/js/custom.js?v=1.3') ?>"></script>

<!-- Invoices Page - Customer Autocomplete Script -->
<script>
window.addEventListener('load', function() {
    var customerCache = {};
    var searchUrl = '<?php echo base_url("index.php/invoices/search_customers"); ?>';
    
    var $searchInput = $('#customer_search');
    var $companyFilter = $('#company_filter');
    var $customerId = $('#customer_id');
    
    if ($searchInput.length > 0) {
        $searchInput.autocomplete({
            source: function(request, response) {
                var term = request.term;
                var companyFilter = $companyFilter.val();
                
                if (term in customerCache) {
                    response(customerCache[term]);
                    return;
                }
                
                $.ajax({
                    url: searchUrl,
                    dataType: 'json',
                    data: {
                        q: term,
                        company_id: companyFilter
                    },
                    success: function(data) {
                        customerCache[term] = data;
                        response(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Search error:', status, error);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $searchInput.val(ui.item.name + ' (ID: ' + ui.item.id + ')');
                $customerId.val(ui.item.id);
                return false;
            },
            focus: function(event, ui) {
                $searchInput.val(ui.item.name);
                return false;
            }
        }).autocomplete('instance')._renderItem = function(ul, item) {
            return $('<li>')
                .append('<div>' + item.name + ' <small class="text-muted">(' + item.email + ')</small></div>')
                .appendTo(ul);
        };
        
        $searchInput.on('input', function() {
            if ($(this).val() === '') {
                $customerId.val('');
            }
        });
    }
});
</script>

<?php if ($this->session->flashdata('success')): ?>
<script>
$(document).ready(function() {
    Toast.success('<?= addslashes($this->session->flashdata('success')) ?>');
});
</script>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
<script>
$(document).ready(function() {
    Toast.error('<?= addslashes($this->session->flashdata('error')) ?>');
});
</script>
<?php endif; ?>

<?php if ($this->session->flashdata('warning')): ?>
<script>
$(document).ready(function() {
    Toast.warning('<?= addslashes($this->session->flashdata('warning')) ?>');
});
</script>
<?php endif; ?>

<?php if ($this->session->flashdata('info')): ?>
<script>
$(document).ready(function() {
    Toast.info('<?= addslashes($this->session->flashdata('info')) ?>');
});
</script>
<?php endif; ?>

</body>
</html>
