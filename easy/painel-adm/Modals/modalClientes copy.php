<?php


$pag = 'clientes';
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id_cliente = $_GET['id'];

    // Preparando a consulta PDO
    $query = $pdo->prepare("SELECT * FROM clientes WHERE id = :id_cliente");
    $query->execute([':id_cliente' => $id_cliente]);
    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    $total_reg = @count($res);
    $titulo_modal = "Editar Cadastro do Cliente";
    $tipo_cadastro= "edicao";
    if($total_reg > 0){ 
            $nome = $res[0]['nome'];
            $email = $res[0]['email'];
            $cpf = $res[0]['cpf'];
            $senha = $res[0]['senha'];
            $nivel = $res[0]['nivel'];
            $parts = explode('/', $res[0]['aniversario']);
            $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
            $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
            $year = strlen($parts[2]) == 2 ? '20' . $parts[2] : $parts[2];

            $aniversario = implode('/', [$day, $month, $year]);
            $aniversario = $res[0]['aniversario'];
            $telefone = $res[0]['telefone'];
            $celular = $res[0]['celular'];
            $sexo = $res[0]['sexo'];
            $como_conheceu = $res[0]['como_conheceu'];
            $cep = $res[0]['cep'];
            $endereco = $res[0]['endereco'];
            $numero = $res[0]['numero'];
            $estado = $res[0]['estado'];
            $cidade = $res[0]['cidade'];
            $bairro = $res[0]['bairro'];
            $profissao = $res[0]['profissao'];
            $cadastrado = $res[0]['cadastrado'];
            $obs = $res[0]['obs'];
            $rg = $res[0]['rg'];
            $complemento = $res[0]['complemento'];

                $foto_edit = $res[0]['foto']; // mostra a foto no editar
    }



  
} else {
  $titulo_modal = "Novo Cadastro de Cliente";
  $tipo_cadastro = "novo";
  // Como é um novo cadastro, os campos do formulário ficariam vazios
}

?>
<style>
#mensagem-sucesso {
    display: none; /* Escondido inicialmente */
    margin-top: 20px;
    color: green; /* Cor do texto */
    background-color: #ebf9eb; /* Cor de fundo */
    border: 1px solid green; /* Borda */
    text-align: center; /* Alinhamento do texto */
    padding: 10px; /* Espaçamento interno */
    border-radius: 5px; /* Bordas arredondadas */
}
</style>


<?php

echo '
<div class="modal fade" tabindex="-1" style ="z-index: 95000;" id="modalCadCliente" data-bs-backdrop="static">
	<div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">' . $titulo_modal . '</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="form">
                <div class="modal-body" style="height: auto;">
                <div id="mensagem-sucesso" style="display: none;"></div>
                    <div class="row">
                        
                        <div  ' . ($tipo_cadastro == "novo" ? 'style="visibility: hidden; width:40px;"' : ' class="col-md-1 ') . '>
                            <div class="nav flex-column nav-tabs" id="v-tab" role="tablist" aria-orientation="vertical">
                                
                             <div>
                            <a class="nav-link active" id="cadastro-tab" data-bs-toggle="tab" href="#cadastro" role="tab" aria-controls="cadastro" aria-selected="true">Cadastro</a>
                               
                                <!-- Condicionais para exibir ou ocultar baseado no tipo de cadastro -->
                               
                                    <a class="nav-link" id="documentos-tab" data-bs-toggle="tab" href="#documentos" role="tab" aria-controls="documentos" aria-selected="false">Documentos</a>
                                    <a class="nav-link" id="agendamentos-tab" data-bs-toggle="tab" href="#agendamentos" role="tab" aria-controls="agendamentos" aria-selected="false">Agendamentos</a>
                                    <a class="nav-link" id="atendimentos-tab" data-bs-toggle="tab" href="#atendimentos" role="tab" aria-controls="atendimentos" aria-selected="false">Atendimentos</a>
                                    <a class="nav-link" id="planos-tab" data-bs-toggle="tab" href="#planos" role="tab" aria-controls="planos" aria-selected="false">Planos</a>
                                    <a class="nav-link" id="financeiro-tab" data-bs-toggle="tab" href="#financeiro" role="tab" aria-controls="financeiro" aria-selected="false">Financeiro</a>
                                    <a class="nav-link" id="galeria-tab" data-bs-toggle="tab" href="#galeria" role="tab" aria-controls="galeria" aria-selected="false">Galeria</a>
                                    <!-- Adicione outras abas conforme necessário -->
                                </div>
                            </div>
                        </div>
                        <div class="col-10">
                            <div class="tab-content" id="v-tabContent">
                                <div class="tab-pane fade show active" id="cadastro" role="tabpanel" aria-labelledby="cadastro-tab">Dados do Cliente

                                <div class="tab-pane fade show active" id="cadastro" role="tabpanel" aria-labelledby="cadastro-tab">
                    				
                                <div class="row">
                                    <!-- <small><div align="right" class="mt-1" id="mensagem"></div></small> -->
                                        
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input type="file" style="display:none;" id="inpt-foto_cliente" name="imagem" onChange="carregarImg();">
                                                


                                            </div>							
                                            <div id="divImgConta" class="mt-2">
                                                ' . (@$foto_edit != "" ? '<img style="cursor: pointer; border-radius:50%;" src="../img/' . $pag . '/' . $foto_edit . '"   width="100px" height="100px" id="img-foto_cliente">' : '<img style="cursor: pointer; border-radius:50%;" src="../img/' . $pag . '/' . 'sem-foto.jpg" width="100px" height="100px" id="img-foto_cliente">') . '
                                            </div>
                                    
                                        </div>
                                          <div class="col-md-4">
                                              
                                              <label for="exampleFormControlInput1" class="form-group">Nome</label>
                                              <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" required="" value="' .  @$nome . '">
                                        </div>		
                                        <div class="col-md-2">
                                              
                                              <label  class="form-group">Aniversário</label>
                                              <input type="text" class="form-control" id="aniversario" name="aniversario" placeholder="Aniversario " value="' .  @$aniversario . '"> 
                                              </div>
                                         <div class="col-md-3">
                                             
                                              <label for="exampleFormControlInput1" class="form-group">Gênero</label>															
                                              <select class="form-select" aria-label="Default select example" name="sexo">
                                                    <option ' . (@$sexo == 'Feminino' ? 'selected' : '') . ' value="Feminino">Feminino</option>
                                                    <option ' . (@$sexo == 'Masculino' ? 'selected' : '') . ' value="Masculino">Masculino</option>
                                                    <option ' . (@$sexo == 'Não Informado' ? 'selected' : '') . ' value="Não Informado">Não Informado</option>									
                                            </select>
                                         </div>
                                  </div>					
                                         
                                 <div class="row">
                                                 <div class="col-md-2">
                                                    <label for="exampleFormControlInput1" class="form-group">Celular</label>
                                                    <input type="text" class="form-control" id="celular" name="celular" placeholder="Celular"  value="' .  @$celular . '">
                                                </div> 
                                                <div class="col-md-2">
                                                    <label for="exampleFormControlInput1" class="form-group">Telefone</label>
                                                    <input type="text" class="form-control" id="telefone" name="telefone" placeholder="Telefone"  value="' .  @$telefone . '">
                                                </div> 
                                                <div class="col-md-4">
                                                    <label for="exampleFormControlInput1" class="form-group">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email"  value="' .  @$email . '">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="exampleFormControlInput1" class="form-group">Como nos Conheceu?</label>
                                                    <select class="form-select " aria-label="Default select example" id="como_conheceu" name="como_conheceu" placeholder="Como Conheceu">
                                                    <option ' . (@$como_conheceu == 'Pesquisa no google' ? 'selected' : '') . ' value="Pesquisa Google">Pesquisa no google</option>
                                                    <option ' . (@$como_conheceu == 'Anúncio em um site- rede de Display' ? 'selected' : '') . ' value="Anúncio em um site- rede de Display">Anúncio em um site- rede de Display</option>
                                                    <option ' . (@$como_conheceu == 'Email Marketing' ? 'selected' : '') . ' value="Email Marketing">Email Marketing</option>
                                                    <option ' . (@$como_conheceu == 'Instagram' ? 'selected' : '') . ' value="Instagram">Instagram</option>
                                                    <option ' . (@$como_conheceu == 'Facebook' ? 'selected' : '') . ' value="Facebook">Facebook</option>
                                                    <option ' . (@$como_conheceu == 'Indicação' ? 'selected' : '') . ' value="Indicação">Indicação</option>
                                                    
                                                    </select>
                                                </div>  
                                </div>
                               
                                    
                              <div class="row">
                                        <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="exampleFormControlInput1" class="form-group">CPF</label>
                                                    <input type="text" class="form-control" id="cpf" name="cpf" placeholder="CPF" required="" value="' .  @$cpf . '">
                                                </div>  
                                        </div>
                
                                        <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="exampleFormControlInput1" class="form-group">RG</label>
                                                    <input type="text" class="form-control" id="rg" name="rg" placeholder="RG"  value="' .  @$rg . '">
                                                </div>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="exampleFormControlInput1" class="form-group">Profissão</label>
                                            <input type="text" class="form-control" id="profissao" name="profissao" placeholder="Profissão"  value="' .  @$profissao . '">
                                        </div>
                               </div>

                            <!-- linha-->
                             <div class="row">
                                    <div class="col-md-9">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-group">Endereço</label>
                                            <input type="text" class="form-control" id="endereco" name="endereco" placeholder="Endereçso"  value="' .  @$endereco . '">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <label for="exampleFormControlInput1" class="form-group">Numero</label>
                                            <input type="text" class="form-control" id="numero" name="numero" placeholder="Num."  value="' .  @$numero . '">
                                        </div>  
                                    </div>		
                            </div>
                                
                                
                            <!-- linha-->
                            <div class="row">
                                     <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-group">Complemento</label>
                                            <input type="text" class="form-control" id="complemento" name="complemento" placeholder="complemento"  value="' .  @$complemento . '">
                                        </div>  
                                    </div>	
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-group">Bairro</label>
                                            <input type="text" class="form-control" id="bairro" name="bairro" placeholder="Bairro"  value="' .  @$bairro . '">
                                        </div>  
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-group">Cidade</label>
                                            <input type="text" class="form-control" id="cidade" name="cidade" placeholder="Cidade"  value="' .  @$cidade . '">
                                        </div>
                                    </div>						
                            </div>
                                
                                
                            <!-- linha-->
                            <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-group">Estado</label>
                                            <input type="text" class="form-control" id="estado" name="estado" placeholder="Estado"  value="' .  @$estado . '">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-group">Cep</label>
                                            <input type="text" class="form-control" id="cep" name="cep" placeholder="Cep"  value="' .  @$cep . '">
                                        </div>  
                                    </div>						
                                </div>
                                
                                
                                <!-- linha-->
                                <div class="row">
                                    <div class="col-md-15">
                                        <div class="mb-5">
                                            <label for="exampleFormControlInput1" class="form-group">Observações</label>																
                                            <textarea class="form-control" id="obsservacoes" name="obsservacoes" placeholder="Observações" maxlength="255">' . @$obs . '</textarea>

                                        </div>
                                    </div>						
                                </div>
                                
                                 <div class="row">
                                       <div class="col-md-2">
                                            <label for="exampleFormControlInput1" class="form-group">Nível</label>
                                            <select class="form-select " aria-label="Default select example" name="nivel">
                                                <option ' . (@$nivel == 'Cliente' ? 'selected' : '') . ' value="Cliente">Cliente</option>

                                             </select>
                                        </div> 
                                       <div class="col-md-2">
                                            <label for="exampleFormControlInput1" class="form-group">Data Cadastro </label>
                                            <input type="date" class="form-control" id="cadastrado" name="cadastrado" placeholder="Data Cadastro" value="' .  (isset($cadastrado) && $cadastrado != '' ? $cadastrado : '') . '">

                                        </div>  
                                        <div class="col-md-3">
                                            <label for="exampleFormControlInput1" class="form-group">Senha</label>
                                            <!--<input type="password" class="form-control" id="senha" name="senha" placeholder="Senha"  value="' .  @$senha . '">-->
                                        </div>
                                        
                                    
                                </div>		
                                
                                
                                
                                
                                <div class="modal-footer">
                                    
                                            <button type="button" id="btn-fechar_cliente" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                            <button type="submit"name="btn-salvar" id="btn-salvar_cliente"  class="btn btn-primary">Salvar</button>
                                            
                                            <input name="id" type="hidden" value="' .  @$_GET['id'] . '">
                                            <input name="antigo" type="hidden" value="' .  @$cpf . '">
                                            <input name="antigo2" type="hidden" value="' .  @$email . '">
                                </div>  







                                </div>



                                
                                
                                <!-- Adicione outros painéis de conteúdo conforme necessário -->
                            </div>
                        </div>
                    </div>
                </div> <!-- FECHANDO O CONTEÚDO DAS TABS -->
            </form>
        </div>
    </div>
</div>';

?>



