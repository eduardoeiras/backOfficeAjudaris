<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Livros Por Ano</title>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
    <script src="{{asset('js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{asset('js/popper.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" charset="utf8" src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" charset="utf8" src="{{ asset('js/dataTable.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
</head>

<body>
    <div class="d-flex" id="wrapper">
        @include("admin/sideBar")
        <div id="page-content-wrapper">
            @include("admin/topBar")
            <?php
                if(isset($nome)) {
                    echo '<h2 style="padding: 3%">Estabelecimento de Ensino: '.$nome.'</h2>';
                }
            ?>
            <div class="container-fluid">
                <div class="tabelasCrud">
                    <div class="table-responsive">
                        <div class="table-wrapper">
                            <div class="table-title">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h2>Gerir <b>Livros Por Ano</b></h2>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="#add" class="btn btn-success" data-toggle="modal"><i
                                            class="material-icons">&#xE147;</i> <span>Registar atribuição de livros</span></a>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-hover" id="tabelaLivrosAno">
                                <thead>
                                    <tr>
                                        <th>Ano</th>
                                        <th>Número de Livros</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <?php
                                        if(isset($data)) {
                                            foreach($data as $linha) {
                                                $dados = '<tr>';
                                                $dados = $dados.'<td>'.$linha->ano.'</td>';
                                                $dados = $dados.'<td>'.$linha->numLivros.'</td>';
                                                $dados = $dados.'<td>
                                                        <a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$linha->ano.', '.$linha->id_escola.')"><i
                                                                class="material-icons" data-toggle="tooltip"
                                                                title="Edit">&#xE254;</i></a>
                                                        <a href="#delete" class="delete" data-toggle="modal" onclick="remover('.$linha->ano.', '.$linha->id_escola.')"><i
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
                            <form method="POST" action="gerirLivrosAno/add" onsubmit="return verificarErroAno()">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Adicionar atribuição de livros</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label for="anoAdd" class="col-2 col-form-label">Data de Comunicação:</label>
                                        <div class="col-10">
                                          <input class="form-control" type="text" name="anoLivros" id="anoAdd" autocomplete="false" required>
                                          <label style="color: red" id="erroAnoExiste"></label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="numLivrosAdd" class="col-2 col-form-label">Número de Livros:</label>
                                        <div class="col-10">
                                          <input class="form-control" type="number" name="numLivros" id="numLivrosAdd" required>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    if(isset($id_escola) && isset($nome)) {
                                        echo '<input type="hidden" name="nome" value="'.$nome.'">';
                                        echo '<input type="hidden" id="idEscolaAdd" name="id_escola" value="'.$id_escola.'">';
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
                                    <h4 class="modal-title">Editar atribuição de livros</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label for="anoEdit" class="col-2 col-form-label">Data de Comunicação:</label>
                                        <div class="col-10">
                                            <input class="date-own form-control" type="text" id="anoEdit" autocomplete="false" readonly required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="numLivrosEdit" class="col-2 col-form-label">Número de Livros:</label>
                                        <div class="col-10">
                                          <input class="form-control" type="number" name="numLivros" id="numLivrosEdit" required>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    if(isset($id_escola) && isset($nome)) {
                                        echo '<input type="hidden" name="nome" value="'.$nome.'">';
                                        echo '<input type="hidden" name="id_escola" value="'.$id_escola.'">';
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
            </div>
        </div>
    </div>
    </div>
</body>
<script src="{{ asset('js/paginas/pagGerirLivrosAno.js') }}"></script>
</html>