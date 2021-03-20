var emailsAdicionadosAdd = [];
var emailsAdicionadosEdit = [];

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