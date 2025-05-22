<div class="hero-section position-relative">
    <!-- Banner principal con overlay de gradiente -->
    <div class="banner-overlay position-absolute w-100 h-100" style="background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(198,35,104,0.5) 100%);"></div>
    
    <div class="container-fluid py-5">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-6 text-white position-relative z-index-1">
                    <h1 class="display-4 font-weight-bold mb-3 animate__animated animate__fadeInLeft">Encuentra compañía de calidad</h1>
                    <p class="lead mb-4 animate__animated animate__fadeInLeft animate__delay-1s">Miles de perfiles verificados disponibles 24/7 en toda la ciudad</p>
                    
                    <!-- Badges destacados -->
                    <div class="d-flex flex-wrap mb-4 animate__animated animate__fadeInUp animate__delay-2s">
                        <span class="badge badge-pill badge-light mr-2 mb-2 p-2"><i class="fas fa-check-circle text-success"></i> 100% Verificados</span>
                        <span class="badge badge-pill badge-light mr-2 mb-2 p-2"><i class="fas fa-shield-alt text-primary"></i> Discreto y Seguro</span>
                        <span class="badge badge-pill badge-light mr-2 mb-2 p-2"><i class="fas fa-clock text-info"></i> Disponibles 24/7</span>
                        <span class="badge badge-pill badge-light mr-2 mb-2 p-2"><i class="fas fa-map-marker-alt text-danger"></i> En toda la ciudad</span>
                    </div>

                    <form action="<?= url('/buscar') ?>" method="GET" class="search-form animate__animated animate__fadeIn animate__delay-2s">
                        <div class="input-group input-group-lg mb-3 shadow">
                            <input type="text" name="q" class="form-control border-0" placeholder="Buscar Por Nombre, ciudad...?">
                            <div class="input-group-append">
                                <button class="btn btn-primary px-4" type="submit">
                                    <i class="fas fa-search mr-1"></i> Buscar
                                </button>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <select name="city" class="form-control">
                                    <option value="">Todas las ciudades</option>
                                    <?php foreach ($provinces as $province): ?>
                                        <option value="<?= $province['id'] ?>"><?= htmlspecialchars($province['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="btn-group btn-group-toggle w-100 gender-toggle" data-toggle="buttons">
                                    <label class="btn btn-outline-light active">
                                        <input type="radio" name="gender" value="female" checked> Erophia
                                    </label>
                                    <label class="btn btn-outline-light">
                                        <input type="radio" name="gender" value="male"> Erophian
                                    </label>
                                    <label class="btn btn-outline-light">
                                        <input type="radio" name="gender" value="trans"> Eromix
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-6 d-none d-lg-block position-relative z-index-1">
                    <div class="hero-image-container animate__animated animate__fadeIn">
                        <img src="<?= url('img/foto_banner.png') ?>" alt="Encuentra compañía" class="img-fluid rounded-lg banner-image">
                        <div class="floating-badge bg-success text-white px-3 py-2 rounded-pill">
                            <i class="fas fa-camera"></i> Fotos 100% reales
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <?php if (!$hasAccess): ?>
        <div class="alert alert-primary border-0 shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-2"><i class="fas fa-lock mr-2"></i> Acceso exclusivo</h4>
                    <p class="mb-0">Para ver los detalles completos de los perfiles y contactar directamente,
                        <a href="<?= url('/login') ?>" class="font-weight-bold">inicia sesión</a> o
                        <a href="<?= url('/registro') ?>" class="font-weight-bold">regístrate</a> ahora.
                    </p>
                </div>
                <div class="col-md-4 text-md-right mt-3 mt-md-0">
                    <a href="<?= url('/pago/planes') ?>" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-crown mr-1"></i> Ver planes
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Categorías principales con iconos -->
    <div class="category-cards row mb-5 mt-4">
        <div class="col-6 col-md-3 mb-4">
            <a href="<?= url('/categoria/female') ?>" class="card h-100 bg-gradient-pink text-white border-0 category-card">
                <div class="card-body text-center py-4">
                    <div class="category-icon mb-3">
                        <i class="fas fa-female fa-3x"></i>
                    </div>
                    <h3 class="h4 mb-2">Erophia</h3>
                    <p class="mb-0"><?= Profile::countByGender('female') ?> perfiles disponibles</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3 mb-4">
            <a href="<?= url('/categoria/male') ?>" class="card h-100 bg-gradient-blue text-white border-0 category-card">
                <div class="card-body text-center py-4">
                    <div class="category-icon mb-3">
                        <i class="fas fa-male fa-3x"></i>
                    </div>
                    <h3 class="h4 mb-2">Erophian</h3>
                    <p class="mb-0"><?= Profile::countByGender('male') ?> perfiles disponibles</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3 mb-4">
            <a href="<?= url('/categoria/trans') ?>" class="card h-100 bg-gradient-purple text-white border-0 category-card">
                <div class="card-body text-center py-4">
                    <div class="category-icon mb-3">
                        <i class="fas fa-transgender fa-3x"></i>
                    </div>
                    <h3 class="h4 mb-2">Eromix</h3>
                    <p class="mb-0"><?= Profile::countByGender('trans') ?> perfiles disponibles</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3 mb-4">
            <a href="<?= url('/buscar') ?>" class="card h-100 bg-gradient-orange text-white border-0 category-card">
                <div class="card-body text-center py-4">
                    <div class="category-icon mb-3">
                        <i class="fas fa-search fa-3x"></i>
                    </div>
                    <h3 class="h4 mb-2">Búsqueda</h3>
                    <p class="mb-0">Encuentra exactamente lo que buscas</p>
                </div>
            </a>
        </div>
    </div>

    <h2 class="section-title mb-4">
        <span class="text-primary"><i class="fas fa-star mr-2"></i>Perfiles Destacados</span>
        <small class="text-muted float-right d-none d-md-block mt-2">Actualizados recientemente</small>
    </h2>

    <div class="featured-profiles mb-5">
        <div class="row">
            <?php if (empty($featuredProfiles)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No hay perfiles destacados disponibles en este momento.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($featuredProfiles as $profile): ?>
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                        <div class="card profile-card h-100 shadow-sm border-0">
                            <div class="profile-card-badge bg-primary text-white">Destacado</div>
                            <div class="protected-photo-container position-relative" style="height: 220px;">
                                <?php if (!empty($profile['main_photo'])): ?>
                                    <img src="<?= url('uploads/photos/' . $profile['main_photo']) ?>"
                                        class="photo-preview" alt="<?= htmlspecialchars($profile['name']) ?>"
                                        loading="lazy" draggable="false">
                                <?php else: ?>
                                    <img src="<?= url('img/profile-placeholder.jpg') ?>"
                                        class="photo-preview" alt="Sin foto"
                                        loading="lazy" draggable="false">
                                <?php endif; ?>

                                <?php if ($profile['is_verified']): ?>
                                    <span class="badge badge-success verified-badge" style="position: absolute; z-index: 15; right: 10px; font-size: 10px;">
                                        <i class="fas fa-check-circle"></i> Verificado
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="card-body p-2">
                                <h5 class="card-title mb-1 text-truncate font-weight-bold"><?= htmlspecialchars($profile['name']) ?></h5>
                                <p class="card-text mb-1 text-muted small">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    <?php
                                    // Mostrar provincia y distrito si están disponibles
                                    if (!empty($profile['province_name']) && !empty($profile['district_name'])) {
                                        echo htmlspecialchars($profile['province_name'] . ', ' . $profile['district_name']);
                                    } elseif (!empty($profile['province_name'])) {
                                        echo htmlspecialchars($profile['province_name']);
                                    } elseif (!empty($profile['province_id'])) {
                                        // Si solo tenemos IDs pero no nombres, intentar obtener los nombres
                                        $provinceName = Profile::getProvinceNameById($profile['province_id']);
                                        $districtName = !empty($profile['district_id']) ? Profile::getDistrictNameById($profile['district_id']) : '';

                                        if ($districtName) {
                                            echo htmlspecialchars($provinceName . ', ' . $districtName);
                                        } else {
                                            echo htmlspecialchars($provinceName);
                                        }
                                    } else {
                                        echo 'Ubicación no especificada';
                                    }
                                    ?>
                                </p>
                            </div>

                            <div class="card-footer bg-white p-2 text-center border-0">
                                <a href="<?= url('/perfil/' . $profile['id']) ?>" class="btn btn-sm btn-primary btn-block">
                                    Ver Perfil <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="text-center mt-3 mb-5">
        <a href="<?= url('/categoria/female') ?>" class="btn btn-lg btn-primary shadow-sm px-4">
            Ver Todos los Perfiles <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <h2 class="section-title mb-4">
        <span class="text-success"><i class="fas fa-star mr-2"></i>Nuevos Perfiles</span>
        <small class="text-muted float-right d-none d-md-block mt-2">Recién llegados</small>
    </h2>

    <div class="new-profiles mb-5">
        <div class="row">
            <?php if (empty($newProfiles)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No hay perfiles nuevos disponibles en este momento.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($newProfiles as $profile): ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card profile-card h-100 border-0 shadow-sm">
                            <!-- Solo cambiamos esta clase para aplicar la protección -->
                            <div class="profile-image protected-photo-container">
                                <?php if (!empty($profile['main_photo'])): ?>
                                    <img src="<?= url('uploads/photos/' . $profile['main_photo']) ?>"
                                        class="card-img-top photo-preview" alt="<?= htmlspecialchars($profile['name']) ?>">
                                <?php else: ?>
                                    <img src="<?= url('img/profile-placeholder.jpg') ?>"
                                        class="card-img-top photo-preview" alt="Sin foto">
                                <?php endif; ?>

                                <span class="badge badge-info new-badge">
                                    <i class="fas fa-star"></i> Nuevo
                                </span>

                                <!-- La marca de agua se añadirá automáticamente por JavaScript -->
                            </div>

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title mb-0"><?= htmlspecialchars($profile['name']) ?></h5>
                                    <?php if ($profile['is_verified']): ?>
                                        <span class="badge badge-pill badge-success">
                                            <i class="fas fa-check-circle"></i> Verificado
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    <?php
                                    // Mostrar provincia y distrito si están disponibles
                                    if (!empty($profile['province_name']) && !empty($profile['district_name'])) {
                                        echo htmlspecialchars($profile['province_name'] . ', ' . $profile['district_name']);
                                    } elseif (!empty($profile['province_name'])) {
                                        echo htmlspecialchars($profile['province_name']);
                                    } elseif (!empty($profile['province_id'])) {
                                        // Si solo tenemos IDs pero no nombres, intentar obtener los nombres
                                        $provinceName = Profile::getProvinceNameById($profile['province_id']);
                                        $districtName = !empty($profile['district_id']) ? Profile::getDistrictNameById($profile['district_id']) : '';
                                        
                                        if ($districtName) {
                                            echo htmlspecialchars($provinceName . ', ' . $districtName);
                                        } else {
                                            echo htmlspecialchars($provinceName);
                                        }
                                    } else {
                                        echo 'Ubicación no especificada';
                                    }
                                    ?>
                                </p>
                                <p class="card-text small text-muted">
                                    <i class="fas fa-clock mr-1"></i> Hace <?= rand(1, 24) ?> horas
                                </p>
                            </div>

                            <div class="card-footer bg-white border-0">
                                <a href="<?= url('/perfil/' . $profile['id']) ?>" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-eye mr-1"></i> Ver Perfil
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-5 mb-5">
        <div class="col-md-4 mb-4">
            <div class="feature-box text-center p-4 bg-white rounded shadow-sm border-0">
                <div class="feature-icon mb-3">
                    <div class="icon-circle bg-primary-light text-primary mx-auto">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
                <h4>Perfiles Verificados</h4>
                <p class="text-muted">Todos nuestros perfiles pasan por un riguroso proceso de verificación para garantizar su autenticidad.</p>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="feature-box text-center p-4 bg-white rounded shadow-sm border-0">
                <div class="feature-icon mb-3">
                    <div class="icon-circle bg-success-light text-success mx-auto">
                        <i class="fas fa-lock fa-2x"></i>
                    </div>
                </div>
                <h4>Privacidad Garantizada</h4>
                <p class="text-muted">Tu información personal está protegida con las mejores prácticas de seguridad y confidencialidad.</p>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="feature-box text-center p-4 bg-white rounded shadow-sm border-0">
                <div class="feature-icon mb-3">
                    <div class="icon-circle bg-danger-light text-danger mx-auto">
                        <i class="fas fa-heart fa-2x"></i>
                    </div>
                </div>
                <h4>Contacto Directo</h4>
                <p class="text-muted">Comunícate directamente por WhatsApp sin intermediarios para una experiencia más personal y discreta.</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="mb-3">¿Quieres publicar tu perfil?</h2>
                <p class="lead mb-4">Únete a nuestra plataforma y comienza a recibir contactos hoy mismo.</p>
                <div class="benefits-list mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success text-white mr-3">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Publicación gratuita por 15 días</h5>
                            <p class="text-muted mb-0 small">Prueba nuestro servicio sin compromiso</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success text-white mr-3">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Estadísticas detalladas</h5>
                            <p class="text-muted mb-0 small">Monitorea visitas y contactos en tiempo real</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success text-white mr-3">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Soporte personalizado</h5>
                            <p class="text-muted mb-0 small">Asistencia 24/7 para tus consultas</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success text-white mr-3">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Verificación de perfil</h5>
                            <p class="text-muted mb-0 small">Añade credibilidad para generar más confianza</p>
                        </div>
                    </div>
                </div>
                <a href="<?= url('/registro?tipo=advertiser') ?>" class="btn btn-primary btn-lg px-4 shadow-sm">
                    <i class="fas fa-user-plus mr-2"></i> Publicar mi Perfil
                </a>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="card border-0 shadow">
                    <div class="card-header bg-primary text-white py-3">
                        <h3 class="mb-0 text-center">Planes para Anunciantes</h3>
                    </div>
                    <div class="card-body px-0">
                        <div class="row mx-0">
                            <div class="col-6 px-3">
                                <div class="pricing-item text-center p-3 h-100">
                                    <div class="mb-3">
                                        <span class="pricing-icon bg-primary-light text-primary">
                                            <i class="fas fa-star"></i>
                                        </span>
                                    </div>
                                    <h4>Plan Básico</h4>
                                    <div class="price">S/. 50</div>
                                    <div class="period mb-3">por mes</div>
                                    <ul class="list-unstyled pricing-features">
                                        <li><i class="fas fa-check text-success mr-1"></i> 2 fotos</li>
                                        <li><i class="fas fa-check text-success mr-1"></i> 1 video</li>
                                        <li><i class="fas fa-check text-success mr-1"></i> Perfil destacado</li>
                                        <li class="text-muted"><i class="fas fa-times mr-1"></i> Estadísticas avanzadas</li>
                                    </ul>
                                    <a href="<?= url('/registro?tipo=advertiser&plan=basic') ?>" class="btn btn-outline-primary btn-block mt-3">
                                        Elegir Plan
                                    </a>
                                </div>
                            </div>
                            <div class="col-6 px-3">
                                <div class="pricing-item text-center p-3 border border-primary rounded shadow-sm position-relative h-100">
                                    <div class="ribbon">Popular</div>
                                    <div class="mb-3">
                                        <span class="pricing-icon bg-primary text-white">
                                            <i class="fas fa-crown"></i>
                                        </span>
                                    </div>
                                    <h4>Plan Premium</h4>
                                    <div class="price">S/. 100</div>
                                    <div class="period mb-3">por mes</div>
                                    <ul class="list-unstyled pricing-features">
                                        <li><i class="fas fa-check text-success mr-1"></i> 5 fotos</li>
                                        <li><i class="fas fa-check text-success mr-1"></i> 2 videos</li>
                                        <li><i class="fas fa-check text-success mr-1"></i> Perfil destacado</li>
                                        <li><i class="fas fa-check text-success mr-1"></i> Estadísticas avanzadas</li>
                                    </ul>
                                    <a href="<?= url('/registro?tipo=advertiser&plan=premium') ?>" class="btn btn-primary btn-block mt-3">
                                        Elegir Plan
                                    </a>
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
        // Añadir efectos de hover a las tarjetas
        const profileCards = document.querySelectorAll('.profile-card');
        profileCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.classList.add('shadow');
            });
            card.addEventListener('mouseleave', function() {
                this.classList.remove('shadow');
            });
        });
        
        // Animar aparición de las tarjetas en scroll
        if ('IntersectionObserver' in window) {
            const cards = document.querySelectorAll('.profile-card, .feature-box');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(card => {
                card.style.opacity = 0;
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            });
        }
    });
</script>