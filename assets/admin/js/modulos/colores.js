const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");
const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));
let tblColores;
document.addEventListener("DOMContentLoaded", function() {
    tblColores = $("#tblColores").DataTable({
        ajax: {
            url: base_url + "colores/listar",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "nombre" },
            { data: "color" },
            { data: "accion" }
        ],
        language,
        dom,
        buttons,
    });

    //levantar modal
    nuevo.addEventListener("click", function() {
        document.querySelector('#id').value = '';
        titleModal.textContent = "NUEVA COLOR";
        btnAccion.textContent = 'Registrar';
        frm.reset();
        myModal.show();
        //$('#nuevoModal').modal('show');
    });
    //submit colores
    frm.addEventListener("submit", function(e) {
        e.preventDefault();
        let data = new FormData(this);
        const url = base_url + "colores/registrar";
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(data);
        http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                const res = JSON.parse(this.responseText);
                if (res.icono == "success") {
                    myModal.hide();
                    tblColores.ajax.reload();
                }
                alertas(res.msg.toUpperCase(), res.icono);
            }
        }
    });
});

function eliminarColor(idColor) {
    Swal.fire({
        title: "Aviso?",
        text: "Esta seguro de eliminar el registro!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, Eliminar!",
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "colores/delete/" + idColor;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    if (res.icono == "success") {
                        tblColores.ajax.reload();
                    }
                    alertas(res.msg.toUpperCase(), res.icono);
                }
            }
        }
    });
}

function editColor(idColor) {
    const url = base_url + "colores/edit/" + idColor;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            document.querySelector('#id').value = res.id;
            document.querySelector('#nombre').value = res.nombre;
            document.querySelector('#color').value = res.color;
            btnAccion.textContent = 'Actualizar';
            titleModal.textContent = "MODIFICAR nombre";
            myModal.show();
            //$('#nuevoModal').modal('show');
        }
    }
}