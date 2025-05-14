<?php
// views/layouts/footer.php
?>

</div>
<!-- /.main-content -->
</div>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
        v<?= APP_VERSION ?>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; <?= date('Y') ?> <a href="<?= url('/') ?>"><?= APP_NAME ?></a>.</strong> Todos los derechos reservados.
</footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="<?= url('plugins/jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?= url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= url('plugins/adminlte/js/adminlte.min.js') ?>"></script>

<?php if (isset($extraJs)): ?>
    <?= $extraJs ?>
<?php endif; ?>
</body>

</html>