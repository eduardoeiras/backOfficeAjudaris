<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Projetos</title>
    <link rel="stylesheet" href="{{ asset('fonts/font-roboto-varela-round.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material_icons.css') }}">
    <link rel="stylesheet"
        href="{{ asset('fonts/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilizadores.css') }}">
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/simple-sidebar.css') }}" rel="stylesheet">
    <link href="{{asset('css/sideBarImg.css')}}" rel="stylesheet">
    <link href="{{asset('css/form-pesquisa.css')}}" rel="stylesheet">
    <link type="text/css" href="{{asset('css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <script src="{{asset('js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{asset('js/popper.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" charset="utf8" src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" charset="utf8" src="{{ asset('js/dataTable.bootstrap4.min.js') }}"></script>
</head>

<body>
    <div class="d-flex" id="wrapper">
        @include("colaborador/sideBar")
        <div id="page-content-wrapper">
            @include("colaborador/topBar")
            <?php
                if(isset($title)) {
                    $input = '<input type="hidden" id="idUtilizador" value="'.session('id_utilizador').'">';
                    echo $input;
                    echo '<h1 style="padding: 3%">'.$title.'</h1>';
                }
            ?>
            <div class="container-fluid">
                <div class="tabelasCrud">
                    <div class="table-responsive">
                        <div class="table-wrapper">
                            <div class="table-title">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h2>Gerir <b>Projetos Associados ao Utilizador</b></h2>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="#add" class="btn btn-success" data-toggle="modal" onclick="inicializarTabelaAdd()"><i
                                                class="material-icons">&#xE147;</i> <span>Associar um projeto</span></a>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-hover" id="tabelaProjetosAssociados">
                                <thead>
                                    <tr>
                                        <th>Nome do Projeto</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="add" class="modal fade">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Associar Projeto</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="form-group">
                                    <div style="padding-left: 3%; padding-right: 3%">
                                        <table class="display table table-striped table-bordered" id="tabelaAdd">
                                            <thead id="tableHeadAdd">
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Selecionar</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableBodyAdd">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <form id="formAdd" method="POST" action="">
                                @csrf
                                <div class="form-group" id="elementoSelecionado" style="padding-left: 3%;padding-right: 3%;padding-top: 10%">
                                    <h4>Projeto selecionado:</h4>
                                    <br>
                                    <label>Nome:</label>
                                    <input type="text" name="nome" id="nome" value="" readonly class="form-control" style="margin-right: 5%">
                                    <br><br>
                                    <input type="hidden" id="id_utilizador" name="id_utilizador" value="">
                                    <input type="hidden" id="id_projeto" name="id_projeto" value="">
                                    <br><br>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                                    <input type="submit" id="adicionar" class="btn btn-success" value="Adicionar">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>
<script src="{{ asset('js/colaborador/pagGerirProjetosUser.js') }}"></script>
</html>