<?php
$form_id     = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
$token       = isset($_GET['token']) ? $_GET['token'] : '';
$response_id = isset($_GET['response_id']) ? (int)$_GET['response_id'] : 0;
$patient_id  = isset($_GET['patient_id']) ? $_GET['patient_id'] : '';
$readonly    = isset($_GET['readonly']) ? (int)$_GET['readonly'] : 0;
$print       = isset($_GET['print']) ? (int)$_GET['print'] : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Responder formulário</title>

  <link  href="https://unpkg.com/survey-core/survey-core.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/survey-core/survey.core.min.js"></script>
  <script src="https://unpkg.com/survey-js-ui/survey-js-ui.min.js"></script>
  <script src="https://unpkg.com/survey-core/themes/index.min.js"></script>
  <!-- i18n (inclui vários idiomas) -->
  <script src="https://unpkg.com/survey-core/survey.i18n.min.js"></script>

  <style>
    body{margin:0;font-family:system-ui,Segoe UI,Arial}
    #surveyContainer{max-width:960px;margin:24px auto;padding:12px}
    @media print {
      #surveyContainer { margin:0; padding:0; max-width:100%; }
    }
  </style>
</head>
<body>
  <div id="surveyContainer"></div>

  <script>
    Survey.localization.currentLocale = "pt-br";

    const FORM_ID     = <?= (int)$form_id ?>;
    const TOKEN       = <?= json_encode($token) ?>;
    const RESPONSE_ID = <?= (int)$response_id ?>;
    const PATIENT     = <?= json_encode($patient_id) ?>;
    const READONLY    = <?= (int)$readonly ?> === 1;
    const DO_PRINT    = <?= (int)$print ?> === 1;

    async function loadSchemaAndData() {
      // 1) token (público)
      if (TOKEN) {
        const r = await fetch(`../api/tokens/resolve.php?token=${encodeURIComponent(TOKEN)}`);
        const j = await r.json();
        if (!j.ok) throw new Error(j.error || 'falha token');
        return { schema: parseMaybe(j.form.schema_json), formId: j.form.id, answers: null };
      }
      // 2) response_id (editar/visualizar existente)
      if (RESPONSE_ID > 0) {
        const r = await fetch(`../api/responses/get.php?response_id=${RESPONSE_ID}`);
        const j = await r.json();
        if (!j.ok) throw new Error(j.error || 'falha resposta');
        const schema = j.response.schema_json ? parseMaybe(j.response.schema_json) : await loadCurrentFormSchema(j.response.form_id);
        const answers = parseMaybe(j.response.answers) || null;
        return { schema, formId: j.response.form_id, answers };
      }
      // 3) form_id (novo interno)
      if (FORM_ID > 0) {
        const r = await fetch(`../api/forms/get.php?id=${FORM_ID}`);
        const j = await r.json();
        if (!j.ok) throw new Error(j.error || 'falha form');
        const schema = typeof j.form.schema_json === 'string' ? JSON.parse(j.form.schema_json) : j.form.schema_json;
        return { schema, formId: j.form.id, answers: null };
      }
      throw new Error('Informe token, response_id ou form_id');
    }

    function parseMaybe(x){ try { return (typeof x === 'string') ? JSON.parse(x) : x; } catch(e){ return null; } }
    async function loadCurrentFormSchema(fid){
      const r = await fetch(`../api/forms/get.php?id=${fid}`);
      const j = await r.json();
      if (!j.ok) throw new Error(j.error || 'falha form');
      return typeof j.form.schema_json === 'string' ? JSON.parse(j.form.schema_json) : j.form.schema_json;
    }

    loadSchemaAndData().then(({schema, formId, answers}) => {
      const survey = new Survey.Model(schema);
      Survey.ThemeManager.applyTheme(Survey.ThemeManager.Theme.Halo);

      if (answers) survey.data = answers;
      if (READONLY) survey.mode = "display";

      survey.onComplete.add(async (sender, options) => {
        if (READONLY) { options.showSaveSuccess('Somente leitura.'); return; }
        options.showSaveInProgress();
        try {
          let out;
          // público por token → cria response pendente de validação
          if (TOKEN) {
            const fd = new FormData();
            fd.append('token', TOKEN);
            fd.append('answers_json', JSON.stringify(sender.data));
            const r = await fetch('../api/responses/save_by_token.php', { method:'POST', body: fd });
            out = await r.json();
          }
          // edição/atualização de uma resposta existente (interna)
          else if (RESPONSE_ID > 0) {
            const fd = new FormData();
            fd.append('response_id', String(RESPONSE_ID));
            fd.append('answers_json', JSON.stringify(sender.data));
            fd.append('status', 'validated'); // interno: já valida no ato (ajuste se quiser)
            const r = await fetch('../api/responses/save_update.php', { method:'POST', body: fd });
            out = await r.json();
          }
          // nova resposta interna (sem token, sem response_id)
          else {
            const fd = new FormData();
            fd.append('form_id', String(formId));
            fd.append('patient_id', PATIENT || '');
            fd.append('answers_json', JSON.stringify(sender.data));
            fd.append('status', 'validated'); // interno: já valida
            const r = await fetch('../api/responses/save.php', { method:'POST', body: fd });
            out = await r.json();
          }

          if (!out.ok) throw new Error(out.error || 'erro salvar');
          options.showSaveSuccess('Respostas enviadas com sucesso!');
        } catch (e) {
          console.error(e);
          options.showSaveError('Erro ao salvar. Tente novamente.');
        }
      });

      document.addEventListener('DOMContentLoaded', function(){
        survey.render(document.getElementById('surveyContainer'));
        if (DO_PRINT) setTimeout(()=>window.print(), 600);
      });
    }).catch(err => {
      document.getElementById('surveyContainer').innerHTML = `<p style="color:#b00020">Erro: ${err.message}</p>`;
    });
  </script>
</body>
</html>
