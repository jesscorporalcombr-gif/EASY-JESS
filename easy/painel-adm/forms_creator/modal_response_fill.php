<?php
@session_start();
header('Content-Type: text/html; charset=utf-8');
require_once(__DIR__ . '/../../conexao.php');

$form_id    = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
$response_id= isset($_GET['response_id']) ? (int)$_GET['response_id'] : 0;

$schema = null; $answers = null; $version_id = 0;

try{
  if($response_id){
    // editar existente
    $r = $pdo->prepare("SELECT form_id, schema_version_id, answers_json FROM form_responses WHERE id=? AND excluido=0");
    $r->execute([$response_id]);
    $row = $r->fetch(PDO::FETCH_ASSOC);
    if(!$row) throw new Exception('Resposta não encontrada');
    $form_id = (int)$row['form_id'];
    $version_id = (int)$row['schema_version_id'];
    $answers = $row['answers_json'] ? json_decode($row['answers_json'], true) : null;

    $s = $pdo->prepare("SELECT schema_json FROM form_versions WHERE id=?");
    $s->execute([$version_id]);
    $sj = $s->fetch(PDO::FETCH_ASSOC);
    $schema = $sj ? json_decode($sj['schema_json'], true) : null;
  } else {
    // nova: usa versão publicada
    if(!$form_id) throw new Exception('form_id inválido');
    $s = $pdo->prepare("SELECT id, schema_json FROM form_versions WHERE form_id=? AND status='published' ORDER BY versao DESC LIMIT 1");
    $s->execute([$form_id]);
    $sj = $s->fetch(PDO::FETCH_ASSOC);
    if(!$sj) throw new Exception('Formulário sem versão publicada');
    $version_id = (int)$sj['id'];
    $schema = json_decode($sj['schema_json'], true);
  }
}catch(Exception $e){
  echo "<div class='ec-modal ec-modal-error'><div class='ec-modal-content'><p class='error'>".$e->getMessage()."</p><button data-close>Fechar</button></div></div>";
  exit;
}
?>
<script>
  window.__EC_MODAL_CTX__ = {
    form_id: <?= (int)$form_id ?>,
    response_id: <?= (int)$response_id ?>,
    version_id: <?= (int)$version_id ?>,
    schema: <?= json_encode($schema ?? new stdClass(), JSON_UNESCAPED_UNICODE) ?>,
    answers: <?= json_encode($answers ?? new stdClass(), JSON_UNESCAPED_UNICODE) ?>
  };
</script>

<div class="ec-modal" role="dialog" aria-modal="true">
  <div class="ec-modal-content">
    <header class="ec-modal-hd">
      <strong><?= htmlspecialchars($schema['meta']['title'] ?? 'Anamnese') ?></strong>
      <button data-close class="btn btn-light">X</button>
    </header>

    <div id="modalFormMount" class="ec-form-mount"></div>

    <footer class="ec-modal-ft">
      <button id="btnSalvarResp" class="btn btn-primary">Salvar</button>
      <button data-close class="btn btn-light">Cancelar</button>
    </footer>
  </div>
</div>

<style>
.ec-modal{position:fixed;inset:0;background:rgba(255, 255, 255, 0.35);display:flex;align-items:center;justify-content:center;z-index:100}
.ec-modal[aria-hidden="true"]{display:none}
.ec-modal-content{width:min(900px,95vw);max-height:90vh;overflow:auto;background: #fff;;border:1px solid #1f2532;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,.4)}
.ec-modal-hd,.ec-modal-ft{display:flex;justify-content:space-between;align-items:center;padding:10px;border-bottom:1px solid #1f2532}
.ec-modal-ft{border-top:1px solid #fcfdffff;border-bottom:none}
.ec-form-mount{padding:16px}
</style>
