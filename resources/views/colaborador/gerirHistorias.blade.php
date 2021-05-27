<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gerir Histórias</title>
    <link rel="stylesheet" href="{{ asset('fonts/font-roboto-varela-round.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material_icons.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilizadores.css') }}">
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/simple-sidebar.css') }}" rel="stylesheet">
    <link href="{{asset('css/sideBarImg.css')}}" rel="stylesheet">
    <link type="text/css" href="{{asset('css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <link type="text/css" href="{{asset('css/form-pesquisa.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.css')}}"/>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{asset('js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/popper.min.js')}}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTable.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
</head>

<body>
    <div class="d-flex" id="wrapper">
        @include("colaborador/sideBar")
        <div id="page-content-wrapper">
            @include("colaborador/topBar")
            <?php
                if(isset($nome) && isset($id_escola)) {
                    echo '<h2 style="padding: 3%">Estabelecimento de Ensino: '.$nome.'</h2>';
                    echo '<input type="hidden" value="'.$id_escola.'" id="idEscola">';
                }
            ?>
            <div class="container-fluid">
                <div class="tabelasCrud">
                    <div class="table-responsive">
                        <div class="table-wrapper">
                            <div class="table-title">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h2>Gerir <b>Historias</b></h2>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="#add" class="btn btn-success" data-toggle="modal"><i
                                            class="material-icons">&#xE147;</i> <span>Registar uma história</span></a>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-hover" style="width:100%" id="tabelaDados">
                                <thead>
                                    <tr>
                                        <th>Número Identificador</th>
                                        <th>Título</th>
                                        <th>Ano</th>
                                        <th>História</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="add" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="formAdd" enctype="multipart/form-data"> 
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Adicionar História</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Título</label>
                                        <input type="text" id="titulo" name="titulo" class="form-control" maxlength="70" required>
                                    </div>
                                    <br>
                                    <div class="form-group row" style="margin-right: 3%">
                                        <label for="anoHistoria" class="col-2 col-form-label">Ano</label>
                                        <div class="col-10">
                                          <input class="form-control" type="text" name="anoHistoria" id="anoHistoria" autocomplete="autocomplete_no" required>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label>História</label>
                                        <input type="file" id="historia" name="historia" class="form-control-file" required>
                                        <label style="color: red" id="erroFicheiro"></label>
                                    </div>
                                </div>
                            </form>
                            <div class="modal-footer">
                                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                                <input class="btn btn-success" value="Adicionar" onclick="submeterNovo()">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="edit" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" id="formEditar" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Editar História</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Título</label>
                                        <input id="edit_titulo" type="text" name="titulo" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="ano" class="col-2 col-form-label">Ano:</label>
                                        <input class="form-control" type="text" name="anoHistoria" readonly required>
                                    </div>
                                    <div class="form-group">
                                        <label>História</label>
                                        <input type="file" id="edit_historia" name="historia" class="form-control">
                                    </div>
                                    <input type="hidden" id="editHistoriaId" name="id_historia" value="">
                                </div>
                            </form>
                            <div class="modal-footer">
                                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                                <input class="btn btn-info" value="Guardar Alterações" onclick="submeterEditar()">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="delete" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="" id="formDelete">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Remover história</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>Tem a certeza que deseja remover a história?</p>
                                    <p class="text-warning"><small>Esta ação não pode ser retrocedida.</small></p>
                                </div>
                                <input type="hidden" id="removerHistoriaId" name="id_historia" value="">
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                                    <input type="submit" class="btn btn-danger" value="Remover">
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
<script src="{{ asset('js/paginas/pagGerirHistorias.js') }}"></script>
</html>