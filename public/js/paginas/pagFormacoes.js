$(document).ready(function () {
    inicializarDataTable();
});

function inicializarDataTable() {
    $('#tabelaDados').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url":"formacoes/getAll", 
          "type": "GET"
        },
        "columns": [
            {data: 'id_formacao', name: 'formacao.id_formacao'},
            {data: 'nomeInstituicao', name: 'formacao.nomeInstituicao'},
            {data: 'email', name: 'formacao.email'},
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
    var url = "formacoes/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (formacao) {
            if (formacao != null) {
                url = 'formacoes/edit/' + formacao.id_formacao
                $('#formEditar').attr('action', url)
                $('#nomeInstituicao').val(formacao.nomeInstituicao)
                $('#email').val(formacao.email)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'formacoes/delete/' + id
    $('#formDelete').attr('action', url)
}