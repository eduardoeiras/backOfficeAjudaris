$(document).ready(function () {
    inicializarDataTable();
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
    var url = "trocasAgrupamento/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (troca) {
            if (troca != null) {
                url = 'trocasAgrupamento/edit/' + troca.id_troca
                $('#formEditar').attr('action', url)
                $('#agrupamentoAntigo').val(troca.agrupamentoAntigo)
                $('#novoAgrupamento').val(troca.novoAgrupamento)
                $('#observacoes').val(troca.observacoes)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'trocasAgrupamento/delete/' + id
    $('#formDelete').attr('action', url)
}