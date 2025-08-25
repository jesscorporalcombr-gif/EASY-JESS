<?php
$form_id    = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : '';
if ($form_id<=0) { http_response_code(400); echo "form_id obrigatório"; exit; }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Responder formulário</title>

  <!-- SurveyJS Form Library -->
  <link  href="https://unpkg.com/survey-core/survey-core.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/survey-core/survey.core.min.js"></script>
  <script src="https://unpkg.com/survey-js-ui/survey-js-ui.min.js"></script>
  <!-- Temas prontos (opcional): -->
  <script src="https://unpkg.com/survey-core/themes/index.min.js"></script>

  <style>
    body{margin:0;font-family:system-ui,Segoe UI,Arial}
    #surveyContainer{max-width:960px;margin:24px auto;padding:12px}
  </style>
</head>
<body>
  <div id="surveyContainer"></div>

  <script>
    const FORM_ID = <?= $form_id ?>;
    const PATIENT_ID = <?= json_encode($patient_id) ?>;

    // 1) Busca o schema
    fetch(`../api/forms/get.php?id=${FORM_ID}`)
      .then(r => r.json())
      .then(j => {
        if (!j.ok) throw new Error(j.error || 'falha get');
        const schema = (typeof j.form.schema_json === 'string') ? JSON.parse(j.form.schema_json) : j.form.schema_json;

        // 2) Renderiza
        const survey = new Survey.Model(schema);
        // tema (opcional)
        Survey.ThemeManager.applyTheme(Survey.ThemeManager.Theme.Halo);

        survey.onComplete.add(async (sender, options) => {
          // 3) Envia respostas
          options.showSaveInProgress();
          const fd = new FormData();
          fd.append('form_id', String(FORM_ID));
          fd.append('patient_id', PATIENT_ID || '');
          fd.append('answers_json', JSON.stringify(sender.data));
          fd.append('status', 'completed');

          try{
            const resp = await fetch('../api/responses/save.php', { method:'POST', body: fd });
            const out = await resp.json();
            if (!out.ok) throw new Error(out.error || 'erro salvar');
            options.showSaveSuccess('Respostas enviadas com sucesso!');
          }catch(e){
            console.error(e);
            options.showSaveError('Erro ao salvar. Tente novamente.');
          }
        });

        document.addEventListener('DOMContentLoaded', function(){
          survey.render(document.getElementById('surveyContainer'));
        });
      })
      .catch(err => {
        document.getElementById('surveyContainer').innerHTML = '<p>Falha ao carregar formulário.</p>';
        console.error(err);
      });
  </script>
</body>
</html>
