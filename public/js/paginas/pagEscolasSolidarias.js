var idSelecionado = 0;

$(document).ready(function () {
    $('#tabelaDados').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url":"escolas/getAll", 
          "type": "GET"
        },
        "columns": [
            {data: 'nome', name: 'colaborador.nome'},
            {data: 'telemovel', name: 'colaborador.telemovel'},
            {data: 'telefone', name: 'colaborador.telefone'},
            {data: 'contactoAssPais', name: 'escola_solidaria.contactoAssPais'},
            {data: 'emails', name: '', orderable: false, searchable: false},
            {data: 'disponibilidade', name: 'colaborador.disponivel'},
            {data: 'agrupamento', name: '', orderable: false, searchable: false},
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
});

function inicializarDataTable(idTabela) {
    $(idTabela).DataTable({
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

function destruirTabela(idTabela) {
    if ($.fn.DataTable.isDataTable(idTabela)) {
        $(idTabela).DataTable().clear().destroy();
    }
}

$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});

function editar(id) {
    var url = "escolas/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (resposta) {
            if (resposta != null) {
                var escola = resposta[0]
                if(escola.id_agrupamento != null) {
                    getNomeAgrupamento = 'agrupamentos/getPorId/' + escola.id_agrupamento;
                    $.ajax({
                        url: getNomeAgrupamento,
                        method: "GET",
                        dataType: "json",
                        success: function (agrupamento) {
                            apresentarFormEditar(escola, agrupamento)
                        },
                        error: function (error) {
                        }
                    })
                }
                else {
                    apresentarFormEditar(escola, null);
                }
            }
        },
        error: function (error) {
        }
    })
}

function apresentarFormEditar(escola, agrupamento) {
    url = 'escolas/edit/' + escola.id_escolaSolidaria
    $('#formEditar').attr('action', url)
    $('#nome').val(escola.nome)
    $('#disponibilidade').val(escola.disponivel.toString())
    $('#observacoes').val(escola.observacoes)
    $('#telefone').val(escola.telefone)
    $('#telemovel').val(escola.telemovel)
    $('#contactoAssPais').val(escola.contactoAssPais)
    escola.emails.original.forEach(linha => {
        emailsAdicionadosEdit.push(linha.email)
        let index = emailsAdicionadosEdit.indexOf(linha.email)
        linha = `<div id="emailEdit_${index}"><input id="email_${index}" type="checkbox" name="emails[]" style="display: none;" value="${linha.email}" checked>
        <label style="font-size: 14px" onclick="removerEmail(false, true, ${index})">${linha.email}</label></div>`
        $('#emailsAssociadosEdit').append(linha)
        });
        $('#numPorta').val(escola.numPorta)
        $('#rua').val(escola.rua)
        $('#localidade').val(escola.localidade)
        $('#distrito').val(escola.distrito)
        $('#codPostal').val(escola.codPostal)
        $('#codPostalRua').val(escola.codPostalRua)
        carregarAgrupamentosEdit()
        if(escola.id_agrupamento != null && agrupamento != null) {
            selecionar(false, escola.id_agrupamento, agrupamento[0].nome)
        }
}

function remover(id) {
    url = 'escolas/delete/' + id
    $('#formDelete').attr('action', url)
}

function carregarAgrupamentosAdd() {
    destruirTabela('#tabelaAdd')
    $.ajax({
        url: 'agrupamentos/getAllComLocalidade',
        method: "GET",
        dataType: "json",
        success: function (agrupamentos) {
            if (agrupamentos != null) {
                for (elemento of agrupamentos) {
                    var linha = '<tr>'
                    linha = linha + `<td>${elemento.nome}</td>`
                    linha = linha + verificaNull(elemento.localidade)
                    linha = linha + verificaNull(elemento.nomeDiretor)
                    linha = linha + `<td><a onclick="selecionar(true, ${elemento.id_agrupamento}, \'${elemento.nome}\')"><img src="http://backofficeAjudaris/images/select.png"></img></a></td>`
                    linha = linha + '</tr>'
                    $('#tableBodyAdd').append(linha)
                }
                inicializarDataTable('#tabelaAdd')
            }
        },
        error: function (error) {
        }
    })
}

function carregarAgrupamentosEdit() {
    destruirTabela('#tabelaEdit')
    $.ajax({
        url: 'agrupamentos/getAllComLocalidade',
        method: "GET",
        dataType: "json",
        success: function (agrupamentos) {
            if (agrupamentos != null) {
                for (elemento of agrupamentos) {
                    var linha = '<tr>'
                    linha = linha + `<td>${elemento.nome}</td>`
                    linha = linha + verificaNull(elemento.localidade)
                    linha = linha + verificaNull(elemento.nomeDiretor)
                    linha = linha + `<td><a onclick="selecionar(false, ${elemento.id_agrupamento}, \'${elemento.nome}\')"><img src="http://backofficeAjudaris/images/select.png"></img></a></td>`
                    linha = linha + '</tr>'
                    $('#tableBodyEdit').append(linha)
                }
                inicializarDataTable('#tabelaEdit')
            }
        },
        error: function (error) {
        }
    })
}

function verificaNull(valor) {
    if (valor != null) {
        return `<td>${valor}</td>`;
    }
    else {
        return '<td> --- </td>';
    }
}

function selecionar(adicionar, id_agrupamento, nome) {
    if (adicionar) {
        $('#agrupamentoAdd').val(id_agrupamento)
        $('#nomeAgrupamentoAdd').val(nome)
    }
    else {
        $('#agrupamento').val(id_agrupamento)
        $('#nomeAgrupamento').val(nome)
    }

}