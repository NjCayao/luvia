<!-- Contenido principal -->
<div class="row">
    <div class="col-md-4">
        <!-- Tarjeta de perfil -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <?php if ($profile && $profilePhoto = Media::getPrimaryPhoto($profile['id'])): ?>
                        <img class="profile-user-img img-fluid img-circle" 
                             src="<?= url('uploads/photos/' . $profilePhoto['filename']) ?>" 
                             alt="Foto de perfil">
                    <?php else: ?>
                        <img class="profile-user-img img-fluid img-circle" 
                             src="<?= url('plugins/adminlte/img/user-default.jpg') ?>" 
                             alt="Foto de perfil">
                    <?php endif; ?>
                </div>

                <h3 class="profile-username text-center">
                    <?= htmlspecialchars($profile['name'] ?? $user['phone']) ?>
                </h3>

                <p class="text-muted text-center">
                    <?= ucfirst($user['user_type']) ?> - 
                    <?php
                        if ($user['user_type'] === 'advertiser') {
                            echo ucfirst($profile['gender'] ?? 'female');
                        }
                    ?>
                </p>

                <?php if ($profile): ?>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Vistas del perfil</b> <a class="float-right"><?= $profile['views'] ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Clics en WhatsApp</b> <a class="float-right"><?= $profile['whatsapp_clicks'] ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Ciudad</b> <a class="float-right"><?= htmlspecialchars($profile['city']) ?></a>
                        </li>
                    </ul>
                <?php endif; ?>

                <?php if ($user['user_type'] === 'advertiser' && !$profile): ?>
                    <a href="<?= url('/usuario/editar') ?>" class="btn btn-primary btn-block">
                        <i class="fas fa-plus-circle"></i> Crear Perfil
                    </a>
                <?php elseif ($user['user_type'] === 'advertiser'): ?>
                    <a href="<?= url('/usuario/editar') ?>" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Editar Perfil
                    </a>
                <?php endif; ?>
                
                <?php if ($profile): ?>
                    <a href="<?= url('/perfil/' . $profile['id']) ?>" class="btn btn-default btn-block mt-2">
                        <i class="fas fa-eye"></i> Ver Perfil Público
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Información de suscripción -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Estado de Suscripción</h3>
            </div>
            <div class="card-body">
                <?php if ($user['user_type'] === 'advertiser' && $hasTrial): ?>
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Período de Prueba</h5>
                        Su período de prueba expira en <?= $trialDaysLeft ?> días.
                    </div>
                <?php endif; ?>

                <?php if ($subscription): ?>
                    <strong><i class="fas fa-check-circle mr-1"></i> Plan Activo</strong>
                    <p class="text-muted">
                        <?= htmlspecialchars($subscription['plan_name']) ?>
                    </p>
                    <hr>
                    <strong><i class="fas fa-calendar mr-1"></i> Expira el</strong>
                    <p class="text-muted">
                        <?= formatDate($subscription['end_date'], 'd/m/Y') ?>
                    </p>
                    <hr>
                    <a href="<?= url('/pago/planes') ?>" class="btn btn-secondary btn-block">
                        <i class="fas fa-sync-alt"></i> Renovar / Cambiar Plan
                    </a>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Sin suscripción activa</h5>
                        <?php if ($user['user_type'] === 'advertiser' && $hasTrial): ?>
                            Actualmente está en su período de prueba.
                        <?php else: ?>
                            Para acceder a todas las funcionalidades, adquiera un plan.
                        <?php endif; ?>
                    </div>
                    <a href="<?= url('/pago/planes') ?>" class="btn btn-success btn-block">
                        <i class="fas fa-shopping-cart"></i> Ver Planes
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Tarjeta de Acciones -->
        <div class="card">
            <div class="card-header p-2">
                <h3 class="card-title">Acciones Rápidas</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if ($user['user_type'] === 'advertiser'): ?>
                        <div class="col-md-4">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= Media::countPhotos($profile['id'] ?? 0) ?></h3>
                                    <p>Fotos</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-image"></i>
                                </div>
                                <a href="<?= url('/usuario/medios') ?>" class="small-box-footer">
                                    Gestionar <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?= count(Rate::getByProfileId($profile['id'] ?? 0)) ?></h3>
                                    <p>Tarifas</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <a href="<?= url('/usuario/tarifas') ?>" class="small-box-footer">
                                    Gestionar <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?= $profile ? ($profile['views'] > 0 ? $profile['views'] : 0) : 0 ?></h3>
                                    <p>Vistas</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <a href="<?= url('/usuario/estadisticas') ?>" class="small-box-footer">
                                    Ver estadísticas <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-md-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>Explorar</h3>
                                    <p>Perfiles Destacados</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <a href="<?= url('/') ?>" class="small-box-footer">
                                    Ver perfiles <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>Suscripción</h3>
                                    <p>Estado de cuenta</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <a href="<?= url('/pago/planes') ?>" class="small-box-footer">
                                    Ver planes <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Actividad Reciente</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <!-- Timeline items will be populated dynamically with actual data -->
                    <div>
                        <i class="fas fa-user bg-primary"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> <?= formatDate($user['last_login']) ?></span>
                            <h3 class="timeline-header">Último inicio de sesión</h3>
                        </div>
                    </div>
                    
                    <?php if ($profile): ?>
                        <div>
                            <i class="fas fa-eye bg-info"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> Hoy</span>
                                <h3 class="timeline-header">Vistas del perfil</h3>
                                <div class="timeline-body">
                                    Tu perfil ha sido visto <?= $profile['views'] ?> veces en total.
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($subscription): ?>
                        <div>
                            <i class="fas fa-credit-card bg-success"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> <?= formatDate($subscription['start_date']) ?></span>
                                <h3 class="timeline-header">Suscripción activada</h3>
                                <div class="timeline-body">
                                    Plan: <?= htmlspecialchars($subscription['plan_name']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <i class="fas fa-user-plus bg-success"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> <?= formatDate($user['created_at']) ?></span>
                            <h3 class="timeline-header">Cuenta creada</h3>
                        </div>
                    </div>
                    
                    <div>
                        <i class="fas fa-clock bg-gray"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>