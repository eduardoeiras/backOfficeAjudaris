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
    var url = "professores/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (resposta) {
            if (resposta != null) {
                professor = resposta[0]
                $('#emailsAssociadosEdit').empty()
                url = 'professores/edit/' + professor.id_professor
                $('#formEditar').attr('action', url)
                $('#nome').val(professor.nome)
                $('#telefone').val(professor.telefone)
                $('#telemovel').val(professor.telemovel)
                professor.emails.original.forEach(linha => {
                    emailsAdicionadosEdit.push(linha.email)
                    let index = emailsAdicionadosEdit.indexOf(linha.email)
                    linha = `<div id="emailEdit_${index}"><input id="email_${index}" type="checkbox" name="emails[]" style="display: none;" value="${linha.email}" checked>
                    <label style="font-size: 14px" onclick="removerEmail(false, true, ${index})">${linha.email}</label></div>`
                    $('#emailsAssociadosEdit').append(linha)
                });
                $('#observacoes').val(professor.observacoes)
                $('#rua').val(professor.rua)
                $('#numPorta').val(professor.numPorta)
                $('#localidade').val(professor.localidade)
                $('#distrito').val(professor.distrito)
                $('#codPostal').val(professor.codPostal)
                $('#codPostalRua').val(professor.codPostalRua)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'professores/delete/' + id
    $('#formDelete').attr('action', url)
}