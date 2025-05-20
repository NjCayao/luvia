<div class="register-container">
    <h3 class="text-center mb-4"><?= $userType === 'advertiser' ? 'Registrarme para Publicar' : 'Registrarme como visitante' ?></h3>

    <form id="register-form" action="<?= url('/registro/procesar') ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        <input type="hidden" name="user_type" value="<?= htmlspecialchars($userType) ?>">

        <div class="form-group">
            <label for="phone">Número de teléfono</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <select class="form-control country-select" id="country_code" name="country_code">
                        <option value="+51" selected>🇵🇪 +51</option>
                        <option value="+55">🇧🇷 +55</option>
                        <option value="+591">🇧🇴 +591</option>
                        <option value="+593">🇪🇨 +593</option>
                        <option value="+58">🇻🇪 +58</option>
                        <option value="+56">🇨🇱 +56</option>
                        <option value="+57">🇨🇴 +57</option>
                        <option value="+54">🇦🇷 +54</option>
                        <option value="+595">🇵🇾 +595</option>
                        <option value="+598">🇺🇾 +598</option>
                        <option value="+52">🇲🇽 +52</option>
                        <option value="+1">🇺🇸 +1</option>
                        <option value="+34">🇪🇸 +34</option>
                    </select>
                </div>
                <input type="tel" name="phone" id="phone" class="form-control" placeholder="Número sin código de país" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-phone"></span>
                    </div>
                </div>
            </div>
            <div class="invalid-feedback" id="phone-error"></div>
            <small class="form-text text-muted">Recibirás un código de verificación en este número</small>
        </div>

        <style>
            /* Estilo para el selector de país */
            .country-select {
                min-width: 100px;
                border-radius: 8px 0 0 8px !important;
                padding-right: 8px;
                background-image: none !important;
                /* quitar la flecha default de select */
            }
        </style>

        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <div class="input-group">
                <input type="email" name="email" id="email" class="form-control" placeholder="tucorreo@ejemplo.com" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="invalid-feedback" id="email-error"></div>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="invalid-feedback" id="password-error"></div>
            <small class="form-text text-muted">Mínimo 8 caracteres</small>
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirmar contraseña</label>
            <div class="input-group">
                <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Confirmar contraseña" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="invalid-feedback" id="password_confirm-error"></div>
        </div>

        <?php if ($userType === 'advertiser'): ?>
            <div class="form-group">
                <label>Género</label>
                <div class="gender-selector">
                    <div class="gender-option">
                        <input type="radio" id="gender-female" name="gender" value="female" checked>
                        <label for="gender-female" class="btn btn-outline-pink">
                            <i class="fas fa-venus"></i> Mujer
                        </label>
                    </div>
                    <div class="gender-option">
                        <input type="radio" id="gender-male" name="gender" value="male">
                        <label for="gender-male" class="btn btn-outline-blue">
                            <i class="fas fa-mars"></i> Hombre
                        </label>
                    </div>
                    <div class="gender-option">
                        <input type="radio" id="gender-trans" name="gender" value="trans">
                        <label for="gender-trans" class="btn btn-outline-purple">
                            <i class="fas fa-transgender"></i> Trans
                        </label>
                    </div>
                </div>
                <div class="invalid-feedback d-block" id="gender-error"></div>
            </div>
        <?php endif; ?>

        <div class="form-group terms-container">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="terms" id="terms" class="custom-control-input" required>
                <label class="custom-control-label" for="terms">
                    Acepto los <a href="<?= url('/terminos') ?>" target="_blank">Términos y Condiciones</a>
                </label>
            </div>
            <div class="invalid-feedback d-block" id="terms-error"></div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg btn-block" id="register-btn">
                <i class="fas fa-user-plus mr-2"></i>Crear mi cuenta
            </button>
        </div>
    </form>

    <div class="alert alert-danger mt-3 d-none" id="register-error"></div>

    <div class="login-links mt-4">
        <p class="text-center">
            <a href="<?= url('/login') ?>"><i class="fas fa-sign-in-alt mr-1"></i> Ya tengo una cuenta</a>
        </p>
    </div>

    <div class="account-type-switch mt-4">
        <?php if ($userType === 'advertiser'): ?>
            <div class="alert alert-info text-center">
                <p class="m-0"><strong>¿Buscas compañía?</strong></p>
                <p class="mb-2">Regístrate como visitante para acceder a perfiles exclusivos</p>
                <a href="<?= url('/registro?tipo=visitor') ?>" class="btn btn-info">
                    <i class="fas fa-user mr-1"></i> Registrarme como visitante
                </a>
            </div>
        <?php else: ?>
            <div class="alert alert-success text-center">
                <p class="m-0"><strong>¿Quieres publicar tu perfil?</strong></p>
                <p class="mb-2">Regístrate como anunciante y comienza a recibir contactos</p>
                <a href="<?= url('/registro?tipo=advertiser') ?>" class="btn btn-success">
                    <i class="fas fa-ad mr-1"></i> Registrarme como anunciante
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const registerForm = document.getElementById('register-form');
        const registerBtn = document.getElementById('register-btn');
        const registerError = document.getElementById('register-error');

        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function(e) {
                const passwordField = this.closest('.input-group').querySelector('input');
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        });

        // Format phone number
        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('input', function(e) {
            // Allow only numbers
            this.value = this.value.replace(/\D/g, '');

            // Limit to 9 digits
            if (this.value.length > 9) {
                this.value = this.value.slice(0, 9);
            }
        });

        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Reset error messages
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            registerError.classList.add('d-none');
            registerError.textContent = '';

            // Change button state
            registerBtn.disabled = true;
            registerBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';

            // Send data
            const formData = new FormData(registerForm);

            fetch(registerForm.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.errors) {
                        // Show validation errors
                        Object.keys(data.errors).forEach(field => {
                            const input = document.getElementById(field);
                            const error = document.getElementById(field + '-error');

                            if (input && error) {
                                input.classList.add('is-invalid');
                                error.textContent = data.errors[field];
                            }
                        });
                    } else if (data.error) {
                        // Show general error
                        registerError.classList.remove('d-none');
                        registerError.textContent = data.error;
                    } else if (data.success) {
                        // Redirect if needed
                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }

                        // Show success message
                        registerForm.reset();
                        registerError.classList.remove('d-none');
                        registerError.classList.remove('alert-danger');
                        registerError.classList.add('alert-success');
                        registerError.textContent = data.message || 'Registro exitoso';
                    }

                    // Restore button
                    registerBtn.disabled = false;
                    registerBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Crear mi cuenta';
                })
                .catch(error => {
                    console.error('Error:', error);

                    // Show connection error
                    registerError.classList.remove('d-none');
                    registerError.textContent = 'Error de conexión. Intente nuevamente.';

                    // Restore button
                    registerBtn.disabled = false;
                    registerBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Crear mi cuenta';
                });
        });
    });
</script>