// servicos/tabModServAbaProf.js
(function(){


  
  function br2dec(str){
    if (str == null) return null;
    const s = String(str).trim();
    if (s === '') return null;
    return parseFloat(s.replace(/\./g,'').replace(',','.'));
  }

  function decimalBR(num, casas=2){
    if (num == null || num === '') return '';
    const n = Number(num);
    if (isNaN(n)) return '';
    return n.toFixed(casas).replace('.',',');
  }

  // debounce para inputs (evita salvar a cada keypress)
  const __debouncers = new Map();
  function debounceRow(tr, fn, delay=700){
    const key = tr.dataset.id_profissional || Math.random();
    if (__debouncers.has(key)) clearTimeout(__debouncers.get(key));
    const t = setTimeout(fn, delay);
    __debouncers.set(key, t);
  }

const container = document.querySelector('.table-containerProf');
  if (!container) return;

  const modal     = document.getElementById('modalCadServico');
  const idInput   = document.querySelector('#frm-id');
  const idServico = (idInput?.value || '').trim();

  // NOVO SERVIÇO: ainda não tem ID → espera o evento e sai
  if (!idServico) {
    const onSaved = (e) => {
      const novoId = e.detail?.id;
      if (!novoId) return;
      document.removeEventListener('servico:salvo', onSaved);
      if (idInput) idInput.value = novoId;
      TabelaModServAbaProf(); // re-roda agora com ID
    };
    document.addEventListener('servico:salvo', onSaved, { once: true });

    // limpeza: se o modal fechar sem salvar, remove o listener
    modal?.addEventListener('hidden.bs.modal', () => {
      document.removeEventListener('servico:salvo', onSaved);
    }, { once: true });

    return; // <- nada de carregar dados sem ID
  }



  function TabelaModServAbaProf () {
    const container = document.querySelector('.table-containerProf');
    if (!container) return;

    const table = container.querySelector('#tabelaAbaProf');
    const tbody = table.querySelector('tbody');
    const searchInput = container.querySelector('.searchBox');
    const idServico = document.querySelector('#frm-id')?.value || '';

    // Carrega dados
    function load(){
      const url = `servicos/SModServTabProf.php?id_servico=${encodeURIComponent(idServico)}`;
      return fetch(url, {cache:'no-store'})
        .then(r => r.json())
        .then(j => Array.isArray(j.rows) ? j.rows : []);
    }

    function buildRow(row, idx){
      const tr = document.createElement('tr');
      tr.dataset.id_profissional = row.id_profissional;
      tr.dataset.id_serv_prof    = row.id_serv_prof || '';
      tr.dataset.id_contrato     = row.id_contrato || '';

      // FOTO
      const tdFoto = document.createElement('td');
      const img = document.createElement('img');
      const base = (pastaFiles ? `../${pastaFiles}/img/cadastro_colaboradores/` : '../img/');
      img.src = row.foto_profissional ? (base + row.foto_profissional) : '../img/sem-foto.svg';
      img.style.width = '40px';
      img.style.height = '40px';
      img.style.objectFit = 'cover';
      img.style.borderRadius = '50%';
      tdFoto.appendChild(img);

      // NOME
      const tdNome = document.createElement('td');
      tdNome.textContent = row.profissional || '';
      
      // EXECUTA
      const tdExe = document.createElement('td');
      tdExe.style.textAlign = 'center';
      const chkExe = document.createElement('input');
      chkExe.type = 'checkbox';
      chkExe.className = 'form-check-input';
      chkExe.checked = String(row.executa) === '1';
      tdExe.appendChild(chkExe);


      // hidden (se quiser inspecionar)
      const tdIdProf = document.createElement('td'); tdIdProf.hidden = true; tdIdProf.textContent = row.id_profissional;
      const tdIdContrato = document.createElement('td'); tdIdContrato.hidden = true; tdIdContrato.textContent = row.id_contrato || '';
      const tdIdServProf = document.createElement('td'); tdIdServProf.hidden = true; tdIdServProf.textContent = row.id_serv_prof || '';

      // TEMPO
      const tdTempo = document.createElement('td');
      const inpTempo = document.createElement('input');
      inpTempo.type = 'text';
      inpTempo.className = 'form-control form-control-sm numero-inteiro';
      inpTempo.style.maxWidth = '90px';
      inpTempo.value = row.tempo != null ? row.tempo : '';
      tdTempo.appendChild(inpTempo);

      // COMISSÃO (%)
      const tdComissao = document.createElement('td');
      const grpC = document.createElement('div'); grpC.className = 'input-group input-group-sm'; grpC.style.maxWidth = '120px';
      const inpComissao = document.createElement('input');
      inpComissao.type = 'text';
      inpComissao.className = 'form-control numero-porcento';
      inpComissao.value = (row.comissao != null && row.comissao !== '') ? decimalBR(row.comissao) : '';
      const spPct = document.createElement('span'); 
      spPct.className = 'input-group-text'; 
      spPct.textContent = '%';
      spPct.style.height ='25px';
      grpC.appendChild(inpComissao); grpC.appendChild(spPct);
      tdComissao.appendChild(grpC);

      // PREÇO (R$)
      const tdPreco = document.createElement('td');
      const grpP = document.createElement('div'); grpP.className = 'input-group input-group-sm'; grpP.style.maxWidth = '140px';
      const spR = document.createElement('span'); spR.className = 'input-group-text'; spR.textContent = 'R$';
      spR.style.height ='25px';
      const inpPreco = document.createElement('input');
      inpPreco.type = 'text';
      inpPreco.className = 'form-control numero-virgula-financeiro';
      inpPreco.value = (row.preco != null && row.preco !== '') ? decimalBR(row.preco) : '';
      grpP.appendChild(spR); grpP.appendChild(inpPreco);
      tdPreco.appendChild(grpP);

      // AGENDAMENTO ONLINE
      const tdAg = document.createElement('td');
      tdAg.style.textAlign = 'center';
      const chkAg = document.createElement('input');
      chkAg.type = 'checkbox';
      chkAg.className = 'form-check-input';
      chkAg.checked = String(row.agendamento_online) === '1';
      tdAg.appendChild(chkAg);


      // listeners para salvar
      function gatherPayload(){
        return {
          id_servico:      idServico,
          id_profissional: tr.dataset.id_profissional,
          id_contrato:     tr.dataset.id_contrato,
          id_serv_prof:    tr.dataset.id_serv_prof || '',
          tempo:           inpTempo.value.trim(),
          comissao:        inpComissao.value.trim(),
          preco:           inpPreco.value.trim(),
          agendamento_online: chkAg.checked ? 1 : 0,
          executa:            chkExe.checked ? 1 : 0
        };
      }

      function saveNow(){
        const payload = gatherPayload();

        // Normaliza números do lado do cliente (não obrigatório; PHP também trata)
        payload.tempo    = payload.tempo === '' ? '' : parseInt(payload.tempo,10);
        payload.comissao = payload.comissao === '' ? '' : decimalBR(br2dec(payload.comissao)); // mantém vírgula no input
        payload.preco    = payload.preco === '' ? '' : decimalBR(br2dec(payload.preco));

        $.ajax({
          url: 'servicos/UModServicosProf.php',
          type: 'POST',
          dataType: 'json',
          data: payload,
          success: function(res){
            if (res && res.success && res.data && res.data.id){
              tr.dataset.id_serv_prof = res.data.id; // passa a atualizar ao invés de inserir
            } else if (res && !res.success) {
              alert(res.msg || 'Falha ao salvar');
            }
          },
          error: function(xhr){
            alert('Erro ao salvar: ' + (xhr.statusText || xhr.status));
          }
        });
      }

      // inputs com debounce
      inpTempo.addEventListener('input', () => debounceRow(tr, saveNow));
      inpTempo.addEventListener('change', saveNow);
      inpComissao.addEventListener('input', () => debounceRow(tr, saveNow));
      inpComissao.addEventListener('change', saveNow);
      inpPreco.addEventListener('input', () => debounceRow(tr, saveNow));
      inpPreco.addEventListener('change', saveNow);

      // checkboxes salvam imediato
      chkAg.addEventListener('change', saveNow);
      chkExe.addEventListener('change', saveNow);

      // monta tr
      tr.appendChild(tdFoto);
      tr.appendChild(tdNome);
      tr.appendChild(tdExe);
      tr.appendChild(tdIdProf);
      tr.appendChild(tdIdContrato);
      tr.appendChild(tdIdServProf);
      tr.appendChild(tdTempo);
      tr.appendChild(tdComissao);
      tr.appendChild(tdPreco);
      tr.appendChild(tdAg);
      

      return tr;
    }

    function render(rows){
      // filtro local por nome
      const q = (searchInput.value || '').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase();
      const filtered = rows.filter(r => {
        const s = String(r.profissional || '')
          .normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase();
        return s.includes(q);
      });

      tbody.innerHTML = '';
      filtered.forEach((r, i) => tbody.appendChild(buildRow(r, i)));
    }

    // busca inicial e a cada digitação no search
    let cache = [];
    load().then(rows => { cache = rows; render(cache); });
    searchInput?.addEventListener('input', () => render(cache));
  }

  // exporta e auto-inicializa (como você fez)
  window.TabelaModServAbaProf = TabelaModServAbaProf;
  TabelaModServAbaProf();

})();
