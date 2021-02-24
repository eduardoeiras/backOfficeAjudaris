var carregamento = false;

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
    var url = "rbes/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (rbe) {
            if (rbe != null) {
                url = 'rbes/edit/' + rbe.id_rbe
                $('#formEditar').attr('action', url)
                $('#regiao').val(rbe.regiao)
                $('#nome').val(rbe.nomeCoordenador)
                carregarConcelhos(false)
                var concelho = rbe.id_concelho
                $('#concelho').val(concelho)
                var disp = rbe.disponivel
                $('#disponibilidade').val(disp.toString())
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'rbes/delete/' + id
    $('#formDelete').attr('action', url)
}

function carregarConcelhos(adicionar) {
    if(carregamento == false) {
        $.ajax({
            url: 'concelhos/getAll',
            method: "GET",
            dataType: "json",
            success: function (concelhos) {
                var opcoes = ''
                if (concelhos != null) {
                    carregamento = true;
                    for(concelho of concelhos) {
                        opcoes = opcoes + `<option value="${concelho.id_concelho}">${concelho.nome}</option>`
                    }
                    if(adicionar) {
                        $('#concelhosAdd').append(opcoes)   
                    }
                    else {
                        $('#concelho').append(opcoes)   
                    }
                    
                }
            },
            error: function (error) {

            }
        })    
    }
}

function getNomeConcelho(id) {
    var url = `concelhos/getPorId/` + id
    var nome = ''
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (concelho) {
            if (concelho != null) {
                nome = concelho.nome
            }
        },
        error: function (error) {
        }
    })

    if(nome != '') {
        return nome;
    }
    else {
        return null;
    }
}