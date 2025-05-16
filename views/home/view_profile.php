<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <!-- Tarjeta de Perfil -->
            <div class="card profile-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><?= htmlspecialchars($profile['name']) ?></h3>
                </div>

                <!-- Foto principal con marca de agua -->
                <div class="profile-main-photo protected-photo-container">
                    <?php if ($mainPhoto): ?>
                        <img src="<?= url('uploads/photos/' . $mainPhoto['filename']) ?>"
                            class="img-fluid photo-preview" alt="<?= htmlspecialchars($profile['name']) ?>"
                            data-photo-id="main">
                    <?php else: ?>
                        <img src="<?= url('img/profile-placeholder.jpg') ?>"
                            class="img-fluid photo-preview" alt="Sin foto">
                    <?php endif; ?>

                    <?php if ($profile['is_verified']): ?>
                        <div class="verified-badge">
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i> Verificado
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <div class="profile-info">
                        <p>
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            <strong>Ubicación:</strong>
                            <?= htmlspecialchars($profile['city']) ?> - <?= htmlspecialchars($profile['location']) ?>
                        </p>

                        <?php if ($hasAccess || $isOwner): ?>
                            <p>
                                <i class="fas fa-clock text-info"></i>
                                <strong>Horario:</strong>
                                <?= nl2br(htmlspecialchars($profile['schedule'])) ?>
                            </p>

                            <p>
                                <i class="fas fa-eye text-secondary"></i>
                                <strong>Vistas:</strong>
                                <?= $profile['views'] ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if ($hasAccess): ?>
                        <div class="whatsapp-button mt-3">
                            <a href="https://wa.me/<?= htmlspecialchars($profile['whatsapp']) ?>"
                                class="btn btn-success btn-lg btn-block"
                                target="_blank"
                                id="whatsapp-button"
                                data-profile-id="<?= $profile['id'] ?>">
                                <i class="fab fa-whatsapp"></i> Contactar por WhatsApp
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="locked-content mt-3">
                            <div class="alert alert-warning">
                                <i class="fas fa-lock"></i> Para ver los detalles completos y contactar,
                                <a href="<?= url('/login') ?>">inicia sesión</a> o
                                <a href="<?= url('/registro') ?>">regístrate</a>.
                            </div>
                            <a href="<?= url('/pago/planes') ?>" class="btn btn-primary btn-block">
                                Ver Planes de Acceso
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tarifas - Solo visibles para usuarios con acceso -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Tarifas</h4>
                </div>
                <div class="card-body">
                    <?php if ($hasAccess): ?>
                        <?php if (empty($profile['rates'])): ?>
                            <p class="text-muted">No hay tarifas disponibles.</p>
                        <?php else: ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th class="text-right">Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($profile['rates'] as $rate): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($rate['description']) ?></td>
                                            <td class="text-right">S/. <?= number_format($rate['price'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="locked-content text-center">
                            <div class="alert alert-warning">
                                <i class="fas fa-lock"></i>
                                <span>Para ver las tarifas, <a href="<?= url('/login') ?>">inicia sesión</a> y activa un plan.</span>
                            </div>
                            <a href="<?= url('/pago/planes') ?>" class="btn btn-primary">
                                Ver Planes de Acceso
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Descripción -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Descripción</h4>
                </div>
                <div class="card-body">
                    <div class="description">
                        <?php if ($hasAccess || strlen($profile['description']) < 150): ?>
                            <?= nl2br(htmlspecialchars($profile['description'])) ?>
                        <?php else: ?>
                            <?= nl2br(htmlspecialchars(substr($profile['description'], 0, 150))) ?>...
                            <div class="locked-content mt-3">
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-lock"></i> Para ver la descripción completa,
                                    <a href="<?= url('/login') ?>">inicia sesión</a> o
                                    <a href="<?= url('/registro') ?>">regístrate</a>.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Galería de Fotos - Mostrar solo 1 foto a usuarios sin acceso -->
            <?php if (!empty($photos)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Fotos</h4>
                    </div>
                    <div class="card-body">
                        <div class="row photo-gallery">
                            <?php foreach ($photos as $index => $photo): ?>
                                <?php if (!$hasAccess && $index >= 1): ?>
                                    <?php continue; // Mostrar solo la primera foto si no tiene acceso 
                                    ?>
                                <?php endif; ?>

                                <div class="col-md-4 col-6 mb-3">
                                    <!-- Aplicar protección con marca de agua a cada foto de la galería -->
                                    <div class="protected-photo-container gallery-container">
                                        <img src="<?= url('uploads/photos/' . $photo['filename']) ?>"
                                            class="img-fluid img-thumbnail gallery-img photo-preview"
                                            alt="Foto <?= $index + 1 ?>"
                                            data-photo-id="<?= $photo['id'] ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (!$hasAccess && count($photos) > 1): ?>
                                <div class="col-12 mt-3">
                                    <div class="locked-content text-center">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-lock"></i> Para ver todas las <?= count($photos) ?> fotos,
                                            <a href="<?= url('/login') ?>">inicia sesión</a> y activa un plan.
                                        </div>
                                        <a href="<?= url('/pago/planes') ?>" class="btn btn-primary">
                                            Ver Planes de Acceso
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Videos - Solo para usuarios con acceso -->
            <?php if (!empty($videos) && $hasAccess): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Videos</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($videos as $video): ?>
                                <div class="col-md-3 video-item">
                                    <!-- Contenedor de video con marca de agua -->
                                    <div class="protected-video-container">
                                        <video controls class="w-100 rounded video-player" id="video-<?= $video['id'] ?>">
                                            <source src="<?= APP_URL ?>/uploads/videos/<?= $video['filename'] ?>" type="video/mp4">
                                            Tu navegador no soporta la reproducción de videos.
                                        </video>
                                        <!-- Marca de agua directa -->
                                        <div class="video-watermark">Luvia.pe</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php elseif (!empty($videos) && !$hasAccess): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Videos</h4>
                    </div>
                    <div class="card-body">
                        <div class="locked-content text-center">
                            <div class="alert alert-warning">
                                <i class="fas fa-lock"></i> Para ver los videos,
                                <a href="<?= url('/login') ?>">inicia sesión</a> y activa un plan.
                            </div>
                            <a href="<?= url('/pago/planes') ?>" class="btn btn-primary">
                                Ver Planes de Acceso
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Perfiles similares -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Perfiles Similares</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Obtener perfiles similares (misma ciudad y género)
                    $similarProfiles = Profile::searchByCity($profile['city'], $profile['gender'], 3);
                    // Filtrar el perfil actual
                    $similarProfiles = array_filter($similarProfiles, function ($p) use ($profile) {
                        return $p['id'] != $profile['id'];
                    });
                    ?>

                    <?php if (empty($similarProfiles)): ?>
                        <p class="text-muted">No hay perfiles similares disponibles.</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($similarProfiles as $similar): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <!-- Aplicar protección con marca de agua a la miniatura del perfil similar -->
                                        <div class="similar-profile-img protected-photo-container thumbnail-mode">
                                            <?php if (!empty($similar['main_photo'])): ?>
                                                <img src="<?= url('uploads/photos/' . $similar['main_photo']) ?>"
                                                    class="card-img-top photo-preview" alt="<?= htmlspecialchars($similar['name']) ?>">
                                            <?php else: ?>
                                                <img src="<?= url('img/profile-placeholder.jpg') ?>"
                                                    class="card-img-top photo-preview" alt="Sin foto">
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($similar['name']) ?></h5>
                                            <p class="card-text">
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                                <?= htmlspecialchars($similar['city']) ?>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <a href="<?= url('/perfil/' . $similar['id']) ?>" class="btn btn-sm btn-outline-primary btn-block">
                                                Ver Perfil
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS Adicional para protección de imágenes -->
<style>
    /* Estilos existentes */
    .profile-card {
        border: none;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .profile-main-photo {
        position: relative;
        max-height: 400px;
        overflow: hidden;
    }

    .profile-main-photo img {
        width: 100%;
        object-fit: cover;
    }

    .verified-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        z-index: 20;
        /* Por encima de marca de agua */
    }

    /* Ajustes para imágenes de galería con protección */
    .gallery-container {
        height: 150px;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .gallery-img {
        height: 100%;
        width: auto;
        max-width: 100%;
        object-fit: contain;
    }

    .similar-profile-img {
        height: 180px;
        overflow: hidden;
    }

    .similar-profile-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .locked-content {
        position: relative;
    }

    .profile-info p {
        margin-bottom: 0.5rem;
        border-bottom: 1px dashed #eee;
        padding-bottom: 0.5rem;
    }

    .profile-info p:last-child {
        border-bottom: none;
    }

    /* Ajustes específicos para marcas de agua en esta página */
    .profile-main-photo.protected-photo-container .photo-watermark {
        font-size: 28px;
        opacity: 0.8;
    }

    .gallery-container .photo-watermark {
        font-size: 14px;
        opacity: 0.7;
    }

    .similar-profile-img.protected-photo-container .photo-watermark {
        font-size: 14px;
        opacity: 0.6;
    }

    /* Asegurar que los botones de acción estén por encima de capas de protección */
    .btn,
    .badge {
        position: relative;
        z-index: 25;
    }



    /* Estilos para videos con marca de agua */
.protected-video-container {
    position: relative;
    overflow: hidden;
    width: 100%;
    margin-bottom: 15px;
}

.video-player {
    display: block;
    width: 100%;
    height: auto;
}

.video-watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 24px;
    font-weight: bold;
    color: rgba(255, 255, 255, 0.9);
    text-shadow: -2px -2px 0 rgba(0, 0, 0, 0.7),
                 2px -2px 0 rgba(0, 0, 0, 0.7),
                 -2px 2px 0 rgba(0, 0, 0, 0.7),
                 2px 2px 0 rgba(0, 0, 0, 0.7);
    opacity: 0.7;
    pointer-events: none; /* Importante: permite hacer clic a través */
    z-index: 10;
    padding: 5px 10px;
    border-radius: 5px;
    background-color: rgba(0, 0, 0, 0.3);
    user-select: none;
}
</style>

<!-- Cargar el script de protección de medios -->
<script src="<?= url('js/media-protection.js') ?>"></script>

<!-- Configuración personalizada para la protección de medios -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar protección de medios con configuración personalizada
        if (typeof LuviaMediaProtection !== 'undefined') {
            LuviaMediaProtection.init({
                watermarkText: 'Luvia.pe',
                watermarkOpacity: 0.8,
                enableCarousel: true,
                enableContextMenuBlocking: true
            });
        }

        // Prevenir clic derecho en videos
        document.querySelectorAll('video').forEach(video => {
            video.addEventListener('contextmenu', e => {
                e.preventDefault();
                return false;
            });
        });

        // Seguimiento de clics en WhatsApp
        const whatsappButton = document.getElementById('whatsapp-button');
        if (whatsappButton) {
            whatsappButton.addEventListener('click', function() {
                const profileId = this.getAttribute('data-profile-id');

                // Enviar petición para registrar el clic
                fetch('<?= url('/track-whatsapp') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'profile_id=' + profileId
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        }

        // Habilitar clic en foto para abrir carrusel (reemplazando lightbox)
        const galleryPhotos = document.querySelectorAll('.gallery-img');
        galleryPhotos.forEach(photo => {
            const container = photo.closest('.protected-photo-container');
            if (container) {
                container.addEventListener('click', function() {
                    if (typeof LuviaMediaProtection !== 'undefined') {
                        const photoId = photo.getAttribute('data-photo-id');
                        LuviaMediaProtection.openPhotoCarousel(photoId);
                    }
                });
            }
        });
    });
</script>