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
  // Inicializar selectores con búsqueda
  initSelectSearch('province_id', {
    placeholder: 'Buscar provincia...',
    noResultsText: 'No se encontraron provincias'
  });
  
  initSelectSearch('district_id', {
    placeholder: 'Buscar distrito...',
    noResultsText: 'No se encontraron distritos',
    dependsOn: 'province_id'
  });
  
  /**
   * Inicializa un selector con búsqueda
   * @param {string} selectId - ID del select original
   * @param {object} options - Opciones de configuración
   */
  function initSelectSearch(selectId, options = {}) {
    const originalSelect = document.getElementById(selectId);
    if (!originalSelect) return;
    
    // Opciones por defecto
    const config = Object.assign({
      placeholder: 'Buscar...',
      noResultsText: 'No se encontraron resultados',
      dependsOn: null, // ID del select del que depende (para distritos)
      onSelect: null // Callback cuando se selecciona una opción
    }, options);
    
    // Si depende de otro selector, inicialmente podría estar deshabilitado
    if (config.dependsOn) {
      const parentSelect = document.getElementById(config.dependsOn);
      if (!parentSelect || !parentSelect.value) {
        // Mantener la funcionalidad original sin alterar
        return;
      }
    }
    
    // Crear contenedor
    const container = document.createElement('div');
    container.className = 'select-search-container';
    container.id = `${selectId}-container`;
    
    // Crear campo de búsqueda
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control select-search-input';
    searchInput.placeholder = config.placeholder;
    searchInput.id = `${selectId}-search`;
    searchInput.autocomplete = 'off';
    
    // Añadir ícono de búsqueda
    const searchIcon = document.createElement('span');
    searchIcon.className = 'select-search-icon';
    searchIcon.innerHTML = '<i class="fas fa-search"></i>';
    
    // Añadir spinner de carga
    const loadingSpinner = document.createElement('span');
    loadingSpinner.className = 'select-search-loading';
    loadingSpinner.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Crear dropdown de resultados
    const dropdown = document.createElement('div');
    dropdown.className = 'select-search-dropdown';
    
    // Preparar el valor inicial
    const selectedOption = originalSelect.options[originalSelect.selectedIndex];
    if (selectedOption && selectedOption.value) {
      searchInput.value = selectedOption.textContent.trim();
    }
    
    // Añadir elementos al contenedor
    container.appendChild(searchInput);
    container.appendChild(searchIcon);
    container.appendChild(loadingSpinner);
    container.appendChild(dropdown);
    
    // Insertar en el DOM
    originalSelect.parentNode.insertBefore(container, originalSelect);
    originalSelect.classList.add('select-search-original');
    
    // Variable para almacenar todas las opciones
    let allOptions = [];
    
    // Función para obtener opciones del select original
    function getOptionsFromSelect() {
      allOptions = [];
      Array.from(originalSelect.options).forEach(option => {
        if (option.value) {
          allOptions.push({
            value: option.value,
            text: option.textContent.trim()
          });
        }
      });
      return allOptions;
    }
    
    // Inicializar con las opciones actuales
    getOptionsFromSelect();
    
    // Función para mostrar opciones filtradas
    function showFilteredOptions(filterText = '') {
      // Limpiar dropdown
      dropdown.innerHTML = '';
      
      // Obtener opciones actualizadas
      const options = getOptionsFromSelect();
      
      // Filtrar opciones según texto
      const filteredOptions = filterText ? 
        options.filter(option => option.text.toLowerCase().includes(filterText.toLowerCase())) :
        options;
      
      // Mostrar mensaje si no hay resultados
      if (filteredOptions.length === 0) {
        const noResults = document.createElement('div');
        noResults.className = 'select-search-no-results';
        noResults.textContent = config.noResultsText;
        dropdown.appendChild(noResults);
      } else {
        // Añadir opciones al dropdown
        filteredOptions.forEach(option => {
          const optionElement = document.createElement('div');
          optionElement.className = 'select-search-option';
          optionElement.dataset.value = option.value;
          
          // Resaltar texto si hay filtro
          let displayText = option.text;
          if (filterText) {
            const regex = new RegExp(`(${filterText})`, 'gi');
            displayText = displayText.replace(regex, '<span class="select-search-highlight">$1</span>');
          }
          
          optionElement.innerHTML = displayText;
          
          // Marcar como seleccionada si es la opción actual
          if (option.value === originalSelect.value) {
            optionElement.classList.add('selected');
          }
          
          // Evento de clic
          optionElement.addEventListener('click', function() {
            // Establecer valor en el select original
            originalSelect.value = option.value;
            
            // Actualizar texto del input
            searchInput.value = option.text;
            
            // Cerrar dropdown
            dropdown.classList.remove('show');
            
            // Disparar evento change en el select original
            const event = new Event('change', { bubbles: true });
            originalSelect.dispatchEvent(event);
            
            // Ejecutar callback si existe
            if (typeof config.onSelect === 'function') {
              config.onSelect(option);
            }
          });
          
          dropdown.appendChild(optionElement);
        });
      }
      
      // Mostrar dropdown
      dropdown.classList.add('show');
    }
    
    // Eventos para el campo de búsqueda
    searchInput.addEventListener('focus', function() {
      showFilteredOptions(this.value);
    });
    
    searchInput.addEventListener('input', function() {
      showFilteredOptions(this.value);
      
      // Si hay una coincidencia exacta, seleccionarla
      const searchTerm = this.value.toLowerCase();
      const exactMatch = allOptions.find(option => option.text.toLowerCase() === searchTerm);
      
      if (exactMatch) {
        originalSelect.value = exactMatch.value;
        
        // Disparar evento change
        const event = new Event('change', { bubbles: true });
        originalSelect.dispatchEvent(event);
      }
    });
    
    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(e) {
      if (!container.contains(e.target)) {
        dropdown.classList.remove('show');
        
        // Restaurar texto de la opción seleccionada
        const selectedOption = originalSelect.options[originalSelect.selectedIndex];
        searchInput.value = selectedOption && selectedOption.value ? 
          selectedOption.textContent.trim() : '';
      }
    });
    
    // Navegación con teclado
    searchInput.addEventListener('keydown', function(e) {
      const options = dropdown.querySelectorAll('.select-search-option');
      let activeOption = dropdown.querySelector('.select-search-option.active');
      let selectedIndex = -1;
      
      if (activeOption) {
        selectedIndex = Array.from(options).indexOf(activeOption);
      }
      
      switch (e.key) {
        case 'ArrowDown':
          e.preventDefault();
          if (!dropdown.classList.contains('show')) {
            showFilteredOptions(this.value);
          } else if (options.length > 0) {
            const nextIndex = selectedIndex < options.length - 1 ? selectedIndex + 1 : 0;
            if (activeOption) activeOption.classList.remove('active');
            options[nextIndex].classList.add('active');
            options[nextIndex].scrollIntoView({ block: 'nearest' });
          }
          break;
        
        case 'ArrowUp':
          e.preventDefault();
          if (options.length > 0) {
            const prevIndex = selectedIndex > 0 ? selectedIndex - 1 : options.length - 1;
            if (activeOption) activeOption.classList.remove('active');
            options[prevIndex].classList.add('active');
            options[prevIndex].scrollIntoView({ block: 'nearest' });
          }
          break;
        
        case 'Enter':
          e.preventDefault();
          if (activeOption) {
            activeOption.click();
          } else if (options.length === 1) {
            // Si solo hay una opción, seleccionarla
            options[0].click();
          }
          break;
        
        case 'Escape':
          e.preventDefault();
          dropdown.classList.remove('show');
          break;
      }
    });
    
    // Si el select depende de otro, actualizar cuando cambie
    if (config.dependsOn) {
      const parentSelect = document.getElementById(config.dependsOn);
      if (parentSelect) {
        parentSelect.addEventListener('change', function() {
          // Si la dependencia está seleccionada, habilitar el campo
          if (this.value) {
            searchInput.disabled = false;
            
            // Actualizar opciones después de que el select original se actualice
            setTimeout(() => {
              // Actualizar la lista de opciones
              getOptionsFromSelect();
              
              // Limpiar input y cerrar dropdown
              searchInput.value = '';
              dropdown.classList.remove('show');
            }, 300);
          } else {
            // Si no hay valor en la dependencia, deshabilitar el campo
            searchInput.disabled = true;
            searchInput.value = '';
            dropdown.classList.remove('show');
          }
        });
        
        // Inicializamos según el estado actual
        searchInput.disabled = !parentSelect.value;
      }
    }
    
    // Actualizar el input si cambia el select programáticamente
    originalSelect.addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      if (selectedOption && selectedOption.value) {
        searchInput.value = selectedOption.textContent.trim();
      } else {
        searchInput.value = '';
      }
    });
  }
  
  // Mantener la funcionalidad original de carga de distritos al cambiar provincia
  const provinceSelect = document.getElementById('province_id');
  if (provinceSelect) {
    provinceSelect.addEventListener('change', function() {
      const provinceId = this.value;
      const districtSelect = document.getElementById('district_id');
      const districtSearchInput = document.getElementById('district_id-search');
      
      if (!districtSelect) return;
      
      // Limpiar distritos
      districtSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
      
      if (!provinceId) {
        districtSelect.disabled = true;
        if (districtSearchInput) {
          districtSearchInput.disabled = true;
          districtSearchInput.value = '';
        }
        return;
      }
      
      // Habilitar select
      districtSelect.disabled = false;
      if (districtSearchInput) {
        districtSearchInput.disabled = false;
      }
      
      // Indicador de carga
      districtSelect.innerHTML = '<option value="">Cargando...</option>';
      
      // Mostrar spinner de carga si existe
      const loadingSpinner = document.querySelector('#district_id-container .select-search-loading');
      if (loadingSpinner) {
        loadingSpinner.style.display = 'block';
      }
      
      // Realizar petición AJAX
      const xhr = new XMLHttpRequest();
      xhr.open('GET', `<?= url('/ajax_districts.php?province_id=') ?>${provinceId}`, true);
      
      xhr.onload = function() {
        // Ocultar spinner de carga
        if (loadingSpinner) {
          loadingSpinner.style.display = 'none';
        }
        
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
              
              // Disparar un evento change para actualizar el campo de búsqueda
              const event = new Event('change', { bubbles: true });
              districtSelect.dispatchEvent(event);
            } else {
              districtSelect.innerHTML = '<option value="">No hay distritos disponibles</option>';
              
              // Limpiar campo de búsqueda si existe
              if (districtSearchInput) {
                districtSearchInput.value = '';
              }
            }
          } catch (e) {
            console.error('Error al procesar respuesta:', e);
            districtSelect.innerHTML = '<option value="">Error al cargar distritos</option>';
            
            // Limpiar campo de búsqueda si existe
            if (districtSearchInput) {
              districtSearchInput.value = '';
            }
          }
        } else {
          districtSelect.innerHTML = '<option value="">Error al cargar distritos</option>';
          
          // Limpiar campo de búsqueda si existe
          if (districtSearchInput) {
            districtSearchInput.value = '';
          }
        }
      };
      
      xhr.onerror = function() {
        // Ocultar spinner de carga
        if (loadingSpinner) {
          loadingSpinner.style.display = 'none';
        }
        
        districtSelect.innerHTML = '<option value="">Error de conexión</option>';
        
        // Limpiar campo de búsqueda si existe
        if (districtSearchInput) {
          districtSearchInput.value = '';
        }
      };
      
      xhr.send();
    });
  }
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
  // Referencia al select de provincias y distritos
  const provinceSelect = document.getElementById('province_id');
  const districtSelect = document.getElementById('district_id');
  
  if (!provinceSelect || !districtSelect) return;
  
  // Función para cargar distritos cuando cambia la provincia
  function loadDistricts(provinceId) {
    // Mostrar indicador de carga
    districtSelect.innerHTML = '<option value="">Cargando distritos...</option>';
    districtSelect.disabled = true;
    
    // Mostrar spinner de carga si existe
    const loadingSpinner = document.querySelector('#district_id-container .select-search-loading');
    if (loadingSpinner) {
      loadingSpinner.style.display = 'block';
    }
    
    // Realizar petición AJAX para cargar distritos
    fetch(`/ajax_districts.php?province_id=${provinceId}`)
      .then(response => response.json())
      .then(data => {
        // Restaurar opción por defecto
        districtSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
        districtSelect.disabled = false;
        
        // Ocultar spinner
        if (loadingSpinner) {
          loadingSpinner.style.display = 'none';
        }
        
        // Agregar opciones de distritos
        if (data.success && data.districts && data.districts.length > 0) {
          data.districts.forEach(function(district) {
            const option = document.createElement('option');
            option.value = district.id;
            option.textContent = district.name;
            districtSelect.appendChild(option);
          });
          
          // Disparar un evento change para actualizar cualquier componente dependiente
          const event = new Event('change', { bubbles: true });
          districtSelect.dispatchEvent(event);
          
          // Reinicializar el buscador de distritos si existe
          reinitDistrictSearch();
        } else {
          districtSelect.innerHTML = '<option value="">No hay distritos disponibles</option>';
        }
      })
      .catch(error => {
        console.error('Error al cargar distritos:', error);
        districtSelect.innerHTML = '<option value="">Error al cargar distritos</option>';
        districtSelect.disabled = true;
        
        if (loadingSpinner) {
          loadingSpinner.style.display = 'none';
        }
      });
  }
  
  // Escuchar cambios en el select de provincias
  provinceSelect.addEventListener('change', function() {
    const provinceId = this.value;
    
    // Limpiar distrito cuando se cambia la provincia
    districtSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
    districtSelect.value = '';
    
    if (!provinceId) {
      districtSelect.disabled = true;
      // Si hay un contenedor de búsqueda, deshabilitarlo
      const districtSearchInput = document.getElementById('district_id-search');
      if (districtSearchInput) {
        districtSearchInput.disabled = true;
        districtSearchInput.value = '';
      }
      return;
    }
    
    // Cargar distritos para la provincia seleccionada
    loadDistricts(provinceId);
  });
  
  // Reinicializar la búsqueda de distritos
  function reinitDistrictSearch() {
    // Si existe el select-search para distrito, reinicializarlo
    const districtContainer = document.getElementById('district_id-container');
    const districtSearchInput = document.getElementById('district_id-search');
    
    if (districtContainer && districtSearchInput) {
      // Eliminar distrito container existente
      districtContainer.remove();
      
      // Hacer visible el select original temporalmente
      districtSelect.classList.remove('select-search-original');
      districtSelect.style.display = '';
      
      // Volver a inicializar el selector de búsqueda
      setTimeout(() => {
        if (typeof initSelectSearch === 'function') {
          initSelectSearch('district_id', {
            placeholder: 'Buscar distrito...',
            noResultsText: 'No se encontraron distritos',
            dependsOn: 'province_id'
          });
        }
      }, 100);
    }
  }
  
  // Inicializar distritos si ya hay una provincia seleccionada
  if (provinceSelect.value) {
    loadDistricts(provinceSelect.value);
  }
  
  // Mejorar la funcionalidad de la búsqueda de distritos
  document.addEventListener('click', function(e) {
    // Si el usuario hace clic en el contenedor del distrito y está deshabilitado
    if (e.target.closest('#district_id-container') && districtSelect.disabled) {
      // Informar al usuario que debe seleccionar una provincia primero
      alert('Por favor, seleccione una provincia primero');
      
      // Opcional: enfocar en el selector de provincia para guiar al usuario
      const provinceSearchInput = document.getElementById('province_id-search');
      if (provinceSearchInput) {
        provinceSearchInput.focus();
      } else {
        provinceSelect.focus();
      }
    }
  });
  
  // Comprobar si los selectores de búsqueda están inicializados correctamente
  setTimeout(function() {
    const provinceContainer = document.getElementById('province_id-container');
    const districtContainer = document.getElementById('district_id-container');
    
    // Si provinciaContainer existe pero districtContainer no, podría haber un problema
    if (provinceContainer && !districtContainer && !districtSelect.disabled) {
      reinitDistrictSearch();
    }
  }, 500);
});
</script>
