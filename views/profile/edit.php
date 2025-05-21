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
                    value="<?= htmlspecialchars(preg_replace('/^(\+?51)/', '', ($profile['whatsapp'] ?? ''))) ?>"
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

    // Envío del formulario
    const profileForm = document.getElementById('profile-form');
    const submitBtn = document.getElementById('submit-btn');
    const formError = document.getElementById('form-error');

    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();

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
                console.log('Respuesta del servidor:', data);
                
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
                    console.log('Redirección exitosa a:', data.redirect);
                    
                    // Mostrar mensaje antes de redirigir
                    formError.classList.remove('d-none');
                    formError.classList.remove('alert-danger');
                    formError.classList.add('alert-success');
                    formError.textContent = data.message || 'Perfil guardado correctamente';
                    
                    // Forzar redirección después de un breve retraso
                    setTimeout(function() {
                        console.log('Ejecutando redirección...');
                        window.location.replace(data.redirect);
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Error en la petición:', error);

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

<!-- Estilos para los selectores con búsqueda -->
<style>
  /* Contenedor de select personalizado */
  .select-search-container {
    position: relative;
    width: 100%;
  }

  /* Estilos para el campo de búsqueda */
  .select-search-input {
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }

  .select-search-input:focus {
    color: #495057;
    background-color: #fff;
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  }

  /* Dropdown de resultados */
  .select-search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    width: 100%;
    max-height: 300px;
    overflow-y: auto;
    padding: 0.5rem 0;
    margin: 0.125rem 0 0;
    font-size: 1rem;
    color: #212529;
    text-align: left;
    list-style: none;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0, 0, 0, 0.15);
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
    display: none;
  }

  .select-search-dropdown.show {
    display: block;
  }

  /* Opciones en el dropdown */
  .select-search-option {
    display: block;
    width: 100%;
    padding: 0.25rem 1.5rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
    cursor: pointer;
  }

  .select-search-option:hover,
  .select-search-option.active {
    color: #16181b;
    text-decoration: none;
    background-color: #f8f9fa;
  }

  .select-search-option.selected {
    color: #000;
    text-decoration: none;
    background-color: #007bff;
  }

  /* Mensaje de no resultados */
  .select-search-no-results {
    padding: 0.5rem 1.5rem;
    color: #6c757d;
    font-style: italic;
  }

  /* Highlight para términos de búsqueda */
  .select-search-highlight {
    background-color: #ffffd0;
    font-weight: bold;
  }

  /* Ícono de búsqueda */
  .select-search-icon {
    position: absolute;
    right: 10px;
    top: 10px;
    color: #6c757d;
    pointer-events: none;
  }

  /* Spinner de carga */
  .select-search-loading {
    display: none;
    position: absolute;
    right: 10px;
    top: 10px;
    color: #6c757d;
  }

  /* Original select (oculto) */
  .select-search-original {
    position: absolute;
    opacity: 0;
    pointer-events: none;
  }
</style>

<!-- Script para implementar la búsqueda en los selectores -->

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos
    const provinceSelect = document.getElementById('province_id');
    const districtSelect = document.getElementById('district_id');

    if (!provinceSelect || !districtSelect) {
      console.error('Error: No se encontraron los selectores de provincia o distrito');
      return;
    }

    // Función para cargar distritos
    function loadDistricts(provinceId) {
      // Deshabilitar temporalmente el selector
      districtSelect.disabled = true;

      // Mostrar opción de carga
      districtSelect.innerHTML = '<option value="">Cargando distritos...</option>';

      // Usar directamente la URL que ya funciona
      const ajaxUrl = '<?= url('/ajax_districts.php') ?>?province_id=' + provinceId;

      console.log('Cargando distritos desde: ' + ajaxUrl);

      // Realizar petición AJAX
      const xhr = new XMLHttpRequest();
      xhr.open('GET', ajaxUrl, true);

      xhr.onload = function() {
        if (xhr.status === 200) {
          try {
            const response = JSON.parse(xhr.responseText);
            console.log('Respuesta recibida:', response);

            // Restaurar opciones por defecto
            districtSelect.innerHTML = '<option value="">Seleccione un distrito</option>';

            if (response.success && response.districts && response.districts.length > 0) {
              // Añadir opciones
              response.districts.forEach(function(district) {
                const option = document.createElement('option');
                option.value = district.id;
                option.textContent = district.name;
                districtSelect.appendChild(option);
              });

              // Habilitar selector
              districtSelect.disabled = false;

              // Restaurar distrito seleccionado si existe
              const savedDistrictId = '<?= isset($profile['district_id']) ? $profile['district_id'] : '' ?>';
              if (savedDistrictId) {
                // Buscar y seleccionar el distrito guardado
                Array.from(districtSelect.options).forEach(function(option, index) {
                  if (option.value === savedDistrictId) {
                    districtSelect.selectedIndex = index;
                  }
                });
              }
            } else {
              districtSelect.innerHTML = '<option value="">No hay distritos disponibles</option>';
            }
          } catch (e) {
            console.error('Error al procesar la respuesta:', e);
            console.log('Texto de respuesta:', xhr.responseText);
            districtSelect.innerHTML = '<option value="">Error al cargar distritos</option>';
          }
        } else {
          console.error('Error HTTP:', xhr.status);
          districtSelect.innerHTML = '<option value="">Error al cargar distritos</option>';
        }
      };

      xhr.onerror = function() {
        console.error('Error de conexión');
        districtSelect.innerHTML = '<option value="">Error de conexión</option>';
        districtSelect.disabled = true;
      };

      xhr.send();
    }

    // Manejar cambio de provincia
    provinceSelect.addEventListener('change', function() {
      const provinceId = this.value;

      if (!provinceId) {
        // Limpiar y deshabilitar distritos si no hay provincia seleccionada
        districtSelect.innerHTML = '<option value="">Seleccione una provincia primero</option>';
        districtSelect.disabled = true;
      } else {
        // Cargar distritos para la provincia seleccionada
        loadDistricts(provinceId);
      }
    });

    // Cargar distritos iniciales si hay una provincia seleccionada
    if (provinceSelect.value) {
      console.log('Iniciando carga de distritos para la provincia seleccionada:', provinceSelect.value);
      // Usar setTimeout para asegurar que los eventos se procesen correctamente
      setTimeout(function() {
        loadDistricts(provinceSelect.value);
      }, 300);
    }
  });
</script>

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
    
    // Preparar el campo de tarifas antes de enviar el formulario
    document.getElementById('profile-form').addEventListener('submit', function() {
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
        
        document.getElementById('rates-json').value = JSON.stringify(rates);
    });
});
</script>