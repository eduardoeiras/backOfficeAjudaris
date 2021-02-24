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
    var url = "ilustradores/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (ilustrador) {
            if (ilustrador != null) {
                url = 'ilustradores/edit/' + ilustrador.id_ilustradorSolidario
                $('#formEditar').attr('action', url)
                $('#nome').val(ilustrador.nome)
                $('#telefone').val(ilustrador.telefone)
                $('#telemovel').val(ilustrador.telemovel)
                $('#email').val(ilustrador.email)
                $('#volumeLivro').val(ilustrador.volumeLivro)
                var disp = ilustrador.disponivel
                $('#disponibilidade').val(disp.toString())
                $('#observacoes').val(ilustrador.observacoes)
            }
        },
        error: function (error) {

        }
    })
}