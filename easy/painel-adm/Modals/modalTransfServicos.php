<?php
// Início do script PHP
$pag = 'consversao';
session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

$id_usuario    = $_SESSION['id_usuario'];    // user_criacao ou user_alteracao
$nome_usuario  = $_SESSION['nome_usuario'];

// Recebe dados via POST
$id = isset($_POST['id']) ? intval($_POST['id']) : '';



// captura os dados da transferencia de serviços informada
if ($id){
 
  $query_transferencia_servicos = $pdo->prepare("SELECT * 
                                      FROM venda_conversoes 
                                      WHERE id = :id
                                            AND tipo = 'transferencia servicos'");
  $query_transferencia_servicos->execute([':id' => $id]);
  $transferencias= $query_transferencia_servicos->fetchAll(PDO::FETCH_ASSOC);

  $id_venda_destino = $transferencias[0]['id_venda_destino'];
  $id_cliente_origem = $transferencias[0]['id_cliente_origem'];
  $id_comum = $transferencias[0]['id_comum'];
  $data = $transferencias[0]['data'];

  $query_cliente_origem=$pdo->prepare("SELECT id, nome, cpf, sexo, celular, foto, email, saldo FROM clientes WHERE id = :id_cliente");
  $query_cliente_origem -> execute([':id_cliente'=> $id_cliente_origem]);
  $cliente_origem = $query_cliente_origem->fetch(PDO::FETCH_ASSOC);

  $id_cliente = $cliente_origem['id'];
  $cpf_cliente = $cliente_origem['cpf'];
  $celular_cliente = $cliente_origem['celular'];
  $email_cliente = $cliente_origem['email'];
  $foto_cliente = $cliente_origem['foto'];
  $sexo_cliente = $cliente_origem['sexo'];
  $saldo_cliente= $cliente_origem['saldo'];


  $query_venda = $pdo->prepare("SELECT * 
                                          FROM venda
                                          WHERE id = :id_venda_destino
                                                ");
  $query_venda->execute([':id_venda_destino' => $id_venda_destino]);
  $venda= $query_venda->fetch(PDO::FETCH_ASSOC);

  $valor_transferencia_servicos = $venda['saldo'];
  $saldo_cliente_anterior = $saldo_cliente - $valor_transferencia_servicos;




  if($saldo_cliente_anterior<0){
    $classeSaldoCliente='num-negativo';
  }else{
    $classeSaldoCliente='num-positivo';
  }

  if($saldo_cliente<0){
    $classeNovoSaldoCliente='num-negativo';
  }else{
    $classeNovoSaldoCliente='num-positivo';
  }
    // 1) já tendo $transferencias vindo do fetchAll…
    $query_item = $pdo->prepare("
        SELECT 
            data_venda,
            tipo_venda,
            quantidade, 
            convertidos, 
            realizados, 
            transferidos, 
            descontados 
        FROM venda_itens 
        WHERE id = :id
    ");

    $transferidos = [];

    foreach ($transferencias as $transf) {
        // busca os dados do item
        $query_item->execute([':id' => $transf['id_item_servico_origem']]);
        $item = $query_item->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // calcula disponíveis = (qtd – convertidos – realizados – transferidos – descontados) + qtd da conversão
            $disp = 
                ($item['quantidade'] 
                - $item['convertidos'] 
                - $item['realizados'] 
                - $item['transferidos'] 
                - $item['descontados']
                )
                + $transf['quantidade'];
                
                $vTot = $transf['valor_un']*$transf['quantidade'];

        } else {
            // se não encontrar o item, deixa só a qtd de conversão
            $disp = $transf['quantidade'];
            $vTot = $transf['valor_un'];
        }

        // adiciona a chave 'disponiveis' ao array de conversão
        $transf['disponiveis'] = $disp;
        $transf['valor_total'] = $vTot;
        $$transferidos[] = $transf;
        

    }
} else{

$classeNovoSaldoCliente='num-positivo';




}

?>


<div class="modal fade" tabindex="-1" style="z-index: 95000;" id="modalTransferencia_servicos" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Transferências de Serviços<?= $titulo_modal ?></h5>

          <button type="button" class="btn-fecha-modal" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>
      </div>  
      <form method="POST" id="formTransferencia_servicos">
        <div class="modal-body" style="min-height: 200px; overflow-y: auto; background-color:#F8F8F8;">
          <div class="row justify-content-end" id="mod-venda-ft-ln" style="text-align:right; max-height:15px; margin-top:-20px;">
                    <span style="font-size: 12px;">Usuário do Sistema: <?= $id_usuario . ' - ' . $nome_usuario ?></span>
          </div>
          <div class="row mt-2">
            <div class="col-12">
              <div  id="bloco-cliente-recebe-transferencia">
                <label for="nome-cliente-recebe" class="form-group label-bl-fin">Cliente que TRANSFERE os Serviços :</label>
                <br>
                  <div class="row">
                    <div class="col-md-12" style="min-height:40px; ">
                      <div class="row" style="padding-left: 15px; width: 95%" id="linha-cliente-mod-transferencia_servicos">
                        <input type="hidden" name="id-transferencia_servicos" id="id-transferencia_servicos" value="<?= isset($id) ? $id : '' ?>">
                        <input type="hidden" name="id-cliente" id="id-cliente"  value="<?= isset($id_cliente) ? $id_cliente : '' ?>">
                        <input type="hidden" id="sexo-cliente"  value="<?= isset($sexo_cliente) ? $sexo_cliente : '' ?>">
                        <div class="col-auto" id="col-img-foto-cliente" style="padding-left: 10px; width: 40px; display:<?= (!$foto_cliente) ? 'none' : 'block' ?>"> <!-- Foto do cliente -->
                          <div id="divImgConta">
                            <img style="width: 35px; border-radius: 50%;" id="img-foto-cliente-modTransferencia_servicos" <?=$foto_cliente?'src="../img/clientes/'. $foto_cliente . '"':'' ?>>
                          </div>
                        </div>
                        <div class="col-auto mb-3">
                          <label for="nome" class="form-group" style="margin-left:10px;" >Nome</label>
                          <div class="input-group">
                            <input 
                              type="text" 
                                class="form-control nome-cliente input-nome-cliente <?= ($id > 0 || $origemAgenda) ? 'blockItem' : '' ?>"
                                style="min-width:185px; max-width:max-content;"
                                id="nome-cliente" 
                                autocomplete="off"   
                                name="nome-cliente" 
                                placeholder="Nome" 
                                required 
                                value="<?= isset($nome_cliente) ? $nome_cliente : '' ?>">  
                            <button 
                                  class="btn btn-outline-secondary" 
                                  type="button" 
                                  id="btn-adicionar-cliente"
                                  <?=isset($id) ? 'style="border:none;"' : '' ?>
                                  title="<?= isset($id_cliente) ? 'Visualizar cliente' : 'Adicionar novo cliente' ?>"
                                  onclick="abrirModal('modalClientes', document.getElementById('id-cliente').value)">
                                  <i class="bi <?= ($id_cliente) ? 'bi-eye" style="border: none;"' : 'bi-person-plus' ?>" id= "ico-inputCliente"></i>
                            </button>
                          </div>
                          <ul id="lista-clientes" class="sugestoes lista-clientes"></ul>
                        </div>
                        <div class="col-auto mb-3 blClienteTransfere">
                          <label for="cpf" class="form-group " style="margin-left:10px;" >CPF</label>
                          <input type="text" class="form-control input-cpf-cliente <?= isset($id)? ' blockItem':'' ?>" id="cpf-cliente" name="cpf-cliente" placeholder=""  value="<?= isset($cpf_cliente) ? $cpf_cliente : '' ?>">
                          <div class="invalid-feedback" id="cpfError" style="display: none; color: red;">
                            CPF inválido!
                          </div>
                        </div>
                        <div class="col-auto mb-3 blClienteTransfere">
                          <label for="celular" class="form-group" style="margin-left:10px;" >Celular</label>
                          <input type="text" class="form-control input-celular-cliente<?= isset($id)? ' blockItem':'' ?>" id="celular-cliente" name="celular-cliente" placeholder="" required value="<?= isset($celular_cliente) ? $celular_cliente : '' ?>">
                        </div>
                        <div class="col-auto mb-3 blClienteTransfere">
                          <label for="email" class="form-group" style="margin-left:10px;" >Email</label>
                          <input type="text" class="form-control input-email-cliente <?= isset($id)? ' blockItem':'' ?>" id="email-cliente" name="email-cliente" placeholder="" value="<?= isset($email_cliente) ? $email_cliente : '' ?>">
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
            </div> <!--fechamento da col 10-->
            
          </div> <!-- fechamento da linha-->

          <hr class="mt-1" style="border:none; border-bottom: 1px solid #000;">

          <div id="transferencia-container" style="<?= ($id) ? 'display:block' : 'display:none' ?> ;" >        <!--- encapsulamento do conteudo da proposta-->
            <div class="row">
              <div class="col-md-12" id="coluna-esquerda">
                <!--DADOS DA TRANSFERENCIA DE SERVIÇOS-->
                 <div class="container px-1 mb-3" id="resumoTransferenciaServicos">
                    <div class="row">
                      <div class="col-12">
                        <div id="bloco-valor-transferencia_servicos">
                          <label for="sp-valor-transferencia_servicos" class="form-group label-bl-fin">A Transferir:</label>
                          <span id="sp-valor-transferencia_servicos" style="margin-left:5px; padding-left:10px;" class="num-positivo"><?= $itens_transferencia_servicos ?></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <hr style=" border:none; border-bottom: 1px solid #000;">

                  <div class="container px-1 mb-3" id="clienteRecebeTransferenciaServicos" >
                    <div class="row">
                      <div class="col-12">
                        <div id="bloco-cliente-recebe-transferencia">
                          <label for="nome-cliente-recebe" class="form-group label-bl-fin">Cliente que RECEBE os Serviços</label>
                          <br>
                          <div class="row align-items-center" style ="padding-left: 15px; width: 95%" id="linha-cliente-mod-transferencia_servicos_destino">
                            <input type="hidden" name="id-transferencia_servicos_destino" id="id-transferencia_servicos_destino" value="<?= isset($id) ? $id : '' ?>">
                            <input type="hidden" name="id-cliente-destino" id="id-cliente-destino" value="<?= isset($id_cliente_destino) ? $id_cliente_destino : '' ?>">
                            <input type="hidden" id="sexo-cliente-destino" value="<?= isset($sexo_cliente_destino) ? $sexo_cliente_destino : '' ?>">

                            <div class="col-auto" id="col-img-foto-cliente-destino" style="margin-top:-10px; padding-left:10px; width:40px; display:<?= (!$foto_cliente_destino) ? 'none' : 'block' ?>;">
                              <div id="divImgContaDestino">
                                <img style="width:35px; border-radius:50%;" id="img-foto-cliente-modTransferenciaServicos_destino" <?= $foto_cliente_destino ? 'src="../img/clientes/'.$foto_cliente_destino.'"' : '' ?>>
                              </div>
                            </div>
                            <div class="col-auto mb-3">
                              <label for="nome-cliente-recebe" class="form-group" style="margin-left:10px;">Nome</label>
                              <div class="input-group">
                                <input type="text" style="min-width:185px;" class="form-control nome-cliente input-nome-cliente<?= ($id>0) ? ' blockItem' : '' ?>" id="nome-cliente-recebe" name="nome-cliente-recebe" placeholder="Nome" autocomplete="off" required value="<?= isset($nome_cliente_destino) ? $nome_cliente_destino : '' ?>">
                                <button class="btn btn-outline-secondary" style="border:none;" type="button" id="btn-adicionar-cliente-destino" <?= isset($id_cliente_destino) ? 'style="border:none;"' : '' ?> title="<?= isset($id_cliente_destino) ? 'Visualizar cliente' : 'Adicionar novo cliente' ?>" onclick="abrirModal('modalClientes', document.getElementById('id-cliente-destino').value)">
                                  <i class="bi <?= ($id_cliente_destino) ? 'bi-eye' : 'bi-person-plus' ?>" id="ico-inputClienteDestino"></i>
                                </button>
                              </div>
                              <ul id="lista-clientes-destino" class="sugestoes lista-clientes"></ul>
                            </div>
                            <div class="col-auto mb-3 blClienteRecebe">
                              <label for="cpf-cliente-recebe" class="form-group" style="margin-left:10px;">CPF</label>
                              <input type="text" readonly class="form-control input-cpf-cliente-recebe<?= isset($id) ? ' blockItem' : '' ?>" id="cpf-cliente-recebe" name="cpf-cliente-recebe" value="<?= isset($cpf_cliente_destino) ? $cpf_cliente_destino : '' ?>">
                            </div>
                            <div class="col-auto mb-3 blClienteRecebe">
                              <label for="celular-cliente-recebe" class="form-group" style="margin-left:10px;">Celular</label>
                              <input type="text" readonly class="form-control input-celular-cliente-recebe<?= isset($id) ? ' blockItem' : '' ?>" id="celular-cliente-recebe" name="celular-cliente-recebe" required value="<?= isset($celular_cliente_destino) ? $celular_cliente_destino : '' ?>">
                            </div>
                            <div class="col-auto mb-3 blClienteRecebe">
                              <label for="email-cliente-recebe" class="form-group" style="margin-left:10px;">Email</label>
                              <input type="text" readonly class="form-control input-email-cliente-recebe<?= isset($id) ? ' blockItem' : '' ?>" id="email-cliente-recebe" name="email-cliente-recebe" value="<?= isset($email_cliente_destino) ? $email_cliente_destino : '' ?>">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>


                  <!--navegação das abas -->
                  <ul style="cursor:pointer; height:30px;" class="nav nav-tabs" id="v-tab" role="tablist">
                    <li class="nav-link active tab-btn" id="itens-tab" data-bs-toggle="tab" data-bs-target="#aba-itens" role="tab" aria-controls="itens" aria-selected="true">
                      itens
                    </li>
                    <li class="nav-link tab-btn listTable" id="informacoes-tab" data-bs-toggle="tab" data-bs-target="#aba-informacoes" role="tab" aria-controls="informacoes" aria-selected="false">
                      Informações
                    </li>
                  </ul> 


                  <!--Contuúdo das TABS -->
                  <div class="tab-content"  id="v-tabContent">
                    
                    <!--TAB ITENS -->
                    <div class="tab-pane fade show active" id="aba-itens" role="tabpanel" aria-labelledby="itens-tab" style="padding:10px;">
                      <div class="table-responsive" style="overflow-x:auto;">
                        <table id="tabela-itensTransferencia_servicos" class="dataTable dTlinhaFina">
                          <input type="hidden" name="id-comum" id="id-comum" value="<?= $id_comum ??''?>">
                          <thead>
                            <tr>
                              <th style= "min-width: 70px;">Data:</th>
                              <th style= "min-width: 100px;">Adquirido:</th>
                              <th style= "min-width: 230px;">Serviço:</th>
                              <th style="display: none;"></th>
                              <th style="min-width: 30px;">Disp</th>
                              <th style= "min-width: 100px;">$ Unitário</th>
                              <th style= "min-width:30px;">Un</th>
                              <th style= "min-width: 100px;">$ Total</th>
                            </tr>
                          </thead>
                          <tbody id="itens-body">
                          <?php if (!empty($id)): ?>
                            <?php foreach ($transferidos as $it): ?>
                                <tr>
                                  <!-- Botão de remover à esquerda -->
                                  <td>
                                    <input name="data-venda[]" type="date" class="blockitem form-control" readOnly value="<?php $it['data_venda']?>">
                                  </td> 
                                  <td>
                                    <input name="tipo-venda[]" type="text" class="blockitem form-control" readOnly value="<?php $it['tipo_venda']?>">
                                  </td>
                                  <td>
                                    <input name="servico[]" class="blockitem" readOnly value="<?php $it['servico']?>">
                                    <input hidden name="id_servico[]" value="<?php $it['id_servico']?>">
                                  </td>                                  
                                  <td hidden>
                                    <input type="hidden" name="id_venda[]" value="<?= $it['id_venda'] ?>">
                                    <input type="hidden" name="id_transferencia_servicos[]" value="<?= $it['id'] ?>">
                                    <input type="hidden" name="id_venda_item[]" value="<?=$it['id_item_servico_origem']?>">
                                  </td>
                                  <td>
                                    <input name="qtd-disp[]" class="blockitem" readOnly value="<?php $it['disponiveis']?>">
                                  </td>
                                  <td>
                                    <input name="valor-unitario[]" class="blockItem" readOnly value="<?php $it['valor_un']?>">
                                  </td>
                                  <td>
                                    <input name="qtd-transf[]" class="form-control"  value="<?php $it['quantidade']?>">
                                    <input type="hidden" name="qtd-transf-anterior[]" class="form-control" readOnly value="<?php $it['quantidade']?>">
                                  </td>
                                  <td>
                                    <input name="valor-total-item[]" class="form-control" readOnly value="<?php $it['valor_total']?>">
                                  </td>
                                </tr>
                              <?php endforeach; ?>
                            <?php endif; ?>
                           </tbody>
                        </table>
                        
                      </div>
                    </div>
                 <!-- Tab Informações -->
                  <div class="tab-pane fade show" id="aba-informacoes" role="tabpanel" aria-labelledby="informacoes-tab">
                      <!-- Conteúdo para informações -->
                    <textarea class="form-control" name="informacoes" style = "height:200px;" ><?= $informacoes ?></textarea>
                  </div>

                
                
                </div> <!-- fechamnento do conteudo das abas -->


  
              
              
             
              </div>
            
            </div>

           

        
          </div> <!--fechamento do encapsulamento do conteudo da proposta menos os dados do cliente -->

            

        </div>  <!-- Fechameto do body do modal-->

        <div class="modal-footer" <?=($id>0)? 'hidden' : '' ?> id="footer" >
          
          <div class="footer-left">
            <div id="mensagem"></div>
          </div>
          
          <div>
            <button type="button" id="btn-fechar_venda" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            <button type="submit" id="btn-salvar_venda" class="btn btn-primary"><?=($id>0)? 'Atualizar ' : 'Gravar ' ?></button>
          </div>

        </div>

      </form>
    </div>
  </div>
</div>




<script>

$('#modalTransfServicos').on('hidden.bs.modal', function () {
  $(this).remove(); // remove o HTML
});
</script>






<!-- Arrays de produtos e serviços -->
<script>

  // ARRAYS //

  var array$transferidos = <?php echo json_encode($$transferidos); ?>;
  var cliente    = <?php echo json_encode($cliente_origem); ?>;
  var valor_transferencia_servicos = <?php echo json_encode($valor_transferencia_servicos); ?>;
  var id_comum = <?php echo json_encode($id_comum); ?>;
  var id_venda_destino = <?php echo json_encode($id_venda_destino); ?>;

  var SaldoFinalCliente =  <?php echo json_encode($saldo_cliente); ?>;
  var SaldoCliente =  <?php echo json_encode($saldo_cliente_anterior); ?>;
  //$var Variaveis dos totais
  function sanitizeNumber(value) {
    return Number(value) || 0;
  }

  
</script>

<?php
  // Caminho absoluto até o seu JS
  $path = __DIR__ . '/Modals/modalTransfServicos.js';
  // Timestamp da última modificação
  $version = file_exists($path) ? filemtime($path) : time();
?>
<script src="Modals/modalTransfServicos.js?v=<?= $version ?>" defer></script>