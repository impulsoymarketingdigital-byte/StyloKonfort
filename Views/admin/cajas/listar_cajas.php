<?php include_once 'Views/template/header-admin.php'; ?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title text-center"><i class="fas fa-cash-register"></i> Historial de Cajas</h5>
        <hr>
        <div class="d-flex justify-content-center mb-3">
            <div class="form-group me-2">
                <label for="desde">Desde</label>
                <input id="desde" class="form-control" type="date">
            </div>
            <div class="form-group">
                <label for="hasta">Hasta</label>
                <input id="hasta" class="form-control" type="date">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle" id="tblHistorialCajas" style="width: 100%;">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Fecha Apertura</th>
                        <th>Fecha Cierre</th>
                        <th>Monto Inicial</th>
                        <th>Monto Final</th>
                        <th>Monto Físico</th>
                        <th>Estado</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>
<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/listar_cajas.js'; ?>"></script>