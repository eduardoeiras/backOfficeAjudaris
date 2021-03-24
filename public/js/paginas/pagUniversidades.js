$(document).ready(function () {
    inicializarDataTable()
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
    var url = "universidades/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (resposta) {
            if (resposta != null) {
                uni = resposta[0]
                $('#emailsAssociadosEdit').empty()
                url = 'universidades/edit/' + uni.id_universidade
                $('#formEditar').attr('action', url)
                $('#curso').val(uni.curso)
                $('#tipo').val(uni.tipo)
                $('#nome').val(uni.nome)
                $('#telefone').val(uni.telefone)
                $('#telemovel').val(uni.telemovel)
                uni.emails.original.forEach(linha => {
                    emailsAdicionadosEdit.push(linha.email)
                    let index = emailsAdicionadosEdit.indexOf(linha.email)
                    linha = `<div id="emailEdit_${index}"><input id="email_${index}" type="checkbox" name="emails[]" style="display: none;" value="${linha.email}" checked>
                    <label style="font-size: 14px" onclick="removerEmail(false, true, ${index})">${linha.email}</label></div>`
                    $('#emailsAssociadosEdit').append(linha)
                });
                var disp = uni.disponivel
                $('#disponibilidade').val(disp.toString())
                $('#rua').val(uni.rua)
                $('#numPorta').val(uni.numPorta)
                $('#localidade').val(uni.localidade)
                $('#distrito').val(uni.distrito)
                $('#codPostal').val(uni.codPostal)
                $('#codPostalRua').val(uni.codPostalRua)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'universidades/delete/' + id
    $('#formDelete').attr('action', url)
}