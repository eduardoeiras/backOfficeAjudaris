$(document).ready(function () {
    inicializarDataTable();
});

function inicializarDataTable() {
    $('#tabelaDados').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url":"contadores/getAll", 
          "type": "GET"
        },
        "columns": [
            {data: 'nome', name: 'colaborador.nome'},
            {data: 'telemovel', name: 'colaborador.telemovel'},
            {data: 'telefone', name: 'colaborador.telefone'},
            {data: 'emails', name: '', orderable: false, searchable: false},
            {data: 'disponibilidade', name: 'colaborador.disponivel'},
            {data: 'localidade', name: 'cod_postal.localidade'},
            {data: 'rua', name: 'cod_postal_rua.rua'},
            {data: 'cod_postal', name: '', orderable: false, searchable: false},
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
    var url = "contadores/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (resposta) {
            if (resposta != null) {
                contador = resposta[0]
                $('#emailsAssociadosEdit').empty()
                url = 'contadores/edit/' + contador.id_contadorHistorias
                $('#formEditar').attr('action', url)
                $('#nome').val(contador.nome)
                $('#disponibilidade').val(contador.disponivel.toString())
                $('#observacoes').val(contador.observacoes)
                $('#telefone').val(contador.telefone)
                $('#telemovel').val(contador.telemovel)
                contador.emails.original.forEach(linha => {
                    emailsAdicionadosEdit.push(linha.email)
                    let index = emailsAdicionadosEdit.indexOf(linha.email)
                    linha = `<div id="emailEdit_${index}"><input id="email_${index}" type="checkbox" name="emails[]" style="display: none;" value="${linha.email}" checked>
                    <label style="font-size: 14px" onclick="removerEmail(false, true, ${index})">${linha.email}</label></div>`
                    $('#emailsAssociadosEdit').append(linha)
                });
                $('#numPorta').val(contador.numPorta)
                $('#rua').val(contador.rua)
                $('#localidade').val(contador.localidade)
                $('#distrito').val(contador.distrito)
                $('#codPostal').val(contador.codPostal)
                $('#codPostalRua').val(contador.codPostalRua)
            }
        },
        error: function (error) {

        }
    })
}

function remover(id) {
    url = 'contadores/delete/' + id
    $('#formDelete').attr('action', url)
}