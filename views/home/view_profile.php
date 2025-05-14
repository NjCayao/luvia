<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <!-- Tarjeta de Perfil -->
            <div class="card profile-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><?= htmlspecialchars($profile['name']) ?></h3>
                </div>
                
                <div class="profile-main-photo">
                    <?php if ($mainPhoto): ?>
                        <img src="<?= url('uploads/photos/' . $mainPhoto['filename']) ?>"
                             class="img-fluid" alt="<?= htmlspecialchars($profile['name']) ?>">
                    <?php else: ?>
                        <img src="<?= url('img/profile-placeholder.jpg') ?>"
                             class="img-fluid" alt="Sin foto">
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
            
            <!-- Tarifas -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Tarifas</h4>
                </div>
                <div class="card-body">
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
                    
                    <?php if (!$hasAccess && !empty($profile['rates'])): ?>
                        <div class="locked-content mt-3 text-center">
                            <i class="fas fa-lock"></i> 
                            <span>Para ver todas las tarifas, <a href="<?= url('/login') ?>">inicia sesión</a></span>
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
            
            <!-- Galería de Fotos -->
            <?php if (!empty($photos)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Fotos</h4>
                    </div>
                    <div class="card-body">
                        <div class="row photo-gallery">
                            <?php foreach ($photos as $index => $photo): ?>
                                <div class="col-md-4 col-6 mb-3">
                                    <a href="<?= url('uploads/photos/' . $photo['filename']) ?>" 
                                       data-lightbox="profile-photos" 
                                       data-title="<?= htmlspecialchars($profile['name']) ?>">
                                        <img src="<?= url('uploads/photos/' . $photo['filename']) ?>" 
                                             class="img-fluid img-thumbnail gallery-img" 
                                             alt="Foto <?= $index + 1 ?>">
                                    </a>
                                </div>
                                
                                <?php if (!$hasAccess && $index >= 1): ?>
                                    <?php break; // Mostrar solo la primera foto si no tiene acceso ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            
                            <?php if (!$hasAccess && count($photos) > 1): ?>
                                <div class="col-12 mt-3">
                                    <div class="locked-content text-center">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-lock"></i> Para ver todas las fotos, 
                                            <a href="<?= url('/login') ?>">inicia sesión</a> o 
                                            <a href="<?= url('/registro') ?>">regístrate</a>.
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Videos -->
            <?php if (!empty($videos) && $hasAccess): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Videos</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($videos as $video): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <video controls class="embed-responsive-item">
                                            <source src="<?= url('uploads/videos/' . $video['filename']) ?>" 
                                                    type="<?= getMimeType(UPLOAD_DIR . 'videos/' . $video['filename']) ?>">
                                            Tu navegador no soporta la reproducción de videos.
                                        </video>
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
                                <a href="<?= url('/login') ?>">inicia sesión</a> o 
                                <a href="<?= url('/registro') ?>">regístrate</a>.
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
                    $similarProfiles = array_filter($similarProfiles, function($p) use ($profile) {
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
                                        <div class="similar-profile-img">
                                            <?php if (!empty($similar['main_photo'])): ?>
                                                <img src="<?= url('uploads/photos/' . $similar['main_photo']) ?>"
                                                     class="card-img-top" alt="<?= htmlspecialchars($similar['name']) ?>">
                                            <?php else: ?>
                                                <img src="<?= url('img/profile-placeholder.jpg') ?>"
                                                     class="card-img-top" alt="Sin foto">
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

<!-- CSS -->
<link rel="stylesheet" href="<?= url('plugins/lightbox/css/lightbox.min.css') ?>">

<style>
.profile-card {
    border: none;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
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
}

.gallery-img {
    height: 150px;
    object-fit: cover;
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
</style>

<!-- JavaScript -->
<script src="<?= url('plugins/lightbox/js/lightbox.min.js') ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Lightbox
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true
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
});
</script>