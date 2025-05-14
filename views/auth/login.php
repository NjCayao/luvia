<p class="login-box-msg">Ingrese sus credenciales para iniciar sesión</p>

<form id="login-form" action="<?= url('/login/procesar') ?>" method="post">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    
    <div class="form-group">
        <div class="input-group">
            <input type="text" name="username" id="username" class="form-control" placeholder="Teléfono o Correo Electrónico" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>
        <div class="invalid-feedback" id="username-error"></div>
    </div>
    
    <div class="form-group">
        <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <div class="invalid-feedback" id="password-error"></div>
    </div>
    
    <div class="row">
        <div class="col-8">
            <div class="icheck-primary">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">
                    Recordarme
                </label>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block" id="login-btn">Ingresar</button>
        </div>
        <!-- /.col -->
    </div>
</form>

<div class="alert alert-danger mt-3 d-none" id="login-error"></div>

<p class="mb-1 mt-3">
    <a href="<?= url('/forgot-password') ?>">Olvidé mi contraseña</a>
</p>
<p class="mb-0">
    <a href="<?= url('/registro') ?>" class="text-center">Registrarme</a>
</p>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const loginBtn = document.getElementById('login-btn');
    const loginError = document.getElementById('login-error');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Resetear mensajes de error
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        loginError.classList.add('d-none');
        loginError.textContent = '';
        
        // Cambiar estado del botón
        loginBtn.disabled = true;
        loginBtn.textContent = 'Procesando...';
        
        // Enviar datos
        const formData = new FormData(loginForm);
        
        fetch(loginForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.errors) {
                // Mostrar errores de validación
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(field);
                    const error = document.getElementById(field + '-error');
                    
                    if (input && error) {
                        input.classList.add('is-invalid');
                        error.textContent = data.errors[field];
                    }
                });
            } else if (data.error) {
                // Mostrar error general
                loginError.classList.remove('d-none');
                loginError.textContent = data.error;
            } else if (data.success) {
                // Redirigir a la página indicada
                window.location.href = data.redirect;
                return;
            } else if (data.redirect) {
                // Redirigir a otra página (ej: verificación)
                window.location.href = data.redirect;
                return;
            }
            
            // Restaurar botón
            loginBtn.disabled = false;
            loginBtn.textContent = 'Ingresar';
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Mostrar error de conexión
            loginError.classList.remove('d-none');
            loginError.textContent = 'Error de conexión. Intente nuevamente.';
            
            // Restaurar botón
            loginBtn.disabled = false;
            loginBtn.textContent = 'Ingresar';
        });
    });
});
</script>