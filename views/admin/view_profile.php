<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-id-card mr-1"></i>
            Información del Perfil
        </h3>
        <div class="card-tools">
            <a href="<?= url('/admin/perfiles') ?>" class="btn btn-tool">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <!-- Foto principal -->
                <div class="text-center mb-3">
                    <?php
                    $mainPhoto = null;
                    foreach ($profile['media'] ?? [] as $media) {
                        if ($media['media_type'] === 'photo' && $media['is_primary']) {
                            $mainPhoto = $media;
                            break;
                        }
                    }
                    ?>

                    <?php if ($mainPhoto): ?>
                        <img src="<?= url('uploads/photos/' . $mainPhoto['filename']) ?>" alt="Foto principal" class="img-fluid rounded" style="max-height: 300px;">
                    <?php else: ?>
                        <img src="<?= url('img/profile-placeholder.jpg') ?>" alt="Sin foto" class="img-fluid rounded" style="max-height: 300px;">
                    <?php endif; ?>
                </div>

                <!-- Estado de verificación -->
                <div class="text-center mb-3">
                    <?php if ($profile['is_verified']): ?>
                        <span class="badge badge-success">Perfil Verificado</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Perfil No Verificado</span>
                    <?php endif; ?>
                </div>

                <!-- Estadísticas -->
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-eye"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Visualizaciones</span>
                        <span class="info-box-number"><?= $profile['views'] ?? 0 ?></span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fab fa-whatsapp"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Contactos</span>
                        <span class="info-box-number"><?= $profile['whatsapp_clicks'] ?? 0 ?></span>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="mt-3">
                    <a href="<?= url('/admin/perfil/' . $profile['id'] . '/editar') ?>" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Editar Perfil
                    </a>

                    <?php if (!$profile['is_verified']): ?>
                        <form id="verifyForm" method="POST" action="<?= url('/admin/perfil/verificar') ?>">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="profile_id" value="<?= $profile['id'] ?>">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check-circle"></i> Verificar Perfil
                            </button>
                        </form>
                    <?php endif; ?>

                    <a href="<?= url('/perfil/' . $profile['id']) ?>" target="_blank" class="btn btn-info btn-block mt-2">
                        <i class="fas fa-external-link-alt"></i> Ver en Sitio
                    </a>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Datos del perfil -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Datos Personales</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th style="width: 30%">Nombre:</th>
                                <td><?= htmlspecialchars($profile['name']) ?></td>
                            </tr>
                            <tr>
                                <th>Usuario:</th>
                                <td>
                                    <a href="<?= url('/admin/usuario/' . $profile['user_id']) ?>">
                                        <?= htmlspecialchars($user['email'] ?? 'N/A') ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Género:</th>
                                <td>
                                    <span class="badge <?= $profile['gender'] === 'female' ? 'badge-pink' : ($profile['gender'] === 'male' ? 'badge-blue' : 'badge-purple') ?>">
                                        <?= $profile['gender'] === 'female' ? 'Mujer' : ($profile['gender'] === 'male' ? 'Hombre' : 'Trans') ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>WhatsApp:</th>
                                <td><?= htmlspecialchars($profile['whatsapp']) ?></td>
                            </tr>
                            <tr>
                                <th>Ciudad:</th>
                                <td><?= htmlspecialchars($profile['city']) ?></td>
                            </tr>
                            <tr>
                                <th>Ubicación:</th>
                                <td><?= htmlspecialchars($profile['location']) ?></td>
                            </tr>
                            <tr>
                                <th>Horario:</th>
                                <td><?= nl2br(htmlspecialchars($profile['schedule'])) ?></td>
                            </tr>
                            <tr>
                                <th>Fecha de Registro:</th>
                                <td><?= formatDate($profile['created_at']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Descripción</h3>
                    </div>
                    <div class="card-body">
                        <?= nl2br(htmlspecialchars($profile['description'])) ?>
                    </div>
                </div>

                <!-- Tarifas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tarifas</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($profile['rates'])): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No hay tarifas registradas</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($profile['rates'] as $rate): ?>
                                        <tr>
                                            <td>
                                                <?= $rate['rate_type'] === 'hour' ? 'Hora' : ($rate['rate_type'] === 'half_hour' ? 'Media Hora' : 'Extra') ?>
                                            </td>
                                            <td><?= htmlspecialchars($rate['description']) ?></td>
                                            <td>S/. <?= number_format($rate['price'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Galería de fotos -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Fotos</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            $photos = [];
                            foreach ($profile['media'] ?? [] as $media) {
                                if ($media['media_type'] === 'photo') {
                                    $photos[] = $media;
                                }
                            }
                            ?>

                            <?php if (empty($photos)): ?>
                                <div class="col-12 text-center">
                                    <p>No hay fotos disponibles</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($photos as $photo): ?>
                                    <div class="col-md-4 mb-3">
                                        <a href="<?= url('uploads/photos/' . $photo['filename']) ?>" target="_blank">
                                            <img src="<?= url('uploads/photos/' . $photo['filename']) ?>" alt="Foto" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                                        </a>
                                        <?php if ($photo['is_primary']): ?>
                                            <span class="badge badge-success">Principal</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Galería de videos -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Videos</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            $videos = [];
                            foreach ($profile['media'] ?? [] as $media) {
                                if ($media['media_type'] === 'video') {
                                    $videos[] = $media;
                                }
                            }
                            ?>

                            <?php if (empty($videos)): ?>
                                <div class="col-12 text-center">
                                    <p>No hay videos disponibles</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($videos as $video): ?>
                                    <div class="col-md-6 mb-3">
                                        <video controls class="img-fluid rounded" style="max-height: 200px; width: 100%; object-fit: cover;">
                                            <source src="<?= url('uploads/videos/' . $video['filename']) ?>" type="video/mp4">
                                            Tu navegador no soporta videos HTML5.
                                        </video>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .badge-pink {
        background-color: #f27eb5;
        color: white;
    }

    .badge-blue {
        background-color: #3490dc;
        color: white;
    }

    .badge-purple {
        background-color: #8e44ad;
        color: white;
    }
</style>