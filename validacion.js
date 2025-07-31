$(document).ready(function() {
    cargarTabla();

    // Evento al enviar el formulario
    $("#formularioArchivo").submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        // Validación básica de campos obligatorios
        if (!$("#nom_arc").val().trim() || !$("#des_arc").val().trim()) {
            alert('Por favor, llena todos los campos obligatorios.');
            return;
        }

        // Si está en modo "crear", se requiere archivo
        if ($("#action").val() === "crear_archivo" && $("#arc_arc")[0].files.length === 0) {
            alert('Debes seleccionar un archivo para subir.');
            return;
        }

        // Validar archivo si fue cargado
        if ($("#arc_arc")[0].files[0] && !validarArchivo()) {
            alert('Archivo inválido. Revisa el tamaño o extensión.');
            return;
        }

        // Enviar al backend
        $.ajax({
            url: 'controlador.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#modalArchivo').modal('hide');
                    $("#formularioArchivo")[0].reset();
                    cargarTabla();
                } else {
                    alert(res.message);
                }
            }
        });
    });
});

// Cargar tabla desde PHP
function cargarTabla() {
    $.post('controlador.php', { action: 'listar' }, function(data) {
        $("#tablaArchivos").html(data);
    });
}

// Abre el modal, ya sea en blanco o con datos
function abrirModal(id = '', nombre = '', descripcion = '') {
    $("#formularioArchivo")[0].reset();
    $("#id_arc").val(id);
    $("#nom_arc").val(nombre);
    $("#des_arc").val(descripcion);
    $("#action").val(id ? "editar_archivo" : "crear_archivo");
    $("#modalArchivo").modal('show');
}

// Trae datos y abre modal en modo edición
function editarArchivo(id) {
    $.post('controlador.php', { action: 'obtener', id: id }, function(res) {
        if (res.success) {
            abrirModal(res.data.id_arc, res.data.nom_arc, res.data.des_arc);
        } else {
            alert(res.message);
        }
    }, 'json');
}

// Confirma y elimina el archivo
function eliminarArchivo(id) {
    if (!confirm("¿Seguro que deseas eliminar este archivo?")) return;

    $.post('controlador.php', { action: 'eliminar_archivo', id: id }, function(res) {
        if (res.success) {
            cargarTabla();
        } else {
            alert(res.message);
        }
    }, 'json');
}

// Validación de archivo (extensión y peso)
function validarArchivo() {
    var archivo = $("#arc_arc")[0].files[0];
    var ext = archivo.name.split('.').pop().toLowerCase();
    var tam = archivo.size;

    if (!['pdf', 'jpg', 'jpeg', 'png', 'docx'].includes(ext)) return false;
    if (tam > 5242880) return false;
    return true;
}
