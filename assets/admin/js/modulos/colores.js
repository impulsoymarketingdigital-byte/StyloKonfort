const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");
const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));
const checkColorCombinado = document.querySelector("#checkColorCombinado");
const divColorSecundario = document.querySelector("#divColorSecundario");
const colorSecundario = document.querySelector("#color_secundario");
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
            { data: "estado" },
            { data: "accion" }
        ],
        language,
        dom,
        buttons,
    });
    
    // Control del checkbox para mostrar/ocultar color secundario
    checkColorCombinado.addEventListener("change", function() {
        if (this.checked) {
            divColorSecundario.style.display = "block";
        } else {
            divColorSecundario.style.display = "none";
            colorSecundario.value = "#000000";
        }
    });
    
    nuevo.addEventListener("click", function() {
        document.querySelector('#id').value = '';
        document.querySelector('#color').value = '#000000';
        document.querySelector('#color_secundario').value = '#000000';
        checkColorCombinado.checked = false;
        divColorSecundario.style.display = "none";
        titleModal.textContent = "NUEVO COLOR";
        btnAccion.textContent = 'Registrar';
        frm.reset();
        myModal.show();
    });
    
    frm.addEventListener("submit", function(e) {
        e.preventDefault();
        let data = new FormData(this);
        
        // Si NO está checkeado, NO enviar color secundario
        if (!checkColorCombinado.checked) {
            data.delete('color_secundario');
        }
        
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

function restaurarColor(idColor) {
    Swal.fire({
        title: "Aviso?",
        text: "Esta seguro de restaurar el registro!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, Restaurar!",
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "colores/restaurar/" + idColor;
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
            
            // Si tiene color secundario, activar checkbox y mostrar
            if (res.color_secundario && res.color_secundario !== '' && res.color_secundario !== null) {
                checkColorCombinado.checked = true;
                divColorSecundario.style.display = "block";
                document.querySelector('#color_secundario').value = res.color_secundario;
            } else {
                checkColorCombinado.checked = false;
                divColorSecundario.style.display = "none";
                document.querySelector('#color_secundario').value = '#000000';
            }
            
            btnAccion.textContent = 'Actualizar';
            titleModal.textContent = "MODIFICAR COLOR";
            myModal.show();
        }
    }
}