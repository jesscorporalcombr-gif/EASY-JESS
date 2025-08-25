<?php 
$pag = 'agenda_configuracoes';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');
gerarMenu($pag, $grupos);

$stmt = $pdo->query("SELECT mensagem, mostrar_profissional, mostrar_etiqueta_pagamento, mostrar_tempo_total, mostrar_horario_procedimento, mostrar_preco, mostrar_status FROM agenda_lembrete_padrao WHERE id = 1 LIMIT 1");
$config = $stmt->fetch(PDO::FETCH_ASSOC);

// Define valores padrão se não houver no banco
$lembrete_mensagem = $config['mensagem'] ?? '';
$mostrar_profissional = isset($config['mostrar_profissional']) ? (bool)$config['mostrar_profissional'] : false;
$mostrar_etiqueta_pagamento = isset($config['mostrar_etiqueta_pagamento']) ? (bool)$config['mostrar_etiqueta_pagamento'] : false;
$mostrar_tempo_total = isset($config['mostrar_tempo_total']) ? (bool)$config['mostrar_tempo_total'] : false;
$mostrar_horario_procedimento = isset($config['mostrar_horario_procedimento']) ? (bool)$config['mostrar_horario_procedimento'] : false;
$mostrar_preco = isset($config['mostrar_preco']) ? (bool)$config['mostrar_preco'] : false;
$mostrar_status = isset($config['mostrar_status']) ? (bool)$config['mostrar_status'] : false;


?>

<div class="container-fluid py-3">
  <div class="card shadow-sm rounded-3">
    <div class="card-body">
      <h5 class="card-title mb-3">Mensagens Configuráveis do WhatsApp</h5>
      <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0" id="tblMensagensAgenda">
          <thead>
            <tr>
              <th style="width: 120px;">Nome</th>
              <th style="width: 65%;">Mensagem</th>
              <th style="width: 48px;" class="text-end"></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $query = $pdo->query("SELECT id, nome, mensagem, mostrar_menu FROM agenda_mensagens ORDER BY id DESC");
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
              $id = $row['id'];
              $nome = htmlspecialchars($row['nome']);
              $mensagem = nl2br(htmlspecialchars($row['mensagem']));
              echo "
              <tr class='linha-msg' data-id='$id' data-mostrar_menu='{$row['mostrar_menu']}' style='cursor:pointer;'>
                <td class='align-middle'>$nome</td>
                <td class='align-middle'>$mensagem</td>
                <td class='text-end align-middle'>
                  <button type='button' class='btn btn-sm btn-outline-danger btn-excluir-msg' data-id='$id' title='Excluir'>
                    <i class='bi bi-trash'></i>
                  </button>
                </td>
              </tr>
              ";
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class="mt-3">
        <button type="button" class="btn btn-primary" id="btnNovaMensagem">
          <i class="bi bi-plus-circle me-1"></i> Nova Mensagem
        </button>
      </div>
    </div>
  </div>
  
<div class="card shadow-sm rounded-3 mt-4">
  <div class="card-body">
    <h5 class="card-title mb-3">Lembrete Padrão da Agenda (Lote)</h5>
    <form id="form-lembrete-agenda">
      <div class="row">
        <!-- TEXTAREA À ESQUERDA -->
        <div class="col-md-7 mb-3">
          <label for="lembrete_mensagem" class="form-label">Mensagem padrão do lembrete</label>
          <textarea
            id="lembrete_mensagem"
            name="lembrete_mensagem"
            class="form-control"
            rows="7"
            style="min-height:180px; resize:vertical"
            placeholder=""
          ><?php echo htmlspecialchars($lembrete_mensagem); ?></textarea>
        </div>

        <!-- CHECKBOXES À DIREITA -->
        <div class="col-md-5 mb-3">
          <label class="form-label">Configurações de exibição</label>
          <div class="d-flex flex-column gap-2">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="mostrar_profissional" name="mostrar_profissional"
                <?php if ($mostrar_profissional) echo 'checked'; ?>>
              <label class="form-check-label" for="mostrar_profissional">
                Mostrar profissional
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="mostrar_etiqueta_pagamento" name="mostrar_etiqueta_pagamento"
                <?php if ($mostrar_etiqueta_pagamento) echo 'checked'; ?>>
              <label class="form-check-label" for="mostrar_etiqueta_pagamento">
                Mostrar etiqueta de pagamento
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="mostrar_horario_procedimento" name="mostrar_horario_procedimento"
                <?php if ($mostrar_horario_procedimento) echo 'checked'; ?>>
              <label class="form-check-label" for="mostrar_horario_procedimento">
                Mostrar horário de cada procedimento
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="mostrar_preco" name="mostrar_preco"
                <?php if ($mostrar_preco) echo 'checked'; ?>>
              <label class="form-check-label" for="mostrar_preco">
                Mostrar Preço
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="mostrar_status" name="mostrar_status"
                <?php if ($mostrar_status) echo 'checked'; ?>>
              <label class="form-check-label" for="mostrar_status">
                Mostrar Status
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="row align-items-center">
        <div class="col-auto">
          <button type="button" class="btn btn-success" id="salvarLembreteAgenda">
            Salvar Configuração
          </button>
        </div>
        <div class="col">
          <span id="statusSalvarLembrete" class="ms-3"></span>
        </div>
      </div>

      <div class="mt-3 alert alert-info py-2 mb-0" style="max-height: 120px; overflow-y: auto;">
        <strong>Dica:</strong> Use variáveis para personalizar. Exemplos:<br>
        <code>            
          <li><b>{nome}</b> – Primeiro nome do cliente</li>
          <li><b>{nomecompleto}</b> – Nome completo do cliente</li>
          <li><b>{data}</b> – Data do agendamento</li>
          <li><b>{dataextenso}</b> – Data do agendamento</li>
          <li><b>{sexo}</b> – 'o' ou 'a'</li>
          <li><b>{sexo2}</b> – 'ao' ou 'a'</li>
          <li><b>{sexo3}</b> – 'ele' ou 'ela'</li>
          <li><b>{empresa}</b> – Nome fantasia</li>
          <li><b>{enderecoestabelecimento}</b> – Endereço da empresa</li>
          <li><b>{whatsap}</b> – WhatsApp da empresa</li>
          <li><b>{instagram}</b> – Instagram da empresa</li>
          <li><b>{lista_procedimentos}</b> – Lista de todos os procedimentos conforme as configurações</li>
          <li><b>{s}</b> – completa com "s" se for mais de um procedimento Ex:"...seu{s} procedimento{s}... </li>
          <li><b>{entrada}</b> – Início do primeiro horário</li>
          <li><b>{saida}</b> – Hora de prevista para saida da clínica</li>
          <li><b>{duracao}</b> – Tempo total ex: 1h 45min </li>
        </code>
      </div>
    </form>
  </div>
</div>





<!--===================================MODAL ATENDIMENTO AGENDA======================================-->
 <div class="modal fade" tabindex="-1" style="z-index:25000;" id="modMensagemAgenda">
  <div class="modal-dialog modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="headModMsgAgenda" class="modal-title">Mensagem</h5>
        <button type="button"  class="btn-close btn-close-white" id = "fechaModalMsgAgenda" data-bs-dismiss="modal" aria-label="Fechar">
         
        </button>
      </div>


        <div class="modal-body">
        <div class="container-fluid">
            <input type="hidden" id="idMsgAgenda">
            <form>
            <div class="mb-3">
                <label for="nome-msg-ag" class="form-label">Nome da Mensagem</label>
                <input type="text" class="form-control" id="nome-msg-ag" name="nome-msg-ag" autocomplete="off" maxlength="80">
            </div>
            <div class="mb-3">
                <label for="texto-msg-ag" class="form-label">Texto da Mensagem</label>
                <textarea style="height: 100px;" class="form-control" id="texto-msg-ag" name="texto-msg-ag" rows="4" maxlength="1000"
                placeholder="Exemplo: Olá {NOMECLIENTE}, lembramos do seu agendamento dia {DATADAGENDAMENTO} às {HORARIOAGENDAMENTO}."></textarea>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="mostrar_menu" name="mostrar_menu">
                <label class="form-check-label" for="mostrar_menu">
            Incluir no menu
        </label>
        </div>


      <div class="alert alert-info py-2 mb-0" style="max-height: 170px; overflow-y: auto;">
        <strong>Como personalizar sua mensagem:</strong>
        <br>Use as variáveis abaixo para inserir informações automáticas:
          <ul class="mb-0" style="font-size:0.67em;">
            <li><b>{nome}</b> – Primeiro nome do cliente</li>
            <li><b>{nomecompleto}</b> – Nome completo do cliente</li>
            <li><b>{data}</b> – Data do agendamento</li>
            <li><b>{dataextenso}</b> – Data do agendamento</li>
            <li><b>{hora}</b> – Horário do agendamento</li>
            <li><b>{sexo}</b> – 'o' ou 'a'</li>
            <li><b>{sexo2}</b> – 'ao' ou 'a'</li>
            <li><b>{sexo3}</b> – 'ele' ou 'ela'</li>
            <li><b>{servico}</b> – Nome do serviço</li>
            <li><b>{profissional}</b> – Profissional</li>
            <li><b>{preco}</b> – Preço do serviço</li>
            <li><b>{telefone}</b> – Telefone do cliente</li>
            <li><b>{empresa}</b> – Nome fantasia</li>
            <li><b>{enderecoestabelecimento}</b> – Endereço da empresa</li>
            <li><b>{whatsap}</b> – WhatsApp da empresa</li>
            <li><b>{instagram}</b> – Instagram da empresa</li>
          </ul>
         <span style="font-size:0.64em;">Escreva normalmente e use as variáveis acima entre chaves.<br>
        <b>Exemplo:</b> Olá {nome}, seja bem vind{sexo} à {empresa}, seu atendimento será {dataextenso} às {hora}.<br>
        <b> RESULTADO:</b> Olá Funala, seja bem vinda à Clínica Exemplo, seu atendimento será AMANHÃ, 25 de março de 2020 às 15:00.</span>
      </div>
    </form>
  </div>
</div>




      <div class="modal-footer">
        <div id="erroModalMsg" class="erro-modal-msg"></div>
        <button type="submit" class="btn btn-primary"  id="gravaAtendimento">Grava</button>
        <button type="button" class="btn btn-secondary"  id="cancelaModAtendimento" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Seleciona a tabela e os botões
  const tabela = document.getElementById('tblMensagensAgenda');
  const btnNova = document.getElementById('btnNovaMensagem');
  const modal = new bootstrap.Modal(document.getElementById('modMensagemAgenda'));

  // Campos do modal
  const inputId = document.getElementById('idMsgAgenda');
  const inputNome = document.getElementById('nome-msg-ag');
  const inputTexto = document.getElementById('texto-msg-ag');

  // Clique na linha para editar
  tabela.addEventListener('click', function (event) {
    // Ignora se clicou no botão de exclusão
    if (event.target.closest('.btn-excluir-msg')) return;

    // Linha clicada
    const tr = event.target.closest('tr.linha-msg');
    if (!tr) return;

    // Extrai dados da linha (melhor puxar do banco no backend, mas como tá na tela, pega daqui)
    const id = tr.getAttribute('data-id');
    const nome = tr.children[0].textContent.trim();
    const mensagem = tr.children[1].innerText.trim();
    const mostrar_menu = tr.getAttribute('data-mostrar_menu') || '0';
    document.getElementById('mostrar_menu').checked = (mostrar_menu === '1');

    // Preenche o modal
    inputId.value = id;
    inputNome.value = nome;
    inputTexto.value = mensagem.replace(/\u200B/g, ''); // remove caracteres invisíveis, se houver

    modal.show();
  });

  // Clique em "Nova Mensagem" para zerar modal
  btnNova.addEventListener('click', function () {
    inputId.value = '';
    inputNome.value = '';
    inputTexto.value = '';
    modal.show();
  });

  // (Opcional) Foca no primeiro input ao abrir o modal
  document.getElementById('modMensagemAgenda').addEventListener('shown.bs.modal', function () {
    inputNome.focus();
  });
});



document.getElementById('gravaAtendimento').addEventListener('click', function () {
  const id = document.getElementById('idMsgAgenda').value;
  const nome = document.getElementById('nome-msg-ag').value.trim();
  const mensagem = document.getElementById('texto-msg-ag').value.trim();
  const erroDiv = document.getElementById('erroModalMsg');
  const mostrar_menu = document.getElementById('mostrar_menu').checked ? 1 : 0;

  // Validação simples no front
  if (!nome || !mensagem) {
    erroDiv.textContent = 'Preencha todos os campos!';
    return;
  }

  // Limpa mensagens de erro
  erroDiv.textContent = '';

  // Monta e envia via AJAX
  fetch('agenda_configuracoes/insere_atualiza_mensagem.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `id=${encodeURIComponent(id)}&nome=${encodeURIComponent(nome)}&mensagem=${encodeURIComponent(mensagem)}&mostrar_menu=${mostrar_menu}`

  })
  .then(resp => resp.json())
  .then(data => {
    if (data.success) {
      // Atualiza tabela na página (mais profissional) ou recarrega tudo (mais simples)
      location.reload(); // Simples: recarrega página para refletir mudança

      // --- Se quiser fazer atualização dinâmica sem reload, peça que te passo um exemplo pronto! ---
    } else {
      erroDiv.textContent = data.msg || 'Erro ao gravar mensagem!';
    }
  })
  .catch(() => {
    erroDiv.textContent = 'Erro na conexão!';
  });
});

document.addEventListener('DOMContentLoaded', function () {
  // Evento delegado para pegar qualquer lixeira clicada
  document.getElementById('tblMensagensAgenda').addEventListener('click', function (event) {
    const btn = event.target.closest('.btn-excluir-msg');
    if (!btn) return;

    const id = btn.getAttribute('data-id');
    if (!id) return;

    // Confirmação rápida
    if (!confirm('Tem certeza que deseja excluir esta mensagem?')) return;

    // AJAX para exclusão
    fetch('agenda_configuracoes/agenda_mensagem_excluir.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'id=' + encodeURIComponent(id)
    })
    .then(resp => resp.json())
    .then(data => {
      if (data.success) {
        location.reload(); // Simples, recarrega a tabela/página
      } else {
        alert(data.msg || 'Erro ao excluir!');
      }
    })
    .catch(() => {
      alert('Erro de conexão!');
    });
  });
});








document.getElementById('salvarLembreteAgenda').addEventListener('click', function () {
  const data = {
    mensagem: document.getElementById('lembrete_mensagem').value.trim(),
    mostrar_profissional: document.getElementById('mostrar_profissional').checked ? 1 : 0,
    mostrar_etiqueta_pagamento: document.getElementById('mostrar_etiqueta_pagamento').checked ? 1 : 0,
    //mostrar_tempo_total: document.getElementById('mostrar_tempo_total').checked ? 1 : 0,
    mostrar_horario_procedimento: document.getElementById('mostrar_horario_procedimento').checked ? 1 : 0,
    mostrar_preco: document.getElementById('mostrar_preco').checked ? 1 : 0,
    mostrar_status: document.getElementById('mostrar_status').checked ? 1 : 0
  };
  fetch('agenda_atendimentos/salvar_lembrete_padrao.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  })
  .then(resp => resp.json())
  .then(res => {
    document.getElementById('statusSalvarLembrete').textContent = res.success ? 'Salvo com sucesso!' : 'Erro ao salvar';
  })
  .catch(() => {
    document.getElementById('statusSalvarLembrete').textContent = 'Erro de conexão!';
  });
});






</script>