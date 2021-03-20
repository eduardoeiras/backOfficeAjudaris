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
    var url = "concelhos/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (concelho) {
            if (concelho != null) {
                url = 'concelhos/edit/' + concelho.id_concelho
                $('#formEditar').attr('action', url)
                $('#nome').val(concelho.nome)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'concelhos/verificaRbe/' + id
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
        url: url,
        method: "GET",
        success: function (msg) {
            if (msg != null && msg != '') {
                $('#titulo').text("Erro");
                $('#mensagem').text(msg);
                $('#msg').modal('show');
            }
            if(msg == '') {
                url = 'concelhos/delete/' + id
                $('#formDelete').attr('action', url)
                $('#delete').modal("show");
            }
        },
        error: function (error) {
        }
    })
}