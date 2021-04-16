$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});

$(document).ready(function () {
    inicializarDataTable('#tabelaDados')
});

function inicializarDataTable(idTabela) {
    $(idTabela).DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url":"pesqGlobal/getColaboradores", 
          "type": "GET"
        },
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
