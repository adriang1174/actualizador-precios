<?php
/**
 * Vista de Resumen de Operaciones
 * 
 * Esta vista muestra un resumen de las operaciones de actualización realizadas,
 * incluyendo estadísticas y detalles de los registros procesados.
 */
include 'views/templates/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item active">Resumen de Operaciones</li>
            </ol>
        </nav>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="mb-4 text-primary"><i class="fas fa-chart-bar me-2"></i>Resumen de Operaciones</h2>
                <p class="lead">
                    Visualice estadísticas y detalles de las actualizaciones de precios realizadas.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Estadísticas de la Última Actualización</h5>
                <span class="badge bg-primary">07/08/2024 14:35:22</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Estadística: Total Artículos en Familia -->
                    <div class="col-md-3">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Total Artículos en Familia</h6>
                                <h2 class="mb-0 text-primary">78</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Estadística: Total Filas Planilla -->
                    <div class="col-md-3">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Total Filas Planilla</h6>
                                <h2 class="mb-0 text-primary">65</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Estadística: Total Artículos Actualizados -->
                    <div class="col-md-3">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Artículos Actualizados</h6>
                                <h2 class="mb-0 text-success">62</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Estadística: Artículos Sin Actualizar -->
                    <div class="col-md-3">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Artículos Sin Actualizar</h6>
                                <h2 class="mb-0 text-danger">16</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de resumen -->
                <div class="mt-4 pt-3">
                    <h5 class="mb-3">Distribución de Resultados</h5>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: 79.5%;" aria-valuenow="79.5" aria-valuemin="0" aria-valuemax="100">
                            79.5% Actualizados
                        </div>
                        <div class="progress-bar bg-danger" role="progressbar" 
                             style="width: 20.5%;" aria-valuenow="20.5" aria-valuemin="0" aria-valuemax="100">
                            20.5% Sin Actualizar
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light py-3">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Artículos Sin Actualizar</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Código Equivalente</th>
                                <th>Descripción</th>
                                <th>Familia</th>
                                <th>Estado</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Estos datos se cargarían dinámicamente desde la base de datos -->
                            <tr>
                                <td>ART001</td>
                                <td>PRV102355</td>
                                <td>Tornillo hexagonal 8mm</td>
                                <td>TORNILLERIA</td>
                                <td><span class="badge bg-danger">No actualizado</span></td>
                                <td>Código equivalente no encontrado en la planilla</td>
                            </tr>
                            <tr>
                                <td>ART042</td>
                                <td>PRV111078</td>
                                <td>Arandela plana 10mm</td>
                                <td>TORNILLERIA</td>
                                <td><span class="badge bg-danger">No actualizado</span></td>
                                <td>Código equivalente no encontrado en la planilla</td>
                            </tr>
                            <tr>
                                <td>ART078</td>
                                <td>PRV100532</td>
                                <td>Tuerca autoblocante M8</td>
                                <td>TORNILLERIA</td>
                                <td><span class="badge bg-danger">No actualizado</span></td>
                                <td>Valor no numérico en la planilla</td>
                            </tr>
                            <tr>
                                <td>ART125</td>
                                <td>PRV112987</td>
                                <td>Perno de anclaje 12x150mm</td>
                                <td>TORNILLERIA</td>
                                <td><span class="badge bg-danger">No actualizado</span></td>
                                <td>Código equivalente no encontrado en la planilla</td>
                            </tr>
                            <tr>
                                <td>ART156</td>
                                <td>PRV120014</td>
                                <td>Tornillo autorroscante 5x40mm</td>
                                <td>TORNILLERIA</td>
                                <td><span class="badge bg-danger">No actualizado</span></td>
                                <td>Valor no numérico en la planilla</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <nav aria-label="Paginación de resultados" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light py-3">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Actualizaciones</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Familia</th>
                                <th>Filas Procesadas</th>
                                <th>Artículos Actualizados</th>
                                <th>% Éxito</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Estos datos se cargarían dinámicamente desde la base de datos -->
                            <tr>
                                <td>07/08/2024 14:35:22</td>
                                <td><span class="badge bg-info">Planilla</span></td>
                                <td>TORNILLERIA</td>
                                <td>65</td>
                                <td>62</td>
                                <td>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: 95%;" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small>95%</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>05/08/2024 10:15:07</td>
                                <td><span class="badge bg-warning">Familia</span></td>
                                <td>ELECTRICIDAD</td>
                                <td>42</td>
                                <td>42</td>
                                <td>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small>100%</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>01/08/2024 16:42:53</td>
                                <td><span class="badge bg-info">Planilla</span></td>
                                <td>HERRAMIENTAS</td>
                                <td>58</td>
                                <td>51</td>
                                <td>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: 88%;" aria-valuenow="88" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small>88%</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Script específico para la vista de resumen
document.addEventListener('DOMContentLoaded', function() {
    // En una implementación real, aquí se cargarían dinámicamente los datos
    // mediante AJAX desde el servidor
});
</script>

<?php include 'views/templates/footer.php'; ?>
