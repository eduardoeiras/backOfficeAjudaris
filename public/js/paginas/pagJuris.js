$(document).ready(function () {
    inicializarDataTable();
});

function inicializarDataTable() {
    $('#tabelaDados').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url":"juris/getAll", 
          "type": "GET"
        },
        "columns": [
            {data: 'nome', name: 'colaborador.nome'},
            {data: 'telemovel', name: 'colaborador.telemovel'},
            {data: 'telefone', name: 'colaborador.telefone'},
            {data: 'emails', name: '', orderable: false, searchable: false},
            {data: 'disponibilidade', name: 'colaborador.disponivel'},
            {data: 'tipoJuri', name: 'juri.tipoJuri'},
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
    var url = "juris/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (resposta) {
            if (resposta != null) {
                juri = resposta[0]
                $('#emailsAssociadosEdit').empty()
                url = 'juris/edit/' + juri.id_juri
                $('#formEditar').attr('action', url)
                $('#nome').val(juri.nome)
                $('#telemovel').val(juri.telemovel)
                $('#telefone').val(juri.telefone)
                var disp = juri.disponivel
                $('#disponibilidade').val(disp.toString())
                var tipo = juri.tipoJuri
                if(tipo != null) {
                   $('#tipo').val(tipo.toString()) 
                }
                juri.emails.original.forEach(linha => {
                    emailsAdicionadosEdit.push(linha.email)
                    let index = emailsAdicionadosEdit.indexOf(linha.email)
                    linha = `<div id="emailEdit_${index}"><input id="email_${index}" type="checkbox" name="emails[]" style="display: none;" value="${linha.email}" checked>
                    <label style="font-size: 14px" onclick="removerEmail(false, true, ${index})">${linha.email}</label></div>`
                    $('#emailsAssociadosEdit').append(linha)
                });
                $('#observacoes').val(juri.observacoes)
                $('#localidade').val(juri.localidade)
                $('#rua').val(juri.rua)
                $('#numPorta').val(juri.numPorta)
                $('#codPostal').val(juri.codPostal)
                $('#codPostalRua').val(juri.codPostalRua)
                $('#distrito').val(juri.distrito)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'juris/delete/' + id
    $('#formDelete').attr('action', url)
}