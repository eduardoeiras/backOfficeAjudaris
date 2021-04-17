$(document).ready(function () {
    inicializarDataTable();
});

function inicializarDataTable() {
    $('#tabelaDados').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url":"agrupamentos/getAll", 
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
    var url = "agrupamentos/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (resposta) {
            if (resposta != null) {
                agrupamento = resposta[0]
                $('#emailsAssociadosEdit').empty()
                url = 'agrupamentos/edit/' + agrupamento.id_agrupamento
                $('#formEditar').attr('action', url)
                $('#nome').val(agrupamento.nome)
                $('#nif').val(agrupamento.nif)
                agrupamento.emails.original.forEach(linha => {
                    emailsAdicionadosEdit.push(linha.email)
                    let index = emailsAdicionadosEdit.indexOf(linha.email)
                    linha = `<div id="emailEdit_${index}"><input id="email_${index}" type="checkbox" name="emails[]" style="display: none;" value="${linha.email}" checked>
                    <label style="font-size: 14px" onclick="removerEmail(false, true, ${index})">${linha.email}</label></div>`
                    $('#emailsAssociadosEdit').append(linha)
                });
                $('#observacoes').val(agrupamento.observacoes)
                $('#telefone').val(agrupamento.telefone)
                $('#telemovel').val(agrupamento.telemovel)
                $('#nomeDiretor').val(agrupamento.nomeDiretor)
                $('#rua').val(agrupamento.rua)
                $('#numPorta').val(agrupamento.numPorta)
                $('#localidade').val(agrupamento.localidade)
                $('#distrito').val(agrupamento.distrito)
                $('#codPostal').val(agrupamento.codPostal)
                $('#codPostalRua').val(agrupamento.codPostalRua)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'agrupamentos/delete/' + id
    $('#formDelete').attr('action', url)
}