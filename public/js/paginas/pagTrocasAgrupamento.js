$(document).ready(function () {
    inicializarDataTable();
});

function inicializarDataTable() {
    $('#tabelaDados').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url":"trocasAgrupamento/getAll", 
          "type": "GET"
        },
        "columns": [
            {data: 'id_troca', name: 'troca_agrupamento.id_troca'},
            {data: 'nome', name: '', orderable: false, searchable: false},
            {data: 'agrupamentoAntigo', name: 'troca_agrupamento.agrupamentoAntigo'},
            {data: 'novoAgrupamento', name: 'troca_agrupamento.novoAgrupamento'},
            {data: 'data', name: 'troca_agrupamento.data'},
            {data: 'opcoes', name: '', orderable: false, searchable: false},
        ],
        "language": {
            "sSearch": "Pesquisar",
            "lengthMenu": "Mostrar _MENU_ registos por página",
            "zeroRecords": "Nehum registo encontrado!",
            "info": "A mostrar a página _PAGE_ de _PAGES_",
            "infoEmpty": "Nehuns registos disponíveis!",
            "infoFiltered": "(filtrados _MAX_ do total de registos)",
            "processing": "Obtendo registos. Por favor aguarde...",
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