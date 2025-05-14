<?php
// views/payment/plans.php
require_once __DIR__ . '/../layouts/main.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <?php if ($subscription): ?>
                <div class="alert alert-info">
                    <div class="row align-items-center">
                        <div class="col-md-1 text-center">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <div class="col-md-9">
                            <h5 class="mb-1">Ya tienes una suscripción activa</h5>
                            <p class="mb-0">
                                Plan: <strong><?= htmlspecialchars($subscription['plan_name']) ?></strong> | 
                                Válido hasta: <strong><?= formatDate($subscription['end_date']) ?></strong>
                                <?php if ($subscription['auto_renew']): ?>
                                    | <span class="badge badge-success">Renovación automática activada</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-2 text-right">
                            <a href="<?= url('/usuario/suscripciones') ?>" class="btn btn-sm btn-outline-primary">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($userType === 'advertiser' && isset($trialStatus) && $trialStatus['has_trial'] && !$trialStatus['trial_ended']): ?>
                <div class="alert alert-success">
                    <div class="row align-items-center">
                        <div class="col-md-1 text-center">
                            <i class="fas fa-gift fa-2x"></i>
                        </div>
                        <div class="col-md-11">
                            <h5 class="mb-1">Estás disfrutando de tu período de prueba gratuito</h5>
                            <p class="mb-0">
                                Te quedan <strong><?= $trialStatus['days_left'] ?> días</strong> de prueba. 
                                Para seguir disfrutando de todos los beneficios, selecciona un plan antes de que finalice tu prueba.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row mt-3 mb-5">
        <div class="col-md-12 text-center">
            <h2 class="mb-4"><?= $userType === 'advertiser' ? 'Planes para Anunciantes' : 'Planes para Visitantes' ?></h2>
            <p class="lead">
                <?php if ($userType === 'advertiser'): ?>
                    Selecciona el plan que mejor se adapte a tus necesidades y haz que tu perfil destaque
                <?php else: ?>
                    Selecciona un plan para acceder a todos los perfiles y contactar directamente
                <?php endif; ?>
            </p>
        </div>
    </div>
    
    <div class="row">
        <?php if (empty($plans)): ?>
            <div class="col-md-12">
                <div class="alert alert-warning">
                    No hay planes disponibles en este momento.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($plans as $plan): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card pricing-card h-100 <?= $plan['featured'] ? 'border-primary' : '' ?>">
                        <?php if ($plan['featured']): ?>
                            <div class="ribbon">Popular</div>
                        <?php endif; ?>
                        
                        <div class="card-header bg-<?= $plan['featured'] ? 'primary text-white' : 'light' ?>">
                            <h3 class="card-title mb-0 text-center"><?= htmlspecialchars($plan['name']) ?></h3>
                        </div>
                        
                        <div class="card-body">
                            <div class="price-container text-center mb-4">
                                <span class="price-currency">S/</span>
                                <span class="price-amount"><?= number_format($plan['price'], 0) ?></span>
                                <span class="price-period">/ <?= $plan['duration'] ?> días</span>
                            </div>
                            
                            <ul class="feature-list">
                                <?php if ($userType === 'advertiser'): ?>
                                    <li>
                                        <i class="fas fa-check text-success"></i> 
                                        Hasta <?= $plan['max_photos'] ?> fotos
                                    </li>
                                    <li>
                                        <i class="fas fa-check text-success"></i> 
                                        Hasta <?= $plan['max_videos'] ?> videos
                                    </li>
                                    <?php if ($plan['featured']): ?>
                                        <li>
                                            <i class="fas fa-check text-success"></i> 
                                            Perfil destacado
                                        </li>
                                        <li>
                                            <i class="fas fa-check text-success"></i> 
                                            Estadísticas detalladas
                                        </li>
                                    <?php endif; ?>
                                    <li>
                                        <i class="fas fa-check text-success"></i> 
                                        Contacto directo por WhatsApp
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <i class="fas fa-check text-success"></i> 
                                        Acceso a todos los perfiles
                                    </li>
                                    <li>
                                        <i class="fas fa-check text-success"></i> 
                                        Ver fotos y videos completos
                                    </li>
                                    <li>
                                        <i class="fas fa-check text-success"></i> 
                                        Contacto directo por WhatsApp
                                    </li>
                                    <?php if ($plan['featured']): ?>
                                        <li>
                                            <i class="fas fa-check text-success"></i> 
                                            Acceso prioritario a nuevos perfiles
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <li>
                                    <i class="fas fa-check text-success"></i> 
                                    Soporte 24/7
                                </li>
                            </ul>
                            
                            <?php if (!empty($plan['description'])): ?>
                                <p class="plan-description">
                                    <?= nl2br(htmlspecialchars($plan['description'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-footer bg-white text-center">
                            <?php if ($subscription && $subscription['plan_id'] == $plan['id']): ?>
                                <button class="btn btn-success btn-block" disabled>
                                    <i class="fas fa-check-circle"></i> Plan Actual
                                </button>
                            <?php else: ?>
                                <a href="<?= url('/pago/checkout/' . $plan['id']) ?>" class="btn btn-<?= $plan['featured'] ? 'primary' : 'outline-primary' ?> btn-block">
                                    Seleccionar Plan
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>