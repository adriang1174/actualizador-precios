<?php
/**
 * Página principal del Actualizador de Precios
 * 
 * Esta vista muestra la página de inicio con opciones para navegar a las
 * diferentes funcionalidades del sistema.
 */
include 'views/templates/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="display-5 text-primary mb-4">
                    <i class="fas fa-sync-alt me-3"></i>Actualizador de Precios Factusol
                </h1>
                <p class="lead">
                    Bienvenido al sistema de actualización masiva de precios para Factusol. 
                    Seleccione una de las opciones disponibles para comenzar.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Opción: Actualización por Planilla -->
    <div class="col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="feature-icon bg-primary bg-gradient text-white p-3 rounded-circle me-3">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <h3 class="card-title">Actualización por Planilla</h3>
                </div>
                <p class="card-text">
                    Actualice la lista de precios (costo y precio de artículos) 
                    a través de la carga de una planilla de Excel dinámica.
                </p>
                <ul class="list-unstyled mb-4">
                    <li><i class="fas fa-check-circle text-success me-2"></i>Carga de planillas Excel</li>
                    <li><i class="fas fa-check-circle text-success me-2"></i>Mapeo de columnas personalizable</li>
                    <li><i class="fas fa-check-circle text-success me-2"></i>Actualización masiva de precios</li>
                </ul>
                <a href="index.php?view=planilla" class="btn btn-primary">
                    <i class="fas fa-arrow-right me-2"></i>Ir a Actualización por Planilla
                </a>
            </div>
        </div>
    </div>

    <!-- Opción: Actualización por Familia -->
    <div class="col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="feature-icon bg-primary bg-gradient text-white p-3 rounded-circle me-3">
                        <i class="fas fa-folder"></i>
                    </div>
                    <h3 class="card-title">Actualización por Familia</h3>
                </div>
                <p class="card-text">
                    Actualice la lista de precios (costo y precio de artículos) 
                    a través de la selección de una familia de Factusol.
                </p>
                <ul class="list-unstyled mb-4">
                    <li><i class="fas fa-check-circle text-success me-2"></i>Selección por familia de artículos</li>
                    <li><i class="fas fa-check-circle text-success me-2"></i>Coeficiente de actualización personalizable</li>
                    <li><i class="fas fa-check-circle text-success me-2"></i>Actualización específica de costo y/o precio</li>
                </ul>
                <a href="index.php?view=familia" class="btn btn-primary">
                    <i class="fas fa-arrow-right me-2"></i>Ir a Actualización por Familia
                </a>
            </div>
        </div>
    </div>

    <!-- Opción: Resumen de Operaciones -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="feature-icon bg-primary bg-gradient text-white p-3 rounded-circle me-3">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="card-title">Resumen de Operaciones</h3>
                </div>
                <p class="card-text">
                    Visualice el resultado de las operaciones de actualización realizadas.
                    Consulte estadísticas y detalles de los registros procesados.
                </p>
                <a href="index.php?view=resumen" class="btn btn-primary">
                    <i class="fas fa-arrow-right me-2"></i>Ver Resumen de Operaciones
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'views/templates/footer.php'; ?>
