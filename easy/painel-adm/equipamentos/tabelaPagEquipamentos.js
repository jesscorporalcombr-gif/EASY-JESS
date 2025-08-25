

(function(){
    // Suporta várias tabelas; cada tabela deve estar dentro de um contêiner com a classe .table-container.
    const tableContainers = document.querySelectorAll('.table-container');
    
    let activeConditions = {};
    


   
    tableContainers.forEach(container => {
      // Seleciona os elementos relacionados à tabela dentro do contêiner.
      const dataTableElement = container.querySelector('.tablePagEquipamentos');
      if (!dataTableElement) return;
  
      const searchInput = container.querySelector('.searchBox');
      const rowsPerPageSelect = container.querySelector('.rowsPerPage');
      const paginationDiv = container.querySelector('.pagination');
      const infoRangeElement = container.querySelector('.info-range');
  
      // Se houver botões de filtro no container, selecione-os.
      const filterButtons = container.querySelectorAll('.filter-button');
   
      // Variáveis de controle para esta instância da tabela.
      let allData = [];      // Armazena os dados carregados via fetch.
      let currentPage = 1;
      let rowsPerPage = parseInt(rowsPerPageSelect.value);
      let currentSort = { field: 'id', order: 'ASC' };
      
      const checkboxMostrarInativos = container.querySelector('#mostrarInativos');
      if (checkboxMostrarInativos) {
        checkboxMostrarInativos.addEventListener('change', () => {
          currentPage = 1;
          loadRemote().then(() => {
            fetchData(); // agora com allData novo
          });
        });
      }

      // Função para normalização de texto (útil para a busca).
      function normalizeText(text) {
        // garante que text seja string
        const str = (text == null) ? '' : String(text);
        try {
          return str
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase();
        } catch {
          return str.toLowerCase();
        }
      }
      // Atualiza activeConditions a partir do atributo data-filtro da tabela.
      function updateFiltersFromAttribute() {
        const dataFiltroAttr = dataTableElement.getAttribute('data-filtro');
        if (dataFiltroAttr) {
          try {
            activeConditions = JSON.parse(dataFiltroAttr);
          } catch (e) {
            console.error('Erro ao parsear data-filtro:', e);
            activeConditions = {};
          }
        } else {
          activeConditions = {};
        }
      }
      updateFiltersFromAttribute();
  



      function loadRemote() {
        const url = buildUrl();
        return fetch(url)
          .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
          })
          .then(data => {
            allData = data.rows;
            return data.rows;
          })
          .catch(err => {
            console.error('Erro carregando do backend:', err);
            allData = [];
            return [];
          });

           if (checkboxMostrarInativos && checkboxMostrarInativos.checked) {
            url += `&mostrarInativos=1`;
          }
          console.log('[buildUrl]', url);
          return url;
      }
      // Obtém os campos definidos nos <th> com data-field dentro do THEAD.
      const thElements = dataTableElement.querySelectorAll('thead th[data-field]');
      //const fieldsArray = Array.from(thElements).map(th => th.getAttribute('data-field'));
      //const fields = fieldsArray.join(',');
  
      //const tableName = dataTableElement.getAttribute('data-table');
      const dataTipo = dataTableElement.getAttribute('data-tipo');
  
      // Função que monta a URL para requisição.
      function buildUrl() {
        // Recalcula os campos lendo os th com data-field no THEAD
        const thElements = dataTableElement.querySelectorAll('thead th[data-field]');
        const fieldsArray = Array.from(thElements).map(th => th.getAttribute('data-field'));
        const fields = fieldsArray.join(',');

        // base da URL
        let url = `equipamentos/searchModEquipamentos.php?table=${dataTableElement.getAttribute('data-table')}&fields=${fields}&all=true`;
       
        // aplica filtros genéricos
        Object.entries(activeConditions).forEach(([key, value]) => {
          url += `&${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
        });

        // adiciona mostrarInativos conforme checkbox (se desmarcado, não envia e backend filtra ativo=1)
        if (checkboxMostrarInativos && checkboxMostrarInativos.checked) {
          url += `&mostrarInativos=1`;
        }

        return url;
      }
      
  
      // Função para exibir os registros na tabela.
      function displayRows(rows) {
        const tbody = dataTableElement.querySelector('tbody');
        tbody.innerHTML = '';
      
        rows.forEach(row => {
              const tr = document.createElement('tr');
          
              // Define ação para a linha se houver data-get ou data-modal no cabeçalho
             
             
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
                    const FilterAttr = th.getAttribute('data-sort')
                    
                    let cellValue = '';

                    if (field) {
                      cellValue = (row[field] !== undefined && row[field] !== null) ? row[field] : '';
                    }






                    
                    if (th.hasAttribute('data-modal')) {
                          // dispara o modal ao clicar em qualquer ponto da TD
                          td.addEventListener('click', function(e) {
                            abrirModal(th.getAttribute('data-modal'), row['equipamento_id'], dataTipo);
                      });
                    }

                    
                    if (th.hasAttribute('data-foto')) {
                     
                        const foto = document.createElement('img');

                        if (cellValue!==''){
                          basePath = `../${pastaFiles}/img/equipamentos/`;
                          imgPath = cellValue;
                        } else{
                        basePath = '../img/';
                          imgPath = 'sem-imagem.svg';
                        }

                        foto.src = basePath + imgPath;
                        foto.style.width = '40px';
                        foto.style.borderRadius = '50%';
                        td.appendChild(foto);
                     
                    }else if(td.classList.contains('reais')){
                      td.textContent = 'R$ ' + DecimalBr(cellValue);
                   
                    } else if (field === 'status') {
                      
                          const situacao = cellValue;              // ex: "Lead", "Ativo"…
                          const span   = document.createElement('span');
                          let cls      = 'status-label';         // classe base
                          let textoStatus = '';

                          // define classe extra conforme o status
                          switch (situacao) {
                            case 1:
                              cls += ' status-deletado';
                              textoStatus = 'Excluído';
                              break;
                            case 0:
                              cls += ' status-ativo';
                              textoStatus = 'Ativo';
                              break;
                            default:
                              cls += ' status-outro';
                          }

                          span.className   = cls;
                          span.textContent = textoStatus;
                          td.appendChild(span);

                     }else if (field === 'agendamento_online') {
                          const agendamento = cellValue;              // ex: "Lead", "Ativo"…
                          const span   = document.createElement('span');
                          let cls      = 'status-agonline';         // classe base
                          let textoAg='';

                          // define classe extra conforme o status
                          switch (agendamento) {
                            case 1:
                              cls += ' status-online';
                              textoAg = 'Agendamento Online';
                              break;
                            case 0:
                              cls += ' status-interno';
                              textoAg = 'Interno';
                              break;
                            default:
                              cls += ' status-outro';

                          }

                          span.className   = cls;
                          span.textContent = textoAg;
                          td.appendChild(span);
                                   
                    } else if (FilterAttr=='data'){
                      // Para colunas padrão, formata a data se necessário
                      td.textContent = formatarDataBr(cellValue);
                    }else{
                      td.textContent = cellValue;
                    }
              
                    // Se o th tiver o atributo hidden, oculta a TD




                    if (th.hasAttribute('hidden')) {
                      td.style.display = 'none';
                    }

              
                    // Se a TD deve ser formatada como número (classe "numVirg2c"), armazena o valor bruto
                    if (td.classList.contains('numero')) {
                      // Armazena o valor bruto antes de qualquer formatação
                      td.setAttribute('data-raw-value', cellValue);
                    }

                    if (td.classList.contains('posNeg')) {
                        if(cellValue<0){
                          td.classList.add('num-negativo')
                        }else{
                          td.classList.add('num-positivo')
                        }
                    }


              
                    tr.appendChild(td);
              });
              
             
              tbody.appendChild(tr);
        });
       
      }
      
      
      
  
 
      // Funções de comparação para ordenação.
      function compareNumbers(a, b, order) {
        const numA = parseFloat(a) || 0;
        const numB = parseFloat(b) || 0;
        return order === 'ASC' ? numA - numB : numB - numA;
      }
      function compareStrings(a, b, order) {
        return order === 'ASC' ? a.localeCompare(b) : b.localeCompare(a);
      }
      function compareDates(a, b, order) {
         const tA = parseDateString(a);
         const tB = parseDateString(b);
        return order === 'ASC' ? tA - tB : tB - tA;
      }
      function compareAniversario(a, b, order) {
        const daysA = daysUntilNextBirthday(a);
        const daysB = daysUntilNextBirthday(b);
        // ordena ASC = quem tem menos dias (mais próximo) vem primeiro
        return order === 'ASC' ? daysA - daysB : daysB - daysA;
      }
  
      // Se houver <th> com data-sort-init, define a ordenação inicial.
      const thWithInitSort = dataTableElement.querySelector('thead th[data-sort-init]');
      if (thWithInitSort) {
        currentSort.field = thWithInitSort.getAttribute('data-field');
        currentSort.order = thWithInitSort.getAttribute('data-sort-init').toUpperCase();
      }
  
      // Evento de clique nos cabeçalhos para ordenar.
      dataTableElement.querySelectorAll('thead th[data-field][data-sort]').forEach(th => {


        th.addEventListener('click', () => {
          const field = th.dataset.field;
          // alterna ASC/DESC ou define novo field
          if (currentSort.field === field) {
            currentSort.order = currentSort.order === 'ASC' ? 'DESC' : 'ASC';
          } else {
            currentSort.field = field;
            currentSort.order = 'ASC';
          }
          // dispara a re-renderização (fetchData já faz a ordenação)
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
  
      // Caso haja botões de filtro individuais no container.
      if (filterButtons.length) {
        filterButtons.forEach(button => {
          if (button.classList.contains('active')) {
            const field = button.getAttribute('data-field-cond');
            const value = button.getAttribute('data-cond-search');
            activeConditions[field] = value;
          }
          button.addEventListener('click', function() {
            const field = this.getAttribute('data-field-cond');
            const value = this.getAttribute('data-cond-search');
            if (this.classList.contains('active')) {
              delete activeConditions[field];
              this.classList.remove('active');
            } else {
              activeConditions[field] = value;
              this.classList.add('active');
            }
            fetchData();
          });
        });
      }
  
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
  
      // Função principal para buscar os dados e aplicar filtros, ordenação e paginação.
      function fetchData() {
        // 1. Termo de busca normalizado
        const searchTerm = normalizeText(searchInput.value);

        // 2. Campos a pesquisar (data-field de cada th)
        const fieldsToSearch = Array.from(
          dataTableElement.querySelectorAll('thead th[data-field]')
        ).map(th => th.getAttribute('data-field'));

        // 3. Filtrar por busca local
        let filteredData = allData.filter(row => {
          return fieldsToSearch.some(field => {
            const value = row[field] == null ? '' : String(row[field]);
            return normalizeText(value).includes(searchTerm);
          });
        });

        // 4. Ordenação centralizada
        const thSort = dataTableElement.querySelector(
          `thead th[data-field="${currentSort.field}"]`
        );
        const sortType = thSort ? thSort.getAttribute('data-sort') : 'a-z';

        filteredData.sort((a, b) => {

          
          const valA = a[currentSort.field], valB = b[currentSort.field];
        


          if (sortType === 'num') {
            return compareNumbers(valA, valB, currentSort.order);
          }
          if (sortType === 'data') {
            return compareDates(valA, valB, currentSort.order);
          }
          if (sortType === 'aniversario'){
            return compareAniversario(valA, valB, currentSort.order);
          }


          // padrão alfabético
          return compareStrings(
            normalizeText(valA == null ? '' : String(valA)),
            normalizeText(valB == null ? '' : String(valB)),
            currentSort.order
          );
        });

        // 5. Paginação
        const totalRecords = filteredData.length;
        const startIdx = (currentPage - 1) * rowsPerPage;
        const paginatedData = filteredData.slice(startIdx, startIdx + rowsPerPage);

        // 6. Renderizar
        displayRows(paginatedData);
        setupPagination(totalRecords);
        updateInfoRange(currentPage, rowsPerPage, totalRecords);
      }

  
      // Atualiza a exibição do intervalo de registros.
      function updateInfoRange(currentPage, rowsPerPage, totalRows) {
        const startIndex = (currentPage - 1) * rowsPerPage + 1;
        const endIndex = Math.min(startIndex + rowsPerPage - 1, totalRows);
        infoRangeElement.textContent = `Exibindo de ${startIndex} a ${endIndex} de um total de ${totalRows} registros`;
      }
  



      // MutationObserver para monitorar alterações no atributo data-filtro e refazer a consulta.
      const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
          if (mutation.type === 'attributes' && mutation.attributeName === 'data-filtro') {
          
            updateFiltersFromAttribute();
            const url = buildUrl();
            fetch(url)
              .then(response => {
                if (!response.ok) {
                  throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
              })
              .then(data => {
                allData = data.rows;





                fetchData();
              })
              .catch(error => console.error('Error loading data on filter change:', error));
          }
        });
      });
      observer.observe(dataTableElement, { attributes: true, attributeFilter: ['data-filtro'] });
  

      document.addEventListener('equipamento:salvo', function () {
        loadRemote().then(() => {
          fetchData();   // mantém busca/ordem/página atuais
        });
      });


            // Busca inicial dos dados.
            const initUrl = buildUrl();
            fetch(initUrl)
              .then(response => {
                if (!response.ok) {
                  throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
              })
              .then(data => {
                allData = data.rows;

                fetchData();
              })
              .catch(error => console.error('Error loading initial data:', error));
          });


  function parseDateString(dateStr) {
    if (!dateStr) return 0; // null, undefined ou string vazia
    // Tenta ISO (YYYY-MM-DD ou YYYY-MM-DDTHH:MM:SS)
    const isoTs = Date.parse(dateStr);
    if (!isNaN(isoTs)) {
      return isoTs;
    }
    // Tenta dd/mm/yyyy
    const parts = dateStr.split('/');
    if (parts.length === 3) {
      const [d, m, y] = parts;
      // monta YYYY-MM-DD
      const reformatted = `${y}-${m.padStart(2,'0')}-${d.padStart(2,'0')}`;
      const ts = Date.parse(reformatted);
      return isNaN(ts) ? 0 : ts;
    }
    // formato totalmente desconhecido
    return 0;
  }
function daysUntilNextBirthday(dateStr) {
  if (!dateStr) return Infinity;       // dados ausentes vão para o fim
  const parts = dateStr.split('-').map(Number);
  if (parts.length !== 3) return Infinity;
  const [ , month, day] = parts;

  const today = new Date();
  const todayMonth = today.getMonth() + 1;
  const todayDay   = today.getDate();

  // se for hoje, retorna 0
  if (month === todayMonth && day === todayDay) {
    return 0;
  }

  // monta data do aniversário neste ano
  let next = new Date(today.getFullYear(), month - 1, day);
  // se já passou, joga pro próximo ano
  if (next < today) {
    next = new Date(today.getFullYear() + 1, month - 1, day);
  }

  const diffMs = next - today;
  return Math.ceil(diffMs / (1000 * 60 * 60 * 24));
}


})();