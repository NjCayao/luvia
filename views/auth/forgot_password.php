<p class="login-box-msg">Recuperar Contraseña</p>

<p>Ingrese su teléfono o correo electrónico para recibir instrucciones.</p>

<form id="forgot-password-form" action="<?= url('/forgot-password/procesar') ?>" method="post">
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
    
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block" id="forgot-btn">Enviar Instrucciones</button>
        </div>
    </div>
</form>

<div class="alert mt-3 d-none" id="forgot-message"></div>

<p class="mt-3 mb-1">
    <a href="<?= url('/login') ?>">Volver al login</a>
</p>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const forgotForm = document.getElementById('forgot-password-form');
    const forgotBtn = document.getElementById('forgot-btn');
    const forgotMessage = document.getElementById('forgot-message');
    
    forgotForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Resetear mensajes
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        forgotMessage.classList.add('d-none');
        forgotMessage.textContent = '';
        
        // Cambiar estado del botón
        forgotBtn.disabled = true;
        forgotBtn.textContent = 'Procesando...';
        
        // Enviar datos
        const formData = new FormData(forgotForm);
        
        fetch(forgotForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                // Mostrar error
                forgotMessage.classList.remove('d-none');
                forgotMessage.classList.add('alert-danger');
                forgotMessage.classList.remove('alert-success');
                forgotMessage.textContent = data.error;
            } else if (data.success) {
                // Mostrar mensaje de éxito
                forgotForm.reset();
                forgotMessage.classList.remove('d-none');
                forgotMessage.classList.remove('alert-danger');
                forgotMessage.classList.add('alert-success');
                forgotMessage.textContent = data.message || 'Instrucciones enviadas correctamente';
                
                // Redireccionar si es necesario
                if (data.redirect) {
                    setTimeout(function() {
                        window.location.href = data.redirect;
                    }, 3000);
                }
            }
            
            // Restaurar botón
            forgotBtn.disabled = false;
            forgotBtn.textContent = 'Enviar Instrucciones';
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Mostrar error de conexión
            forgotMessage.classList.remove('d-none');
            forgotMessage.classList.add('alert-danger');
            forgotMessage.classList.remove('alert-success');
            forgotMessage.textContent = 'Error de conexión. Intente nuevamente.';
            
            // Restaurar botón
            forgotBtn.disabled = false;
            forgotBtn.textContent = 'Enviar Instrucciones';
        });
    });
});
</script>