const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");
const titulo = document.querySelector("#titulo");
const subtitulo = document.querySelector("#subtitulo");
const imagen = document.querySelector("#imagen");
const imagen_actual = document.querySelector("#imagen_actual");
const container_img = document.querySelector("#container-img");
const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));
let tblSliders;
document.addEventListener("DOMContentLoaded", function () {

    imagen.addEventListener('change', function (e) {
        imagen_actual.value = '';
        if (e.target.files[0].type == 'image/png' ||
            e.target.files[0].type == 'image/jpg' ||
            e.target.files[0].type == 'image/jpeg') {
            const url = e.target.files[0];
            const tmpUrl = URL.createObjectURL(url);
            container_img.innerHTML = `<img class="img-thumbnail" src="${tmpUrl}" width="200">`;
        } else {
            imagen.value = '';
            alertas('SOLO SE PERMITEN IMG DE TIPO PNG-JPG-JPEG', 'warning');
        }
    })

    tblSliders = $("#tblSliders").DataTable({
        ajax: {
            url: base_url + "sliders/listar",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "titulo" },
            { data: "subtitulo" },
            { data: "imagen" },
            { data: "accion" }
        ],
        responsive: true,
        language,
        dom,
        buttons,
    });

    //submit sliders
    frm.addEventListener("submit", function (e) {
        e.preventDefault();
        if (titulo.value == '' || subtitulo.value == '') {
            alertas('TODO LOS CAMPOS SON REQUERIDOS', 'warning');
        } else {
            let data = new FormData(this);
            const url = base_url + "sliders/registrar";
            const http = new XMLHttpRequest();
            http.open("POST", url, true);
            http.send(data);
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    if (res.icono == "success") {
                        myModal.hide();
                        tblSliders.ajax.reload();
                        imagen.value = '';
                        container_img.value = '';
                    }
                    alertas(res.msg.toUpperCase(), res.icono);
                }
            }
        }
    });
});

function editSli(idSli) {
    const url = base_url + "sliders/edit/" + idSli;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            document.querySelector('#id').value = res.id;
            document.querySelector('#titulo').value = res.titulo;
            document.querySelector('#subtitulo').value = res.subtitulo;
            document.querySelector('#enlace').value = res.link;
            document.querySelector('#imagen_actual').value = res.imagen;
            btnAccion.textContent = 'Actualizar';
            container_img.innerHTML = `<img class="img-thumbnail" src="${base_url + res.imagen}" width="300">`;
            titleModal.textContent = "MODIFICAR titulo";
            myModal.show();
            //$('#nuevoModal').modal('show');
        }
    }
}