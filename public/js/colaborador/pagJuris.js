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
    var url = "juris/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (juri) {
            if (juri != null) {
                url = 'juris/edit/' + juri.id_juri
                $('#formEditar').attr('action', url)
                $('#nome').val(juri.nome)
                $('#telefone').val(juri.telefone)
                $('#telemovel').val(juri.telemovel)
                $('#email').val(juri.email)
                var disp = juri.disponivel
                $('#disponibilidade').val(disp.toString())
            }
        },
        error: function (error) {

        }
    })
}