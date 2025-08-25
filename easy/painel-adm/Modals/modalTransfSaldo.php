<?php
// Início do script PHP
$pag = 'transferencia de saldo';
session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

$id_usuario    = $_SESSION['id_usuario'];    // user_criacao ou user_alteracao
$nome_usuario  = $_SESSION['nome_usuario'];

// Recebe dados via POST
$id = isset($_POST['id']) ? intval($_POST['id']) : '';



// captura os dados da transferencia de serviços informada


?>


<div class="modal fade" tabindex="-1" style="z-index: 95000;" id="modalTransfSaldo" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Transferências de Créditos</h5>

          <button type="button" class="btn-fecha-modal" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>
      </div>  
      <form method="POST" id="formTransferencia_saldo">
        <div class="modal-body" style="min-height: 200px; overflow-y: auto; background-color:#F8F8F8;">
          <div class="row justify-content-end" id="mod-venda-ft-ln" style="text-align:right; max-height:15px; margin-top:-20px;">
                    <span style="font-size: 12px;">Usuário do Sistema: <?= $id_usuario . ' - ' . $nome_usuario ?></span>
          </div>
          <div class="row">
            <input type="hidden" name="id-comum-transferencia" id="id-comum" value ="<?= $id_comum?>">
            <div class="col-12">
              <div id="bloco-cliente-recebe-transferencia">
                <label for="nome-cliente" class="label-input-easy label-bl-fin">Cliente que TRANSFERE:</label>
      
                  <div class="row pt-2">
                    <div class="col-md-12" style="min-height:40px; ">
                      <div class="row" style="padding-left: 15px; width: 95%" id="linha-cliente-mod-transferencia_saldo">
                        
                      
                        <input type="hidden" name="id-venda-transferencia" id="id-venda-envia" value="<?= isset($id_comum) ? $id_venda_transf : '' ?>">
                        
                        
                        <input type="hidden" name="id-cliente" id="id-cliente"  value="<?= isset($id_cliente) ? $id_cliente : '' ?>">
                        <input type="hidden" id="sexo-cliente"  value="<?= isset($sexo_cliente) ? $sexo_cliente : '' ?>">
                        

                        
                        
                        
                        <div class="col-auto" id="col-img-foto-cliente" style="padding-left: 10px; width: 40px; display:<?= (!$foto_cliente) ? 'none' : 'block' ?>"> <!-- Foto do cliente -->
                          <div id="divImgConta">
                            <img style="width: 35px; border-radius: 50%;" id="img-foto-cliente-modTransferencia_saldo" <?=$foto_cliente?'src="../img/clientes/'. $foto_cliente . '"':'' ?>>
                          </div>
                        </div>
                        <div class="col-auto mb-3">
                          <label for="nome" class="label-input-easy" style="margin-left:10px;" >Nome:</label>
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
                        <div class="col-auto mb-3 bloco-input-group-cliente blClienteTransfere">
                          <label for="cpf" class="label-input-easy " style="margin-left:10px;" >CPF:</label>
                          <input type="text" readonly class="form-control input-cpf-cliente <?= isset($id)? ' blockItem':'' ?>" id="cpf-cliente" name="cpf-cliente" placeholder=""  value="<?= isset($cpf_cliente) ? $cpf_cliente : '' ?>">
                          <div class="invalid-feedback" id="cpfError" style="display: none; color: red;">
                            CPF inválido!
                          </div>
                        </div>
                        <div class="col-auto mb-3 bloco-input-group-cliente blClienteTransfere">
                          <label for="celular" class="label-input-easy" style="margin-left:10px;" >Celular:</label>
                          <input type="text" readonly class="form-control input-celular-cliente<?= isset($id)? ' blockItem':'' ?>" id="celular-cliente" name="celular-cliente" placeholder="" required value="<?= isset($celular_cliente) ? $celular_cliente : '' ?>">
                        </div>
                        <div class="col-auto mb-3 bloco-input-group-cliente blClienteTransfere">
                          <label for="email" class="label-input-easy" style="margin-left:10px;" >Email:</label>
                          <input type="text" readonly class="form-control input-email-cliente <?= isset($id)? ' blockItem':'' ?>" id="email-cliente" name="email-cliente" placeholder="" value="<?= isset($email_cliente) ? $email_cliente : '' ?>">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 blClienteTransfere" style="min-height:40px;">
                      <div class="row" style="padding-left: 15px; width: 95%" id="linha-cliente-mod-transferencia_saldo">
                        <div class="col-auto mb-3 bloco-input-group-cliente">
                          <label for="saldo" class="label-input-easy" style="margin-left:10px;" >SALDO:</label>
                          <input type="text" readonly id="saldo-cliente" class="form-control blockItem input-saldo-cliente" value="">
                        </div>
                        <div class="col-auto mb-3 bloco-input-group-cliente">
                          <label for="novo-saldo-cliente" class="label-input-easy" style="margin-left:10px;" >NOVO SALDO:</label>
                          <input type="text" readonly id="novo-saldo-cliente" class="form-control blockItem input-saldo-cliente" value="">
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
            </div> 
            
          </div> <!-- fechamento da linha-->

          

          <div id="transferencia-container" style="<?= ($id) ? 'display:block' : 'display:none' ?> ;" >        <!--- encapsulamento do conteudo da proposta-->
             <hr style="margin-top: -5px; margin-bottom: 25px; border:none; border-bottom: 1px solid #000;"> 
             <div class="row">
              <div class="col-md-12" id="coluna-esquerda">
                <!--DADOS DA TRANSFERENCIA DE SERVIÇOS-->
                  <div id="resumoTransferenciasaldo">
                    <div class="row">
                      <div class="col-auto p-3" id="bloco-saldo-cliente">
                        <div id="bloco-valor-transferencia_saldo" style="width: 230px; height: 75px; position: relative;">
                          <label for="valor-transferencia_saldo" class="label-input-easy label-bl-fin">A Transferir:</label>
                          <div style="display: flex; align-items: center; padding-top: 5px; margin-left: 15px;">
                            <img src="/easy/painel-adm/svg/moneydown.svg" alt="Ícone" style="width: 35px; height: 35px; margin-left: -8px; color:aqua;">
                            <div class="input-group flex-nowrap" style="width: 145px;">
                              <span class="input-group-text" id="addon-wrapping">R$</span>
                              <input id="valor-transferencia_saldo" class="form-control num-positivo" style="width:130px;">
                              <input type="hidden" id="valor-transferencia" name="valor-transferencia" >
                            </div>
                            <!-- Ícone SVG -->
                            
                          </div>
                        </div>
                      
                      </div>
                      <div class="col p-3" id="bloco-saldo-cliente" style="min-width: 450px;">
                        <div id="bloco-valor-transferencia_saldo" style="min-height: 75px;">
                          <label for="valor-transferencia_saldo" class="label-input-easy label-bl-fin">Informações:</label>
                          <div class="row" style="display: flex; align-items: center; padding-top: 5px; margin-left: 15px;">
                             <textarea class="form-control" name="informacoes" style = "height:90%; width:87%" ><?= $informacoes ?></textarea>
                             <input type="file" id="anexo-transferencia" name="anexo" class="d-none">
                            <!-- label estilizado como botão -->
                            <label for="anexo-transferencia" class="btn btn-outline-primary ms-2" style="height: 30px; width: 30px;">
                              <i class="bi bi-paperclip" style="margin-left: -8px; position: absolute; margin-top: -3px;"></i>
                            </label>
                          </div>
                          <div class="row">
                            <span style="text-align:right; font-size: 12px; padding-right: 105px;" id="nome-arquivo-anexo"></span>
                            <input type="hidden" id="input-nome-arquivo-anexo" name="nome-arquivo-anexo">
                          </div>
                        </div>
                        
                      </div>
                    </div>





                  </div>
                <hr style="margin-top: -5px; margin-bottom: 25px; border:none; border-bottom: 1px solid #000;">
                  <div class="container px-1 mb-3" id="clienteRecebeTransferenciasaldo" >
                    <div class="row">
                      <div class="col-12">
                        <div id="bloco-cliente-recebe-transferencia">
                          <label for="nome-cliente-recebe" class="label-input-easy label-bl-fin">Cliente que RECEBE:</label>
                          
                          <div class="row pt-2 align-items-center" style ="padding-left: 15px; width: 95%" id="linha-cliente-mod-transferencia_saldo_recebe">
                            
                            <input type="hidden" name="id-venda-recebimento"  id="id-venda-recebe" value="<?= isset($id_comum) ? $id_venda_recebe : '' ?>">
                            
                            <input type="hidden" name="id-cliente-recebe" id="id-cliente-recebe" value="<?= isset($id_cliente_recebe) ? $id_cliente_recebe : '' ?>">
                            <input type="hidden" id="sexo-cliente-recebe" value="<?= isset($sexo_cliente_recebe) ? $sexo_cliente_recebe : '' ?>">

                            <div class="col-auto" id="col-img-foto-cliente-recebe" style="margin-top:-10px; padding-left:10px; width:40px; display:<?= (!$foto_cliente_recebe) ? 'none' : 'block' ?>;">
                              <div id="divImgContaRecebe">
                                <img style="width:35px; border-radius:50%;" id="img-foto-cliente-modTransferenciasaldo_recebe" <?= $foto_cliente_recebe ? 'src="../img/clientes/'.$foto_cliente_recebe.'"' : '' ?>>
                              </div>
                            </div>
                            <div class="col-auto mb-3">
                              <label for="nome-cliente-recebe" class="label-input-easy" style="margin-left:10px;">Nome</label>
                              <div class="input-group">
                                <input type="text" style="min-width:185px;" class="form-control nome-cliente input-nome-cliente<?= ($id>0) ? ' blockItem' : '' ?>" id="nome-cliente-recebe" name="nome-cliente-recebe" placeholder="Nome" autocomplete="off" required value="<?= isset($nome_cliente_recebe) ? $nome_cliente_recebe : '' ?>">
                                <button class="btn btn-outline-secondary" style="border:none;" type="button" id="btn-adicionar-cliente-recebe" <?= isset($id_cliente_recebe) ? 'style="border:none;"' : '' ?> title="<?= isset($id_cliente_recebe) ? 'Visualizar cliente' : 'Adicionar novo cliente' ?>" onclick="abrirModal('modalClientes', document.getElementById('id-cliente-recebe').value)">
                                  <i class="bi <?= ($id_cliente_recebe) ? 'bi-eye' : 'bi-person-plus' ?>" id="ico-inputClienteRecebe"></i>
                                </button>
                              </div>
                              <ul id="lista-clientes-recebe" class="sugestoes lista-clientes"></ul>
                            </div>
                            <div class="col-auto mb-3 bloco-input-group-cliente blClienteRecebe">
                              <label for="cpf-cliente-recebe" class="label-input-easy" style="margin-left:10px;">CPF</label>
                              <input type="text" readonly class="form-control input-cpf-cliente-recebe<?= isset($id) ? ' blockItem' : '' ?>" id="cpf-cliente-recebe" name="cpf-cliente-recebe" value="<?= isset($cpf_cliente_recebe) ? $cpf_cliente_recebe : '' ?>">
                            </div>
                            <div class="col-auto mb-3 bloco-input-group-cliente blClienteRecebe">
                              <label for="celular-cliente-recebe" class="label-input-easy" style="margin-left:10px;">Celular</label>
                              <input type="text" readonly class="form-control input-celular-cliente-recebe<?= isset($id) ? ' blockItem' : '' ?>" id="celular-cliente-recebe" name="celular-cliente-recebe" required value="<?= isset($celular_cliente_recebe) ? $celular_cliente_recebe : '' ?>">
                            </div>
                            <div class="col-auto mb-3 bloco-input-group-cliente blClienteRecebe">
                              <label for="email-cliente-recebe" class="label-input-easy" style="margin-left:10px;">Email</label>
                              <input type="text" readonly class="form-control input-email-cliente-recebe<?= isset($id) ? ' blockItem' : '' ?>" id="email-cliente-recebe" name="email-cliente-recebe" value="<?= isset($email_cliente_recebe) ? $email_cliente_recebe : '' ?>">
                            </div>
                          </div>
                          <div class="row blClienteRecebe">
                            <div class="col-md-12" style="min-height:40px; ">
                              <div class="row" style="padding-left: 15px; width: 95%" id="linha-cliente-mod-transferencia_saldo">
                                <div class="col-auto mb-3 bloco-input-group-cliente">
                                  <label for="saldo-cliente-recebe" class="label-input-easy" style="margin-left:10px;" >SALDO:</label>
                                  <input type="text" readonly id="saldo-cliente-recebe" class="form-control blockItem input-saldo-cliente-recebe" value="">
                                </div>
                                <div class="col-auto mb-3 bloco-input-group-cliente">
                                  <label for="novo-saldo-cliente-recebe" class="label-input-easy" style="margin-left:10px;" >NOVO SALDO:</label>
                                  <input type="text" readonly id="novo-saldo-cliente-recebe" class="form-control blockItem input-saldo-cliente-recebe" value="">
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>


                  <!--navegação das abas -->
                 
                      <!-- Conteúdo para informações -->
                   

         
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

$('#modalTransfSaldo').on('hidden.bs.modal', function () {
  $(this).remove(); // remove o HTML
});
</script>






<!-- Arrays de produtos e serviços -->
<script>

  // ARRAYS //

  var array$transferidos = <?php echo json_encode($$transferidos); ?>;
  var cliente    = <?php echo json_encode($cliente_origem); ?>;
  var valor_transferencia_saldo = <?php echo json_encode($valor_transferencia_saldo); ?>;
  var id_comum = <?php echo json_encode($id_comum); ?>;
  var id_venda_recebe = <?php echo json_encode($id_venda_recebe); ?>;

  var SaldoFinalCliente =  <?php echo json_encode($saldo_cliente); ?>;
  var SaldoCliente =  <?php echo json_encode($saldo_cliente_anterior); ?>;
  //$var Variaveis dos totais
  function sanitizeNumber(value) {
    return Number(value) || 0;
  }

  
</script>

<?php
  // Caminho absoluto até o seu JS
  $path = __DIR__ . '/Modals/modalTransfSaldo.js';
  // Timestamp da última modificação
  $version = file_exists($path) ? filemtime($path) : time();
?>
<script src="Modals/modalTransfSaldo.js?v=<?= $version ?>" defer></script>