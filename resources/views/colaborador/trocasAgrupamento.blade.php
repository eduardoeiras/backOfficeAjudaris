<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Trocas de Agrupamentos</title>
    <link rel="stylesheet" href="{{ asset('fonts/font-roboto-varela-round.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material_icons.css') }}">
    <link rel="stylesheet"
        href="{{ asset('fonts/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilizadores.css') }}">
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/simple-sidebar.css') }}" rel="stylesheet">
    <link href="{{asset('css/sideBarImg.css')}}" rel="stylesheet">
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
            <div class="container-fluid">
                <div class="tabelasCrud">
                    <div class="table-responsive">
                        <div class="table-wrapper">
                            <div class="table-title">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h2>Gerir <b>Trocas de Agrupamento</b></h2>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="#add" class="btn btn-success" data-toggle="modal"><i
                                                class="material-icons">&#xE147;</i> <span>Criar uma nova Troca de Agrupamento</span></a>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-hover" style="width:100%" id="tabelaDados">
                                <thead>
                                    <tr>
                                        <th>Número identificador</th>
                                        <th>Nome do Professor</th>
                                        <th>Nome do Agrupamento Antigo</th>
                                        <th>Nome do Novo Agrupamento</th>
                                        <th>Data</th>
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
                            <form method="POST" action="trocasAgrupamento/add">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Adicionar Troca de Agrupamento</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nome do Agrupamento Antigo</label>
                                        <input type="text" name="agrupamentoAntigo" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Nome do Novo Agrupamento</label>
                                        <input type="text" name="novoAgrupamento" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea name="obs" class="form-control" placeholder="Observações" maxlength="200"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                                    <input type="submit" class="btn btn-success" value="Adicionar">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="edit" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="" id="formEditar">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Editar Troca de Agrupamento</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nome do Agrupamento Antigo</label>
                                        <input type="text" id="agrupamentoAntigo" name="agrupamentoAntigo" class="form-control" maxlength="70">
                                    </div>
                                    <div class="form-group">
                                        <label>Nome do Novo Agrupamento</label>
                                        <input type="text" id="novoAgrupamento" name="novoAgrupamento" class="form-control" maxlength="70">
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea id="observacoes" name="obs" class="form-control" placeholder="Observações" maxlength="200"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                                    <input type="submit" class="btn btn-info" value="Guardar Alterações">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="{{ asset('js/paginas/pagTrocasAgrupamento.js') }}"></script>
</body>

</html>