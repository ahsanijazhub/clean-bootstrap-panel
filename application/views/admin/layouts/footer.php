    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <strong>&copy; <?= date('Y') ?> <a href="<?= site_url() ?>" style="color: var(--primary-color); text-decoration: none;">Admin Panel</a>.</strong> All rights reserved.
            </div>
            <div>
                Version 1.0.0
            </div>
        </div>
    </footer>
</div><!-- /.content-wrapper -->

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
