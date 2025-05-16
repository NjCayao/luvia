<!-- Main Footer -->
<footer class="main-footer">
    <!-- Default to the left -->
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5><?= APP_NAME ?></h5>
                <p class="text-muted">Tu plataforma de confianza</p>
            </div>
            <div class="col-md-4">
                <h5>Enlaces</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= url('/terminos') ?>">Términos y Condiciones</a></li>
                    <li><a href="<?= url('/privacidad') ?>">Política de Privacidad</a></li>
                    <li><a href="<?= url('/contacto') ?>">Contacto</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Síguenos</h5>
                <div class="social-icons">
                    <a href="#" class="mr-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="mr-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="mr-2"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <strong>Copyright &copy; <?= date('Y') ?> <a href="<?= url('/') ?>"><?= APP_NAME ?></a>.</strong> Todos los derechos reservados.
        </div>
    </div>
</footer>