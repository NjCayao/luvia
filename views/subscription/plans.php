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
    
    <div class="row mt-5">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-light">
                    <h4 class="mb-0">Preguntas Frecuentes</h4>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="card border-0">
                            <div class="card-header bg-white" id="faqHeading1">
                                <h2 class="mb-0">
                                    <button class="btn btn-link text-dark text-left w-100" type="button" data-toggle="collapse" data-target="#faqCollapse1">
                                        ¿Cómo funciona la suscripción?
                                    </button>
                                </h2>
                            </div>
                            <div id="faqCollapse1" class="collapse show" data-parent="#faqAccordion">
                                <div class="card-body">
                                    <?php if ($userType === 'advertiser'): ?>
                                        La suscripción te permite mantener tu perfil activo y visible para todos los visitantes. 
                                        Tienes un período de prueba gratuito de <?= FREE_TRIAL_DAYS ?> días, después del cual 
                                        debes seleccionar un plan para continuar disfrutando de todos los beneficios.
                                    <?php else: ?>
                                        La suscripción te da acceso completo a todos los perfiles, permitiéndote ver todas 
                                        las fotos, videos y contactar directamente mediante WhatsApp. Sin una suscripción activa, 
                                        solo podrás ver información limitada.
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0">
                            <div class="card-header bg-white" id="faqHeading2">
                                <h2 class="mb-0">
                                    <button class="btn btn-link text-dark text-left w-100 collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse2">
                                        ¿Puedo cancelar mi suscripción?
                                    </button>
                                </h2>
                            </div>
                            <div id="faqCollapse2" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Sí, puedes cancelar la renovación automática de tu suscripción en cualquier momento 
                                    desde la sección "Mis Suscripciones". Sin embargo, no ofrecemos reembolsos por el 
                                    período ya pagado. Tu suscripción seguirá activa hasta la fecha de finalización.
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0">
                            <div class="card-header bg-white" id="faqHeading3">
                                <h2 class="mb-0">
                                    <button class="btn btn-link text-dark text-left w-100 collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse3">
                                        ¿Cómo se procesan los pagos?
                                    </button>
                                </h2>
                            </div>
                            <div id="faqCollapse3" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Los pagos se procesan de forma segura a través de nuestra pasarela de pagos (Izipay). 
                                    Aceptamos tarjetas de crédito/débito y Yape. Todos los datos son encriptados y nunca 
                                    almacenamos la información completa de tu tarjeta en nuestros servidores.
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0">
                            <div class="card-header bg-white" id="faqHeading4">
                                <h2 class="mb-0">
                                    <button class="btn btn-link text-dark text-left w-100 collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse4">
                                        ¿Qué ocurre cuando vence mi suscripción?
                                    </button>
                                </h2>
                            </div>
                            <div id="faqCollapse4" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    <?php if ($userType === 'advertiser'): ?>
                                        Cuando vence tu suscripción, tu perfil deja de ser visible para los visitantes. 
                                        Todos tus datos y medios permanecen en nuestro sistema, por lo que al renovar 
                                        tu suscripción tu perfil estará disponible de inmediato sin necesidad de volver a cargarlo.
                                    <?php else: ?>
                                        Cuando vence tu suscripción, ya no podrás acceder a los detalles completos de los 
                                        perfiles ni contactar directamente. Podrás seguir navegando en la plataforma, pero 
                                        con acceso limitado hasta que renueves tu suscripción.
                                    <?php endif; ?>
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
.pricing-card {
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.price-container {
    position: relative;
}

.price-currency {
    position: relative;
    top: -15px;
    font-size: 20px;
    font-weight: 500;
}

.price-amount {
    font-size: 48px;
    font-weight: 700;
    line-height: 1;
}

.price-period {
    font-size: 16px;
    color: #6c757d;
}

.feature-list {
    list-style: none;
    padding-left: 0;
    margin-bottom: 30px;
}

.feature-list li {
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
}

.feature-list li:last-child {
    border-bottom: none;
}

.ribbon {
    position: absolute;
    top: 20px;
    right: -30px;
    width: 120px;
    height: 30px;
    background-color: #28a745;
    color: white;
    font-size: 14px;
    font-weight: bold;
    text-align: center;
    line-height: 30px;
    transform: rotate(45deg);
    z-index: 1;
}
</style>