<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
$id_agendamento = $_POST['id_agendamento'];
$id_cliente = $_POST['id_cliente'];






?>




<div class="janela" id="janela-agendamentos" style="opacity: 0.95; border-radius:8px; overflow: hidden;">
    <div class="modal-header" id="janela-header">
        <span class="modal-title">Minha Janela</span>
        <div>
            <button class="btn-minimizar" id="minimizar">_</button>
            <button class="btn-fechar" id="fechar">X</button>
        </div>
    </div>
    <div class="janela-body" id="janela-body">
        <div class="container-fluid">
            <div class="row" style="min-height:30px; border-radius: 8px;" id="dados-cliente">
                <div class="col-auto mb-2 mt-2" id="div-foto-cliente" style="width: 80px;">
                    <img style="cursor: pointer; border-radius:50%;  width:70px; margin-left:-8px; padding-top:3px;" src="" id="janela_foto_cliente" name="img-foto_cadCliente">
                </div>

                <div class="col  mb-2 mt-2" id="div-dados-cliente" style="min-width:280px; max-width:330px;">
                    <div class="client-search">
                        <input type="hidden" class="client-search__id" id="janela_id_cliente" name="janela_id_cliente" value="<?=$id_cliente?>">
                        
                        <div class="input-group client-search__wrapper">
                            <input 
                                type="text" 
                                class="form-control client-search__input"
                                id="janela_nome_cliente" 
                                autocomplete="off"   
                                name="nome-cliente" 
                                placeholder="Nome" 
                                value="">  
                            <button 
                                    class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="btn-adicionar-cliente"
                                    style="width:38px;border: none;"
                                    onclick="abrirModal('modalClientes', document.getElementById('janela_id_cliente').value)">
                                    <i class="bi client-search__icon bi-person-plus" id="ico-abrir-cliente"></i>
                            </button>
                       </div>
                        <ul id="lista-clientes" class="sugestoes lista-clientes client-search__list"></ul>
                    </div>
                </div>
                

            </div>
            <div class="row" style="height:350px;">
                <div class="col" style="height:320px;">
                    <div class="tab-pane fade show active" id="aba-itens" role="tabpanel" aria-labelledby="itens-tab" style="padding:10px;">
                    
                        <div class="table-responsive" style="overflow-x:auto;">
                            <table id="tabela-itens" class="table table-striped">
                                <thead>
                                    <tr>
                                    <th <?= $bloquearCampos ? 'hidden' : '' ?>><button type="button" id="adicionar-item" class="btn btn-success centBt">+</button></th>
                                    <th style="display: none;">ID</th>
                                    <th style= "min-width: 100px;">Data:</th>
                                    <th style= "min-width: 100px;">Profissional:</th>
                                    <th style= "min-width: 100px;">Serviço:</th>
                                    <th style= "min-width: 100px;">Preço:</th>
                                    <th style= "min-width:70px;">Início:</th>
                                    <th style= "min-width:70px;">Tempo:</th>
                                    <th style= "min-width:70px;">Fim:</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
                    
    <div class="footer" style="height:70px; top:450px;">
        <div class="container-fluid">
        <button id="btn-cancelar" class="btn btn-cancelar">Cancelar</button>
        <button class="btn btn-salvar">Salvar</button>
        </div>
    </div>
</div>






<script>
    
console.log('abrindo');

function inicial() {


  function ClientSearch(container) {
    const input     = container.querySelector('#janela_nome_cliente');
    const hiddenId  = container.querySelector('#janela_id_cliente');
    const list      = container.querySelector('#lista-clientes');
    const icon      = container.querySelector('#ico-abrir-cliente');
    const minChars  = 3;
    const delayMs   = 300;
    let timer       = null;
    let results     = [];
    let selectedIndex = -1;

    input.addEventListener('input', e => {
      clearTimeout(timer);
      timer = setTimeout(() => onInput(e.target.value), delayMs);
    });

    input.addEventListener('keydown', onKeydown);

    function onInput(raw) {
      const term = raw.trim();
      hiddenId.value = '';
      icon.className = 'bi client-search__icon bi-person-plus';
      if (term.length < minChars) return clearList();
      fetchMatches(term);
    }

    async function fetchMatches(term) {
      try {
        const res = await fetch('./endPoints/clientes-busca.php?q=' + encodeURIComponent(term));
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        results = await res.json();
        selectedIndex = -1;
        renderList();
      } catch {
        console.error('Erro ao buscar clientes');
        clearList();
      }
    }

    function renderList() {
      list.innerHTML = '';
      results.forEach((cli, i) => {
        const li = document.createElement('li');
        li.textContent = cli.nome;
        li.addEventListener('click', () => choose(cli));
        list.appendChild(li);
      });
      list.style.display = results.length ? 'block' : 'none';
    }

    function onKeydown(e) {
      const items = Array.from(list.querySelectorAll('li'));
      if (!items.length) return;
      if (e.key === 'ArrowDown') {
        selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
        highlight(items);
        e.preventDefault();
      }
      if (e.key === 'ArrowUp') {
        selectedIndex = Math.max(selectedIndex - 1, 0);
        highlight(items);
        e.preventDefault();
      }
      if (e.key === 'Enter') {
        if (selectedIndex >= 0) {
          choose(results[selectedIndex]);
        } else if (results.length === 1) {
          choose(results[0]);
        }
        e.preventDefault();
      }
    }

    function highlight(items) {
      items.forEach((li, i) => li.classList.toggle('selecionado', i === selectedIndex));
      items[selectedIndex]?.scrollIntoView({ block: 'nearest' });
    }

    function choose(cli) {
      input.value = cli.nome;
      hiddenId.value = cli.id;
      icon.className = 'bi client-search__icon bi-eye';
      clearList();
      hiddenId.dispatchEvent(new CustomEvent('clienteSelecionado', {
        detail: cli,
        bubbles: true
      }));
    }

    function clearList() {
      list.innerHTML = '';
      list.style.display = 'none';
    }
  }

  const modal = document.getElementById('janela-agendamentos');
  
  if (modal) {
   
    ClientSearch(modal);
  }


}


inicial();




</script>

