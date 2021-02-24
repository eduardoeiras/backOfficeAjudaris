var id_utilizador = 0;
var inicializada = false;
var existe = false;

$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
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
};

function destruirTabela(idTabela) {
    if ($.fn.DataTable.isDataTable(idTabela)) {
        $(idTabela).DataTable().clear().destroy();
    }
};

$(document).ready(function () {
    id_utilizador = $('#idUtilizador').val()
    let url = "getProjetosAssociados/" + id_utilizador
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response != null && response.length > 0) {
                carregarProjetos(response)
            }
            inicializarDataTable('#tabelaProjetosAssociados')
        },
        error: function (error) {
        }
    })

    $("#formAdd").submit(function (e) {
        if (existe) {
            return false;
        }
    })
});

function carregarProjetos(response) {
    for (projeto of response) {
        criarLinha(projeto)
    }
}

function criarLinha(elemento) {
    var linha = "<tr>"
    linha = linha + `<td>${elemento.nome}</td>`
    linha = linha + '</tr>'
    $('#tableBody').append(linha)
}

function verificaNull(valor) {
    if (valor != null) {
        return `<td>${valor}</td>`;
    }
    else {
        return '<td> --- </td>';
    }
}

function inicializarTabelaAdd() {
    let url = 'projetos/getSemAssociacao/' + id_utilizador
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response != null && response.length > 0) {
                destruirTabela('#tabelaAdd')
                carregarProjetosAdd(response)
            }
            inicializarDataTable('#tabelaAdd')
        },
        error: function (error) {
        }
    })
}

function carregarProjetosAdd(response) {
    for(projeto of response) {
        criarLinhasAdd(projeto)
    }
}

function criarLinhasAdd(elemento) {
    var linha = "<tr>"
    linha = linha + `<td>${elemento.nome}</td>`
    linha = linha + `<td><a onclick="selecionar(${elemento.id_projeto}, \'${elemento.nome}\')"><img src="http://backofficeAjudaris/images/select.png"></img></a></td>`
    linha = linha + '</tr>'
    $('#tableBodyAdd').append(linha)
}

function selecionar(id_projeto, nome) {
    $('#formAdd').attr('action', 'gerirProjetosUtilizador/add')
    $('#id_utilizador').val(id_utilizador)
    $('#id_projeto').val(id_projeto)
    $('#nome').val(nome)
}
