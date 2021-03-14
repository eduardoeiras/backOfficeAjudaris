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
    var url = "agrupamentos/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (resposta) {
            if (resposta != null) {
                agrupamento = resposta[0]
                $('#emailsFormEdit').empty()
                url = 'agrupamentos/edit/' + agrupamento.id_agrupamento
                $('#formEditar').attr('action', url)
                $('#nome').val(agrupamento.nome)
                resposta[0].emails.original.forEach(email => {
                    linha = `<option value="${email.id_email}">${email.email}</option>`
                    $('#emailsFormEdit').append(linha)
                });
                $('#telefone').val(agrupamento.telefone)
                $('#nomeDiretor').val(agrupamento.nomeDiretor)
                $('#rua').val(agrupamento.rua)
                $('#numPorta').val(agrupamento.numPorta)
                $('#localidade').val(agrupamento.localidade)
                $('#codPostal').val(agrupamento.codPostal)
                $('#codPostalRua').val(agrupamento.codPostalRua)
            }
        },
        error: function (error) {

        }
    })
}

function adicionarEmail(adicionar) {
    if(adicionar) {
        if($('#emailFormAdd').val() != "") {
            var email = $('#emailFormAdd').val()
            if(!$(`#emailsFormAdd option[value='${email}']`).length > 0) {
                linha = `<option value="${email}">${email}</option>`
                $('#emailsFormAdd').append(linha) 
            }
        }
    }
    else {
        if($('#emailFormEdit').val() != "") {
            var email = $('#emailFormEdit').val()
            if(!$(`#emailsFormEdit option[value='${email}']`).length > 0) {
                linha = `<option value="${email}">${email}</option>`
                $('#emailsFormEdit').append(linha) 
            }
        }
    }
}

function removerEmail(adicionar) {
    if(adicionar) {
        if($('#emailsFormAdd').val() != "") {
            $('#emailsFormAdd').find('option:selected').remove();
        }
    }
    else {
        if($('#emailsFormEdit').val() != "") {
            $('#emailsFormEdit').find('option:selected').remove();
        }
    }
}

function remover(id) {
    url = 'agrupamentos/delete/' + id
    $('#formDelete').attr('action', url)
}