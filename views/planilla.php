<?php
/**
 * Vista de Actualización por Planilla
 * 
 * Esta vista permite actualizar precios mediante la carga de una planilla Excel,
 * configurando las columnas de código equivalente y valor, así como el coeficiente
 * de actualización.
 */
include 'views/templates/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item active">Actualización por Planilla</li>
            </ol>
        </nav>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="mb-4 text-primary"><i class="fas fa-file-excel me-2"></i>Actualización por Planilla</h2>
                <p class="lead">
                    Actualice la lista de precios cargando una planilla de Excel y configurando
                    los parámetros de actualización.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <form id="uploadForm" action="index.php?action=actualizar_planilla" method="POST" enctype="multipart/form-data">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Carga de Archivo</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="excelFile" class="form-label">Archivo Excel (.xls, .xlsx)</label>
                        <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xls,.xlsx" required>
                        <div class="form-text">Seleccione el archivo Excel que contiene los datos para actualizar los precios.</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="startRow" class="form-label">Fila inicial de datos</label>
                            <input type="number" class="form-control" id="startRow" name="startRow" min="1" value="2" required>
                            <div class="form-text">Indique a partir de qué fila comienzan los datos (generalmente la fila 2 si hay encabezados).</div>
                        </div>
                        <div class="col-md-4">
                            <label for="codEquivColumn" class="form-label">Columna de código equivalente</label>
                            <input type="text" class="form-control" id="codEquivColumn" name="codEquivColumn" required placeholder="Ej: A">
                            <div class="form-text">Columna que contiene el código equivalente del proveedor (EQUART).</div>
                        </div>
                        <div class="col-md-4">
                            <label for="valorColumn" class="form-label">Columna de valor (precio)</label>
                            <input type="text" class="form-control" id="valorColumn" name="valorColumn" required placeholder="Ej: C">
                            <div class="form-text">Columna que contiene el valor a utilizar para actualizar.</div>
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
// Script específico para la actualización por planilla
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('uploadForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verificar que al menos un campo de actualización esté seleccionado
        const updateCosto = document.getElementById('updateCosto').checked;
        const updateTarifaA = document.getElementById('updateTarifaA').checked;
        const updateTarifaB = document.getElementById('updateTarifaB').checked;
        const updateDolarPapel = document.getElementById('updateDolarPapel').checked;
        
        if (!updateCosto && !updateTarifaA && !updateTarifaB && !updateDolarPapel) {
            alert('Debe seleccionar al menos un campo para actualizar.');
            return;
        }
        
        // Verificar que se haya seleccionado un archivo
        const fileInput = document.getElementById('excelFile');
        if (fileInput.files.length === 0) {
            alert('Debe seleccionar un archivo Excel.');
            return;
        }
        
        // Mostrar modal de progreso
        const processingModal = new bootstrap.Modal(document.getElementById('processingModal'));
        processingModal.show();
        
        // Enviar formulario (en una aplicación real, esto sería mediante AJAX)
        const formData = new FormData(form);
        
        // Simular progreso (esto sería reemplazado por el progreso real de la operación)
        const progressBar = document.querySelector('.progress-bar');
        let progress = 0;
        
        const interval = setInterval(function() {
            progress += 5;
            progressBar.style.width = progress + '%';
            
            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(function() {
                    processingModal.hide();
                    const completedModal = new bootstrap.Modal(document.getElementById('completedModal'));
                    completedModal.show();
                }, 500);
            }
        }, 300);
        
        // En una implementación real, aquí se enviaría el formulario mediante AJAX
        // y se actualizaría el progreso según la respuesta del servidor
    });
});
</script>

<?php include 'views/templates/footer.php'; ?>
