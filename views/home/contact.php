<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h1 class="mb-4"><?= $pageHeader ?></h1>
                    
                    <p class="lead">
                        Estamos aquí para ayudarte. Completa el formulario a continuación y nos pondremos 
                        en contacto contigo lo antes posible.
                    </p>
                    
                    <form id="contact-form" method="post" action="<?= url('/contacto/enviar') ?>">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        
                        <div class="form-group">
                            <label for="name">Nombre completo</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Asunto</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                            <div class="invalid-feedback" id="subject-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Mensaje</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            <div class="invalid-feedback" id="message-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                Enviar Mensaje
                            </button>
                        </div>
                    </form>
                    
                    <div class="alert mt-4 d-none" id="form-message"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Información de Contacto</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                            Av. Principal 123, Lima, Perú
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-phone text-primary mr-2"></i>
                            +51 999 999 999
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-envelope text-primary mr-2"></i>
                            contacto@example.com
                        </li>
                        <li>
                            <i class="fas fa-clock text-primary mr-2"></i>
                            Lunes a Viernes, 9:00 AM - 6:00 PM
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Preguntas Frecuentes</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="card border-0">
                            <div class="card-header bg-white" id="faqHeading1">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed text-left w-100" type="button" 
                                            data-toggle="collapse" data-target="#faqCollapse1">
                                        ¿Cómo publico mi perfil?
                                    </button>
                                </h2>
                            </div>
                            <div id="faqCollapse1" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Para publicar tu perfil, regístrate como anunciante y completa toda la 
                                    información requerida. Tu perfil tendrá 15 días gratuitos.
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0">
                            <div class="card-header bg-white" id="faqHeading2">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed text-left w-100" type="button" 
                                            data-toggle="collapse" data-target="#faqCollapse2">
                                        ¿Cómo contacto a los anunciantes?
                                    </button>
                                </h2>
                            </div>
                            <div id="faqCollapse2" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Para contactar a los anunciantes, necesitas registrarte como visitante 
                                    y adquirir una suscripción. Esto te dará acceso a todos los perfiles y 
                                    a sus datos de contacto.
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0">
                            <div class="card-header bg-white" id="faqHeading3">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed text-left w-100" type="button" 
                                            data-toggle="collapse" data-target="#faqCollapse3">
                                        ¿Cómo funciona la verificación?
                                    </button>
                                </h2>
                            </div>
                            <div id="faqCollapse3" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    La verificación es un proceso opcional para anunciantes que desean 
                                    aumentar la confianza en su perfil. Requiere enviar una identificación 
                                    válida que será revisada por nuestro equipo.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    const submitBtn = document.getElementById('submit-btn');
    const formMessage = document.getElementById('form-message');
    
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Resetear mensajes de error
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        formMessage.classList.add('d-none');
        formMessage.textContent = '';
        
        // Cambiar estado del botón
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        
        // Enviar formulario
        const formData = new FormData(contactForm);
        
        fetch(contactForm.action, {
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
                submitBtn.innerHTML = 'Enviar Mensaje';
                
            } else if (data.error) {
                // Mostrar error general
                formMessage.classList.remove('d-none');
                formMessage.classList.add('alert-danger');
                formMessage.classList.remove('alert-success');
                formMessage.textContent = data.error;
                
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Enviar Mensaje';
                
            } else if (data.success) {
                // Mostrar mensaje de éxito
                formMessage.classList.remove('d-none');
                formMessage.classList.remove('alert-danger');
                formMessage.classList.add('alert-success');
                formMessage.textContent = data.message || 'Mensaje enviado correctamente';
                
                // Limpiar formulario
                contactForm.reset();
                
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Enviar Mensaje';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Mostrar error de conexión
            formMessage.classList.remove('d-none');
            formMessage.classList.add('alert-danger');
            formMessage.classList.remove('alert-success');
            formMessage.textContent = 'Error de conexión. Intente nuevamente.';
            
            // Restaurar botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Enviar Mensaje';
        });
    });
});
</script>