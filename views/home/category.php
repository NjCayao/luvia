<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Filtros</h5>
                </div>
                <div class="card-body">
                    <form action="<?= url('/categoria/' . $gender) ?>" method="GET" id="filter-form">
                        <div class="form-group">
                            <label for="city">Ciudad</label>
                            <select name="city" id="city" class="form-control">
                                <option value="">Todas las ciudades</option>
                                <?php foreach ($cities as $cityOption): ?>
                                    <option value="<?= htmlspecialchars($cityOption) ?>" 
                                            <?= ($city === $cityOption) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cityOption) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <hr>
                        
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter"></i> Aplicar Filtros
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Categorías -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Categorías</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="<?= url('/categoria/female') ?>" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
                                 <?= $gender === 'female' ? 'active' : '' ?>">
                            Mujeres
                            <span class="badge badge-primary badge-pill">
                                <?= Profile::countByGender('female') ?>
                            </span>
                        </a>
                        <a href="<?= url('/categoria/male') ?>" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
                                 <?= $gender === 'male' ? 'active' : '' ?>">
                            Hombres
                            <span class="badge badge-primary badge-pill">
                                <?= Profile::countByGender('male') ?>
                            </span>
                        </a>
                        <a href="<?= url('/categoria/trans') ?>" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
                                 <?= $gender === 'trans' ? 'active' : '' ?>">
                            Trans
                            <span class="badge badge-primary badge-pill">
                                <?= Profile::countByGender('trans') ?>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?= $pageHeader ?> <small class="text-muted">(<?= $totalProfiles ?> perfiles)</small></h2>
                
                <?php if (!$hasAccess): ?>
                    <a href="<?= url('/pago/planes') ?>" class="btn btn-outline-primary">
                        Obtener Acceso Completo
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if (!$hasAccess): ?>
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle"></i> Para ver los detalles completos y contactar, 
                    <a href="<?= url('/login') ?>">inicia sesión</a> o 
                    <a href="<?= url('/registro') ?>">regístrate</a>.
                </div>
            <?php endif; ?>
            
            <!-- Listado de perfiles -->
            <div class="row">
                <?php if (empty($profiles)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            No se encontraron perfiles con los filtros seleccionados.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($profiles as $profile): ?>
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card profile-card h-100">
                                <div class="profile-image">
                                    <?php if (!empty($profile['main_photo'])): ?>
                                        <img src="<?= url('uploads/photos/' . $profile['main_photo']) ?>" 
                                             class="card-img-top" alt="<?= htmlspecialchars($profile['name']) ?>">
                                    <?php else: ?>
                                        <img src="<?= url('img/profile-placeholder.jpg') ?>" 
                                             class="card-img-top" alt="Sin foto">
                                    <?php endif; ?>
                                    
                                    <?php if ($profile['is_verified']): ?>
                                        <span class="badge badge-success verified-badge">
                                            <i class="fas fa-check-circle"></i> Verificado
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($profile['name']) ?></h5>
                                    <p class="card-text">
                                        <i class="fas fa-map-marker-alt text-danger"></i> 
                                        <?= htmlspecialchars($profile['city']) ?> - <?= htmlspecialchars($profile['location']) ?>
                                    </p>
                                    <p class="card-text description">
                                        <?= htmlspecialchars(substr($profile['description'], 0, 100)) ?>...
                                    </p>
                                </div>
                                
                                <div class="card-footer bg-white">
                                    <a href="<?= url('/perfil/' . $profile['id']) ?>" class="btn btn-outline-primary btn-block">
                                        Ver Perfil
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Paginación -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Paginación" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= url('/categoria/' . $gender . '?page=' . ($page - 1) . (!empty($city) ? '&city=' . urlencode($city) : '')) ?>">
                                    <i class="fas fa-chevron-left"></i> Anterior
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link"><i class="fas fa-chevron-left"></i> Anterior</span>
                            </li>
                        <?php endif; ?>
                        
                        <?php
                        // Determinar el rango de páginas a mostrar
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $startPage + 4);
                        
                        if ($endPage - $startPage < 4) {
                            $startPage = max(1, $endPage - 4);
                        }
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('/categoria/' . $gender . '?page=' . $i . (!empty($city) ? '&city=' . urlencode($city) : '')) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= url('/categoria/' . $gender . '?page=' . ($page + 1) . (!empty($city) ? '&city=' . urlencode($city) : '')) ?>">
                                    Siguiente <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">Siguiente <i class="fas fa-chevron-right"></i></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.profile-image {
    position: relative;
    height: 200px;
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

.description {
    height: 50px;
    overflow: hidden;
}

.profile-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.125);
}

.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enviar formulario al cambiar el select de ciudad
    document.getElementById('city').addEventListener('change', function() {
        document.getElementById('filter-form').submit();
    });
});
</script>