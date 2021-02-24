$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});

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

function downloadRegulamento(id) {
    /*
    var url = "projetos/getPdf/" + id;
    $.ajax({
        url: url,
        method: "GET",
        success: function(response){
            window.location.href = response;
        },
        error: function(error){
            
        }
    })
    */
}

function editarProjeto(id) {
    var url = "projetos/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response != null) {
                url = 'projetos/edit/' + response.projeto.id_projeto
                $('#formEditar').attr('action', url)
                $('#editPorjetoId').val(response.projeto.id_projeto)
                $('#edit_Nome').val(response.projeto.nome)
                $('#edit_Obj').val(response.projeto.objetivos)
                $('#edit_PublicoAlvo').val(response.projeto.publicoAlvo)
                $('#edit_Obs').val(response.projeto.observacoes)
            }
        },
        error: function (error) {

        }
    })
}