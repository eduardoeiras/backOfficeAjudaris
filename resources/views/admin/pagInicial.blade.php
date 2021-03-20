<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gerir Projetos</title>
    <link rel="stylesheet" href="{{ asset('fonts/font-roboto-varela-round.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material_icons.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/font-awesome.min.css') }}">
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
                                        <h2>Gerir<b> Projetos</b></h2>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="#addProjeto" class="btn btn-success" data-toggle="modal"><i
                                                class="material-icons">&#xE147;</i> <span>Criar um novo projeto</span></a>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-hover" id="tabelaDados">
                                <thead>
                                    <tr>
                                        <th>Identificador do Projeto</th>
                                        <th>Nome</th>
                                        <th>Objetivos</th>
                                        <th>Regulamento</th>
                                        <th>Público Alvo</th>
                                        <th>Observações</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <?php
                                        if(isset($projetos)) {
                                            foreach($projetos as $projeto) {
                                                $dados = "<tr>";
                                                $dados = $dados.'<td>'.$projeto->id_projeto.'</td>';
                                                $dados = $dados.'<td>'.$projeto->nome.'</td>';
                                                $dados = $dados.'<td>'.$projeto->objetivos.'</td>';
                                                $dados = $dados.'<td><button id="'.$projeto->id_projeto.'" onclick="downloadRegulamento('.$projeto->id_projeto.')">
                                                Visualizar Regulamento</button></td>';
                                                $dados = $dados.'<td>'.$projeto->publicoAlvo.'</td>';
                                                $dados = $dados.verificaNull($projeto->observacoes);
                                                $url = 'gerirProjeto'.$projeto->id_projeto;
                                                $dados = $dados.'<td>
                                                        <a href="#edit" class="edit" data-toggle="modal" onclick="editarProjeto('.$projeto->id_projeto.')"><i
                                                                class="material-icons" data-toggle="tooltip"
                                                                title="Editar">&#xE254;</i></a>
                                                        <a href="#delete" class="delete" data-toggle="modal" onclick="removerProjeto('.$projeto->id_projeto.')"><i
                                                                class="material-icons" data-toggle="tooltip"
                                                                title="Remover">&#xE872;</i></a>
                                                                <br>
                                                        <a href="'.$url.'"><img src="http://backofficeAjudaris/images/gerirParceiros.png"></img></a>
                                                    </td>';
                                                $dados = $dados.'</tr>';
                                                echo $dados;
                                            }
                                        }
                                        function verificaNull($valor) {
                                            if($valor != null) {
                                                return '<td>'.$valor.'</td>';    
                                            }
                                            else {
                                                return '<td> --- </td>';
                                            }
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="addProjeto" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="formAdd" enctype="multipart/form-data"> 
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Adicionar Projeto</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nome do Projeto</label>
                                        <input type="text" id="nome" name="nome" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Objetivos</label>
                                        <input type="text" id="objetivos" name="objetivos" class="form-control" maxlength="400" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Publico Alvo</label>
                                        <input type="text" id="publicoAlvo" name="publicoAlvo" class="form-control" maxlength="45" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea class="form-control" id="observacoes" name="observacoes" maxlength="200"></textarea> 
                                    </div>
                                    <div class="form-group">
                                        <label>Regulamento</label>
                                        <input type="file" id="regulamento" name="regulamento" class="form-control-file" required>
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
                                    <h4 class="modal-title">Editar Projeto</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nome do Projeto</label>
                                        <input id="edit_Nome" type="text" name="nome" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Objetivos</label>
                                        <input id="edit_Obj" type="text" name="objetivos" class="form-control" maxlength="400" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Publico Alvo</label>
                                        <input id="edit_PublicoAlvo" type="text" name="publicoAlvo" class="form-control" maxlength="45" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea id="edit_Obs" class="form-control" name="observacoes" maxlength="200"></textarea> 
                                    </div>
                                    <div class="form-group">
                                        <label>Regulamento</label>
                                        <input type="file" id="edit_regulamento" name="regulamento" class="form-control">
                                    </div>
                                    <input type="hidden" id="editPorjetoId" name="id_projeto" value="">
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
                                    <h4 class="modal-title">Remover projeto</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>Tem a certeza que deseja remover o projeto?</p>
                                    <p class="text-warning"><small>Esta ação não pode ser retrocedida.</small></p>
                                </div>
                                <input type="hidden" id="removerPorjetoId" name="id_projeto" value="">
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
    <script src="{{ asset('js/paginas/pagInicial.js') }}"></script>
</body>

</html>