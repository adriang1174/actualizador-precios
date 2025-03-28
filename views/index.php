<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card bg-light mt-4">
            <div class="card-body text-center p-5">
                <h1 class="mb-4">Actualizador de Precios Factusol</h1>
                <p class="lead">Bienvenido al sistema de actualización masiva de precios para Factusol.</p>
                <p>Este sistema le permite actualizar los precios y costos de sus artículos de forma rápida y eficiente.</p>
                
                <hr class="my-4">
                
                <div class="row g-4 mt-3">
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-file-excel fa-4x text-success mb-3"></i>
                                <h3>Actualización por Planilla</h3>
                                <p>Actualice los precios cargando una planilla de Excel.</p>
                                <a href="<?= BASE_URL ?>planilla" class="btn btn-outline-primary mt-2">Ir a Actualización por Planilla</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-folder-tree fa-4x text-primary mb-3"></i>
                                <h3>Actualización por Familia</h3>
                                <p>Actualice los precios seleccionando una familia de artículos.</p>
                                <a href="<?= BASE_URL ?>familia" class="btn btn-outline-primary mt-2">Ir a Actualización por Familia</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="<?= BASE_URL ?>resumen" class="btn btn-outline-secondary">
                        <i class="fas fa-list-check me-2"></i>
                        Ver Resumen de Operaciones
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
