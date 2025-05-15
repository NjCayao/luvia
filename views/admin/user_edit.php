<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-edit mr-1"></i>
            Editar Usuario
        </h3>
        <div class="card-tools">
            <a href="<?= url('/admin/usuario/' . $user['id']) ?>" class="btn btn-tool">
                <i class="fas fa-arrow-left"></i> Volver a detalles
            </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <?php if (!$user): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Usuario no encontrado
            </div>
        <?php else: ?>
            <form id="edit-user-form" method="POST" action="<?= url('/update_user.php') ?>">
                <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($user['email']) ?>" required>
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">Teléfono</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="<?= htmlspecialchars($user['phone']) ?>" required>
                            <div class="invalid-feedback" id="phone-error"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Nueva contraseña (dejar en blanco para mantener actual)</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="invalid-feedback" id="password-error"></div>
                            <small class="form-text text-muted">Mínimo 8 caracteres</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?>>Pendiente</option>
                                <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Activo</option>
                                <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspendido</option>
                                <option value="deleted" <?= $user['status'] === 'deleted' ? 'selected' : '' ?>>Eliminado</option>
                            </select>
                            <div class="invalid-feedback" id="status-error"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de usuario</label>
                            <input type="text" class="form-control" value="<?= $user['user_type'] === 'admin' ? 'Administrador' : ($user['user_type'] === 'advertiser' ? 'Anunciante' : 'Visitante') ?>" readonly>
                            <small class="form-text text-muted">El tipo de usuario no se puede cambiar</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de registro</label>
                            <input type="text" class="form-control" value="<?= formatDate($user['created_at']) ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="alert alert-danger d-none" id="form-error"></div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="<?= url('/admin/usuario/' . $user['id']) ?>" class="btn btn-default">
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

        // Capturar datos del formulario
        const formData = new FormData(form);
        console.log('Enviando formulario con datos:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }

        // Enviar formulario con fetch
        fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Respuesta recibida con estado:', response.status);
                return response.text().then(text => {
                    try {
                        // Intentar parsear como JSON
                        return JSON.parse(text);
                    } catch (e) {
                        // Si no es JSON, mostrar el texto y lanzar error
                        console.error('Respuesta no es JSON válido:', text.substring(0, 500) + '...');
                        throw new Error('Respuesta del servidor no es JSON válido');
                    }
                });
            })
            .then(data => {
                console.log('Datos JSON procesados:', data);

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
                    formError.classList.remove('d-none');
                    formError.textContent = data.error;
                } else if (data.success) {
                    // Mostrar mensaje de éxito
                    formError.classList.remove('d-none');
                    formError.classList.remove('alert-danger');
                    formError.classList.add('alert-success');
                    formError.textContent = data.message || 'Usuario actualizado correctamente';

                    // Redireccionar después de un breve retraso
                    setTimeout(function() {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    }, 1500);
                }

                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios';
            })
            .catch(error => {
                console.error('Error:', error);

                // Mostrar error de conexión
                formError.classList.remove('d-none');
                formError.textContent = 'Error de conexión o respuesta inválida. Intente nuevamente.';

                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios';
            });
    });
</script>