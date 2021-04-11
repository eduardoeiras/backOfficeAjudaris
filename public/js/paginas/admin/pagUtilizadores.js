var username = null
var erroUsername = false
var editUserName = null

$(document).ready(function () {
    if($('#username').val() != null) {
        username = $('#username').val()
    }
    
    $.ajax({
        url: 'utilizadores/getAll',
        method: "GET",
        dataType: "json",
        success: function (users) {
            if (users != null) {
                for (user of users) {
                    criarLinha(user)
                }
                inicializarDataTable();
            }
        },
        error: function (error) {

        }
    })

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
                    console.log(editUserName, userInput);
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

function criarLinha(user) {
    var linha = '<tr>'
    linha = linha + `<td>${user.nomeUtilizador}</td>`;
    linha = linha + `<td>${user.nome}</td>`;
    linha = linha + `<td>${user.password}</td>`;
    linha = linha + verificaNull(user.email);
    linha = linha + verificaNull(user.telemovel);
    linha = linha + verificaNull(user.telefone);
    linha = linha + `<td>${user.departamento}</td>`;
    if (user.tipoUtilizador == 0) {
        linha = linha + '<td>Administrador</td>';
        if(user.nomeUtilizador == username) {
            linha = linha + `<td>
            <a href="#editUtilizador" class="edit" data-toggle="modal" onclick="editarUtilizador(${user.id_utilizador}, true)"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>
            <a href="#deleteUtilizador" class="delete" data-toggle="modal" onclick="removerUtilizador(${user.id_utilizador})"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Delete">&#xE872;</i></a>
            </td>`;
        }
        else {
            linha = linha + `<td>
                <a href="#editUtilizador" class="edit" data-toggle="modal" onclick="editarUtilizador(${user.id_utilizador}, false)"><i
                        class="material-icons" data-toggle="tooltip"
                        title="Edit">&#xE254;</i></a>
                <a href="#deleteUtilizador" class="delete" data-toggle="modal" onclick="removerUtilizador(${user.id_utilizador})"><i
                        class="material-icons" data-toggle="tooltip"
                        title="Delete">&#xE872;</i></a>
                </td>`;    
        }
    }
    else {
        let url = 'gerirProjetosUser/' + user.id_utilizador;
        linha = linha + '<td>Colaborador</td>';
        linha = linha + `<td>
            <a href="#editUtilizador" class="edit" data-toggle="modal" onclick="editarUtilizador(${user.id_utilizador}, false)"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>
            <a href="#deleteUtilizador" class="delete" data-toggle="modal" onclick="removerUtilizador(${user.id_utilizador})"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Delete">&#xE872;</i></a>
            <a href="${url}"><img src="http://backofficeAjudaris/images/projetos.png"></img></a>
            </td>`;
    }
    $('#tableBody').append(linha);
}

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