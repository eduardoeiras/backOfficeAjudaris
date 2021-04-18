var carregamento = false;
var concelhosAdicionadosAdd = [];
var concelhosAdicionadosEdit = [];
var existeConcelhoBd = false;

$(document).ready(function () {
    inicializarDataTable();
});

function inicializarDataTable() {
    $('#tabelaDados').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url":"rbes/getAll", 
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
                console.log(rbe);
                $('#emailsAssociadosEdit').empty()
                $('#concelhosAssociadosEdit').empty()
                url = 'rbes/edit/' + rbe.id_rbe
                $('#formEditar').attr('action', url)
                $('#regiao').val(rbe.regiao)
                $('#nome').val(rbe.nome)
                rbe.concelhos.forEach(linha => {
                    concelhosAdicionadosEdit.push(linha.nome)
                    let index = concelhosAdicionadosEdit.indexOf(linha.nome)
                    linha= `<div id="concelhoEdit_${index}"><input id="concelho_${index}" type="checkbox" name="concelhos[]" style="display: none;" value="${linha.nome}" checked>
                    <label style="font-size: 14px" onclick="removerConcelho(false, true, ${index})">${linha.nome}</label></div>`
                    $('#concelhosAssociadosEdit').append(linha)
                });
                var disp = rbe.disponivel
                $('#disponibilidade').val(disp.toString())
                rbe.emails.original.forEach(linha => {
                    emailsAdicionadosEdit.push(linha.email)
                    let index = emailsAdicionadosEdit.indexOf(linha.email)
                    linha = `<div id="emailEdit_${index}"><input id="email_${index}" type="checkbox" name="emails[]" style="display: none;" value="${linha.email}" checked>
                    <label style="font-size: 14px" onclick="removerEmail(false, true, ${index})">${linha.email}</label></div>`
                    $('#emailsAssociadosEdit').append(linha)
                });
                $('#observacoes').val(rbe.observacoes)
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

function adicionarConcelho(adicionar) {
    if(adicionar) {
        if($('#concelhoFormAdd').val() != "") {
            var concelho = $('#concelhoFormAdd').val()
            var existe = false;
            for(item of concelhosAdicionadosAdd) {
                if(item === concelho) {
                    existe = true
                }
            }
            if(!existe) {
                concelhosAdicionadosAdd.push(concelho) 
                let index = concelhosAdicionadosAdd.indexOf(concelho)
                let linha = `<div id="concelhoAdd_${index}"><input type="checkbox" name="concelhos[]" style="display: none;" value="${concelho}" checked>
                <label style="font-size: 14px" onclick="removerConcelho(true, false, ${index})">${concelho}</label></div>`
                $('#concelhosAssociadosAdd').append(linha)
                $('#concelhoFormAdd').val("")
            }
            
        }
    }
    else {
        if($('#concelhoFormEdit').val() != "") {
            var concelho = $('#concelhoFormEdit').val()
            var existe = false;
            for(item of concelhosAdicionadosEdit) {
                if(item === concelho) {
                    existe = true
                }
            }
            if(!existe) {
                concelhosAdicionadosEdit.push(concelho) 
                let index = concelhosAdicionadosEdit.indexOf(concelho)
                let linha = `<div id="concelhoEdit_${index}"><input type="checkbox" name="concelhos[]" style="display: none;" value="${concelho}" checked>
                <label style="font-size: 14px" onclick="removerConcelho(false, false, ${index})">${concelho}</label></div>`
                $('#concelhosAssociadosEdit').append(linha)
                $('#concelhoFormEdit').val("")
            }   
        }
    }
}

function removerConcelho(adicionar, jaExistente, index) {
    if(adicionar) {
        if(index != -1) {
            concelhosAdicionadosAdd.splice(index, 1)
            $(`#concelhoAdd_${index}`).remove();
        }
    }
    else {
        if(index != -1) {
            if(jaExistente) {
                concelhosAdicionadosEdit.splice(index, 1)
                $(`#concelho_${index}`).attr('name', 'deletedConcelhos[]');
                $(`#concelhoEdit_${index}`).hide()    
            }
            else {
                concelhosAdicionadosEdit.splice(index, 1)
                $(`#concelhoEdit_${index}`).remove()    
            }
        }
    }
}