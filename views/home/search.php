<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Refinar Búsqueda</h5>
                </div>
                <div class="card-body">
                    <form action="<?= url('/buscar') ?>" method="GET" id="search-form">
                        <div class="form-group">
                            <label for="q">Palabras clave</label>
                            <input type="text" name="q" id="q" class="form-control"
                                value="<?= htmlspecialchars($query ?? '') ?>"
                                placeholder="Nombre, descripción...">
                        </div>

                        <div class="form-group">
                            <label for="province">Provincia</label>
                            <select name="province" id="province" class="form-control">
                                <option value="">Todas las provincias</option>
                                <?php if (isset($provinces)): ?>
                                    <?php foreach ($provinces as $province): ?>
                                        <option value="<?= $province['id'] ?>" <?= isset($_GET['province']) && $_GET['province'] == $province['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($province['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="district">Distrito</label>
                            <select name="district" id="district" class="form-control">
                                <option value="">Todos los distritos</option>
                                <!-- Los distritos se cargarán con JavaScript -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Categoría</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender"
                                    id="gender-all" value=""
                                    <?= ($gender ?? '') === '' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="gender-all">
                                    Todas
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender"
                                    id="gender-female" value="female"
                                    <?= ($gender ?? '') === 'female' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="gender-female">
                                    Mujeres
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender"
                                    id="gender-male" value="male"
                                    <?= ($gender ?? '') === 'male' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="gender-male">
                                    Hombres
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender"
                                    id="gender-trans" value="trans"
                                    <?= ($gender ?? '') === 'trans' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="gender-trans">
                                    Trans
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Enlaces rápidos -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Enlaces Rápidos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="<?= url('/categoria/female') ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-female mr-2"></i> Mujeres
                        </a>
                        <a href="<?= url('/categoria/male') ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-male mr-2"></i> Hombres
                        </a>
                        <a href="<?= url('/categoria/trans') ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-transgender mr-2"></i> Trans
                        </a>
                        <a href="<?= url('/') ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-star mr-2"></i> Perfiles Destacados
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Resultados de Búsqueda</h2>
                    <p class="lead">
                        <?php if (!empty($query)): ?>
                            Búsqueda: <strong>"<?= htmlspecialchars($query) ?>"</strong>
                        <?php endif; ?>

                        <?php if (!empty($gender)): ?>
                            <?= empty($query) ? '' : ' | ' ?>
                            Categoría: <strong>
                                <?= $gender === 'female' ? 'Mujeres' : ($gender === 'male' ? 'Hombres' : 'Trans') ?>
                            </strong>
                        <?php endif; ?>
                    </p>
                    <p>Se encontraron <?= $totalResults ?? 0 ?> perfiles</p>
                </div>
            </div>

            <!-- Listado de perfiles -->
            <div class="row">
                <?php if (empty($searchResults)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            No se encontraron perfiles que coincidan con tu búsqueda.
                            Intenta con otros términos o filtros.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($searchResults as $profile): ?>
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card profile-card h-100">
                                <div class="profile-image protected-photo-container">
                                    <?php if (!empty($profile['main_photo'])): ?>
                                        <img src="<?= url('uploads/photos/' . $profile['main_photo']) ?>"
                                            class="card-img-top photo-preview" alt="<?= htmlspecialchars($profile['name']) ?>">
                                    <?php else: ?>
                                        <img src="<?= url('img/profile-placeholder.jpg') ?>"
                                            class="card-img-top photo-preview" alt="Sin foto">
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
                                        <?php
                                        // Mostrar ubicación usando provincia y distrito
                                        if (!empty($profile['province_name']) && !empty($profile['district_name'])) {
                                            echo htmlspecialchars($profile['province_name'] . ', ' . $profile['district_name']);
                                        } elseif (!empty($profile['province_name'])) {
                                            echo htmlspecialchars($profile['province_name']);
                                        } elseif (!empty($profile['location'])) {
                                            echo htmlspecialchars($profile['location']);
                                        } else {
                                            echo 'Ubicación no especificada';
                                        }
                                        ?>
                                    </p>
                                    <p class="card-text description">
                                        <?= htmlspecialchars(substr($profile['description'] ?? '', 0, 100)) ?>...
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
            <?php if (($totalPages ?? 0) > 1): ?>
                <nav aria-label="Paginación" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if (($page ?? 1) > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= url('/buscar?' . http_build_query(array_merge($_GET, ['page' => ($page ?? 1) - 1]))) ?>">
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
                        $currentPage = $page ?? 1;
                        $totalPagesCount = $totalPages ?? 1;
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPagesCount, $startPage + 4);

                        if ($endPage - $startPage < 4) {
                            $startPage = max(1, $endPage - 4);
                        }

                        for ($i = $startPage; $i <= $endPage; $i++):
                            $queryParams = array_merge($_GET, ['page' => $i]);
                        ?>
                            <li class="page-item <?= ($i === $currentPage) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('/buscar?' . http_build_query($queryParams)) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPagesCount): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= url('/buscar?' . http_build_query(array_merge($_GET, ['page' => $currentPage + 1]))) ?>">
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

