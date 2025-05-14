<p class="login-box-msg">Verificar Cuenta</p>

<p>Se ha enviado un código de verificación al teléfono <strong><?= htmlspecialchars($user['phone']) ?></strong>.</p>

<form id="verify-form" action="<?= url('/verificar/procesar') ?>" method="post">
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
    
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block" id="verify-btn">Verificar</button>
        </div>
    </div>
</form>

<div class="alert alert-danger mt-3 d-none" id="verify-error"></div>

<p class="mt-3 mb-1">
    <a href="#" id="resend-code">No recibí el código</a>
</p>

<p class="mb-0">
    <a href="<?= url('/login') ?>">Volver al login</a>
</p>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const verifyForm = document.getElementById('verify-form');
    const verifyBtn = document.getElementById('verify-btn');
    const verifyError = document.getElementById('verify-error');
    const resendCodeBtn = document.getElementById('resend-code');
    
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
    
    // Enviar formulario de verificación
    verifyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Resetear mensajes de error
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        verifyError.classList.add('d-none');
        verifyError.textContent = '';
        
        // Cambiar estado del botón
        verifyBtn.disabled = true;
        verifyBtn.textContent = 'Verificando...';
        
        // Enviar datos
        const formData = new FormData(verifyForm);
        
        fetch(verifyForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                // Mostrar error
                if (data.error.includes('Código')) {
                    const input = document.getElementById('code');
                    const error = document.getElementById('code-error');
                    
                    if (input && error) {
                        input.classList.add('is-invalid');
                        error.textContent = data.error;
                    }
                } else {
                    verifyError.classList.remove('d-none');
                    verifyError.textContent = data.error;
                }
            } else if (data.success) {
                // Redireccionar
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                
                // Mostrar mensaje de éxito
                verifyForm.reset();
                verifyError.classList.remove('d-none');
                verifyError.classList.remove('alert-danger');
                verifyError.classList.add('alert-success');
                verifyError.textContent = data.message || 'Verificación exitosa';
            }
            
            // Restaurar botón
            verifyBtn.disabled = false;
            verifyBtn.textContent = 'Verificar';
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Mostrar error de conexión
            verifyError.classList.remove('d-none');
            verifyError.textContent = 'Error de conexión. Intente nuevamente.';
            
            // Restaurar botón
            verifyBtn.disabled = false;
            verifyBtn.textContent = 'Verificar';
        });
    });
    
    // Reenviar código
    resendCodeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Cambiar estado del botón
        this.textContent = 'Enviando...';
        this.classList.add('disabled');
        
        // Resetear mensajes de error
        verifyError.classList.add('d-none');
        verifyError.textContent = '';
        
        // Enviar solicitud
        const formData = new FormData();
        formData.append('csrf_token', '<?= generateCsrfToken() ?>');
        
        fetch('<?= url('/verificar/reenviar') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                // Mostrar error
                verifyError.classList.remove('d-none');
                verifyError.textContent = data.error;
            } else if (data.success) {
                // Mostrar mensaje de éxito
                verifyError.classList.remove('d-none');
                verifyError.classList.remove('alert-danger');
                verifyError.classList.add('alert-success');
                verifyError.textContent = data.message || 'Código reenviado correctamente';
                
                // Deshabilitar botón por 60 segundos
                let countdown = 60;
                this.classList.add('disabled');
                
                const intervalId = setInterval(() => {
                    countdown--;
                    this.textContent = `Reenviar código (${countdown}s)`;
                    
                    if (countdown <= 0) {
                        clearInterval(intervalId);
                        this.textContent = 'Reenviar código';
                        this.classList.remove('disabled');
                    }
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Mostrar error de conexión
            verifyError.classList.remove('d-none');
            verifyError.textContent = 'Error de conexión. Intente nuevamente.';
            
            // Restaurar botón
            this.textContent = 'No recibí el código';
            this.classList.remove('disabled');
        });
    });
});
</script>