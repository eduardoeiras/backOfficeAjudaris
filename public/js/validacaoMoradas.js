function verificaValidadeMoradas(adicionar) {
    let rua, localidade, distrito, codPostal, codPostalRua;

    if(adicionar) {
        rua = $('#ruaAdd').val();
        localidade = $('#localidadeAdd').val()
        distrito = $('#distritoAdd').val()
        codPostal = $('#codPostalAdd').val()
        codPostalRua = $('#codPostalRuaAdd').val()    
    }
    else {
        rua = $('#rua').val();
        localidade = $('#localidade').val()
        distrito = $('#distrito').val()
        codPostal = $('#codPostal').val()
        codPostalRua = $('#codPostalRua').val()
    }

    if(rua != '' && localidade != '' && distrito != '' && codPostal != ''
        && codPostalRua != '') {
        return true;
    }
    else if(rua == '' && localidade == '' && distrito == '' && codPostal == ''
        && codPostalRua == '') {
        return true;
    }
    else if(rua == '' && localidade == '' && distrito == '' && codPostal == ' '
        && codPostalRua == ' ') {
        return true;
    }
    else {
        if(adicionar) {
            $('#erroMoradaAdd').text("A morada requer o preenchimento dos campos: Rua, Localidade, Distrito e Código Postal")
        }
        else {
           $('#erroMorada').text("A morada requer o preenchimento dos campos: Rua, Localidade, Distrito e Código Postal") 
        }
        return false;

    } 
}