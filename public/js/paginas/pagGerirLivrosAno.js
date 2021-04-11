var id_escola = null;
var erroAno = false;
$(document).ready(function () {
    
    if($('#idEscolaAdd').val() != null) {
        id_escola = $('#idEscolaAdd').val()
    }

    inicializarDataTable();
    $("#dataComunicacaoAdd").datepicker({
        format: " yyyy",
        viewMode: "years", 
        minViewMode: "years"
    });

    $('#anoAdd').on('keyup', function(e) {
        var ano = e.target.value;
        let url = 'gerirLivrosAno/existeAssociacao/' + ano + "-" + id_escola
        if(ano != "") {
            $.ajax({
                url: url,
                method: "GET",
                dataType: "json",
                success: function (existe) {
                    if(existe == 1) {
                        erroAno = true
                        $('#erroAnoExiste').text("Já existe uma atribuição de livros para o ano introduzido!")
                    }
                    else {
                        erroAno = false
                        $('#erroAnoExiste').text(" ")
                    }
                },
                error: function (error) {
                    
                }
            })    
        }
    });
});

function verificarErroAno() {
    if(erroAno) {
        return false;
    }
    else {
        return true;
    } 
}

function inicializarDataTable() {
    $('#tabelaLivrosAno').DataTable({
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

function editar(ano, id_escola) {
    var url = "gerirLivrosAno/getPorId/" + ano + "-" + id_escola;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (resposta) {
            if (resposta != null) {
                registo = resposta[0];
                console.log(registo);
                url = 'gerirLivrosAno/edit/' + ano + "-" + id_escola
                $('#formEditar').attr('action', url)
                $('#anoEdit').val(registo.ano)
                $('#numLivrosEdit').val(parseInt(registo.numLivros))
            }
        },
        error: function (error) {
            alert("Ocorreu um erro na obtenção do registo!\n Por favor tente novamente.")
        }
    })
}

function remover(ano, id_escola) {
    url = 'gerirLivrosAno/delete/' + ano + "-" + id_escola
    $('#formDelete').attr('action', url)
}