<div class="container-fluid bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4">Encuentra compañía de calidad</h1>
                <p class="lead">Miles de perfiles verificados están esperando por ti</p>

                <form action="<?= url('/buscar') ?>" method="GET" class="mt-4">
                    <div class="input-group mb-3">
                        <input type="text" name="q" class="form-control form-control-lg"
                            placeholder="Buscar por nombre, descripción...">
                        <div class="input-group-append">
                            <button class="btn btn-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <select name="city" class="form-control">
                                <option value="">Todas las ciudades</option>
                                <?php foreach ($cities as $city): ?>
                                    <option value="<?= htmlspecialchars($city) ?>"><?= htmlspecialchars($city) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-light active">
                                    <input type="radio" name="gender" value="female" checked> Mujeres
                                </label>
                                <label class="btn btn-outline-light">
                                    <input type="radio" name="gender" value="male"> Hombres
                                </label>
                                <label class="btn btn-outline-light">
                                    <input type="radio" name="gender" value="trans"> Trans
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 d-none d-md-block">
                <img src="<?= url('img/hero-image.jpg') ?>" alt="Encuentra compañía" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <?php if (!$hasAccess): ?>
        <div class="alert alert-info">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4><i class="fas fa-info-circle"></i> Acceso limitado</h4>
                    <p class="mb-0">Para ver los detalles completos de los perfiles y contactar con ellos,
                        <a href="<?= url('/login') ?>">inicia sesión</a> o
                        <a href="<?= url('/registro') ?>">regístrate</a> para obtener acceso completo.
                    </p>
                </div>
                <div class="col-md-4 text-right">
                    <a href="<?= url('/pago/planes') ?>" class="btn btn-primary">Ver planes</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <h2 class="section-title">Perfiles Destacados</h2>

    <div class="row">
        <?php if (empty($featuredProfiles)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No hay perfiles destacados disponibles en este momento.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($featuredProfiles as $profile): ?>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card profile-card h-100 shadow-sm">
                        <div class="profile-image" style="height: 140px; overflow: hidden;">
                            <?php if (!empty($profile['main_photo'])): ?>
                                <img src="<?= url('uploads/photos/' . $profile['main_photo']) ?>"
                                    class="card-img-top" alt="<?= htmlspecialchars($profile['name']) ?>"
                                    style="object-fit: cover; height: 100%; width: 100%;">
                            <?php else: ?>
                                <img src="<?= url('img/profile-placeholder.jpg') ?>"
                                    class="card-img-top" alt="Sin foto"
                                    style="object-fit: cover; height: 100%; width: 100%;">
                            <?php endif; ?>

                            <?php if ($profile['is_verified']): ?>
                                <span class="badge badge-success verified-badge" style="position: absolute; right: 10px; font-size: 10px;">
                                    <i class="fas fa-check-circle"></i> Verificado
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="card-body p-2">
                            <h6 class="card-title mb-1"><?= htmlspecialchars($profile['name']) ?></h6>
                            <p class="card-text mb-1" style="font-size: 0.8rem;">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <?= htmlspecialchars($profile['city']) ?>
                            </p>
                            <p class="card-text description" style="font-size: 0.75rem; height: 40px; overflow: hidden;">
                                <?= htmlspecialchars(substr($profile['description'], 0, 60)) ?>...
                            </p>
                        </div>

                        <div class="card-footer bg-white p-2 text-center">
                            <a href="<?= url('/perfil/' . $profile['id']) ?>" class="btn btn-sm btn-outline-primary">
                                Ver Perfil <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="text-center mt-3 mb-5">
        <a href="<?= url('/categoria/female') ?>" class="btn btn-primary">
            Ver Todos los Perfiles <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <h2 class="section-title">Nuevos Perfiles</h2>

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
                    <div class="card profile-card h-100">
                        <div class="profile-image">
                            <?php if (!empty($profile['main_photo'])): ?>
                                <img src="<?= url('uploads/photos/' . $profile['main_photo']) ?>"
                                    class="card-img-top" alt="<?= htmlspecialchars($profile['name']) ?>">
                            <?php else: ?>
                                <img src="<?= url('img/profile-placeholder.jpg') ?>"
                                    class="card-img-top" alt="Sin foto">
                            <?php endif; ?>

                            <span class="badge badge-info new-badge">
                                <i class="fas fa-star"></i> Nuevo
                            </span>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($profile['name']) ?></h5>
                            <p class="card-text">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <?= htmlspecialchars($profile['city']) ?>
                            </p>
                        </div>

                        <div class="card-footer bg-white">
                            <a href="<?= url('/perfil/' . $profile['id']) ?>" class="btn btn-sm btn-outline-primary btn-block">
                                Ver Perfil
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="row mt-5 mb-5">
        <div class="col-md-4 mb-4">
            <div class="feature-box text-center p-4 bg-light rounded">
                <div class="feature-icon">
                    <i class="fas fa-user-check fa-3x text-primary mb-3"></i>
                </div>
                <h4>Perfiles Verificados</h4>
                <p>Todos nuestros perfiles son verificados para garantizar autenticidad.</p>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="feature-box text-center p-4 bg-light rounded">
                <div class="feature-icon">
                    <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                </div>
                <h4>Privacidad Garantizada</h4>
                <p>Tu información personal está protegida con las mejores prácticas de seguridad.</p>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="feature-box text-center p-4 bg-light rounded">
                <div class="feature-icon">
                    <i class="fas fa-heart fa-3x text-primary mb-3"></i>
                </div>
                <h4>Contacto Directo</h4>
                <p>Comunícate directamente sin intermediarios por WhatsApp.</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>¿Quieres publicar tu perfil?</h2>
                <p class="lead">Únete a nuestra plataforma y comienza a recibir contactos hoy mismo.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> Publicación gratuita por 15 días</li>
                    <li><i class="fas fa-check text-success"></i> Estadísticas de visitas y contactos</li>
                    <li><i class="fas fa-check text-success"></i> Soporte personalizado</li>
                    <li><i class="fas fa-check text-success"></i> Verificación de perfil para mayor confianza</li>
                </ul>
                <a href="<?= url('/registro?tipo=advertiser') ?>" class="btn btn-primary btn-lg mt-3">
                    Publicar mi Perfil
                </a>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Planes para Anunciantes</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="pricing-item text-center p-3">
                                    <h4>Plan Básico</h4>
                                    <div class="price">S/. 50</div>
                                    <div class="period">por mes</div>
                                    <ul class="list-unstyled mt-3">
                                        <li>2 fotos</li>
                                        <li>1 video</li>
                                        <li>Perfil destacado</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="pricing-item text-center p-3 border border-primary rounded">
                                    <div class="ribbon">Popular</div>
                                    <h4>Plan Premium</h4>
                                    <div class="price">S/. 100</div>
                                    <div class="period">por mes</div>
                                    <ul class="list-unstyled mt-3">
                                        <li>5 fotos</li>
                                        <li>2 videos</li>
                                        <li>Perfil destacado</li>
                                        <li>Estadísticas avanzadas</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .section-title {
        position: relative;
        padding-bottom: 15px;
        margin-bottom: 30px;
        color: #333;
    }

    .section-title:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background-color: var(--primary);
    }

    .profile-card {
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .profile-image {
        position: relative;
        height: 350px;
        overflow: hidden;
    }

    .profile-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .verified-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
    }

    .new-badge {
        position: absolute;
        top: 10px;
        left: 10px;
    }

    .description {
        height: 50px;
        overflow: hidden;
    }

    .feature-box {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .feature-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .price {
        font-size: 24px;
        font-weight: bold;
        color: var(--primary);
    }

    .period {
        color: #6c757d;
        font-size: 14px;
    }

    .pricing-item {
        transition: all 0.3s ease;
    }

    .pricing-item:hover {
        transform: scale(1.05);
    }

    .ribbon {
        position: absolute;
        top: 20px;
        right: -15px;
        padding: 3px 10px;
        background-color: var(--success);
        color: white;
        font-size: 12px;
        transform: rotate(45deg);
    }
</style>