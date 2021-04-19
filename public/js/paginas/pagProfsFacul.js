$(document).ready(function () {
    inicializarDataTable();
});

function inicializarDataTable() {
    $('#tabelaDados').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url":"profsFaculdade/getAll", 
          "type": "GET"
        },
        "columns": [
            {data: 'nome', name: 'colaborador.nome'},
            {data: 'cargo', name: 'professor_faculdade.cargo'},
            {data: 'telemovel', name: 'colaborador.telemovel'},
            {data: 'telefone', name: 'colaborador.telefone'},
            {data: 'emails', name: '', orderable: false, searchable: false},
            {data: 'disponibilidade', name: 'colaborador.disponivel'},
            {data: 'localidade', name: 'cod_postal.localidade'},
            {data: 'rua', name: 'cod_postal_rua.rua'},
            {data: 'cod_postal', name: 'cod_postal.codPostal', orderable: false, searchable: false},
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

function editar(id) {
    var url = "profsFaculdade/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (resposta) {
            if (resposta != null) {
                profFacul = resposta[0]
                $('#emailsAssociadosEdit').empty()
                url = 'profsFaculdade/edit/' + profFacul.id_professorFaculdade
                console.log(url);
                $('#formEditar').attr('action', url)
                $('#nome').val(profFacul.nome)
                $('#cargo').val(profFacul.cargo)
                $('#telemovel').val(profFacul.telemovel)
                $('#telefone').val(profFacul.telefone)
                profFacul.emails.original.forEach(linha => {
                    emailsAdicionadosEdit.push(linha.email)
                    let index = emailsAdicionadosEdit.indexOf(linha.email)
                    linha = `<div id="emailEdit_${index}"><input id="email_${index}" type="checkbox" name="emails[]" style="display: none;" value="${linha.email}" checked>
                    <label style="font-size: 14px" onclick="removerEmail(false, true, ${index})">${linha.email}</label></div>`
                    $('#emailsAssociadosEdit').append(linha)
                });
                $('#disponibilidade').val(profFacul.disponivel.toString())
                $('#observacoes').val(profFacul.observacoes)
                $('#rua').val(profFacul.rua)
                $('#numPorta').val(profFacul.numPorta)
                $('#localidade').val(profFacul.localidade)
                $('#distrito').val(profFacul.distrito)
                $('#codPostal').val(profFacul.codPostal)
                $('#codPostalRua').val(profFacul.codPostalRua)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'profsFaculdade/delete/' + id
    $('#formDelete').attr('action', url)
}