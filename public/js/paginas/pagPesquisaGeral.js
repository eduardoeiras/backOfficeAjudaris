$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});

$(document).ready(function () {
    inicializarDataTable('#tabelaDados')
});

function inicializarDataTable(idTabela) {
    $(idTabela).DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url":"pesqGlobal/getColaboradores", 
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

function editar(id) {
    var url = "colaboradores/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (resposta) {
            if (resposta != null) {
                $('#emailsAssociadosEdit').empty()
                url = 'colaboradores/edit/' + resposta.id_colaborador
                $('#formEditar').attr('action', url)
                $('#nome').val(resposta.nome)
                $('#telefone').val(resposta.telefone)
                $('#telemovel').val(resposta.telemovel)
                $('#disponibilidade').val(resposta.disponivel.toString())
                $('#volumeLivro').val(resposta.volumeLivro)
                $('#observacoes').val(resposta.observacoes)
                resposta.emails.original.forEach(linha => {
                    emailsAdicionadosEdit.push(linha.email)
                    let index = emailsAdicionadosEdit.indexOf(linha.email)
                    linha = `<div id="emailEdit_${index}"><input id="email_${index}" type="checkbox" name="emails[]" style="display: none;" value="${linha.email}" checked>
                    <label style="font-size: 14px" onclick="removerEmail(false, true, ${index})">${linha.email}</label></div>`
                    $('#emailsAssociadosEdit').append(linha)
                });
                $('#numPorta').val(resposta.numPorta)
                $('#rua').val(resposta.rua)
                $('#localidade').val(resposta.localidade)
                $('#distrito').val(resposta.distrito)
                $('#codPostal').val(resposta.codPostal)
                $('#codPostalRua').val(resposta.codPostalRua)
            }
        },
        error: function (error) {

        }
    })
}
