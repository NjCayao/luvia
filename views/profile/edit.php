<!-- Contenido principal -->
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?= $profile ? 'Editar' : 'Crear' ?> Perfil</h3>
            </div>
            <!-- /.card-header -->

            <!-- form start -->
            <form id="profile-form" method="post" action="<?= url('/usuario/editar/procesar') ?>">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nombre / Alias *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Ingrese su nombre o alias"
                                    value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required>
                                <div class="invalid-feedback" id="name-error"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="whatsapp">WhatsApp *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <select class="form-control" id="country_code" name="country_code" style="width: auto; min-width: 100px; border-radius: 4px 0 0 4px;">
                                            <option value="+51" selected>🇵🇪 +51 (Perú)</option>
                                            <option value="+55">🇧🇷 +55 (Brasil)</option>
                                            <option value="+591">🇧🇴 +591 (Bolivia)</option>
                                            <option value="+593">🇪🇨 +593 (Ecuador)</option>
                                            <option value="+58">🇻🇪 +58 (Venezuela)</option>
                                            <option value="+56">🇨🇱 +56 (Chile)</option>
                                            <option value="+57">🇨🇴 +57 (Colombia)</option>
                                            <option value="+54">🇦🇷 +54 (Argentina)</option>
                                            <option value="+595">🇵🇾 +595 (Paraguay)</option>
                                            <option value="+598">🇺🇾 +598 (Uruguay)</option>
                                            <option value="+52">🇲🇽 +52 (México)</option>
                                            <option value="+1">🇺🇸 +1 (USA)</option>
                                            <option value="+34">🇪🇸 +34 (España)</option>
                                        </select>
                                    </div>
                                    <input type="tel" class="form-control" id="whatsapp" name="whatsapp"
                                        placeholder="982226895"
                                        value="<?= htmlspecialchars(preg_replace('/^\+51/', '', ($profile['whatsapp'] ?? ''))) ?>"
                                        pattern="[0-9]{9}" maxlength="9" required>
                                </div>
                                <div class="invalid-feedback" id="whatsapp-error"></div>
                                <small class="form-text text-muted">Este número será visible para los contactos. Introduce solo 9 dígitos.</small>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="province_id">Provincia *</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="province-search" placeholder="Buscar provincia...">
                                </div>
                                <select name="province_id" id="province_id" class="form-control" required>
                                    <option value="">Seleccione una provincia</option>
                                    <?php foreach ($provinces as $province): ?>
                                        <option value="<?= $province['id'] ?>" <?= isset($profile['province_id']) && $profile['province_id'] == $province['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($province['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback" id="province_id-error"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="district_id">Distrito *</label>
                                <select name="district_id" id="district_id" class="form-control" required <?= empty($profile['province_id']) ? 'disabled' : '' ?>>
                                    <option value="">Seleccione una provincia primero</option>
                                    <?php if (!empty($districts)): ?>
                                        <?php foreach ($districts as $district): ?>
                                            <option value="<?= $district['id'] ?>" <?= isset($profile['district_id']) && $profile['district_id'] == $district['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($district['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback" id="district_id-error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="location">Ubicación específica *</label>
                                <input type="text" class="form-control" id="location" name="location"
                                    placeholder="Ej: Miraflores, Surco, etc."
                                    value="<?= htmlspecialchars($profile['location'] ?? '') ?>">
                                <div class="invalid-feedback" id="location-error"></div>
                                <small class="form-text text-muted">Zona o distrito dentro de la ciudad.</small>
                            </div>
                        </div> -->
                </div>

                <div class="form-group">
                    <label for="description">Descripción *</label>
                    <textarea class="form-control" id="description" name="description" rows="4"
                        placeholder="Describe tus servicios, características, etc." required><?= htmlspecialchars($profile['description'] ?? '') ?></textarea>
                    <div class="invalid-feedback" id="description-error"></div>
                    <small class="form-text text-muted">Mínimo 50 caracteres. Describe tus servicios, características, preferencias, etc.</small>
                </div>

                <div class="form-group">
                    <label for="schedule">Horario de atención *</label>
                    <textarea class="form-control" id="schedule" name="schedule" rows="3"
                        placeholder="Ej: Lunes a Viernes de 10:00 a 22:00, fines de semana 24 horas" required><?= htmlspecialchars($profile['schedule'] ?? '') ?></textarea>
                    <div class="invalid-feedback" id="schedule-error"></div>
                </div>

                <!-- Sección de Tarifas -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Tarifas</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="rate-hour">Tarifa por Hora (S/.)</label>
                                    <input type="number" class="form-control rate-input" id="rate-hour"
                                        data-type="hour" placeholder="Ej: 150" min="0" step="1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="rate-half-hour">Tarifa Media Hora (S/.)</label>
                                    <input type="number" class="form-control rate-input" id="rate-half-hour"
                                        data-type="half_hour" placeholder="Ej: 80" min="0" step="1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="rate-extra">Tarifa Extra (S/.)</label>
                                    <input type="number" class="form-control rate-input" id="rate-extra"
                                        data-type="extra" placeholder="Ej: 200" min="0" step="1">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="rate-extra-desc">Descripción de tarifa extra</label>
                                    <input type="text" class="form-control" id="rate-extra-desc"
                                        placeholder="Ej: Salidas, toda la noche, etc.">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="rates" id="rates-json" value="">
                    </div>
                </div>
        </div>
        <!-- /.card-body -->

        <div class="card-footer">
            <button type="submit" class="btn btn-primary" id="submit-btn">
                <i class="fas fa-save"></i> Guardar Perfil
            </button>
            <a href="<?= url('/usuario/dashboard') ?>" class="btn btn-success float-right">
                Cancelar
            </a>
        </div>
        </form>
    </div>
    <!-- /.card -->
</div>
</div>

<div class="alert alert-danger mt-3 d-none" id="form-error"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar tarifas existentes
        <?php
        $rates = [];
        if ($profile) {
            $existingRates = Rate::getByProfileId($profile['id']);
            foreach ($existingRates as $rate) {
                $rates[] = [
                    'rate_type' => $rate['rate_type'],
                    'price' => $rate['price'],
                    'description' => $rate['description']
                ];
            }
        }
        ?>

        const existingRates = <?= json_encode($rates) ?>;

        // Llenar campos de tarifas con datos existentes
        existingRates.forEach(rate => {
            if (rate.rate_type === 'hour') {
                document.getElementById('rate-hour').value = rate.price;
            } else if (rate.rate_type === 'half_hour') {
                document.getElementById('rate-half-hour').value = rate.price;
            } else if (rate.rate_type === 'extra') {
                document.getElementById('rate-extra').value = rate.price;
                document.getElementById('rate-extra-desc').value = rate.description;
            }
        });

        // Manejar selección de ciudad
        const citySelect = document.getElementById('city');
        const otherCityContainer = document.getElementById('other-city-container');
        const otherCityInput = document.getElementById('other-city');

        function toggleOtherCity() {
            if (citySelect.value === 'other') {
                otherCityContainer.style.display = 'block';
                otherCityInput.required = true;
            } else {
                otherCityContainer.style.display = 'none';
                otherCityInput.required = false;
            }
        }

        // Inicializar
        toggleOtherCity();

        // Eventos
        citySelect.addEventListener('change', toggleOtherCity);

        // Envío del formulario
        const profileForm = document.getElementById('profile-form');
        const submitBtn = document.getElementById('submit-btn');
        const formError = document.getElementById('form-error');

        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Antes de enviar, combinar el código de país con el número
            const countryCode = document.getElementById('country_code').value;
            const whatsapp = document.getElementById('whatsapp').value;

            // Crear un campo oculto con el número completo
            const fullWhatsappInput = document.createElement('input');
            fullWhatsappInput.type = 'hidden';
            fullWhatsappInput.name = 'full_whatsapp';
            fullWhatsappInput.value = countryCode + whatsapp;
            this.appendChild(fullWhatsappInput);

            // Resetear mensajes de error
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            formError.classList.add('d-none');
            formError.textContent = '';

            // Recopilar tarifas
            const rates = [];

            const rateHour = document.getElementById('rate-hour').value;
            if (rateHour && parseFloat(rateHour) > 0) {
                rates.push({
                    rate_type: 'hour',
                    price: parseFloat(rateHour),
                    description: '1 hora'
                });
            }

            const rateHalfHour = document.getElementById('rate-half-hour').value;
            if (rateHalfHour && parseFloat(rateHalfHour) > 0) {
                rates.push({
                    rate_type: 'half_hour',
                    price: parseFloat(rateHalfHour),
                    description: 'Media hora'
                });
            }

            const rateExtra = document.getElementById('rate-extra').value;
            const rateExtraDesc = document.getElementById('rate-extra-desc').value;
            if (rateExtra && parseFloat(rateExtra) > 0) {
                rates.push({
                    rate_type: 'extra',
                    price: parseFloat(rateExtra),
                    description: rateExtraDesc || 'Extra'
                });
            }

            // Guardar tarifas en el campo oculto
            document.getElementById('rates-json').value = JSON.stringify(rates);

            // Manejar ciudad personalizada
            if (citySelect.value === 'other') {
                const customCity = otherCityInput.value.trim();
                if (customCity) {
                    citySelect.value = customCity;
                } else {
                    document.getElementById('city-error').textContent = 'Debe especificar la ciudad';
                    citySelect.classList.add('is-invalid');
                    return;
                }
            }

            // Cambiar estado del botón
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            // Enviar formulario
            const formData = new FormData(profileForm);

            fetch(profileForm.action, {
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
                        // Mostrar alerta de éxito antes de redirigir

                        // Usando SweetAlert2 (si está disponible)
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: '¡Perfil guardado!',
                                text: 'Tu perfil ha sido guardado correctamente.',
                                icon: 'success',
                                confirmButtonText: 'Continuar'
                            }).then(() => {
                                // Redirigir después de que el usuario presione el botón
                                window.location.href = '<?= url('/usuario/dashboard') ?>';
                            });
                        } else {
                            // Usando alert nativo como alternativa
                            alert('¡Perfil guardado correctamente!');
                            setTimeout(() => {
                                window.location.href = '<?= url('/usuario/dashboard') ?>';
                            }, 500); // Pequeño retraso para que la alerta sea visible
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    // Mostrar error de conexión
                    formError.classList.remove('d-none');
                    formError.textContent = 'Error de conexión. Intente nuevamente.';

                    // Restaurar botón
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Perfil';
                });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinceSelect = document.getElementById('province_id');
        const districtSelect = document.getElementById('district_id');

        // Función para cargar distritos
        function loadDistricts() {
            const provinceId = provinceSelect.value;

            // Limpiar distritos
            districtSelect.innerHTML = '<option value="">Seleccione un distrito</option>';

            if (!provinceId) {
                districtSelect.disabled = true;
                return;
            }

            // Habilitar select
            districtSelect.disabled = false;

            // Indicador de carga
            districtSelect.innerHTML = '<option value="">Cargando...</option>';

            // Realizar petición AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '<?= url('/ajax_districts.php?province_id=') ?>' + provinceId, true);

            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);

                        // Restaurar opción por defecto
                        districtSelect.innerHTML = '<option value="">Seleccione un distrito</option>';

                        // Agregar opciones de distritos
                        if (response.success && response.districts && response.districts.length > 0) {
                            response.districts.forEach(function(district) {
                                const option = document.createElement('option');
                                option.value = district.id;
                                option.textContent = district.name;

                                // Seleccionar distrito guardado si existe
                                if (<?= isset($profile['district_id']) ? $profile['district_id'] : 'null' ?> == district.id) {
                                    option.selected = true;
                                }

                                districtSelect.appendChild(option);
                            });
                        } else {
                            districtSelect.innerHTML = '<option value="">No hay distritos disponibles</option>';
                        }
                    } catch (e) {
                        console.error('Error al procesar respuesta:', e);
                        districtSelect.innerHTML = '<option value="">Error al cargar distritos</option>';
                    }
                } else {
                    districtSelect.innerHTML = '<option value="">Error al cargar distritos</option>';
                }
            };

            xhr.onerror = function() {
                districtSelect.innerHTML = '<option value="">Error de conexión</option>';
            };

            xhr.send();
        }

        // Evento change en provincia
        provinceSelect.addEventListener('change', loadDistricts);

        // Cargar distritos iniciales si hay provincia seleccionada
        if (provinceSelect.value) {
            loadDistricts();
        }
    });
</script>

<script>
    // Búsqueda de provincias
const provinceSearch = document.getElementById('province-search');
if (provinceSearch) {
    provinceSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const provinceOptions = provinceSelect.querySelectorAll('option');
        let foundProvince = false;
        
        provinceOptions.forEach(option => {
            if (option.value === '') {
                // Mantener siempre visible la opción "Seleccione una provincia"
                option.style.display = '';
                return;
            }
            
            const provinceName = option.textContent.toLowerCase();
            if (provinceName.includes(searchTerm)) {
                option.style.display = '';
                foundProvince = true;
                
                // Si la provincia coincide exactamente, seleccionarla automáticamente
                if (provinceName === searchTerm) {
                    provinceSelect.value = option.value;
                    // Cargar los distritos correspondientes
                    loadDistricts();
                }
            } else {
                option.style.display = 'none';
            }
        });
        
        // Mostrar mensaje si no se encontraron provincias
        if (!foundProvince && searchTerm) {
            // Si no había mensaje de "no se encontraron provincias", agregarlo
            if (!provinceSelect.querySelector('option[data-no-results]')) {
                const noResultsOption = document.createElement('option');
                noResultsOption.textContent = 'No se encontraron provincias con ese nombre';
                noResultsOption.disabled = true;
                noResultsOption.setAttribute('data-no-results', 'true');
                provinceSelect.appendChild(noResultsOption);
            }
        } else {
            // Si había mensaje, eliminarlo
            const noResultsOption = provinceSelect.querySelector('option[data-no-results]');
            if (noResultsOption) {
                provinceSelect.removeChild(noResultsOption);
            }
        }
    });
}

// Búsqueda de distritos
const districtSearch = document.getElementById('district-search');
if (districtSearch) {
    districtSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const districtOptions = districtSelect.querySelectorAll('option');
        let foundDistrict = false;
        
        districtOptions.forEach(option => {
            if (option.value === '') {
                // Mantener siempre visible la opción por defecto
                option.style.display = '';
                return;
            }
            
            const districtName = option.textContent.toLowerCase();
            if (districtName.includes(searchTerm)) {
                option.style.display = '';
                foundDistrict = true;
                
                // Si el distrito coincide exactamente, seleccionarlo automáticamente
                if (districtName === searchTerm) {
                    districtSelect.value = option.value;
                }
            } else {
                option.style.display = 'none';
            }
        });
        
        // Mostrar mensaje si no se encontraron distritos
        if (!foundDistrict && searchTerm) {
            // Si no había mensaje de "no se encontraron distritos", agregarlo
            if (!districtSelect.querySelector('option[data-no-results]')) {
                const noResultsOption = document.createElement('option');
                noResultsOption.textContent = 'No se encontraron distritos con ese nombre';
                noResultsOption.disabled = true;
                noResultsOption.setAttribute('data-no-results', 'true');
                districtSelect.appendChild(noResultsOption);
            }
        } else {
            // Si había mensaje, eliminarlo
            const noResultsOption = districtSelect.querySelector('option[data-no-results]');
            if (noResultsOption) {
                districtSelect.removeChild(noResultsOption);
            }
        }
    });
}

// Actualizar estado del campo de búsqueda de distritos cuando cambia la provincia
provinceSelect.addEventListener('change', function() {
    if (districtSearch) {
        districtSearch.disabled = !this.value;
        if (!this.value) {
            districtSearch.value = '';
        }
    }
});
</script>

<style>
    /* Estilo para opciones ocultas */
    select option[style*="display: none"] {
        display: none !important;
    }
    
    /* Estilo para el mensaje de "no se encontraron resultados" */
    select option[data-no-results] {
        font-style: italic;
        color: #999;
    }
    
    /* Estilo para enfatizar los campos de búsqueda */
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #ced4da;
    }
    
    /* Efecto hover para el ícono de búsqueda */
    .input-group:hover .input-group-text {
        background-color: #e9ecef;
    }
</style>