$(document).ready(function () {
    inicializarDataTable()
});

function inicializarDataTable() {
    $('#tabelaDados').DataTable({
        "language": {
            "sSearch": "Pesquisar",
            "lengthMenu": "Mostrar _MENU_ registos por página",
            "zeroRecords": "Nehum registo encontrado!",
            "info": "A mostrar a página _PAGE_ de _PAGES_",
            "infoEmpty": "Nehuns registos disponíveis!",
            "infoFiltered": "(filtrados _MAX_ do total de registos)",
            "paginate": {
                "previous": "Anterior",
                "next": "Seguinte"
            }
        }
    });
}

$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});

function editar(id) {
    var url = "universidades/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (uni) {
            if (uni != null) {
                url = 'universidades/edit/' + uni.id_universidade
                $('#formEditar').attr('action', url)
                $('#curso').val(uni.curso)
                $('#tipo').val(uni.tipo)
                $('#nome').val(uni.nome)
                $('#telefone').val(uni.telefone)
                $('#telemovel').val(uni.telemovel)
                $('#email').val(uni.email)
                var disp = uni.disponivel
                $('#disponibilidade').val(disp.toString())
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'universidades/delete/' + id
    $('#formDelete').attr('action', url)
}