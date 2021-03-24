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
        success: function (resposta) {
            if (resposta != null) {
                rbe = resposta[0]
                $('#emailsAssociadosEdit').empty()
                url = 'rbes/edit/' + rbe.id_rbe
                $('#formEditar').attr('action', url)
                $('#regiao').val(rbe.regiao)
                $('#nome').val(rbe.nome)
                carregarConcelhos(false)
                var concelho = rbe.id_concelho
                $('#concelho').val(concelho)
                var disp = rbe.disponivel
                $('#disponibilidade').val(disp.toString())
                rbe.emails.original.forEach(linha => {
                    emailsAdicionadosEdit.push(linha.email)
                    let index = emailsAdicionadosEdit.indexOf(linha.email)
                    linha = `<div id="emailEdit_${index}"><input id="email_${index}" type="checkbox" name="emails[]" style="display: none;" value="${linha.email}" checked>
                    <label style="font-size: 14px" onclick="removerEmail(false, true, ${index})">${linha.email}</label></div>`
                    $('#emailsAssociadosEdit').append(linha)
                });
                $('#telefone').val(rbe.telefone)
                $('#telemovel').val(rbe.telemovel)
                $('#nomeDiretor').val(rbe.nomeDiretor)
                $('#rua').val(rbe.rua)
                $('#numPorta').val(rbe.numPorta)
                $('#localidade').val(rbe.localidade)
                $('#distrito').val(rbe.distrito)
                $('#codPostal').val(rbe.codPostal)
                $('#codPostalRua').val(rbe.codPostalRua)
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