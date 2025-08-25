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



// captura os dados da cconversão informada
if ($id){
 
  $query_conversao = $pdo->prepare("SELECT * 
                                      FROM venda_conversoes 
                                      WHERE id = :id
                                            AND tipo = 'conversao'");
  $query_conversao->execute([':id' => $id]);
  $convercoes= $query_conversao->fetchAll(PDO::FETCH_ASSOC);

  $id_venda_destino = $convercoes[0]['id_venda_destino'];
  $id_cliente_origem = $convecoes[0]['id_cliente_origem'];
  $id_comum = $convecoes[0]['id_comum'];
  $data = $convecoes[0]['data'];

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

  $valor_conversao = $venda['saldo'];
  $saldo_cliente_anterior = $saldo_cliente - $valor_conversao;




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
    // 1) já tendo seu $conversoes vindo do fetchAll…
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

    $convertidos = [];

    foreach ($conversoes as $conv) {
        // busca os dados do item
        $query_item->execute([':id' => $conv['id_item_servico_origem']]);
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
                + $conv['quantidade'];
                
                $vTot = $conv['valor_un']*$conv['quantidade'];

        } else {
            // se não encontrar o item, deixa só a qtd de conversão
            $disp = $conv['quantidade'];
            $vTot = $conv['valor_un'];
        }

        // adiciona a chave 'disponiveis' ao array de conversão
        $conv['disponiveis'] = $disp;
        $conv['valor_total'] = $vTot;
        $convertidos[] = $conv;
        

    }
} else{

$classeNovoSaldoCliente='num-positivo';




}

?>


<div class="modal fade" tabindex="-1" style="z-index: 95000;" id="modalConversao" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Converter Serviços em Créditos</h5>

          <button type="button" class="btn-fecha-modal" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>
      </div>  
      <form method="POST" id="formConversao">
        <div class="modal-body" style="min-height: 200px; overflow-y: auto; background-color:#F8F8F8;">
          <div class="row justify-content-end" id="mod-venda-ft-ln" style="text-align:right; max-height:15px; margin-top:-20px;">
                    <span style="font-size: 12px;">Usuário do Sistema: <?= $id_usuario . ' - ' . $nome_usuario ?></span>
          </div>
          <div class="row">
            <div class="col-md-12" style="min-height:40px; ">
              <div class="row" id="linha-cliente-mod-conversao">
                <input type="hidden" name="id-conversao" id="id-conversao" value="<?= isset($id) ? $id : '' ?>">
                <input type="hidden" name="id-cliente" id="id-cliente"  value="<?= isset($id_cliente) ? $id_cliente : '' ?>">
                <input type="hidden" id="sexo-cliente"  value="<?= isset($sexo_cliente) ? $sexo_cliente : '' ?>">
                <div class="col-auto" id="col-img-foto-cliente" style=" margin-top: -10px; padding-left: 10px; width: 40px; display:<?= (!$foto_cliente) ? 'none' : 'block' ?>"> <!-- Foto do cliente -->
                  <div id="divImgConta">
                    <img style="width: 35px; border-radius: 50%;" id="img-foto-cliente-modConversao" <?=$foto_cliente?'src="../img/clientes/'. $foto_cliente . '"':'' ?>>
                  </div>
                </div>
                <div class="col-auto mb-3">
                  <label for="nome" class="form-group" style="margin-left:10px;" >Cliente:</label>
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
                <div class="col-auto mb-3 blClienteConverte">
                  <label for="cpf" class="form-group " style="margin-left:10px;" >CPF</label>
                  <input type="text" class="form-control input-cpf-cliente <?= isset($id)? ' blockItem':'' ?>" id="cpf-cliente" name="cpf-cliente" placeholder=""  value="<?= isset($cpf_cliente) ? $cpf_cliente : '' ?>">
                  <div class="invalid-feedback" id="cpfError" style="display: none; color: red;">
                    CPF inválido!
                  </div>
                </div>
                <div class="col-auto mb-3 blClienteConverte">
                  <label for="celular" class="form-group" style="margin-left:10px;" >Celular</label>
                  <input type="text" class="form-control input-celular-cliente<?= isset($id)? ' blockItem':'' ?>" id="celular-cliente" name="celular-cliente" placeholder="" required value="<?= isset($celular_cliente) ? $celular_cliente : '' ?>">
                </div>
                <div class="col-auto mb-3 blClienteConverte">
                  <label for="email" class="form-group" style="margin-left:10px;" >Email</label>
                  <input type="text" class="form-control input-email-cliente <?= isset($id)? ' blockItem':'' ?>" id="email-cliente" name="email-cliente" placeholder="" value="<?= isset($email_cliente) ? $email_cliente : '' ?>">
                </div>
              </div>
            </div> <!--fechamento da col 10-->
            <hr style=" border:none; border-bottom: 1px solid #000; margin-top:-10px;">
          </div> <!-- fechamento da linha-->

          <div id="conversao-container" style="<?= ($id) ? 'display:block' : 'display:none' ?> ;" >        <!--- encapsulamento do conteudo da proposta-->
            <div class="row">
              <div class="col-md-12" id="coluna-esquerda">
                <!--DADOS DA CONVERSAO-->
                  <div class="container px-1" id="resumoConversao" style="padding: 3px; border-radius:5px; ">
                    <div class="row gx-1 gy-3"> <!-- Adicionado gy-3 para espaçamento vertical -->
                      <div class="row row-cols-1 row-cols-lg-5">
                        <div class="col p-3" id="bloco-saldo-cliente" class="">
                          <div class="bloco-easy bl-easy-modVendas">
                            <label  class="form-group label-bl-fin">Saldo Cliente:</label>
                            <span id="sp-saldo-cliente" class="<?=$classeSaldoCliente?> span-modVendas">R$ <?= ' '. number_format($saldo_cliente_anterior, 2, ",", ".") ?> </span>
                          </div>
                        </div>
                        <div class="col p-3" >
                          <div class="bloco-easy bl-easy-modVendas" id="bloco-total-venda">
                              <label  id="label-valor-conversao"class="form-group label-bl-fin">Convertido:</label>
                              <span id="sp-valor-conversao" class="num-positivo span-modVendas">R$ <?= ' '. number_format($valor_conversao, 2, ",", ".") ?> </span>
                          </div>
                        </div>
                        <div class="col p-3" id="bl-total-pagamentos">
                          <div class="bloco-easy bl-easy-modVendas">
                            <label  class="form-group label-bl-fin">Novo Saldo Cliente:</label>
                            <span class="<?=$classeNovoSaldoCliente?> span-modVendas" id="sp-saldo-final">R$ <?= ' '. number_format($saldo_cliente, 2, ",", ".") ?></span>
                          </div>
                        </div>
                      </div>
                      <!-- Bloco Principal -->
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
                        <table id="tabela-itensConversao" class="dataTable dTlinhaFina"> 
                          <input type="hidden" name="id_comum" id="id_comum" value="<?= $id_comum ?>">
                          <thead>
                            <tr>
                              <th style= "width: 12%;">Data:</th>
                              <th style= "width: 15%;">Adquirido:</th>
                              <th style= "width: 27%;">Serviço:</th>
                              <th style="display: none;"></th>
                              <th style="width: 8%;">Disp</th>
                              <th style= "width: 15%;">$ Unitário</th>
                              <th style= "width: 8%;">Un</th>
                              <th style= "width: 15%x;">$ Total</th>
                            </tr>
                          </thead>
                          <tbody id="itens-body">
                          <?php if (!empty($id)): ?>
                            <?php foreach ($convertidos as $it): ?>
                                <tr>
                                  <!-- Botão de remover à esquerda -->
                                  <td>
                                    <input name="data-venda[]" style="width: 12%;" type="date" class="blockitem form-control" readOnly value="<?php $it['data_venda']?>">
                                  </td> 
                                  <td>
                                    <input name="tipo-venda[]" style="width: 15%;" type="text" class="blockitem form-control" readOnly value="<?php $it['tipo_venda']?>">
                                  </td>
                                  <td>
                                    <input name="servico[]" style="width: 27%;"class="blockitem" readOnly value="<?php $it['servico']?>">
                                    <input hidden name="id_servico[]" value="<?php $it['id_servico']?>">
                                  </td>                                  
                                  <td hidden>
                                    <input type="hidden" name="id_venda[]" value="<?= $it['id_venda'] ?>">
                                    <input type="hidden" name="id_conversao[]" value="<?= $it['id'] ?>">
                                    <input type="hidden" name="id_venda_item[]" value="<?=$it['id_item_servico_origem']?>">
                                  </td>
                                  <td>
                                    <input name="qtd-disp[]" style="width: 8%;"class="blockitem" readOnly value="<?php $it['disponiveis']?>">
                                  </td>
                                  <td>
                                    <input name="valor-unitario[]" style="width: 15%;"class="blockItem" readOnly value="<?php $it['valor_un']?>">
                                  </td>
                                  <td>
                                    <input name="qtd-convert[]" style="width: 8%;"class="form-control qtd-convert"  value="<?php $it['quantidade']?>">
                                    <input type="hidden" name="qtd-convert-anterior[]" class="form-control" readOnly value="<?php $it['quantidade']?>">
                                  </td>
                                  <td>
                                    <input name="valor-total-item[]" style="width: 15%;"class="form-control" readOnly value="<?php $it['valor_total']?>">
                                  </td>
                                </tr>
                              <?php endforeach; ?>
                            <?php endif; ?>
                            <tr style="border-top: blue solid 3px; height:52px;" class="backGround-dataTable-head">
                              <td hidden></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td>
                                <input name="valo-total-conversao" id="input-valor-total-conversao" class="blockItem" readOnly value="<?= $valor_conversao ?>">
                              </td>

                            </tr>
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

$('#modalConversoes').on('hidden.bs.modal', function () {
  $(this).remove(); // remove o HTML
});
</script>






<!-- Arrays de produtos e serviços -->
<script>

  // ARRAYS //

  var arrayConvertidos = <?php echo json_encode($convertidos); ?>;
  var cliente    = <?php echo json_encode($cliente_origem); ?>;
  var valor_conversao = <?php echo json_encode($valor_conversao); ?>;
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
  $path = __DIR__ . '/Modals/modalConversoes.js';
  // Timestamp da última modificação
  $version = file_exists($path) ? filemtime($path) : time();
?>
<script src="Modals/modalConversoes.js?v=<?= $version ?>" defer></script>