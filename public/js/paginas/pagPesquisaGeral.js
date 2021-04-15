$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});

$(document).ready(function () {
    $('#pesquisaNome').modal('show');
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

function obterRegistos() {
    let nome = $('#pesqNome').val()
    console.log(nome);
    if(nome != null && nome != "") {
        let pedidoUrl = 'pesqGlobal/' + nome
        $.ajax({
            url: pedidoUrl,
            method: "GET",
            dataType: "json",
            success: function (response) {
                if (response != null && response.length > 0) {
                    for(colaborador of response) {
                        criarLinha(colaborador)
                    }
                    $('#pesquisaNome').modal('hide');
                }
            },
            error: function (error) {
                alert('Ocorreu um erro na realização da pesquisa!\n\n Por favor tente novamente, se o erro persistir contacte o \
                administrador!')
                voltar()
            }
        })
    }
    else {
        $('#erroPesqNome').text("É obrigatório introduzir o nome do colaborador!")
    }
}

function voltar() {
    window.history.back();
}

function criarLinha(colaborador) {
    var elemento = colaborador.entidade
    var emails = colaborador.emails
    var linha = "<tr>"
    linha = linha + `<td>${elemento.nome}</td>`
    linha = linha + verificaNull(elemento.telefone)
    linha = linha + verificaNull(elemento.telemovel)
    linha = linha + '<td>'
    emails.forEach(element => {
        linha = linha + `${element.email}`
    });
    linha = linha + '</td>'
    if(elemento.disponivel == 0) {
        linha = linha + '<td>Disponível</td>'
    }
    else {
        linha = linha + '<td>Indisponível</td>'
    }
    linha = linha + '<td>' + elemento.localidade + '</td>'
    linha = linha + verificaNull(elemento.rua)
    linha = linha + `<td>${elemento.codPostal}-${elemento.codPostalRua}</td>`
    linha = linha + `<td>
    <a href="#edit" class="edit" data-toggle="modal" onclick="editarUtilizador(${elemento.id_colaborador}, true)"><i
    class="material-icons" data-toggle="tooltip"
    title="Edit">&#xE254;</i></a></td>`;
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
