var emailsAdicionadosAdd = [];
var emailsAdicionadosEdit = [];

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
        success: function (resposta) {
            if (resposta != null) {
                contador = resposta[0]
                $('#emailsAssociadosEdit').empty()
                url = 'contadores/edit/' + contador.id_contadorHistorias
                $('#formEditar').attr('action', url)
                $('#nome').val(contador.nome)
                $('#disponibilidade').val(contador.disponivel.toString())
                $('#observacoes').val(contador.observacoes)
                $('#telefone').val(contador.telefone)
                $('#telemovel').val(contador.telemovel)
                contador.emails.original.forEach(linha => {
                    emailsAdicionadosEdit.push(linha.email)
                    let index = emailsAdicionadosEdit.indexOf(linha.email)
                    linha = `<div id="emailEdit_${index}"><input id="email_${index}" type="checkbox" name="emails[]" style="display: none;" value="${linha.email}" checked>
                    <label style="font-size: 14px" onclick="removerEmail(false, true, ${index})">${linha.email}</label></div>`
                    $('#emailsAssociadosEdit').append(linha)
                });
                $('#numPorta').val(contador.numPorta)
                $('#rua').val(contador.rua)
                $('#localidade').val(contador.localidade)
                $('#distrito').val(contador.distrito)
                $('#codPostal').val(contador.codPostal)
                $('#codPostalRua').val(contador.codPostalRua)
            }
        },
        error: function (error) {

        }
    })
}


function adicionarEmail(adicionar) {
    if(adicionar) {
        if($('#emailFormAdd').val() != "") {
            var email = $('#emailFormAdd').val()
            var existe = false;
            for(item of emailsAdicionadosAdd) {
                if(item === email) {
                    existe = true
                }
            }
            if(!existe) {
                emailsAdicionadosAdd.push(email) 
                let index = emailsAdicionadosAdd.indexOf(email)
                let linha = `<div id="emailAdd_${index}"><input type="checkbox" name="emails[]" style="display: none;" value="${email}" checked>
                <label style="font-size: 14px" onclick="removerEmail(true, false, ${index})">${email}</label></div>`
                $('#emailsAssociadosAdd').append(linha)
                $('#emailFormAdd').val("")
            }
            
        }
    }
    else {
        if($('#emailFormEdit').val() != "") {
            var email = $('#emailFormEdit').val()
            var existe = false;
            for(item of emailsAdicionadosEdit) {
                if(item === email) {
                    existe = true
                }
            }
            if(!existe) {
                emailsAdicionadosEdit.push(email) 
                let index = emailsAdicionadosEdit.indexOf(email)
                let linha = `<div id="emailEdit_${index}"><input type="checkbox" name="emails[]" style="display: none;" value="${email}" checked>
                <label style="font-size: 14px" onclick="removerEmail(false, false, ${index})">${email}</label></div>`
                $('#emailsAssociadosEdit').append(linha)
                $('#emailFormEdit').val("")
            }   
        }
    }
}

function removerEmail(adicionar, jaExistente, index) {
    if(adicionar) {
        if(index != -1) {
            emailsAdicionadosAdd.splice(index, 1)
            $(`#emailAdd_${index}`).remove();
        }
    }
    else {
        if(index != -1) {
            if(jaExistente) {
                emailsAdicionadosEdit.splice(index, 1)
                $(`#email_${index}`).attr('name', 'deletedEmails[]');
                $(`#emailEdit_${index}`).hide()    
            }
            else {
                emailsAdicionadosEdit.splice(index, 1)
                $(`#emailEdit_${index}`).remove()    
            }
        }
    }
}

function remover(id) {
    url = 'contadores/delete/' + id
    $('#formDelete').attr('action', url)
}