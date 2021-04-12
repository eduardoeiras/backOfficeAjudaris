<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Redes de Bibliotecas Escolares</title>
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
                                        <h2>Gerir <b>Redes de Bibliotecas Escolares</b></h2>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="#add" class="btn btn-success" data-toggle="modal" onclick="carregarConcelhos(true)"><i
                                            class="material-icons">&#xE147;</i> <span>Criar um nova Rede de Biblioteca Escolar</span></a>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-hover" id="tabelaDados">
                                <thead>
                                    <tr>
                                        <th>Número identificador</th>
                                        <th>Região</th>
                                        <th>Nome do Coordenador</th>
                                        <th>Concelhos</th>
                                        <th>Disponibilidade</th>
                                        <th>Telemovel</th>
                                        <th>Telefone</th>
                                        <th>Emails</th>
                                        <th>Localidade</th>
                                        <th>Rua</th>
                                        <th>Código Postal</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <?php
                                        use \App\Http\Controllers\ConcelhoController;
                                        if(isset($data)) {
                                            foreach($data as $linha) {
                                                $dados = '<tr>';
                                                $dados = $dados.'<td>'.$linha["entidade"]->id_rbe.'</td>';
                                                $dados = $dados.'<td>'.$linha["entidade"]->regiao.'</td>';
                                                $dados = $dados.'<td>'.$linha["entidade"]->nome.'</td>';
                                                $dados = $dados.'<td>';
                                                foreach ($linha["concelhos"] as $concelho) {
                                                    $dados = $dados." ".$concelho->nome;
                                                }
                                                $dados = $dados.'</td>';
                                                if($linha["entidade"]->disponivel == 0) {
                                                    $dados = $dados.'<td>Disponível</td>';
                                                }
                                                else {
                                                    $dados = $dados.'<td>Indisponível</td>';    
                                                }
                                                $dados = $dados.verificaNull($linha["entidade"]->telemovel);
                                                $dados = $dados.verificaNull($linha["entidade"]->telefone);
                                                $dados = $dados.'<td>';
                                                foreach ($linha["emails"] as $email) {
                                                    $dados = $dados." ".$email->email;
                                                }
                                                $dados = $dados.'</td>';
                                                $dados = $dados.verificaNull($linha["entidade"]->localidade);
                                                $dados = $dados.verificaNull($linha["entidade"]->rua);
                                                if($linha["entidade"]->codPostal != null && $linha["entidade"]->codPostalRua != null) {
                                                    $dados = $dados.'<td>'.$linha["entidade"]->codPostal.'-'.$linha["entidade"]->codPostalRua.'</td>';
                                                }
                                                else {
                                                    $dados = $dados.'<td> --- </td>';
                                                }
                                                $dados = $dados.'<td>
                                                        <a href="#edit" class="edit" data-toggle="modal" onclick="editar('.$linha["entidade"]->id_rbe.')"><i
                                                                class="material-icons" data-toggle="tooltip"
                                                                title="Edit">&#xE254;</i></a>
                                                        <a href="#delete" class="delete" data-toggle="modal" onclick="remover('.$linha["entidade"]->id_rbe.')"><i
                                                                class="material-icons" data-toggle="tooltip"
                                                                title="Delete">&#xE872;</i></a>
                                                        <a href="gerirComunicacoes-'.$linha["entidade"]->id_colaborador.'-'.$linha["entidade"]->nome.'"><img src="http://backofficeAjudaris/images/gerir_comunicacoes.png"></img></a>
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
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="rbes/add">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Adicionar uma Rede de Bibliotecas Escolares</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <label style="font-size: 18px">Informações da Rede de Bibliotecas Escolares</label>
                                    <div class="form-group">
                                        <label>Região</label>
                                        <input type="text" name="regiao" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Nome do Coordenador</label>
                                        <input type="text" name="nome" class="form-control" maxlength="70" requied>
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea name="observacoes" class="form-control" placeholder="Observações" maxlength="200"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Disponibilidade</label>
                                        <select name="disponibilidade">
                                            <option value="0">Disponivel</option>
                                            <option value="1">Indisponivel</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div style="padding-top: 5px">
                                            <label>Concelhos:</label>
                                            <div id="concelhosAssociadosAdd">   
                                            </div>
                                            <input type="text" id="concelhoFormAdd" name="concelho" style="margin-top: 10px;margin-bottom: 20px" class="form-control" maxlength="70" placeholder="Novo Concelho">
                                            <button type="button" class="btn btn-success" onclick="adicionarConcelho(true)">Adicionar Concelho</button>
                                        </div>
                                    </div>
                                    <br><br>
                                    <label style="font-size: 18px">Contactos</label>
                                    <br><br>
                                    <div class="form-group">
                                        <label>Telefone</label>
                                        <input type="tel" id="telefoneAdd" name="telefone" class="form-control" maxlength="15">
                                    </div>
                                    <div class="form-group">
                                        <label>Telemóvel</label>
                                        <input type="tel" id="telemovelAdd" name="telemovel" class="form-control" maxlength="15">
                                    </div>
                                    <div class="form-group">
                                        <div style="padding-top: 5px">
                                            <label>Emails Associados:</label>
                                            <div id="emailsAssociadosAdd">   
                                            </div>
                                            <input type="email" id="emailFormAdd" name="email" style="margin-top: 10px;margin-bottom: 20px" class="form-control" maxlength="70" placeholder="Novo Email">
                                            <button type="button" class="btn btn-success" onclick="adicionarEmail(true)">Adicionar Email</button>
                                            <label style="color: red; margin-top: 10px" id="erroEmailAdd"></label>
                                        </div>
                                    </div>
                                    <label style="font-size: 18px">Morada</label>
                                    <br><br>
                                    <div class="form-group">
                                        <label>Número da Porta</label>
                                        <input type="text" id="numPortaAdd" name="numPorta" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Rua</label>
                                        <input type="text" id="ruaAdd" name="rua" class="form-control" maxlength="50">
                                    </div>
                                    <div class="form-group">
                                        <label>Localidade</label>
                                        <input type="text" id="localidadeAdd" name="localidade" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Distrito</label>
                                        <input type="text" id="distritoAdd" name="distrito" class="form-control" maxlength="70" required>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label>Primeiros dígitos</label>
                                        <input type="number" id="codPostalAdd" name="codPostal" class="form-control" maxlength="10" required>
                                        <label>Segundos dígitos</label>
                                        <input type="number" id="codPostalRuaAdd" name="codPostalRua" class="form-control" maxlength="6" required>
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
                                    <h4 class="modal-title">Editar uma Rede de Bibliotecas Escolares</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <label style="font-size: 18px">Informações do Agrupamento</label>
                                    <div class="form-group">
                                        <label>Região</label>
                                        <input type="text" id="regiao" name="regiao" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Nome do Coordenador</label>
                                        <input type="text" id="nome" name="nome" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Disponibilidade</label>
                                        <select name="disponibilidade" id="disponibilidade">
                                            <option value="0">Disponivel</option>
                                            <option value="1">Indisponivel</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea id="observacoes" name="observacoes" class="form-control" placeholder="Observações" maxlength="200"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div style="padding-top: 5px">
                                            <label style="font-size: 18px">Concelhos Associados:</label>
                                            <div id="concelhoAssociadosEdit"> 
                                            </div>
                                            <input type="text" id="concelhoFormEdit" name="concelho" style="margin-top: 10px;margin-bottom: 20px" class="form-control" maxlength="70" placeholder="Novo Concelho">
                                            <button type="button" class="btn btn-success" onclick="adicionarConcelho(false)">Adicionar Concelho</button>
                                        </div>
                                    </div>
                                    <br><br>
                                    <label style="font-size: 18px">Contactos</label>
                                    <br><br>
                                    <div class="form-group">
                                        <label>Telefone</label>
                                        <input type="tel" id="telefone" name="telefone" class="form-control" maxlength="15">
                                    </div>
                                    <div class="form-group">
                                        <label>Telemóvel</label>
                                        <input type="tel" id="telemovel" name="telemovel" class="form-control" maxlength="15">
                                    </div>
                                    <div class="form-group">
                                        <div style="padding-top: 5px">
                                            <label style="font-size: 18px">Emails Associados:</label>
                                            <div id="emailsAssociadosEdit"> 
                                            </div>
                                            <input type="email" id="emailFormEdit" name="email" style="margin-top: 10px;margin-bottom: 20px" class="form-control" maxlength="70" placeholder="Novo Email">
                                            <button type="button" class="btn btn-success" onclick="adicionarEmail(false)">Adicionar Email</button>
                                            <label style="color: red; margin-top: 10px" id="erroEmailEdit"></label>
                                        </div>
                                    </div>
                                    <label style="font-size: 18px">Morada</label>
                                    <br><br>
                                    <div class="form-group">
                                        <label>Número da Porta</label>
                                        <input type="text" id="numPorta" name="numPorta" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Rua</label>
                                        <input type="text" id="rua" name="rua" class="form-control" maxlength="50">
                                    </div>
                                    <div class="form-group">
                                        <label>Localidade</label>
                                        <input type="text" id="localidade" name="localidade" class="form-control" maxlength="50" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Distrito</label>
                                        <input type="text" id="distrito" name="distrito" class="form-control" maxlength="70" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Primeiros dígitos</label>
                                        <input type="text" id="codPostal" name="codPostal" class="form-control" maxlength="10" required>
                                        <label>Segundos dígitos</label>
                                        <input type="text" id="codPostalRua" name="codPostalRua" class="form-control" maxlength="6" required>
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
                <div id="delete" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="" id="formDelete">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Remover a Rede de Bibliotecas Escolares</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>Tem a certeza que deseja remover a rede de bibliotecas escolares?</p>
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
<script src="{{ asset('js/edicaoEmails.js') }}"></script>
<script src="{{ asset('js/paginas/pagRbes.js') }}"></script>
</html>