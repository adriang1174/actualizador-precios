<?php
/**
 * Vista de Actualización por Familia
 * 
 * Esta vista permite actualizar precios mediante la selección de una familia
 * de artículos de Factusol, aplicando un coeficiente de actualización.
 */
include 'views/templates/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item active">Actualización por Familia</li>
            </ol>
        </nav>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="mb-4 text-primary"><i class="fas fa-folder me-2"></i>Actualización por Familia</h2>
                <p class="lead">
                    Actualice la lista de precios seleccionando una familia de artículos
                    y configurando los parámetros de actualización.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <form id="familiaForm" action="index.php?action=actualizar_familia" method="POST">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Selección de Familia</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="familia" class="form-label">Familia de artículos</label>
                        <select class="form-select" id="familia" name="familia" required>
                            <option value="">Seleccione una familia...</option>
                            <!-- Estas opciones serían cargadas dinámicamente desde la base de datos -->
                            <option value="1">Familia 1</option>
                            <option value="2">Familia 2</option>
                            <option value="3">Familia 3</option>
                        </select>
                        <div class="form-text">Seleccione la familia de artículos cuyos precios desea actualizar.</div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="totalArticulos" class="form-label">Total artículos en la familia:</label>
                            <span id="totalArticulos" class="badge bg-primary rounded-pill">0</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>Parámetros de Actualización</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="coeficiente" class="form-label">Coeficiente de actualización</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="coeficiente" name="coeficiente" step="0.01" min="0.01" value="1.00" required>
                            <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                        </div>
                        <div class="form-text">
                            Valor que multiplicará el costo/precio para obtener el valor actualizado. 
                            Utilice 1.00 para mantener el valor original.
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Campos a actualizar</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="updateCosto" name="updateCosto" value="1">
                                    <label class="form-check-label" for="updateCosto">
                                        Actualizar Costo (PCOART)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="updateDolarPapel" name="updateDolarPapel" value="1">
                                    <label class="form-check-label" for="updateDolarPapel">
                                        Actualizar DolarPapel (Tarifa 3)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="updateTarifaA" name="updateTarifaA" value="1">
                                    <label class="form-check-label" for="updateTarifaA">
                                        Actualizar Tarifa A (Tarifa 1)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="updateTarifaB" name="updateTarifaB" value="1">
                                    <label class="form-check-label" for="updateTarifaB">
                                        Actualizar Tarifa B (Tarifa 2)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end mb-5">
                <button type="button" class="btn btn-secondary me-2" onclick="location.href='index.php'">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="submit" id="btnActualizar" class="btn btn-primary">
                    <i class="fas fa-sync-alt me-1"></i>Actualizar Precios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Script específico para la actualización por familia
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('familiaForm');
    const familiaSelect = document.getElementById('familia');
    const totalArticulosSpan = document.getElementById('totalArticulos');
    const progressBar = document.querySelector('.progress-bar');
    
    // Cuando cambia la selección de familia, obtener el total de artículos
    familiaSelect.addEventListener('change', function() {
        if (this.value) {
            // En una implementación real, esto sería una petición AJAX al servidor
            // para obtener el total de artículos de la familia seleccionada
            
            // Simulación de respuesta
            const familiaId = this.value;
            const totalArticulos = familiaId === '1' ? 42 : familiaId === '2' ? 78 : 35;
            
            totalArticulosSpan.textContent = totalArticulos;
            progressBar.style.width = '100%';
        } else {
            totalArticulosSpan.textContent = '0';
            progressBar.style.width = '0%';
        }
    });
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verificar que se haya seleccionado una familia
        if (!familiaSelect.value) {
            alert('Debe seleccionar una familia de artículos.');
            return;
        }
        
        // Verificar que al menos un campo de actualización esté seleccionado
        const updateCosto = document.getElementById('updateCosto').checked;
        const updateTarifaA = document.getElementById('updateTarifaA').checked;
        const updateTarifaB = document.getElementById('updateTarifaB').checked;
        const updateDolarPapel = document.getElementById('updateDolarPapel').checked;
        
        if (!updateCosto && !updateTarifaA && !updateTarifaB && !updateDolarPapel) {
            alert('Debe seleccionar al menos un campo para actualizar.');
            return;
        }
        
        // Mostrar modal de progreso
        const processingModal = new bootstrap.Modal(document.getElementById('processingModal'));
        processingModal.show();
        
        // Simular progreso (esto sería reemplazado por el progreso real de la operación)
        const modalProgressBar = document.querySelector('#processingModal .progress-bar');
        let progress = 0;
        
        const interval = setInterval(function() {
            progress += 5;
            modalProgressBar.style.width = progress + '%';
            
            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(function() {
                    processingModal.hide();
                    const completedModal = new bootstrap.Modal(document.getElementById('completedModal'));
                    completedModal.show();
                }, 
				500);
                }
            }, 300);
            
            // En una implementación real, aquí se enviaría el formulario mediante AJAX
            // y se actualizaría el progreso según la respuesta del servidor
        });
    });
</script>

<?php include 'views/templates/footer.php'; ?>