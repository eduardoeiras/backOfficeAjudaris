var erroUsername = false
var editUserName = null

$(document).ready(function () {
    inicializarDataTable()

    $('#nomeUtilizadorAdd').on('keyup', function(e) {
        var userInput = e.target.value;
        let url = 'utilizadores/existeUserNome/' + userInput
        if(userInput != "") {
            $.ajax({
                url: url,
                method: "GET",
                dataType: "json",
                success: function (existe) {
                    if(existe == 1) {
                        erroUsername = true
                        $('#erroUserExiste').text("Já existe um utilizador com o nome de utilizador introduzido!")
                    }
                    else {
                        erroUsername = false
                        $('#erroUserExiste').text(" ")
                    }
                },
                error: function (error) {
                    
                }
            })    
        }
    });

    $('#nomeUtilizador').on('keyup', function(e) {
        var userInput = e.target.value;
        let url = 'utilizadores/existeUserNome/' + userInput
        if(userInput != "") {
            $.ajax({
                url: url,
                method: "GET",
                dataType: "json",
                success: function (existe) {
                    if(existe == 1 && userInput != editUserName) {
                        erroUsername = true
                        $('#erroUserExisteEdit').text("Já existe um utilizador com o nome de utilizador introduzido!")
                    }
                    else {
                        erroUsername = false
                        $('#erroUserExisteEdit').text(" ")
                    }
                },
                error: function (error) {
                    
                }
            })    
        }
    });
});

function verificarErroUsername() {
    if(erroUsername) {
        return false;
    }
    else {
        return true;
    }  
}

function mensagem(msg) {
    $('#mensagem').val(msg)
    $('#msg').modal('show'); 
}

function inicializarDataTable() {
    $('#tabelaDados').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url":"utilizadores/getAll", 
          "type": "GET"
        },
        "columns": [
            {data: 'nomeUtilizador', name: 'utilizador.nomeUtilizador'},
            {data: 'nome', name: 'utilizador.nome'},
            {data: 'password', name: 'utilizador.password'},
            {data: 'telemovel', name: 'utilizador.telemovel'},
            {data: 'telefone', name: 'utilizador.telefone'},
            {data: 'email', name: 'utilizador.email'},
            {data: 'departamento', name: 'utilizador.departamento'},
            {data: 'tipoUtilizador', name: 'utilizador.tipoUtilizador'},
            {data: 'opcoes', name: '', orderable: false, searchable: false},
        ],
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

function verificaNull(valor) {
    if (valor == null) {
        return `<td> ---- </td>`;
    }
    else {
        return `<td>${valor}</td>`;
    }
}

function editarUtilizador(id, loggedInAdmin) {
    var url = "utilizadores/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (user) {
            if (user != null) {
                editUserName = user.nomeUtilizador
                url = 'utilizadores/editUtilizador/' + user.id_utilizador
                $('#formEditarUtilizador').attr('action', url)
                $('#nomeUtilizador').val(user.nomeUtilizador)
                $('#nome').val(user.nome)
                $('#password').val(user.password)
                $('#departamento').val(user.departamento)
                if(loggedInAdmin) {
                    let tipoUser = user.tipoUtilizador
                    $('#tipoUtilizador').val(tipoUser.toString()) 
                    $('#tipoUtilizador').hide() 
                    $('#editTipoUserLabel').hide() 
                }
                else {
                    let tipoUser = user.tipoUtilizador
                    $('#tipoUtilizador').val(tipoUser.toString()) 
                    $('#tipoUtilizador').show() 
                    $('#editTipoUserLabel').show() 
                }
                $('#telefone').val(user.telefone)
                $('#telemovel').val(user.telemovel)
                $('#email').val(user.email)
            }
        },
        error: function (error) {

        }
    })
}

function removerUtilizador(id) {
    url = 'utilizadores/deleteUtilizador/' + id
    $('#formDeleteUtilizador').attr('action', url)
}