<?php
// /easy/painel-adm/forms_creator/form_creator.php
@session_start();

$form_id    = isset($_GET['form_id']) ? preg_replace('/[^0-9]/', '', $_GET['form_id']) : '';
$version_id = isset($_GET['version_id']) ? preg_replace('/[^0-9]/', '', $_GET['version_id']) : '';
$storageKey = "EC_FORM_BUILDER_" . ($form_id ?: 'new') . '_' . ($version_id ?: 'draft');

?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Criador de Formulários</title>
  <link rel="stylesheet" href="forms_creator/form_builder.css?v=12">
</head>
<body>

  <header class="ecfb-topbar">
    
    <div class="ecfb-left">
     <button id="btnOpenDrawer" class="btn btn-light">Formulários</button>
      <a class="btn btn-secondary" href="../tecnico/anamnese_configuracoes.php">← Voltar</a>
      <span class="ecfb-breadcrumb">/ Criador de Formulários</span>
    </div>

    <div class="ecfb-right">
      <!-- Botões padrão -->
      <button id="btnLoadLocal"   class="btn btn-light">Carregar</button>
      <button id="btnSaveLocal"   class="btn">Salvar</button>
      <button id="btnSaveDraft"   class="btn">Salvar no sistema</button>
      <button id="btnPublish"     class="btn btn-primary">Publicar</button>
      <button id="btnExportJson"  class="btn">Exportar JSON</button>
      <button id="btnPreview"     class="btn btn-primary">Pré-visualizar</button>

       
    </div>
  </header>
  <aside id="drawerMenu" class="ecfb-drawer" aria-hidden="true">
        <div class="drawer-hd">
            <strong>Modelos</strong>
            <div>
            <button id="btnNewForm" class="btn btn-light btn-xs">+ Novo</button>
            <button id="btnCloseDrawer" class="btn btn-danger btn-xs">X</button>
            </div>
        </div>
        <div id="drawerList" class="drawer-list"><p class="muted">Carregando…</p></div>
  </aside>


  <main class="ecfb-layout" data-storage-key="<?= htmlspecialchars($storageKey) ?>">
    <aside class="ecfb-sidebar">
      <div class="ecfb-panel">
        <h3>Campos</h3>
        <div class="ecfb-field-list">
          <button class="ecfb-field-btn" data-type="text">Texto curto</button>
          <button class="ecfb-field-btn" data-type="textarea">Texto longo</button>
          <button class="ecfb-field-btn" data-type="number">Número</button>
          <button class="ecfb-field-btn" data-type="date">Data</button>
          <button class="ecfb-field-btn" data-type="radio">Múltipla (única)</button>
          <button class="ecfb-field-btn" data-type="checkbox">Múltipla (múltiplas)</button>
          <button class="ecfb-field-btn" data-type="select">Lista (select)</button>
          <button class="ecfb-field-btn" data-type="scale">Escala</button>
        </div>
      </div>

      <div class="ecfb-panel ecfb-meta">
        <h3>Formulário</h3>
        <label>Título <input id="formTitle" type="text"></label>
        <label>Descrição <textarea id="formDesc" rows="3"></textarea></label>
        <label>Tipo
          <select id="formType">
            <option value="anamnese">Anamnese</option>
            <option value="pesquisa">Pesquisa</option>
            <option value="questionario">Questionário</option>
            <option value="outro">Outro</option>
          </select>
        </label>
      </div>
    </aside>

    <section class="ecfb-canvas">
      <div id="canvasEmpty" class="ecfb-empty">Nenhuma seção ainda.</div>
      <div class="ecfb-panel"><button id="btnAddSection" class="btn btn-light">+ Adicionar seção</button></div>
      <div id="sections" class="ecfb-sections"></div>
    </section>

    <aside class="ecfb-props">
      <div class="ecfb-panel"><h3>Propriedades</h3><div id="propsContainer" class="props-form"></div></div>
    </aside>
  </main>

  <div id="previewModal" class="ecfb-modal" style="z-index: 96000;" aria-hidden="true">
    <div class="ecfb-modal-content">
      <header class="ecfb-modal-header">
        <h3>Pré-visualização</h3>
        <button id="btnClosePreview" class="btn btn-light">Fechar</button>
      </header>
      <div id="previewBody" class="ecfb-modal-body"></div>
    </div>
  </div>

  <script>
    window.__ECFB_CONTEXT__ = { form_id: '<?= $form_id ?>', version_id: '<?= $version_id ?>' };
  </script>
  <script src="forms_creator/form_builder.js?v=13"></script>
  <script src="forms_creator/form_renderer.js?v=5"></script>
</body>
</html>