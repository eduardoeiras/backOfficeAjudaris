<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Participantes</title>
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
    <script src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('js/jszip.min.js') }}"></script>
    <script src="{{ asset('js/vfs_fonts.js') }}"></script>
</head>

<body>
    <div class="d-flex" id="wrapper">
        @include("admin/sideBar")
        <div id="page-content-wrapper">
            @include("admin/topBar")
            <?php
                if(isset($title)) {
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
                                        <h2>Gerir <b>Participantes no projeto</b></h2>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="#add" class="btn btn-success" data-toggle="modal" onclick="inicializarTabela()"><i
                                                class="material-icons">&#xE147;</i> <span>Adicionar um novo Participante</span></a>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-hover" id="tabelaDados">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Telefone</th>
                                        <th>Telemóvel</th>
                                        <th>Email</th>
                                        <th>Regiao</th>
                                        <th>Tipo de Participante</th>
                                        <th>Cargo</th>
                                        <th>Ano de Participação</th>
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
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Adicionar Participante</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body" style="margin: 0 auto;">
                                    <div class="form-group" style="text-align: center;">
                                        <h4>Filtrar tabela por:</h4>
                                        <select name="tipoParticipante" id="tiposAdd">
                                            <optgroup label="Tipo de Participante">
                                                <option value="ilustradores">Ilustradores Solidários</option>
                                                <option value="contadores">Contador de Histórias</option>
                                                <option value="entidades">Entidade Oficial</option>
                                                <option value="agrupamentos">Agrupamentos</option>
                                                <option value="escolas">Escola Solidária</option>
                                                <option value="juris">Juri</option>
                                                <option value="professores">Professor</option>
                                                <option value="professores_faculdade">Professor de Faculdade</option>
                                                <option value="rbes">Rede de Bibliotecas Escolares (RBE)</option>
                                                <option value="universidades">Universidade</option>
                                            </optgroup>
                                        </select>
                                        <button onclick="realizarFiltragemTipo()">Filtrar</button>    
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div style="padding-left: 3%; padding-right: 3%">
                                        <label>Selecione o participante a adicionar:</label>
                                        <table class="display table table-striped table-bordered" id="tabelaAdd">
                                            <thead id="tableHeadAdd">
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Telefone</th>
                                                    <th>Telemóvel</th>
                                                    <th>Email</th>
                                                    <th>Selecionar</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableBodyAdd">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <br><br>
                            <form id="formAdd" method="POST" action="">
                                @csrf
                                <div class="form-group" id="elementoSelecionado" style="padding-left: 3%">
                                    <div id="divErro" class="formAdicionar">
                                        
                                    </div>
                                    <div id="divForm" class="formAdicionar">
                                        <h4>Participante selecionado a adicionar ao projeto:</h4>
                                        <br>
                                        <div class="form-group row" style="margin-right: 3%">
                                            <label for="nome" class="col-2 col-form-label">Nome:</label>
                                            <div class="col-10">
                                                <input class="form-control" type="text" name="nome" id="nome" value="" readonly>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-group row" style="margin-right: 3%">
                                            <label for="anoParticipacao" class="col-2 col-form-label">Ano de Participação:</label>
                                            <div class="col-10">
                                              <input class="form-control" type="text" name="anoParticipacao" id="anoParticipacao" required>
                                            </div>
                                        </div>
                                        <input type="hidden" name="id_projeto" id="id_projeto" value="" readonly>
                                        <input type="hidden" name="id_elemento" id="id_elemento" value="" readonly>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                                    <input type="submit" id="adicionar" class="btn btn-success" value="Adicionar">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="msg" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form>
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title" id="titulo" style="color: red"></h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p id="mensagem"></p>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-default" data-dismiss="modal" value="OK">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="delete" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="" id="formDelete">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Remover Participante</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>Tem a certeza que deseja remover o participante?</p>
                                    <p class="text-warning"><small>Esta ação não pode ser retrocedida.</small></p>
                                </div>
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
<script src="{{ asset('js/paginas/pagGerirProjeto.js') }}"></script>
</html>