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
    var url = "contadores/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (contador) {
            if (contador != null) {
                url = 'contadores/edit/' + contador.id_contadorHistorias
                $('#formEditar').attr('action', url)
                $('#nome').val(contador.nome)
                $('#telefone').val(contador.telefone)
                $('#telemovel').val(contador.telemovel)
                $('#email').val(contador.email)
                var disp = contador.disponivel
                $('#disponibilidade').val(disp.toString())
                $('#observacoes').val(contador.observacoes)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'contadores/delete/' + id
    $('#formDelete').attr('action', url)
}