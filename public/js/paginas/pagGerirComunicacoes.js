$(document).ready(function () {
    inicializarDataTable();
});

function inicializarDataTable() {
    $('#tabelaComunicacoes').DataTable({
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
    var url = "gerirComunicacoes/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (resposta) {
            if (resposta != null) {
                console.log(resposta);
                url = 'gerirComunicacoes/edit/' + resposta.id_comunicacao
                $('#formEditar').attr('action', url)
                dataFormatada = formatarData(resposta.data)
                $('#dataComunicacao').val(dataFormatada)
                $('#observacoes').val(resposta.observacoes)
            }
        },
        error: function (error) {
            alert("Ocorreu um erro na obtenção da comunicação!\n Por favor tente novamente.")
        }
    })
}

function formatarData(date) {
    var data = new Date(date),
        mes = '' + (data.getMonth() + 1)
        dia = '' + data.getDate()
        ano = data.getFullYear()
        hora = data.getHours()
        minutos = data.getMinutes()


    if (mes.length < 2) 
        mes = '0' + mes
    if (dia.length < 2) 
        dia = '0' + dia

    let dataString = `${dia}-${mes}-${ano} ${hora}:${minutos}`
    return dataString;
}

function remover(id) {
    url = 'gerirComunicacoes/delete/' + id
    $('#formDelete').attr('action', url)
}