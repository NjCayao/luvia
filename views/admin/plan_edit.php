<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tag mr-1"></i>
            Editar Plan
        </h3>
        <div class="card-tools">
            <a href="<?= url('/admin/planes') ?>" class="btn btn-tool">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <?php if (isset($plan) && !$plan): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Plan no encontrado
            </div>
        <?php else: ?>
            <form id="plan-form" method="POST" action="<?= url('/admin/plan/guardar') ?>">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?? 0 ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Nombre del Plan *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($plan['name'] ?? '') ?>" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_type">Tipo de Usuario *</label>
                            <select class="form-control" id="user_type" name="user_type" required>
                                <option value="advertiser" <?= (isset($plan['user_type']) && $plan['user_type'] === 'advertiser') ? 'selected' : '' ?>>
                                    Anunciante
                                </option>
                                <option value="visitor" <?= (isset($plan['user_type']) && $plan['user_type'] === 'visitor') ? 'selected' : '' ?>>
                                    Visitante
                                </option>
                            </select>
                            <div class="invalid-feedback" id="user_type-error"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="duration">Duración (días) *</label>
                            <input type="number" class="form-control" id="duration" name="duration" 
                                   min="1" step="1" value="<?= $plan['duration'] ?? 30 ?>" required>
                            <div class="invalid-feedback" id="duration-error"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="price">Precio (S/.) *</label>
                            <input type="number" class="form-control" id="price" name="price" 
                                   min="0" step="0.01" value="<?= $plan['price'] ?? 0 ?>" required>
                            <div class="invalid-feedback" id="price-error"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row advertiser-options">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="max_photos">Máximo de Fotos</label>
                            <input type="number" class="form-control" id="max_photos" name="max_photos" 
                                   min="1" step="1" value="<?= $plan['max_photos'] ?? 5 ?>">
                            <div class="invalid-feedback" id="max_photos-error"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="max_videos">Máximo de Videos</label>
                            <input type="number" class="form-control" id="max_videos" name="max_videos" 
                                   min="0" step="1" value="<?= $plan['max_videos'] ?? 2 ?>">
                            <div class="invalid-feedback" id="max_videos-error"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="featured" name="featured" value="1"
                                       <?= (isset($plan['featured']) && $plan['featured']) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="featured">Plan Destacado</label>
                            </div>
                            <small class="form-text text-muted">
                                Los planes destacados aparecen marcados como "Popular" en la página de planes.
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($plan['description'] ?? '') ?></textarea>
                            <div class="invalid-feedback" id="description-error"></div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-danger d-none" id="form-error"></div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save"></i> Guardar Plan
                            </button>
                            <a href="<?= url('/admin/planes') ?>" class="btn btn-default">
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
    const form = document.getElementById('plan-form');
    const submitBtn = document.getElementById('submit-btn');
    const formError = document.getElementById('form-error');
    const userTypeSelect = document.getElementById('user_type');
    const advertiserOptions = document.querySelector('.advertiser-options');
    
    // Función para mostrar/ocultar opciones de anunciante
    function toggleAdvertiserOptions() {
        if (userTypeSelect.value === 'advertiser') {
            advertiserOptions.style.display = 'flex';
            document.getElementById('max_photos').required = true;
            document.getElementById('max_videos').required = true;
        } else {
            advertiserOptions.style.display = 'none';
            document.getElementById('max_photos').required = false;
            document.getElementById('max_videos').required = false;
        }
    }
    
    // Inicializar
    toggleAdvertiserOptions();
    
    // Evento para cambio de tipo de usuario
    userTypeSelect.addEventListener('change', toggleAdvertiserOptions);
    
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
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Plan';
                
            } else if (data.error) {
                // Mostrar error general
                formError.classList.remove('d-none');
                formError.textContent = data.error;
                
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Plan';
                
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
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Plan';
        });
    });
});
</script>