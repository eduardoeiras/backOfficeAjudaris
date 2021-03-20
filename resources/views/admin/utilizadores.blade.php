<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Utilizadores</title>
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
        @include("admin/sideBar")
        <div id="page-content-wrapper">
            @include("admin/topBar")
            <div class="container-fluid">
                <div class="tabelasCrud">
                    <div class="table-responsive">
                        <div class="table-wrapper">
                            <div class="table-title">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h2>Gerir <b>Utilizadores</b></h2>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="#addUtilizador" class="btn btn-success" data-toggle="modal"><i
                                                class="material-icons">&#xE147;</i> <span>Criar um novo utilizador</span></a>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-hover" id="tabelaDados">
                                <thead>
                                    <tr>
                                        <th>Nome de utilizador</th>
                                        <th>Nome</th>
                                        <th>Password</th>
                                        <th>Email</th>
                                        <th>Telemovel</th>
                                        <th>Telefone</th>
                                        <th>Departamento</th>
                                        <th>Tipo de utilizador</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                        
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="msg" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form>
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title" id="titulo"></h4>
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
                <div id="addUtilizador" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="utilizadores/addUtilizador" id="formAdd">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Adicionar Utilizador</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nome de utilizador</label>
                                        <input type="text" id="nomeUtilizadorAdd" name="nomeUtilizador" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="password" class="form-control" maxlength="80" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Nome Completo</label>
                                        <input type="text" name="nome" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Departamento</label>
                                        <input type="text" name="departamento" class="form-control" maxlength="30" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Tipo de Utilizador</label>
                                        <select name="tipoUtilizador">
                                            <option value="0">Administrador</option>
                                            <option value="1">Colaborador</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Telefone</label>
                                        <input type="tel" name="telefone" class="form-control" maxlength="15">
                                    </div>
                                    <div class="form-group">
                                        <label>Telemóvel</label>
                                        <input type="tel" name="telemovel" class="form-control" maxlength="15">
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" maxlength="50">
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
                <div id="editUtilizador" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="" id="formEditarUtilizador">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Editar Utilizador</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nome de utilizador</label>
                                        <input type="text" id="nomeUtilizador" name="nomeUtilizador" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" id="password" name="password" class="form-control" maxlength="80" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Nome Completo</label>
                                        <input type="text" id="nome" name="nome" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Departamento</label>
                                        <input type="text" id="departamento" name="departamento" class="form-control" maxlength="30" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Tipo de Utilizador</label>
                                        <select id="tipoUtilizador" name="tipoUtilizador" required>
                                            <option value="0">Administrador</option>
                                            <option value="1">Colaborador</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Telefone</label>
                                        <input type="tel" id="telefone" name="telefone" class="form-control" maxlength="15">
                                    </div>
                                    <div class="form-group">
                                        <label>Telemóvel</label>
                                        <input type="tel" id="telemovel" name="telemovel" class="form-control" maxlength="15">
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" id="email" name="email" class="form-control" maxlength="50">
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
                <div id="deleteUtilizador" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="" id="formDeleteUtilizador">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Remover Utilizador</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>Tem a certeza que deseja remover o utilizador?</p>
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
    <script src="{{ asset('js/paginas/pagUtilizadores.js') }}"></script>
</body>

</html>