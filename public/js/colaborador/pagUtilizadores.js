$(document).ready(function () {

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


});

function criarLinha(user) {
    var linha = '<tr>'
    linha = linha + `<td>${user.nomeUtilizador}</td>`;
    linha = linha + `<td>${user.nome}</td>`;
    linha = linha + `<td>${user.password}</td>`;
    linha = linha + verificaNull(user.email);
    linha = linha + verificaNull(user.telemovel);
    linha = linha + verificaNull(user.telefone);
    linha = linha + `<td>${user.departamento}</td>`;
    if(user.tipoUtilizador == 0) {
        linha = linha + '<td>Administrador</td>';
        linha = linha + `<td>
            <a href="#editUtilizador" class="edit" data-toggle="modal" onclick="editarUtilizador(${user.id_utilizador})"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>
            </td>`;
    }
    else {
        let url = 'gerirProjetosUser/' + user.id_utilizador;
        linha = linha + '<td>Colaborador</td>';
        linha = linha + `<td>
            <a href="#editUtilizador" class="edit" data-toggle="modal" onclick="editarUtilizador(${user.id_utilizador})"><i
                    class="material-icons" data-toggle="tooltip"
                    title="Edit">&#xE254;</i></a>
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

function editarUtilizador(id) {
    var url = "utilizadores/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (user) {
            if (user != null) {
                url = 'utilizadores/editUtilizador/' + user.id_utilizador
                $('#formEditarUtilizador').attr('action', url)
                $('#nomeUtilizador').val(user.nomeUtilizador)
                $('#nome').val(user.nome)
                $('#password').val(user.password)
                $('#departamento').val(user.departamento)
                var tipoUser = user.tipoUtilizador
                $('#tipoUtilizador').val(tipoUser.toString())
                $('#telefone').val(user.telefone)
                $('#telemovel').val(user.telemovel)
                $('#email').val(user.email)
            }
        },
        error: function (error) {

        }
    })
}