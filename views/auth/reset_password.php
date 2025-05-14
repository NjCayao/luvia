<p class="login-box-msg">Restablecer Contraseña</p>

<p>Se ha enviado un código a su teléfono o correo electrónico.</p>

<form id="reset-password-form" action="<?= url('/reset-password/procesar') ?>" method="post">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    
    <div class="form-group">
        <div class="input-group">
            <input type="text" name="code" id="code" class="form-control" placeholder="Código de verificación" maxlength="6" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-key"></span>
                </div>
            </div>
        </div>
        <div class="invalid-feedback" id="code-error"></div>
    </div>
    
    <div class="form-group">
        <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" placeholder="Nueva Contraseña" required>
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
    
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block" id="reset-btn">Restablecer Contraseña</button>
        </div>
    </div>
</form>

<div class="alert mt-3 d-none" id="reset-message"></div>

<p class="mt-3 mb-1">
    <a href="<?= url('/login') ?>">Volver al login</a>
</p>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const resetForm = document.getElementById('reset-password-form');
    const resetBtn = document.getElementById('reset-btn');
    const resetMessage = document.getElementById('reset-message');
    
    // Formatear input de código
    const codeInput = document.getElementById('code');
    codeInput.addEventListener('input', function(e) {
        // Permitir solo números
        this.value = this.value.replace(/\D/g, '');
        
        // Limitar a 6 dígitos
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
        }
    });
    
    resetForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Resetear mensajes
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        resetMessage.classList.add('d-none');
        resetMessage.textContent = '';
        
        // Cambiar estado del botón
        resetBtn.disabled = true;
        resetBtn.textContent = 'Procesando...';
        
        // Enviar datos
        const formData = new FormData(resetForm);
        
        fetch(resetForm.action, {
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
                resetMessage.classList.remove('d-none');
                resetMessage.classList.add('alert-danger');
                resetMessage.classList.remove('alert-success');
                resetMessage.textContent = data.error;
            } else if (data.success) {
                // Mostrar mensaje de éxito
                resetForm.reset();
                resetMessage.classList.remove('d-none');
                resetMessage.classList.remove('alert-danger');
                resetMessage.classList.add('alert-success');
                resetMessage.textContent = data.message || 'Contraseña restablecida correctamente';
                
                // Redireccionar si es necesario
                if (data.redirect) {
                    setTimeout(function() {
                        window.location.href = data.redirect;
                    }, 3000);
                }
            }
            
            // Restaurar botón
            resetBtn.disabled = false;
            resetBtn.textContent = 'Restablecer Contraseña';
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Mostrar error de conexión
            resetMessage.classList.remove('d-none');
            resetMessage.classList.add('alert-danger');
            resetMessage.classList.remove('alert-success');
            resetMessage.textContent = 'Error de conexión. Intente nuevamente.';
            
            // Restaurar botón
            resetBtn.disabled = false;
            resetBtn.textContent = 'Restablecer Contraseña';
        });
    });
});
</script>