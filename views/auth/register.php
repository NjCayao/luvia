<p class="login-box-msg">Registrar nueva cuenta</p>

<form id="register-form" action="<?= url('/registro/procesar') ?>" method="post">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <input type="hidden" name="user_type" value="<?= htmlspecialchars($userType) ?>">
    
    <div class="form-group">
        <div class="input-group">
            <input type="tel" name="phone" id="phone" class="form-control" placeholder="Teléfono (ej: 999999999)" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-phone"></span>
                </div>
            </div>
        </div>
        <div class="invalid-feedback" id="phone-error"></div>
        <small class="form-text text-muted">Se enviará un código de verificación a este número</small>
    </div>
    
    <div class="form-group">
        <div class="input-group">
            <input type="email" name="email" id="email" class="form-control" placeholder="Correo Electrónico" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <div class="invalid-feedback" id="email-error"></div>
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
        <small class="form-text text-muted">Mínimo 8 caracteres</small>
    </div>
    
    <div class="form-group">
        <div class="input-group">
            <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Confirmar Contraseña" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <div class="invalid-feedback" id="password_confirm-error"></div>
    </div>
    
    <?php if ($userType === 'advertiser'): ?>
        <div class="form-group">
            <label>Género</label>
            <div class="d-flex">
                <div class="icheck-primary mr-3">
                    <input type="radio" id="gender-female" name="gender" value="female" checked>
                    <label for="gender-female">Mujer</label>
                </div>
                <div class="icheck-primary mr-3">
                    <input type="radio" id="gender-male" name="gender" value="male">
                    <label for="gender-male">Hombre</label>
                </div>
                <div class="icheck-primary">
                    <input type="radio" id="gender-trans" name="gender" value="trans">
                    <label for="gender-trans">Trans</label>
                </div>
            </div>
            <div class="invalid-feedback d-block" id="gender-error"></div>
        </div>
    <?php endif; ?>
    
    <div class="form-group">
        <div class="icheck-primary">
            <input type="checkbox" name="terms" id="terms" required>
            <label for="terms">
                Acepto los <a href="<?= url('/terminos') ?>" target="_blank">Términos y Condiciones</a>
            </label>
        </div>
        <div class="invalid-feedback d-block" id="terms-error"></div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block" id="register-btn">Registrarme</button>
        </div>
    </div>
</form>

<div class="alert alert-danger mt-3 d-none" id="register-error"></div>

<p class="mt-3 mb-0">
    <a href="<?= url('/login') ?>" class="text-center">Ya tengo una cuenta</a>
</p>

<div class="mt-3">
    <p class="text-center">
        <?php if ($userType === 'advertiser'): ?>
            <a href="<?= url('/registro?tipo=visitor') ?>">Registrarme como visitante</a>
        <?php else: ?>
            <a href="<?= url('/registro?tipo=advertiser') ?>">Registrarme como anunciante</a>
        <?php endif; ?>
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('register-form');
    const registerBtn = document.getElementById('register-btn');
    const registerError = document.getElementById('register-error');
    
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Resetear mensajes de error
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        registerError.classList.add('d-none');
        registerError.textContent = '';
        
        // Cambiar estado del botón
        registerBtn.disabled = true;
        registerBtn.textContent = 'Procesando...';
        
        // Enviar datos
        const formData = new FormData(registerForm);
        
        fetch(registerForm.action, {
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
                registerError.classList.remove('d-none');
                registerError.textContent = data.error;
            } else if (data.success) {
                // Redireccionar si es necesario
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                
                // Mostrar mensaje de éxito
                registerForm.reset();
                registerError.classList.remove('d-none');
                registerError.classList.remove('alert-danger');
                registerError.classList.add('alert-success');
                registerError.textContent = data.message || 'Registro exitoso';
            }
            
            // Restaurar botón
            registerBtn.disabled = false;
            registerBtn.textContent = 'Registrarme';
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Mostrar error de conexión
            registerError.classList.remove('d-none');
            registerError.textContent = 'Error de conexión. Intente nuevamente.';
            
            // Restaurar botón
            registerBtn.disabled = false;
            registerBtn.textContent = 'Registrarme';
        });
    });
});
</script>