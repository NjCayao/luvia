<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-edit mr-1"></i>
            Editar Perfil
        </h3>
        <div class="card-tools">
            <a href="<?= url('/admin/perfil/' . $profile['id']) ?>" class="btn btn-tool">
                <i class="fas fa-arrow-left"></i> Volver al perfil
            </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <?php if (isset($profile) && !$profile): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Perfil no encontrado
            </div>
        <?php else: ?>
            <form id="profile-form" method="POST" action="<?= url('/admin/perfil/actualizar') ?>">
                <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                <input type="hidden" name="profile_id" value="<?= $profile['id'] ?? 0 ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Nombre *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gender">Género *</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="female" <?= (isset($profile['gender']) && $profile['gender'] === 'female') ? 'selected' : '' ?>>
                                    Mujer
                                </option>
                                <option value="male" <?= (isset($profile['gender']) && $profile['gender'] === 'male') ? 'selected' : '' ?>>
                                    Hombre
                                </option>
                                <option value="trans" <?= (isset($profile['gender']) && $profile['gender'] === 'trans') ? 'selected' : '' ?>>
                                    Trans
                                </option>
                            </select>
                            <div class="invalid-feedback" id="gender-error"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="whatsapp">WhatsApp *</label>
                            <input type="text" class="form-control" id="whatsapp" name="whatsapp" 
                                   value="<?= htmlspecialchars($profile['whatsapp'] ?? '') ?>" required>
                            <div class="invalid-feedback" id="whatsapp-error"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="city">Ciudad *</label>
                            <select class="form-control" id="city" name="city" required>
                                <option value="">Seleccione una ciudad</option>
                                <?php foreach ($cities as $cityOption): ?>
                                    <option value="<?= htmlspecialchars($cityOption) ?>" <?= (isset($profile['city']) && $profile['city'] === $cityOption) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cityOption) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="city-error"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="location">Ubicación *</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="<?= htmlspecialchars($profile['location'] ?? '') ?>" required>
                            <div class="invalid-feedback" id="location-error"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Descripción *</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($profile['description'] ?? '') ?></textarea>
                            <div class="invalid-feedback" id="description-error"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="schedule">Horario *</label>
                            <textarea class="form-control" id="schedule" name="schedule" rows="3" required><?= htmlspecialchars($profile['schedule'] ?? '') ?></textarea>
                            <div class="invalid-feedback" id="schedule-error"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_verified" name="is_verified" value="1"
                                       <?= (isset($profile['is_verified']) && $profile['is_verified']) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="is_verified">Perfil Verificado</label>
                            </div>
                            <small class="form-text text-muted">
                                Los perfiles verificados aparecen con un ícono de verificación y tienen mayor visibilidad.
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-danger d-none" id="form-error"></div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save"></i> Guardar Perfil
                            </button>
                            <a href="<?= url('/admin/perfil/' . $profile['id']) ?>" class="btn btn-default">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profile-form');
    const submitBtn = document.getElementById('submit-btn');
    const formError = document.getElementById('form-error');
    
    // Envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Resetear mensajes de error
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        formError.classList.add('d-none');
        formError.textContent = '';
        
        // Cambiar estado del botón
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        
        // Enviar formulario
        const formData = new FormData(form);
        
        fetch(form.action, {
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
                
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Perfil';
                
            } else if (data.error) {
                // Mostrar error general
                formError.classList.remove('d-none');
                formError.textContent = data.error;
                
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Perfil';
                
            } else if (data.success) {
                // Redirigir en caso de éxito
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Mostrar error general
            formError.classList.remove('d-none');
            formError.textContent = 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.';
            
            // Restaurar botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Perfil';
        });
    });
});
</script>