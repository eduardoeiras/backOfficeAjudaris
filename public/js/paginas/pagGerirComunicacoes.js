$(document).ready(function () {
    inicializarDataTable();
});

function inicializarDataTable() {
    $('#tabelaComunicacoes').DataTable({
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
    var url = "gerirComunicacoes/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (resposta) {
            if (resposta != null) {
                ilustrador = resposta[0]
                $('#emailsAssociadosEdit').empty()
                url = 'gerirComunicacoes/edit/' + ilustrador.id_ilustradorSolidario
                $('#formEditar').attr('action', url)
                $('#nome').val(ilustrador.nome)
                $('#telefone').val(ilustrador.telefone)
                $('#telemovel').val(ilustrador.telemovel)
                $('#disponibilidade').val(ilustrador.disponivel.toString())
                $('#volumeLivro').val(ilustrador.volumeLivro)
                $('#observacoes').val(ilustrador.observacoes)
                ilustrador.emails.original.forEach(linha => {
                    emailsAdicionadosEdit.push(linha.email)
                    let index = emailsAdicionadosEdit.indexOf(linha.email)
                    linha = `<div id="emailEdit_${index}"><input id="email_${index}" type="checkbox" name="emails[]" style="display: none;" value="${linha.email}" checked>
                    <label style="font-size: 14px" onclick="removerEmail(false, true, ${index})">${linha.email}</label></div>`
                    $('#emailsAssociadosEdit').append(linha)
                });
                $('#numPorta').val(ilustrador.numPorta)
                $('#rua').val(ilustrador.rua)
                $('#localidade').val(ilustrador.localidade)
                $('#distrito').val(ilustrador.distrito)
                $('#codPostal').val(ilustrador.codPostal)
                $('#codPostalRua').val(ilustrador.codPostalRua)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'gerirComunicacoes/delete/' + id
    $('#formDelete').attr('action', url)
}