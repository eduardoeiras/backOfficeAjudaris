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
    var url = "profsFaculdade/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (profFacul) {
            if (profFacul != null) {
                url = 'profsFaculdade/edit/' + profFacul.id_professorFaculdade
                $('#formEditar').attr('action', url)
                $('#nome').val(profFacul.nome)
                $('#cargo').val(profFacul.cargo)
                $('#telemovel').val(profFacul.telemovel)
                $('#telefone').val(profFacul.telefone)
                $('#email').val(profFacul.email)
                var disp = profFacul.disponivel
                $('#disponibilidade').val(disp.toString())
                $('#observacoes').val(profFacul.observacoes)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'profsFaculdade/delete/' + id
    $('#formDelete').attr('action', url)
}