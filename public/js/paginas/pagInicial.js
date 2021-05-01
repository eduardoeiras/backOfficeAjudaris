var urlDocRegulamento = ""

$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});

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

function editarProjeto(id) {
    var url = "projetos/getPorId/" + id;
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response != null) {
                $('#editPorjetoId').val(response.projeto.id_projeto)
                $('#edit_Nome').val(response.projeto.nome)
                $('#edit_Obj').val(response.projeto.objetivos)
                $('#edit_PublicoAlvo').val(response.projeto.publicoAlvo)
                $('#edit_Obs').val(response.projeto.observacoes)
                urlDocRegulamento = response.projeto.regulamento
            }
        },
        error: function (error) {
            alert("Erro na obtenção das informações do projeto!")
        }
    })
}

function removerProjeto(id) {
    url = 'projetos/delete/' + id
    $('#formDelete').attr('action', url)
}

function submeterNovo() {
    var formDataFicheiro = new FormData()
    formDataFicheiro.append("upload_file", document.getElementById('regulamento').files[0]);
    $.ajax({
        url: 'projetos/submeterFicheiro',
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
                formData.append('nome', $('#nome').val())
                formData.append('objetivos', $('#objetivos').val())
                formData.append('publicoAlvo', $('#publicoAlvo').val())
                formData.append('observacoes', $('#observacoes').val())
                formData.append('urlFicheiro', response.url)
                $.ajax({
                    url: 'projetos/add',
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false, 
                    contentType: false,
                    success: function (response) {
                        location.reload();

                    },
                    error: function (error) {
                        alert("Erro na atualização das informações do projeto!")
                    }
                })
            }
        },
        error: function (error) {
            alert("Ocorreu um erro na submissão da imagem! \n\nPor favor contacte o técnico se o problema persistir." + 
            "\n\n" + error.toString());
        }
    })
}

function submeterEditar() {
    if(document.getElementById('edit_regulamento').files[0] != null) {
        var formDataFicheiro = new FormData()
        formDataFicheiro.append("upload_file", document.getElementById('edit_regulamento').files[0]);
        $.ajax({
            url: 'projetos/submeterFicheiro',
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
                    formData.append('nome', $('#edit_Nome').val())
                    formData.append('objetivos', $('#edit_Obj').val())
                    formData.append('publicoAlvo', $('#edit_PublicoAlvo').val())
                    formData.append('observacoes', $('#edit_Obs').val())
                    formData.append('urlFicheiro', response.url)
                    let editURL = 'projetos/edit/' + $('#editPorjetoId').val()
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
                            alert("Projeto Atualizado com sucesso!")
                            location.reload();

                        },
                        error: function (error) {
                            alert("Erro na atualização das informações do projeto!")
                        }
                    })
                }
            },
            error: function (error) {
                alert("Ocorreu um erro na submissão da imagem! \n\nPor favor contacte o técnico se o problema persistir."  + 
                "\n\n" + error.toString());
            }
        })
    }
    else {
        var formData = new FormData()
        formData.append('nome', $('#edit_Nome').val())
        formData.append('objetivos', $('#edit_Obj').val())
        formData.append('publicoAlvo', $('#edit_PublicoAlvo').val())
        formData.append('observacoes', $('#edit_Obs').val())
        formData.append('urlFicheiro', urlDocRegulamento)
        let editURL = 'projetos/edit/' + $('#editPorjetoId').val()
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
                alert("Projeto Atualizado com sucesso!")
                location.reload();

            },
            error: function (error) {
                alert("Erro na atualização das informações do projeto!")
            }
        })
    }  
}