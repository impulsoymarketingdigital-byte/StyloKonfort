<?php include_once 'Views/template/header-principal.php'; ?>


<section class="contact-page section-big-py-space b-g-light">
    <div class="custom-container">
        <div class="row section-big-pb-space">
            <div class="col-xl-6 offset-xl-3">
                <h3 class="text-center mb-3">Escribenos un mensaje</h3>
                <form class="theme-form" id="frmContactos" autocomplete="off">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" placeholder="Nombre Completo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Correo Electrónico</label>
                                <input type="text" class="form-control" id="email" placeholder="Correo Electrónico" required>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div>
                                <label>Mensaje</label>
                                <textarea class="form-control" id="message" placeholder="Mensaje" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-normal" type="submit">Enviar Mensaje</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 map">
                <div class="theme-card">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1605.811957341231!2d25.45976406005396!3d36.3940974010114!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sin!4v1550912388321" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once 'Views/template/footer-principal.php'; ?>

<script src="<?php echo BASE_URL . 'assets/admin/js/ckeditor.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/js/contactos.js'; ?>"></script>

</body>

</html>