</div><!-- Fin del contenedor principal -->
    
    <!-- Modal de Progreso -->
    <div class="modal fade" id="progressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Actualización en Progreso</h5>
                </div>
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p>La actualización de precios está en proceso. Por favor, espere...</p>
                    <div class="progress" role="progressbar">
                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Actualización Terminada -->
    <div class="modal fade" id="completedModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Actualización Completada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="text-success mb-3">
                        <i class="fas fa-check-circle fa-4x"></i>
                    </div>
                    <p>La actualización de precios ha sido completada con éxito.</p>
                    <p id="updateSummary"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <a href="#" id="verResumenBtn" class="btn btn-primary">Ver Resumen de Operaciones</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- JS personalizado -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
