var id_escola = 0;

$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});

$(document).ready(function () {
    id_escola = $('#idEscola').val()
    
    $.ajax({
        url: "gerirEscola/getProfessores",
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response != null && response.length > 0) {
                carregarProfessores(response)
            }
            inicializarDataTable('#tabelaProfsAssociados')
        },
        error: function (error) {
        }
    })
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

function carregarProfessores(response) {
    for (professor of response) {
        criarLinha(professor)
    }
}

function criarLinha(elemento) {
    var linha = "<tr>"
    linha = linha + `<td>${elemento.nome}</td>`
    linha = linha + verificaNull(elemento.telefone)
    linha = linha + verificaNull(elemento.telemovel)
    linha = linha + verificaNull(elemento.email)
    linha = linha + `<td>
        <a href="#delete" class="delete" data-toggle="modal" onclick="remover(${elemento.id_professor}, ${id_escola})">
        <i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i>
        </a></td>`;
    linha = linha + '</tr>'
    $('#tableBody').append(linha)
}

function remover(id_professor, id_escola) {
    var urlDelete = 'gerirEscola/delete/' + id_professor + "-" + id_escola
    $('#formDelete').attr('action', urlDelete)

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
    let urlAdd = "professores/getDisponiveisSemEscola/" + id_escola 
    $.ajax({
        url: urlAdd,
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response != null && response.length > 0) {
                destruirTabela('#tabelaAdd')
                carregarProfessoresAdd(response)
            }
            inicializarDataTable('#tabelaAdd')
        },
        error: function (error) {
        }
    })
}

function carregarProfessoresAdd(response) {
    for(professor of response) {
        criarLinhasAdd(professor)
    }
}

function criarLinhasAdd(elemento) {
    var linha = "<tr>"
    linha = linha + `<td>${elemento.nome}</td>`
    linha = linha + verificaNull(elemento.telefone)
    linha = linha + verificaNull(elemento.telemovel)
    linha = linha + verificaNull(elemento.email)
    linha = linha + `<td><a onclick="selecionar(${elemento.id_professor}, \'${elemento.nome}\')"><img src="http://backofficeAjudaris/images/select.png"></img></a></td>`
    linha = linha + '</tr>'
    $('#tableBodyAdd').append(linha)
}

function selecionar(id_professor, nome) {
    $('#formAdd').attr('action', 'gerirEscola/add')
    $('#id_escola').val(id_escola)
    $('#id_professor').val(id_professor)
    $('#nome').val(nome)
}
