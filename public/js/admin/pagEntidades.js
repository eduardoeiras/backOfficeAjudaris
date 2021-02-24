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
})

function editar(id) {
    var url = "entidades/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (entidade) {
            if (entidade != null) {
                url = 'entidades/edit/' + entidade.id_entidadeOficial
                $('#formEditar').attr('action', url)
                $('#nome').val(entidade.nome)
                $('#email').val(entidade.email)
                $('#entidade').val(entidade.entidade)
                $('#telefone').val(entidade.telefone)
                $('#telemovel').val(entidade.telemovel)
                var disp = entidade.disponivel
                $('#disponibilidade').val(disp.toString())
                $('#observacoes').val(entidade.observacoes)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'entidades/delete/' + id
    $('#formDelete').attr('action', url)
}