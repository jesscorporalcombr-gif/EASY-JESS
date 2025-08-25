<?php 
$pag = 'agenda_adm';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');
gerarMenu($pag, $grupos);

?>

<div class="container-fluid py-3">
  <div class="card shadow-sm rounded-3">
    <div class="card-body">
      <h5 class="card-title mb-3">Ações Administrativas Cliente</h5>
      <div class="mt-3">
        <button type="button" class="btn btn-primary" id="btnTransfServicos" onclick= "abrirModal('modalTransfServicos', )">
          <i class="bi bi-plus-circle me-1"></i> Transferência de Serviços
        </button>
      </div>
      <div class="mt-3">
        <button type="button" class="btn btn-success" id="btnNovaMensagem" onclick= "abrirModal('modalConversao', )">
          <i class="bi bi-plus-circle me-1"></i> Conversão de Serviços em Créditos
        </button>
      </div>
      <div class="mt-3">
        <button type="button" class="btn btn-info" id="btnTransfSaldo"  onclick= "abrirModal('modalTransfSaldo', )">
          <i class="bi bi-plus-circle me-1"></i>Transferência de Saldo
        </button>
      </div>
    </div>
  </div>
