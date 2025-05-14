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
                                   value="<?= htmlspecialchars($query) ?>" 
                                   placeholder="Nombre, descripción...">
                        </div>
                        
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
                       
                       <div class="form-group">
                           <label>Categoría</label>
                           <div class="form-check">
                               <input class="form-check-input" type="radio" name="gender" 
                                      id="gender-all" value="" 
                                      <?= $gender === '' ? 'checked' : '' ?>>
                               <label class="form-check-label" for="gender-all">
                                   Todas
                               </label>
                           </div>
                           <div class="form-check">
                               <input class="form-check-input" type="radio" name="gender" 
                                      id="gender-female" value="female" 
                                      <?= $gender === 'female' ? 'checked' : '' ?>>
                               <label class="form-check-label" for="gender-female">
                                   Mujeres
                               </label>
                           </div>
                           <div class="form-check">
                               <input class="form-check-input" type="radio" name="gender" 
                                      id="gender-male" value="male" 
                                      <?= $gender === 'male' ? 'checked' : '' ?>>
                               <label class="form-check-label" for="gender-male">
                                   Hombres
                               </label>
                           </div>
                           <div class="form-check">
                               <input class="form-check-input" type="radio" name="gender" 
                                      id="gender-trans" value="trans" 
                                      <?= $gender === 'trans' ? 'checked' : '' ?>>
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
                       
                       <?php if (!empty($city)): ?>
                           <?= empty($query) ? '' : ' | ' ?>
                           Ciudad: <strong><?= htmlspecialchars($city) ?></strong>
                       <?php endif; ?>
                       
                       <?php if (!empty($gender)): ?>
                           <?= (empty($query) && empty($city)) ? '' : ' | ' ?>
                           Categoría: <strong>
                               <?= $gender === 'female' ? 'Mujeres' : ($gender === 'male' ? 'Hombres' : 'Trans') ?>
                           </strong>
                       <?php endif; ?>
                   </p>
                   <p>Se encontraron <?= $totalResults ?> perfiles</p>
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
                               <a class="page-link" href="<?= url('/buscar?' . http_build_query(array_merge($_GET, ['page' => $page - 1]))) ?>">
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
                           $queryParams = array_merge($_GET, ['page' => $i]);
                       ?>
                           <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                               <a class="page-link" href="<?= url('/buscar?' . http_build_query($queryParams)) ?>">
                                   <?= $i ?>
                               </a>
                           </li>
                       <?php endfor; ?>
                       
                       <?php if ($page < $totalPages): ?>
                           <li class="page-item">
                               <a class="page-link" href="<?= url('/buscar?' . http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>">
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