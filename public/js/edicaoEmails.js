var emailsAdicionadosAdd = [];
var emailsAdicionadosEdit = [];
var existeEmailBd = false;
var email;

function adicionarEmail(adicionar) {
    if(adicionar) {
        email = $('#emailFormAdd').val()
    }
    else {
        email = $('#emailFormEdit').val()
    }
    let url = 'existeEmail/' + email
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (existe) {
            if (existe == 1) {
                existeEmailBd = true
                if(adicionar) {
                    $('#erroEmailAdd').text("O email que pretende adicionar já se encontra associado a um colaborardor! \n \
                    Verifique se introduziu corretamente o endereço de email.")
                }
                else {
                    $('#erroEmailEdit').text("O email que pretende adicionar já se encontra associado a um colaborardor! \n \
                    Verifique se introduziu corretamente o endereço de email.")
                }
            }
            else {
                existeEmailBd = false
                if(adicionar) {
                    $('#erroEmailAdd').text("")
                }
                else {
                    $('#erroEmailEdit').text("")
                }
            }
            if(adicionar) {
                if($('#emailFormAdd').val() != "") {
                    var existe = false;
                    for(item of emailsAdicionadosAdd) {
                        if(item === email) {
                            existe = true
                        }
                    }
                    if(!existeEmailBd) {
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
            }
            else {
                if($('#emailFormEdit').val() != "") {
                    var existe = false;
                    for(item of emailsAdicionadosEdit) {
                        if(item === email) {
                            existe = true
                        }
                    }
                    if(!existeEmailBd)  {
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
        },
        error: function (error) {
            alert("Erro na verificação da existência do email na base de dados, por favor tente novamente!");
            existeEmailBd = true
        }
    })
    
}

function removerEmail(adicionar, jaExistente, index) {
    let url = 'getTipoUser'
    if(adicionar) {
        if(index != -1) {
            emailsAdicionadosAdd.splice(index, 1)
            $(`#emailAdd_${index}`).remove();
        }
    }
    else {
        $.ajax({
            url: url,
            method: "GET",
            dataType: "json",
            success: function(resposta){
                if(resposta == 0){
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
        })
    }
}