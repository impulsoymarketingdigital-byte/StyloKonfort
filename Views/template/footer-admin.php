</div>
</div>
<!--end page wrapper -->
<!--start overlay-->
<div class="overlay toggle-icon"></div>
<!--end overlay-->
<!--Start Back To Top Button-->
<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
<!--End Back To Top Button-->
<footer class="page-footer">
    <p class="mb-0">Copyright © <?php echo date('Y'); ?>. All right reserved.</p>
</footer>
</div>
<!--end wrapper-->

<!-- Bootstrap JS -->
<script src="<?php echo BASE_URL; ?>assets/admin/js/bootstrap.bundle.min.js"></script>
<!--plugins-->
<script src="<?php echo BASE_URL; ?>assets/admin/js/jquery-3.6.0.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/admin/plugins/simplebar/js/simplebar.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/admin/plugins/metismenu/js/metisMenu.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/admin/plugins/chartjs/js/Chart.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/admin/plugins/chartjs/js/Chart.extension.js"></script>

<!--app JS-->
<script src="<?php echo BASE_URL; ?>assets/admin/js/app.js"></script>

<script src="<?php echo BASE_URL; ?>assets/admin/js/dropzone-min.js"></script>

<script src="<?php echo BASE_URL; ?>assets/admin/js/all.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/admin/js/sweetalert2.all.min.js"></script>
<script>
    const base_url = '<?php echo BASE_URL; ?>';
</script>
<script type="text/javascript" src="<?php echo BASE_URL . 'assets/DataTables/datatables.min.js'; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/admin/js/es-ES.js"></script>

<script>
    function alertas(mensaje, type) {
        Swal.fire({
            toast: true,
            position: 'top-right',
            icon: type,
            title: mensaje,
            showConfirmButton: false,
            timer: 2000
        })
    }
</script>