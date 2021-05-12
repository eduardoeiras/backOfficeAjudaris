var urlDocRegulamento = ""
var id_escola = 0;
var ano = 0;

$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});

$(document).ready(function () {
    id_escola = $('#idEscola').val();
    ano = new Date().getFullYear()
    $("#anoHistoria").datepicker({
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years",
        autoclose: true,
        startDate: ano.toString(),
        date: '',
    });
    $("#erroFicheiro").hide
    inicializarDataTable();
});

function inicializarDataTable() {
    $('#tabelaDados').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url":"gerirHistorias/getAll" + id_escola, 
          "type": "GET"
        },
        "columns": [
            {data: 'id_historia', name: 'id_historia'},
            {data: 'titulo', name: 'titulo'},
            {data: 'ano', name: 'ano'},
            {data: 'ficheiro', name: '', orderable: false, searchable: false},
            {data: 'opcoes', name: '', orderable: false, searchable: false},
        ],
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

function editar(id) {
    var url = "gerirHistorias/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response != null) {
                $('#edit_titulo').val(response.titulo)
                $('#edit_ano').val(response.ano)
                $('#editHistoriaId').val(response.id_historia)
                urlDocRegulamento = response.urlFicheiro
            }
        },
        error: function (error) {
            alert("Erro na obtenção das informações do projeto!")
        }
    })
}

function remover(id) {
    url = 'gerirHistorias/delete/' + id
    $('#formDelete').attr('action', url)
}

function submeterNovo() {
    var validade = document.forms['formAdd'].reportValidity();
    if(document.getElementById('historia').files[0] != null && validade) {
        var formDataFicheiro = new FormData()
        formDataFicheiro.append("upload_file", document.getElementById('historia').files[0]);
        $.ajax({
            url: 'gerirHistorias/submeterFicheiro',
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formDataFicheiro,
            cache: false,
            contentType: false,
            processData: false,
            enctype: 'multipart/form-data',
            success: function (response) {
                if (response != null) {
                    var formData = new FormData()
                    formData.append('titulo', $('#titulo').val())
                    formData.append('anoHistoria', $('#anoHistoria').val())
                    formData.append('urlFicheiro', response.url)
                    $.ajax({
                        url: 'gerirHistorias/add',
                        method: "POST",
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false, 
                        contentType: false,
                        success: function (response) {
                            alert("História adicionada com sucesso!")
                            location.reload();

                        },
                        error: function (error) {
                            alert("Erro na submissão da história!")
                        }
                        
                    })
                }
            },
            error: function (error) {
                alert("Ocorreu um erro na submissão da história! \n\nPor favor contacte o técnico se o problema persistir." + 
                "\n\n" + error.toString());
            }
        })    
    }
    else {
        $("#erroFicheiro").text("É necessário selecionar um ficheiro para adicionar a história!")
        $("#erroFicheiro").show
    }
}

function submeterEditar() {
    var validade = document.forms['formEditar'].reportValidity();
    if(document.getElementById('edit_historia').files[0] != null && validade) {
        var formDataFicheiro = new FormData()
        formDataFicheiro.append("upload_file", document.getElementById('edit_historia').files[0]);
        $.ajax({
            url: 'gerirHistorias/submeterFicheiro',
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formDataFicheiro,
            cache: false,
            contentType: false,
            processData: false,
            enctype: 'multipart/form-data',
            success: function (response) {
                if (response != null) {
                    var formData = new FormData()
                    formData.append('titulo', $('#edit_titulo').val())
                    formData.append('anoHistoria', $('#edit_ano').val())
                    formData.append('urlFicheiro', response.url)
                    let editURL = 'gerirHistorias/edit/' + $('#editHistoriaId').val()
                    $.ajax({
                        url: editURL,
                        method: "POST",
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false, 
                        contentType: false,
                        success: function (response) {
                            alert("História atualizada com sucesso!")
                            location.reload();

                        },
                        error: function (error) {
                            alert("Erro na atualização das informações da história!")
                        }
                    })
                }
            },
            error: function (error) {
                alert("Ocorreu um erro na submissão da história! \n\nPor favor contacte o técnico se o problema persistir."  + 
                "\n\n" + error.toString());
            }
        })
    }
    else if(document.getElementById('edit_historia').files[0] == null && validade) {
        var formData = new FormData()
        formData.append('titulo', $('#edit_titulo').val())
        formData.append('anoHistoria', $('#edit_ano').val())
        formData.append('urlFicheiro', urlDocRegulamento)
        let editURL = 'gerirHistorias/edit/' + $('#editHistoriaId').val()
        $.ajax({
            url: editURL,
            method: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false, 
            contentType: false,
            success: function (response) {
                alert("História atualizada com sucesso!")
                location.reload();

            },
            error: function (error) {
                alert("Erro na atualização das informações da história!")
            }
        })
    }  
}