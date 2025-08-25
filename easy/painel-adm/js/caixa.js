let currentSort = { field: 'id', order: 'ASC' };
let somaValorPrincipal = 0;
let somaValorLiquido = 0;
let selectedRow = null;  
let quantidadeMarcados = 0;
let menuPagamentoId = 0;
let pagamentoEmLote = false;
let buscando = false;
//let isModalOpen = false;

function calcularStatus(row, hoje = new Date()) {
  hoje.setHours(0,0,0,0);     // zera horas
  const valor      = Number(row.valor_principal);
  const pago       = !!row.pago;
  const dtVen      = row.data_vencimento   ? new Date(`${row.data_vencimento}T00:00:00`) : null;
  const dtPag      = row.data_pagamento    ? new Date(`${row.data_pagamento}T00:00:00`)   : null;

  let texto = '', cls = 'vazio';

  if (valor < 0) {           // ➜ SAÍDA
    if (pago) {
      texto = 'pago';
      cls   = dtPag && dtPag > dtVen ? 'st-pago-venc' : 'st-pago';
    } else if (dtVen) {
      const diff   = Math.ceil((dtVen - hoje)/86_400_000);
      if (hoje > dtVen)        { texto = 'vencido';        cls = 'st-vencido';   }
      else if (diff === 0)     { texto = 'vence hoje';     cls = 'st-hoje';      }
      else if (diff === 1)     { texto = 'vence amanhã';   cls = 'st-amanha';    }
      else                     { texto = `vence em ${diff} dias`;
                                 cls   = diff <= 7 ? 'st-vencendo' : 'st-pendente'; }
    }
  } else {                    // ➜ ENTRADA
    if (pago) {
      texto = hoje < dtPag ? 'a receber' : 'recebido';
      cls   = hoje < dtPag ? 'st-a-receber' : 'st-pago';
    } else if (dtVen) {
      const diff = Math.ceil((dtVen - hoje)/86_400_000);
      if (hoje > dtVen)        { texto = 'vencido';        cls = 'st-vencido';   }
      else if (diff === 0)     { texto = 'vence hoje';     cls = 'st-hoje';      }
      else if (diff === 1)     { texto = 'vence amanhã';   cls = 'st-amanha';    }
      else                     { texto = `vence em ${diff} dias`;
                                 cls   = diff <= 7 ? 'st-vencendo' : 'st-pendente'; }
    }
  }
  return { texto, cls };
}

let somaBruto   = 0;
let somaLiquido = 0;




document.addEventListener('DOMContentLoaded', function() {


  let recordMap = new Map();
  //const selectedIds = new Set();
  //let somaBruto = 0, somaLiquido = 0;
  

const container = document.querySelector('.financeiro-container');
// Seleciona os elementos relacionados à tabela dentro do contêiner.
const dataTableElement = container.querySelector('.financeiroTable');

if (!dataTableElement) return;

const searchInput = container.querySelector('.searchBox');
const rowsPerPageSelect = container.querySelector('.rowsPerPage');
const paginationDiv = container.querySelector('.pagination');
const infoRangeElement = container.querySelector('.info-range');

const selectedIds = new Set();

// somatórios acumulados


// span onde vai aparecer a soma
const spanResultado = document.getElementById('resultado-selecionados');
const btnExcluir = document.getElementById('btn-excluir');
const btnAlterarData = document.getElementById('btn-pagar');
const RECEITAS_VENDAS_CATEGORY_ID = 2 ;

// Variáveis de controle para esta instância da tabela.
let allData = [];      // Armazena os dados carregados via fetch.
let currentPage = 1;
let rowsPerPage = parseInt(rowsPerPageSelect.value);

  const thInit = dataTableElement.querySelector('thead th[data-sort-init]');
  if (thInit) {
    currentSort.field = thInit.getAttribute('data-field');
    currentSort.order = thInit.getAttribute('data-sort-init').toUpperCase(); // deve ser 'DESC'
  }





  function loadTableData() {
    limparSelecao();
   
    const url = construirURL();    // lerá activeConditions
    fetch(url)
      .then(r => r.json())
      .then(data => {
        allData = data.rows;
        recordMap = new Map(allData.map(r => [r.id, r]));
        // Armazenando saldos recebidos em variáveis globais ou num objeto específico
        window.saldosFinanceiro = {
          saldoBrutoDiaAnterior: data.saldoBrutoDiaAnterior,
          saldoLiquidoDiaAnterior: data.saldoLiquidoDiaAnterior,
          saldoBrutoAtual: data.saldoBrutoAtual,
          saldoLiquidoAtual: data.saldoLiquidoAtual,
          entradas_liquidas: data.entradas_liquidas,
          entradas_brutas: data.entradas_brutas,
          saidas_liquidas: data.saidas_liquidas,
          saidas_brutas: data.saidas_brutas,
          pendencias_liquidas: data.pendencias_liquidas,
          pendencias_brutas: data.pendencias_brutas
        };
  
  
        buscando = false;
        fetchData();
        exibirSaldos(); // função nova, abaixo explicada
      })
      .catch(console.error);
      buscando = false;
  }
  

    
    

  // Função para normalização de texto (útil para a busca).
  function normalizeText(text) {
    return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
  }

    
 

  const dataTipo = dataTableElement.getAttribute('data-tipo');

 
  
  function construirURL() {
    // Recalcula os campos lendo os th com data-field no THEAD
    
        
    const STATIC_FIELDS = [
      'id',
      'data_competencia',
      'data_vencimento',
      'data_pagamento',
      'descricao',
      'valor_principal',
      'valor_liquido',
      'categoria',
      'id_categoria',
      'conta',
      'tipo_pagamento',
      'forma_pagamento',
      'pago',
      'transferencia'
    ];
    // Monta a URL para a consulta
    let url = `entradas-saidas/gerar_tabela.php?table=${dataTableElement.getAttribute('data-table')}&fields=${STATIC_FIELDS}&saldos=${mostrarTotais}&all=true`;
    Object.entries(activeConditions).forEach(([key, value]) => {
      url += `&${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
    });
    return url;
  }
    

    // Função para exibir os registros na tabela.
  function displayRows(rows) {
    const tbody = dataTableElement.querySelector('tbody');
    tbody.innerHTML = '';
    
    
    const hoje = new Date();
    // zera hora/min/seg pra comparar só datas, se quiser:
    hoje.setHours(0, 0, 0, 0);

    rows.forEach(row => {
      const tr = document.createElement('tr');
  
      // Define ação para a linha se houver data-get ou data-modal no cabeçalho
      const headRow = dataTableElement.querySelector('thead tr');
      if (headRow) {
        if (headRow.hasAttribute('data-get')) {
          tr.style.cursor = "pointer";
          tr.addEventListener('click', () => {
            window.location.href = headRow.getAttribute('data-get') + row["id"];
          });
        } else if (headRow.hasAttribute('data-modal')) {
          tr.style.cursor = "pointer";
          tr.addEventListener('click', (e) => {
            // Se clicou em um TD com a classe checkbox OU em um input dentro dele → ignora
            if (isModalOpen) return;

            const el = e.target;
            if (el.closest('td')?.classList.contains('checkbox') || el.type === 'checkbox') {
              return;  // não abre modal
            }
        
            e.preventDefault();
            isModalOpen = true;
            abrirModal(headRow.getAttribute('data-modal'), row["id"], dataTipo);
          });
        }
      }
  
      // Cria uma TD para cada <th> do THEAD
      const ths = dataTableElement.querySelectorAll('thead th');
      ths.forEach(th => {
        const td = document.createElement('td');
  
        // Aplica as classes extras do th para a TD e à TR, se houver
        const tdClasses = th.getAttribute('data-classe-td');
        if (tdClasses) {
          tdClasses.split(" ").forEach(cls => td.classList.add(cls));
        }
        const trClasses = th.getAttribute('data-classe-tr');
        if (trClasses) {
          trClasses.split(" ").forEach(cls => tr.classList.add(cls));
        }
  
        // Se o th possuir data-field, obtém o valor correspondente da linha; caso contrário, deixa vazio
        const field = th.getAttribute('data-field');
        let cellValue = '';
        if (field) {
          cellValue = (row[field] !== undefined && row[field] !== null) ? row[field] : '';
        }
  
        // Processa o conteúdo da TD conforme as classes especiais do th
        if (th.classList.contains('data-img')) {
          const img = document.createElement('img');
          const imgPath = cellValue !== '' ? cellValue : 'sem-foto.svg';
          let basePath = th.getAttribute('data-img') || '../img/';
          img.src = basePath + imgPath;
          img.style.width = '40px';
          img.style.borderRadius = '50%';
          td.appendChild(img);
        } else if (th.classList.contains('data-color')) {
          const input = document.createElement('input');
          input.type = 'color';
          input.value = cellValue !== '' ? cellValue : '#ffffff';
          td.appendChild(input);
          } else if (th.classList.contains('data-modal')) {
          const a = document.createElement('a');
          a.href = "#";
          a.textContent = cellValue;
          a.addEventListener('click', function(e) {
            e.preventDefault();
            abrirModal(th.getAttribute('data-modal'), row['id'], dataTipo);
          });
        } else if (th.classList.contains('checkbox')) {
          const input = document.createElement('input');
          input.type = 'checkbox';
          input.classList.add('form-check-input', 'chktabela');
          input.dataset.id = row['id'];
          input.style.cursor = 'pointer';
        
          // 👉 injeta os raw-values para depois calcular
          input.dataset.valorPrincipal = row.valor_principal;
          input.dataset.valorLiquido  = row.valor_liquido;
        
          // mantém o estado caso já esteja selecionado
          if (selectedIds.has(row.id)) {
            input.checked = true;
          }
          td.style.cursor='default';
          td.appendChild(input);
          //isso para o clique na linha toda liberando a celula
         
          td.addEventListener('click', e => {
            
            e.stopPropagation();
            
          });
        }else if(th.classList.contains('f-pagamento')){
          td.textContent = row['tipo_pagamento'] || ''; // texto principal

            if (row['forma_pagamento']) {
                const span = document.createElement('span');
                span.className = 'sp-fpagamento';
                span.textContent = row['forma_pagamento'];
                span.style.display = 'block'; // força quebra de linha
                td.appendChild(span);
            }

          }  else if (th.classList.contains('status')) {
            const span = document.createElement('span');
            span.textContent = row.statusText;
            span.classList.add('status', row.statusClass);
            td.appendChild(span);
            
        } else {
          // Para colunas padrão, formata a data se necessário
          td.textContent = formatData(cellValue, th.getAttribute('data-sort'));
        }
  
        // Se o th tiver o atributo hidden, oculta a TD
        if (th.hasAttribute('hidden')) {
          td.style.display = 'none';
        }

        if(row.transferencia){
          td.classList.add('fin-transfencia');


        }
  
        // Se a TD deve ser formatada como número (classe "numVirg2c"), armazena o valor bruto
        if (td.classList.contains('numVirg2c')) {
          td.setAttribute('data-raw-value', cellValue);
          const num = parseFloat(cellValue);
        
          td.style.textAlign = 'right'; // alinhamento à direita
        
          // Remove classes antigas
          td.classList.remove('num-positivo', 'num-negativo');
        
          if (!isNaN(num)) {
            // Adiciona classe condicional
            td.classList.add(num > 0 ? 'num-positivo' : 'num-negativo');
        
            td.textContent = new Intl.NumberFormat('pt-BR', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            }).format(num);
          } else {
            td.textContent = '';
          }
        }
  
        tr.appendChild(td);
      });
  
      tbody.appendChild(tr);





  const $trs = $(tbody).find('tr');
  $trs.css({
    position: 'relative',
    left: '-100px',
    opacity: 0,
    filter: 'blur(5px)'
  }).each(function(i) {
    // anima uma a uma com pequeno delay entre elas
    $(this)
     .delay(i * 50)
     .animate(
       { left: '0px', opacity: 1 },
       {
         duration: 500,
         easing: 'swing',
         step(now, fx) {
           if (fx.prop === 'opacity') {
             const blurVal = (1 - now) * 5;
             $(this).css('filter', `blur(${blurVal}px)`);
           }
         },
         complete() {
           $(this).css('filter', '');
         }
       }
     );
  });









    });
    updateDeleteButtonState();
  }
  
    
    

  // Formata dados de data se necessário.
  function formatData(data, sortType) {
    if (sortType === 'data' && data) {
      let dateObj;
      // Se estiver no formato "YYYY-MM-DD", trata de forma local
      if (/^\d{4}-\d{2}-\d{2}$/.test(data)) {
        const [year, month, day] = data.split('-').map(Number);
        dateObj = new Date(year, month - 1, day);
      } else {
        // Caso contrário, tenta criar o objeto Date diretamente
        dateObj = new Date(data);
      }
      // Se a data for válida, retorna no formato dd/mm/yyyy; caso contrário, retorna o valor original
      if (!isNaN(dateObj.getTime())) {
        return (
          ('0' + dateObj.getDate()).slice(-2) + '/' +
          ('0' + (dateObj.getMonth() + 1)).slice(-2) + '/' +
          dateObj.getFullYear()
        );
      } else {
        // Se a conversão falhar (por exemplo, devido a formato inesperado), retorna a string original
        return data;
      }
    }
    return data;
  }

  // Funções de comparação para ordenação.
  function compareNumbers(a, b, order) {
    const numA = parseFloat(a);
    const numB = parseFloat(b);
    return order === 'ASC' ? numA - numB : numB - numA;
  }
  function compareStrings(a, b, order) {
    return order === 'ASC' ? a.localeCompare(b) : b.localeCompare(a);
  }
  function compareDates(a, b, order) {
    const dA = new Date(a);
    const dB = new Date(b);
    return order === 'ASC' ? dA - dB : dB - dA;
  }

  // Se houver <th> com data-sort-init, define a ordenação inicial.
  const thWithInitSort = dataTableElement.querySelector('thead th[data-sort-init]');
  if (thWithInitSort) {
    currentSort.field = thWithInitSort.getAttribute('data-field');
    currentSort.order = thWithInitSort.getAttribute('data-sort-init').toUpperCase();
  }

  // Evento de clique nos cabeçalhos para ordenar.
  dataTableElement.querySelectorAll('thead th[data-field]').forEach(th => {
    if (th.classList.contains('checkbox')) return;
   
    th.addEventListener('click', () => {
       console.log('clique na coluna');
       
      const field = th.getAttribute('data-field');
      const sortType = th.getAttribute('data-sort');
      if (currentSort.field === field) {
        currentSort.order = currentSort.order === 'ASC' ? 'DESC' : 'ASC';
      } else {
        currentSort.field = field;
        currentSort.order = 'ASC';
      }
      if (sortType === 'num') {
        allData.sort((a, b) => compareNumbers(a[field], b[field], currentSort.order));
      } else if (sortType === 'a-z') {
        allData.sort((a, b) => compareStrings(a[field], b[field], currentSort.order));
      } else if (sortType === 'data') {
        allData.sort((a, b) => compareDates(a[field], b[field], currentSort.order));
      }
      fetchData();
    });
  });

    // Event listeners para busca e alteração da quantidade de registros por página.
  searchInput.addEventListener('input', () => {
    currentPage = 1;
    fetchData();
  });
  rowsPerPageSelect.addEventListener('change', () => {
    rowsPerPage = parseInt(rowsPerPageSelect.value);
    currentPage = 1;
    fetchData();
  });

 

    // Função auxiliar para criar botões de paginação.
  function createPaginationButton(label, totalPages) {
    const btn = document.createElement('button');
    btn.textContent = label;
    btn.disabled = (label === '<<' && currentPage <= 1) ||
                    (label === '<' && currentPage <= 1) ||
                    (label === '>>' && currentPage >= totalPages) ||
                    (label === '>' && currentPage >= totalPages);
    btn.onclick = () => {
      switch (label) {
        case '<':
          currentPage = 1;
          break;
        case '<<':
          currentPage = Math.max(1, currentPage - 1);
          break;
        case '>>':
          currentPage = Math.min(totalPages, currentPage + 1);
          break;
        case '>':
          currentPage = totalPages;
          break;
        default:
          currentPage = parseInt(label, 10);
          break;
      }
      fetchData();
    };
    return btn;
  }

    // Função de paginação que preserva as classes e estilos originais.
  function setupPagination(totalRecords) {
    paginationDiv.innerHTML = ''; // Limpa os botões existentes.
    const totalPages = Math.ceil(totalRecords / rowsPerPage);

    // Botões de navegação para voltar: '<' e '<<'
    const navBackButtons = ['<', '<<'];
    navBackButtons.forEach(label => {
      const btn = createPaginationButton(label, totalPages);
      btn.classList.add('page-nav-button');
      paginationDiv.appendChild(btn);
    });

    // Determina o intervalo de páginas a mostrar.
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    for (let i = startPage; i <= endPage; i++) {
      const btn = createPaginationButton(i.toString(), totalPages);
      btn.classList.add('page-number-button');
      if (i === currentPage) {
        btn.classList.add('active-page');
      }
      paginationDiv.appendChild(btn);
    }

    // Botões de navegação para frente: '>>' e '>'
    const navForwardButtons = ['>>', '>'];
    navForwardButtons.forEach(label => {
      const btn = createPaginationButton(label, totalPages);
      btn.classList.add('page-nav-button');
      paginationDiv.appendChild(btn);
    });
  }






  somaValorPrincipal = 0;
  somaValorLiquido = 0;
  // Função principal para buscar os dados e aplicar filtros, ordenação e paginação.
  function fetchData() {

    
    const searchTerm = normalizeText(searchInput.value);
    const fieldsToSearch = Array.from(dataTableElement.querySelectorAll('thead th[data-field]'))
                                .map(th => th.getAttribute('data-field'));


    const hoje = new Date();
    
    const filtroPor = document.getElementById('filtrar-por')?.value || '';
    const filtroSelecionado = document.getElementById('filtro-selecionado')?.value || '';
 


    allData.forEach(row => {
      // calcula apenas uma vez por ciclo (caso queira otimizar)
      if (!row._statusReady) {
        const st = calcularStatus(row, hoje);
        row.statusText  = st.texto;
        row.statusClass = st.cls;
        row._statusReady = true;
      }
    });
    

   


      // Filtragem dos dados usando activeConditions e o termo de busca.
      const formatter = new Intl.NumberFormat('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
      
      function somenteNumeros(texto) {
        return texto.replace(/[^0-9]/g, '');
      }
      
      const filteredData = allData.filter(row => {

        if (filtroPor && filtroSelecionado) {
          if (filtroPor === 'status' && row.statusClass !== filtroSelecionado) return false;
          if (filtroPor === 'categoria' && String(row.id_categoria) !== String(filtroSelecionado)) return false;
        }
      

        const mostrarTransferencias = document.getElementById('chk_transf').checked;
        const mostrarEntradas = document.getElementById('chk_entradas').checked;
        const mostrarSaidas = document.getElementById('chk_saidas').checked;
      
        // Filtragem pela checkbox
        if (!mostrarTransferencias && row['transferencia']==1) {
          return false; // ignora transferências
        }

        if (!mostrarEntradas && row['valor_principal']>0) {
          return false; // ignora transferências
        }

        if (!mostrarSaidas && row['valor_principal']<0) {
          return false; // ignora transferências
        }

        return fieldsToSearch.some(field => {
          let fieldValue = row[field];
      
          if (typeof fieldValue === 'number' || (!isNaN(parseFloat(fieldValue)) && fieldValue !== null)) {
            fieldValue = formatter.format(parseFloat(fieldValue));
          }
      
          const valorCampoNormalizado = normalizeText(somenteNumeros((fieldValue || '').toString()));
          const valorBuscaNormalizado = normalizeText(somenteNumeros(searchTerm));
      
          if (valorBuscaNormalizado) {
            return valorCampoNormalizado.includes(valorBuscaNormalizado);
          } else {
            return normalizeText((fieldValue || '').toString()).includes(searchTerm);
          }
        });
       

      });
      
    
      // Calcula somas após filtragem
      somaValorPrincipal = filteredData.reduce((acc, row) => {
        const valor = parseFloat(row.valor_principal) || 0;
        return acc + valor;
      }, 0);
    
      somaValorLiquido = filteredData.reduce((acc, row) => {
        const valor = parseFloat(row.valor_liquido) || 0;
        return acc + valor;
      }, 0);
    
      // Exemplo de uso das variáveis (log no console para teste)
      let clsadd ='';
      if (somaValorPrincipal>0){
        clsadd='num-positivo';
      }else{
        clsadd='num-negativo';
      }

      const blSoma = document.getElementById('spResultadoFilt');
      const chkLiqui = document.getElementById('chk_liqui');
      let somaValor = (chkLiqui.checked)? somaValorLiquido: somaValorPrincipal;

      blSoma.textContent = 'R$ ' + formatarNumeroParaVirgula(somaValor);
      blSoma.classList.remove('num-positivo', 'num-negativo');
      blSoma.classList.add(clsadd);
      
      console.log(`Soma Valor Principal: ${somaValorPrincipal}`);
      console.log(`Soma Valor Líquido: ${somaValorLiquido}`);
    
      // Restante do código (ordenação, paginação, etc.)
      const orderFunction = (() => {
        const sortType = dataTableElement.querySelector(`thead th[data-field="${currentSort.field}"]`).getAttribute('data-sort');
        if (sortType === 'num') {
          return (a, b) => compareNumbers(a[currentSort.field], b[currentSort.field], currentSort.order);
        } else if (sortType === 'data') {
          return (a, b) => compareDates(a[currentSort.field], b[currentSort.field], currentSort.order);
        } else {
          return (a, b) => compareStrings(normalizeText(a[currentSort.field]), normalizeText(b[currentSort.field]), currentSort.order);
        }
      })();


      filteredData.sort(orderFunction);
    
      const totalRecords = filteredData.length;
      const startIdx = (currentPage - 1) * rowsPerPage;
      const paginatedData = filteredData.slice(startIdx, startIdx + rowsPerPage);
    
      displayRows(paginatedData);
      setupPagination(totalRecords);
      updateInfoRange(currentPage, rowsPerPage, totalRecords);
    
      formatBrazilianNumbers();  
  }
  //=========== FIM DE fetchData==================

const selFiltro = document.getElementById('filtrar-por');
const selDestino = document.getElementById('filtro-selecionado');

  selFiltro.addEventListener('change', function () {
    limparSelecao();

    if (this.value === 'status') {
      /* limpa opções antigas */
      selDestino.innerHTML = '<option value="">Todos</option>';

      /* insere cada status como <option> */
      statusLabels.forEach(({ cls, text }) => {
        const opt = document.createElement('option');
        opt.value = cls;       // value = classe CSS
        opt.textContent = text; // texto visível
        selDestino.appendChild(opt);

        $('#filtro-selecionado').selectpicker('refresh');
      });
      selDestino.removeAttribute('disabled');
    } else if (this.value === 'categoria') {
      selDestino.innerHTML = '<option value="">Todas Categorias</option>';
      categoriasContabeis.forEach(({ id, nome }) => {
        const opt = document.createElement('option');
        opt.value = id;
        opt.textContent = nome;
        selDestino.appendChild(opt);
      });
      $('#filtro-selecionado').selectpicker('refresh');
      selDestino.removeAttribute('disabled');}
    else if (this.value === ''){
      /* se outro critério foi escolhido, limpa / desabilita */
     
     ;
      selDestino.innerHTML = '';
       $('#filtro-selecionado').selectpicker('refresh')
      
     // selDestino.setAttribute('disabled', true);

      fetchData();
     
    }
  });

  selDestino.addEventListener('change', function(){
    limparSelecao();
      fetchData();
  });

  // Atualiza a exibição do intervalo de registros.
  function updateInfoRange(currentPage, rowsPerPage, totalRows) {
    const startIndex = (currentPage - 1) * rowsPerPage + 1;
    const endIndex = Math.min(startIndex + rowsPerPage - 1, totalRows);
    infoRangeElement.textContent = `Exibindo de ${startIndex} a ${endIndex} de um total de ${totalRows} registros`;
  }


  

    
    
    
    
    
  const chkTransf = document.getElementById('chk_transf');
  chkTransf.addEventListener('change', () => {
      limparSelecao();
      currentPage = 1;
      fetchData();
  });

  const chkEnt = document.getElementById('chk_entradas');
  chkEnt.addEventListener('change', () => {
      limparSelecao();
      currentPage = 1;
      fetchData();
  });

  const chkSaid = document.getElementById('chk_saidas');
  chkSaid.addEventListener('change', () => {
    limparSelecao();  
    currentPage = 1;
    fetchData();
  });


  loadTableData();

  //ouvindo o tipo de data para colocar o atributo tadafild no th da tabela
  tipoData.addEventListener('change', () => {
    const novoCampo = tipoData.value;
    thData.setAttribute('data-field', novoCampo);        // atualiza o atributo
    currentSort.field = novoCampo;                       // opcional: manter sort sincronizado
    
  });



//povinte do botão buscar  
  btnBuscar.addEventListener('click', function(e) {
    e.preventDefault();
    blocosSaldos('carrega');
    
    if (buscando) return;

    buscando = true;

  
    let txtFtConta='';
    const ftConta = filtroConta.value; ////
      if (chk_liqui.checked) {
        thLiqu.setAttribute('hidden', 'true');
        thBrut.removeAttribute('hidden',);
    } else if (!chk_liqui) {
        thLiqu.removeAttribute('hidden');
        thBrut.setAttribute('hidden', 'true');
    }

    if(ftConta== 'Todas'){
        txtFtConta='';

    }else{
      txtFtConta= ', "id_conta":"'+ ftConta + '"';
    }

       
    const tpData = tipoData.value;
    
    currentSort.field = tpData;          // mantém sort em sincronia
    /** ----------------- */


    if (currentSort.field === 'data_vencimento' || currentSort.field === 'data_pagamento' || currentSort.field === 'data_competencia') {
      currentSort.order = 'DESC';
    }
    
    const dtInicial = dataInicial.value;
    const dtFinal = dataFinal.value;
    
    console.log('data inicial: ' + dtInicial + '  Data Final:  '  + dtFinal);

    const jsonStr = `{"${tpData}":"${dtInicial}<->${dtFinal}"${txtFtConta}}`;
    console.log('Active condictions em json  ' + jsonStr)
  // converte em objeto JS
    currentPage = 1;
    activeConditions = JSON.parse(jsonStr);

    loadTableData();
  
  }); //fim do ouvinte do botão buscar


//ouvinte dos checks
  
  container.addEventListener('change', e => {
    
    if (!e.target.matches('.chktabela')) return;
    console.log('container check clique acionado');
    const cb   = e.target;
    const id   = Number(cb.dataset.id);
    const vp   = parseFloat(cb.dataset.valorPrincipal) || 0;
    const vl   = parseFloat(cb.dataset.valorLiquido)  || 0;

    if (cb.checked) {
      selectedIds.add(id);
      somaBruto   += vp;
      somaLiquido += vl;
    } else {
      selectedIds.delete(id);
      somaBruto   -= vp;
      somaLiquido -= vl;
    }
    quantidadeMarcados = document.querySelectorAll('.chktabela:checked').length;
    // atualiza o span em PT-BR com R$
    atualizaResultChecks();
    updateDeleteButtonState();

  });



  btnExcluir.addEventListener('click', () => {
    if (btnExcluir.disabled) return;
  
    const idsToDelete = Array.from(selectedIds);
    enviarExclusao(idsToDelete);
  
  
  });





  function updateDeleteButtonState() {
    // começa assumindo que botão deve estar desabilitado

    let disableExcluir = selectedIds.size === 0;
    let anyInvalid    = false;

    let disable = selectedIds.size === 0;
  
    for (const id of selectedIds) {
      const row = recordMap.get(id);
      if (!row) continue;
      // se for transferência ou categoria de vendas, bloqueia
      if (row.transferencia === 1 ||
          row.id_categoria === RECEITAS_VENDAS_CATEGORY_ID) {
      anyInvalid = true;
      break;
      }
    }
  
    btnExcluir.disabled = disableExcluir || anyInvalid;
    const disableAlterarData = selectedIds.size === 0
    || Array.from(selectedIds).some(id => recordMap.get(id)?.transferencia === 1);

    btnAlterarData.disabled = disableAlterarData;
  }



 // btnAlterarData.addEventListener('click', () => {
    
    // ex: abrir modal de alteração de data, enviando esses ids
    //abrirModalAlterarData(ids);
  //});

  //3const BtnPagar = document.getElementById('btn-pagar');

  btnAlterarData.addEventListener('click', function(e){

    if (btnAlterarData.disabled) return;
    const ids = Array.from(selectedIds);
    e.preventDefault();
    pagamentoEmLote=true;
    showCalendPagamento(e.pageX, e.pageY);

  })

  function atualizaResultChecks(){
    const blSel = document.getElementById('bl-result-sel');
      spanResultado.classList.remove('num-negativo', 'num-positivo');
      
      if (quantidadeMarcados==0){
        blSel.setAttribute('hidden', 'true');
      }else{
        blSel.removeAttribute('hidden');
      
        if (chkLiqui.checked){
        spanResultado.textContent =
          `R$ ${formatarNumeroParaVirgula(somaLiquido)}`;
        } else{
          spanResultado.textContent = 
          `R$ ${formatarNumeroParaVirgula(somaBruto)}`;
        }

        
        if (somaBruto<0){
          spanResultado.classList.add('num-negativo');
        }else if(somaBruto>0){
          spanResultado.classList.add('num-positivo');
        }

      }
  }

  const btnSelectAll = document.getElementById('chk-all');
  btnSelectAll.addEventListener('click', () => {
    
    document.querySelectorAll('.chktabela').forEach(cb => {
      if (!cb.checked) {
        cb.checked = true;
        const id = Number(cb.dataset.id);
        const vp = parseFloat(cb.dataset.valorPrincipal) || 0;
        const vl = parseFloat(cb.dataset.valorLiquido)  || 0;
        selectedIds.add(id);
        somaBruto   += vp;
        somaLiquido += vl;
      }
    });
    quantidadeMarcados = selectedIds.length;
    atualizaResultChecks();
  });

  const btnSelectNone = document.getElementById('chk-no');
  btnSelectNone.addEventListener('click', () => {
    document.querySelectorAll('.chktabela').forEach(cb => {
      if (cb.checked) {
        cb.checked = false;
        const id = Number(cb.dataset.id);
        const vp = parseFloat(cb.dataset.valorPrincipal) || 0;
        const vl = parseFloat(cb.dataset.valorLiquido)  || 0;
        selectedIds.delete(id);
        somaBruto   -= vp;
        somaLiquido -= vl;
        quantidadeMarcados = 0;
      }
    });
    atualizaResultChecks();
  });

  function limparSelecao() {
  // limpa o estado
    const blSel = document.getElementById('bl-result-sel');

    blSel.setAttribute('hidden', 'true')
    selectedIds.clear();
    somaBruto   = 0;
    somaLiquido = 0;

    // desmarca os checkboxes na tela
    document
      .querySelectorAll('.chktabela')
      .forEach(cb => { cb.checked = false; });

    // atualiza o span de resultado
    atualizaResultChecks()
  }


//const btnExcInd = document.getElementById('btn-excluir-ind');






// ─────────────EXCLUIR LANÇAMENTO ────────────────
// 1) Função genérica de exclusão

function excluirIndividual(id) {
  if (!confirm('Deseja realmente excluir este registro?')) return;
  enviarExclusao([id]);
}

async function enviarExclusao(ids) {
  try {
    const res = await fetch('entradas-saidas/excluir.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ids })
    });
    const json = await res.json();
    if (!json.success) {
      throw new Error(json.error || 'Erro desconhecido ao excluir');
    }
    // sucesso: limpa seleção e recarrega tabela
    limparSelecao();
    await loadTableData();
  } catch (err) {
    console.error('Falha na exclusão:', err);
    alert('Não foi possível excluir:\n' + err.message);
  }
}

// ───────────────────────────────────────────────────────────








const menu   = document.getElementById('custom-menu');

    /* ----- funções show/hide ----- */
    function showMenu(x, y) {

      menu.style.display = 'block';

      const menuRect = menu.getBoundingClientRect();
      const winW     = window.innerWidth;
      const winH     = window.innerHeight;
    
      let top  = y;
      let left = x;
    
      // se ultrapassar na vertical, posiciona acima
      if (top + menuRect.height > winH) {
        top = y - menuRect.height;
        // se ainda assim for negativo, fixa no topo
        if (top < 0) top = 0;
      }
    
      // se ultrapassar na horizontal, reposiciona à esquerda
      if (left + menuRect.width > winW) {
        left = winW - menuRect.width;
        if (left < 0) left = 0;
      }
    
      menu.style.top  = `${top}px`;
      menu.style.left = `${left}px`;


    }


  

    const CalendPagamento   = document.getElementById('custom-calend');

    function showCalendPagamento(x, y) {
      // obtém o array de IDs selecionados

      

      let idParaCarregar;
      if (pagamentoEmLote) {
        // lote → pega o primeiro dos selecionados
        const ids = Array.from(selectedIds);
        if (ids.length === 0) return;
        idParaCarregar = ids[0];
      } else {
        // menu único → usa o menuPagamentoId
        if (!menuPagamentoId) return;
        idParaCarregar = menuPagamentoId;
      }
      const primeiro = recordMap.get(idParaCarregar);
      const inputDate = document.getElementById('novoDataPagamento');
      if (primeiro && primeiro.data_pagamento) {
        inputDate.value = primeiro.data_pagamento; // formato "YYYY-MM-DD"
      } else {
        inputDate.value = '';
      }
    
      // torna o container visível para medir seu tamanho
      CalendPagamento.style.display = 'block';
    
      // mede dimensões e limites da janela
      const menuRect = CalendPagamento.getBoundingClientRect();
      const winW     = window.innerWidth;
      const winH     = window.innerHeight;
    
      // posiciona inicialmente em x,y
      let top  = y;
      let left = x;
    
      // se ultrapassar verticalmente, posiciona acima do clique
      if (top + menuRect.height > winH) {
        top = y - menuRect.height;
        if (top < 0) top = 0;
      }
    
      // se ultrapassar horizontalmente, reposiciona à esquerda
      if (left + menuRect.width > winW) {
        left = winW - menuRect.width;
        if (left < 0) left = 0;
      }
    
      // aplica as coordenadas ajustadas
      CalendPagamento.style.top  = `${top}px`;
      CalendPagamento.style.left = `${left}px`;
    
      // foca automaticamente o campo de data
      inputDate.focus();
    }
    




    function hideCalend() {
    CalendPagamento.style.display = 'none';
    //selectedRow = null;
    }

    function hideMenu() {
    menu.style.display = 'none';
    
    if (selectedRow){
    selectedRow.classList.remove('linha-selecionada');}

    selectedRow = null;
    }




    /* ----- clique direito na linha ----- */
    tabela.addEventListener('contextmenu', function (e) {
      if (selectedRow){
        selectedRow.classList.remove('linha-selecionada');
      }
    const tr = e.target.closest('tr.tr-avisos');
    if (tr) {

        e.preventDefault();
        selectedRow = tr;
        selectedRow.classList.add('linha-selecionada');
        console.log('TR Capturada: ', tr);               // guarda a linha;
        showMenu(e.pageX, e.pageY);
    } else {
        hideMenu();
    }
    });






    document.addEventListener('click', e => {
      // se clicar num item do menu, NÃO esconda ainda
      if (e.target.closest('#custom-menu .menu-item')) return;
      // se clicou em QUALQUER outra coisa fora do menu, esconde
      if (!e.target.closest('#custom-menu')) hideMenu();
    });


    window.addEventListener('scroll', hideMenu);


    const btnSalvarData    = document.getElementById('btn-salvar-data');
    const btnCancelarData = document.getElementById('btn-cancelar-data');
    
    btnCancelarData.addEventListener('click', () => {
      hideCalend();
      hideMenu();
    });


    
    btnSalvarData.addEventListener('click', async () => {
      const novaData = document.getElementById('novoDataPagamento').value;
      if (!novaData) {
        alert('Escolha uma data válida.');
        return;
      }
      // ids selecionados


        let idsParaAlterar;
        if (pagamentoEmLote) {
          idsParaAlterar = Array.from(selectedIds);
          console.log('vários ids:' + idsParaAlterar);
        } else {
          if (!menuPagamentoId) return alert('Nenhum registro selecionado.');
          idsParaAlterar = [menuPagamentoId];
        }

      try {
        // chame seu endpoint de alterar data, por exemplo:
        const res = await fetch('entradas-saidas/alterar_data_pagamento.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            ids:       idsParaAlterar,  // <- mudou aqui
            novaData:  novaData
          })
        });
        const json = await res.json();
        if (!json.success) throw new Error(json.error||'Falha');
        // recarrega a tabela
        hideCalend();
        hideMenu();
        await loadTableData();
      } catch (err) {
        console.error(err);
        alert('Erro ao alterar data:\n' + err.message);
      }
    });











    /* =========================================================
    2) ⬇ handler do clique dentro do menu de contexto
    ========================================================= */
    document.addEventListener('click', function (e) {

    
    const menuItem = e.target.closest('#custom-menu .menu-item');
    

    if (!menuItem || !selectedRow) return;   // não clicou num item
    const acao = menuItem.dataset.clique;
    
    if (acao === 'pagar') {
     
      const cb = selectedRow.querySelector('.chktabela');
      const id = cb ? Number(cb.dataset.id) : null;
      if (!id) {
        alert('Erro interno: não consegui identificar o registro para pagamento.');
        hideMenu();
        return;
      }
      pagamentoEmLote = false;
      menuPagamentoId = id;
      // grava apenas esse id
      console.log('cliqueno MENU');
      // abre o popup de data naquele ponto
      showCalendPagamento(e.pageX, e.pageY);
  
      //hideMenu();
      return;
    }

    if (acao === 'excluir') {
        // pega o ID da linha
        const cb = selectedRow.querySelector('.chktabela');
        const id = cb ? Number(cb.dataset.id) : null;
        if (id == null) {
            alert('Erro interno: não consegui identificar o registro.');
        } else {
            const row = recordMap.get(id);
            if (row.id_categoria === RECEITAS_VENDAS_CATEGORY_ID) {
            alert('Não é permitido excluir Receitas de Vendas.');
            } else {
            // dispara a exclusão individual
            excluirIndividual(id);
            }
        }
        hideMenu();
        return;
    }
        // editar / pagar / …

    if (acao === 'editar') {
        const headRow = document.querySelector('#tabelaExtrato thead tr');
        const tipo    = document.getElementById('tabelaExtrato').dataset.tipo;
        const id      = selectedRow.querySelector('.chktabela')?.dataset.id;

        if (headRow?.dataset.modal && id) {
        abrirModal(headRow.dataset.modal, id, tipo);
        }
    }

    // outras ações (pagar, upload, excluir) depois…

    hideMenu();   // fecha o menu em qualquer caso
    });


    //-----ouvinte do modal aberto

   
});







//========================================================================

function exibirSaldos() {
     
  saldos = window.saldosFinanceiro;
  if(!saldos) return;


  const spanResultado = document.getElementById('resultado-selecionados');
  const blSel = document.getElementById('bl-result-sel');
  const chekLiqui = document.getElementById('chk_liqui');
      spanResultado.classList.remove('num-negativo', 'num-positivo');
      if (somaBruto==0){
        blSel.setAttribute('hidden', 'true');
      }else{
        blSel.removeAttribute('hidden');
      
        if (chekLiqui.checked){
        spanResultado.textContent =
          `R$ ${formatarNumeroParaVirgula(somaLiquido)}`;
        } else{
          spanResultado.textContent = 
          `R$ ${formatarNumeroParaVirgula(somaBruto)}`;
        }

        
        if (somaBruto<0){
          spanResultado.classList.add('num-negativo');
        }else if(somaBruto>0){
          spanResultado.classList.add('num-positivo');
        }

      }
  // Exemplo simples: console.log ou exibir em divs específicas
  
  console.log("Saldo Líquido Dia Anterior:", saldos.saldoLiquidoDiaAnterior);
  console.log("Saldo Bruto Atual:", saldos.saldoBrutoAtual);
  console.log("Saldo Líquido Atual:", saldos.saldoLiquidoAtual);

  const chkLiqui = document.getElementById('chk_liqui').checked;
  const blSaldoAnterior = document.getElementById('saldo-dia-anterior');
  const blSaldoPeriodo = document.getElementById('saldo-periodo');
  const blSaldoAtual = document.getElementById('saldo-atual');
  const blEntPer = document.getElementById('entradas-periodo');
  const blSaiPer= document.getElementById('saidas-periodo');
  const blVencPer= document.getElementById('vencidos-periodo');

  blSaldoAnterior.classList.remove('num-positivo', 'num-negativo');
  blSaldoPeriodo.classList.remove('num-positivo', 'num-negativo');
  blSaldoAtual.classList.remove('num-positivo', 'num-negativo');
 
  blVencPer.classList.remove('num-positivo', 'num-negativo');



  if (!chkLiqui){
    blSaldoAnterior.textContent = 'R$ ' + formatarNumeroParaVirgula(saldos.saldoBrutoDiaAnterior);
    blSaldoPeriodo.textContent  = 'R$ ' + formatarNumeroParaVirgula(saldos.saldoBrutoAtual-saldos.saldoBrutoDiaAnterior);
    blSaldoAtual.textContent  = 'R$' + formatarNumeroParaVirgula(saldos.saldoBrutoAtual);
    blEntPer.textContent  = 'R$' + formatarNumeroParaVirgula(saldos.entradas_brutas);
    blSaiPer.textContent  = 'R$' + formatarNumeroParaVirgula(saldos.saidas_brutas);
    blVencPer.textContent  = 'R$' + formatarNumeroParaVirgula(saldos.pendencias_brutas);

    if (saldos.saldoBrutoDiaAnterior<0){blSaldoAnterior.classList.add('num-negativo')}else{blSaldoAnterior.classList.add('num-positivo')}
    if (saldos.saldoBrutoAtual-saldos.saldoBrutoDiaAnterior<0){blSaldoPeriodo.classList.add('num-negativo')}else{blSaldoPeriodo.classList.add('num-positivo')}
    if (saldos.saldoBrutoAtual<0){blSaldoAtual.classList.add('num-negativo')}else{blSaldoAtual.classList.add('num-positivo')}

    if (saldos.pendencias_brutas<0){blVencPer.classList.add('num-negativo')}else{blVencPer.classList.add('num-positivo')}




  }else{
    blSaldoAnterior.textContent  = 'R$ ' + formatarNumeroParaVirgula(saldos.saldoLiquidoDiaAnterior);
    blSaldoPeriodo.textContent  = 'R$ ' + formatarNumeroParaVirgula(saldos.saldoLiquidoAtual-saldos.saldoLiquidoDiaAnterior);
    blSaldoAtual.textContent  = 'R$' + formatarNumeroParaVirgula(saldos.saldoLiquidoAtual);
    blEntPer.textContent  = 'R$' + formatarNumeroParaVirgula(saldos.entradas_liquidas);
    blSaiPer.textContent  = 'R$' + formatarNumeroParaVirgula(saldos.saidas_liquidas);
    blVencPer.textContent  = 'R$' + formatarNumeroParaVirgula(saldos.pendencias_liquidas);


    if (saldos.saldoLiquidoDiaAnterior<0){blSaldoAnterior.classList.add('num-negativo')}else{blSaldoAnterior.classList.add('num-positivo')}
    if (saldos.saldoLiquidoAtual-saldos.saldoLiquidoDiaAnterior<0){blSaldoPeriodo.classList.add('num-negativo')}else{blSaldoPeriodo.classList.add('num-positivo')}
    if (saldos.saldoLiquidoAtual<0){blSaldoAtual.classList.add('num-negativo')}else{blSaldoAtual.classList.add('num-positivo')}

    if (saldos.pendencias_liquidas<0){blVencPer.classList.add('num-negativo')}else{blVencPer.classList.add('num-positivo')}

  }

 
}





//para formatar conforme as classas

function formatarBrasileiro() {
  // Cria um formatador para o padrão brasileiro
  const formatter = new Intl.NumberFormat('pt-BR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
  });
  // Percorre todas as células da tabela que possuem a classe numVirg2c
  document.querySelectorAll('.dataTable td.numVirg2c').forEach(td => {
      // Recupera o valor bruto armazenado
      const raw = td.getAttribute('data-raw-value');
      if (raw !== null && raw.trim() !== "") {
          const num = parseFloat(raw);
          if (!isNaN(num)) {
              // Atualiza somente o texto exibido com o valor formatado
              td.textContent = formatter.format(num);
          }
      }
  });
}


 //===================FIM DE DOMLOADED CONTENT===================================