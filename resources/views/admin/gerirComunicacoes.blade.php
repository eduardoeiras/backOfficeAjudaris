<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Comunicações</title>
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
        @include("admin/sideBar")
        <div id="page-content-wrapper">
            @include("admin/topBar")
            <?php
                if(isset($id_colaborador) && isset($nome)) {
                    echo '<h2 style="padding: 3%">Colaborador: '.$nome.'</h2>';
                }
            ?>
            <div class="container-fluid">
                <div class="tabelasCrud">
                    <div class="table-responsive">
                        <div class="table-wrapper">
                            <div class="table-title">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h2>Gerir <b>Comunicações</b></h2>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="#add" class="btn btn-success" data-toggle="modal"><i
                                            class="material-icons">&#xE147;</i> <span>Registar uma comunicação</span></a>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-hover" id="tabelaComunicacoes">
                                <thead>
                                    <tr>
                                        <th>Número Identificador</th>
                                        <th>Data e Hora da Comunicação</th>
                                        <th>Observacoes</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <?php
                                        if(isset($data)) {
                                            foreach($data as $linha) {
                                                $dados = '<tr>';
                                                $dados = $dados.'<td>'.$linha->id_comunicacao.'</td>';
                                                $dados = $dados.'<td>'.$linha->data.'</td>';
                                                $dados = $dados.'<td>'.$linha->observacoes.'</td>';
                                                $dados = $dados.'<td>
                                                        <a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$linha->id_comunicacao.')"><i
                                                                class="material-icons" data-toggle="tooltip"
                                                                title="Edit">&#xE254;</i></a>
                                                        <a href="#delete" class="delete" data-toggle="modal" onclick="remover('.$linha->id_comunicacao.')"><i
                                                                class="material-icons" data-toggle="tooltip"
                                                                title="Delete">&#xE872;</i></a>
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
                <div id="add" class="modal fade">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="POST" action="gerirComunicacoes/add">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Adicionar Comunicação</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <label style="font-size: 18px">Informações da Comunicação</label>
                                    <br><br>
                                    <div class="form-group row">
                                        <label for="dataComunicacaoAdd" class="col-2 col-form-label">Data de Comunicação:</label>
                                        <div class="col-10">
                                          <input class="form-control" type="datetime-local" name="data" id="dataComunicacaoAdd" required>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label">Observações:</label>
                                        <div class="col-10">
                                            <textarea name="obs" class="form-control" placeholder="Observações" maxlength="400" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    if(isset($id_colaborador) && isset($nome)) {
                                        echo '<input type="hidden" name="id_colaborador" value="'.$id_colaborador.'">';
                                        echo '<input type="hidden" name="nome" value="'.$nome.'">';
                                    }
                                ?>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                                    <input type="submit" class="btn btn-success" value="Adicionar">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="edit" class="modal fade">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="POST" id="formEditar" action="">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Editar Comunicação</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <label style="font-size: 18px">Informações da Comunicação</label>
                                    <div class="form-group row">
                                        <label for="dataComunicacao" class="col-2 col-form-label">Data de Comunicação</label>
                                        <div class="col-10">
                                          <input class="form-control" type="text" id="dataComunicacao" required readonly>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea name="obs" id="observacoes" class="form-control" placeholder="Observações" maxlength="400" required></textarea>
                                    </div>
                                </div>
                                <?php 
                                    if(isset($nome)) {
                                        echo '<input type="hidden" name="id_colaborador" value="'.$id_colaborador.'">';
                                        echo '<input type="hidden" name="nome" value="'.$nome.'">';
                                    }
                                ?>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                                    <input type="submit" class="btn btn-success" value="Adicionar">
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
                                    <h4 class="modal-title">Remover Comunicação</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>Tem a certeza que deseja remover a comunicação?</p>
                                    <p class="text-warning"><small>Esta ação não pode ser retrocedida.</small></p>
                                </div>
                                <?php 
                                    if(isset($id_colaborador) && isset($nome)) {
                                        echo '<input type="hidden" name="id_colaborador" value="'.$id_colaborador.'">';
                                        echo '<input type="hidden" name="nome" value="'.$nome.'">';
                                    }
                                ?>
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
<script src="{{ asset('js/paginas/pagGerirComunicacoes.js') }}"></script>
</html>