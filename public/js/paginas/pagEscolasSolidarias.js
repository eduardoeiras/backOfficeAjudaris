var idSelecionado = 0;

$(document).ready(function () {
    inicializarDataTable('#tabelaDados');
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
        success: function (escola) {
            if (escola != null) {
                getNomeAgrupamento = 'agrupamentos/getPorId/' + escola.id_agrupamento;
                $.ajax({
                    url: getNomeAgrupamento,
                    method: "GET",
                    dataType: "json",
                    success: function (agrupamento) {
                        url = 'escolas/edit/' + escola.id_escolaSolidaria
                        $('#formEditar').attr('action', url)
                        $('#nome').val(escola.nome)
                        $('#telefone').val(escola.telefone)
                        $('#telemovel').val(escola.telemovel)
                        $('#contactoAssPais').val(escola.contactoAssPais)
                        carregarAgrupamentosEdit()
                        selecionar(false, escola.id_agrupamento, agrupamento.nome)
                        var disp = escola.disponivel
                        $('#disponibilidade').val(disp.toString())
                    },
                    error: function (error) {
                    }
                })
            }
        },
        error: function (error) {
        }
    })
}

function remover(id) {
    url = 'escolas/delete/' + id
    $('#formDelete').attr('action', url)
}

function carregarAgrupamentosAdd() {
    $.ajax({
        url: 'agrupamentos/getAllComLocalidade',
        method: "GET",
        dataType: "json",
        success: function (agrupamentos) {
            if (agrupamentos != null) {
                destruirTabela('#tabelaAdd')
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
    $.ajax({
        url: 'agrupamentos/getAllComLocalidade',
        method: "GET",
        dataType: "json",
        success: function (agrupamentos) {
            if (agrupamentos != null) {
                destruirTabela('#tabelaEdit')
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