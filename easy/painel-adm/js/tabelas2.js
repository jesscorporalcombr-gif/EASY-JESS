document.addEventListener('DOMContentLoaded', function() {
    // Suporta várias tabelas; cada tabela deve estar dentro de um contêiner com a classe .table-container.
    const tableContainers = document.querySelectorAll('.table-container');
  
    tableContainers.forEach(container => {
      // Seleciona os elementos relacionados à tabela dentro do contêiner.
      const dataTableElement = container.querySelector('.dataTable');
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
      let activeConditions = {};
  
      // Função para normalização de texto (útil para a busca).
      function normalizeText(text) {
        return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
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
  
      // Obtém os campos definidos nos <th> com data-field dentro do THEAD.
      const thElements = dataTableElement.querySelectorAll('thead th[data-field]');
      const fieldsArray = Array.from(thElements).map(th => th.getAttribute('data-field'));
      const fields = fieldsArray.join(',');
  
      const tableName = dataTableElement.getAttribute('data-table');
      const dataTipo = dataTableElement.getAttribute('data-tipo');
  
      // Função que monta a URL para requisição.
      function buildUrl() {
        // Recalcula os campos lendo os th com data-field no THEAD
        const thElements = dataTableElement.querySelectorAll('thead th[data-field]');
        const fieldsArray = Array.from(thElements).map(th => th.getAttribute('data-field'));
        const fields = fieldsArray.join(',');
        
        // Monta a URL para a consulta
        let url = `search.php?table=${dataTableElement.getAttribute('data-table')}&fields=${fields}&all=true`;
        Object.entries(activeConditions).forEach(([key, value]) => {
          url += `&${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
        });
        return url;
      }
      
  
      // Função para exibir os registros na tabela.
      function displayRows(rows) {
        const tbody = dataTableElement.querySelector('tbody');
        tbody.innerHTML = '';
      
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
                e.preventDefault();
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
              let basePath = th.getAttribute('data-img') || `../${pastaFiles}/img/`;
              img.src = basePath + imgPath;
              img.style.width = '40px';
              img.style.borderRadius = '50%';
              td.appendChild(img);
            } else if (th.classList.contains('data-color')) {
              const input = document.createElement('input');
              input.type = 'color';
              input.value = cellValue !== '' ? cellValue : '#ffffff';
              td.appendChild(input);
            } else if (th.classList.contains('data-get')) {
              const a = document.createElement('a');
              a.href = `${th.getAttribute('data-get')}${row['id']}`;
              a.textContent = cellValue;
              td.appendChild(a);
            } else if (th.classList.contains('data-modal')) {
              const a = document.createElement('a');
              a.href = "#";
              a.textContent = cellValue;
              a.addEventListener('click', function(e) {
                e.preventDefault();
                abrirModal(th.getAttribute('data-modal'), row['id'], dataTipo);
              });
              td.appendChild(a);
            } else if (th.classList.contains('data-whats')) {
              const a = document.createElement('a');
              const phone = (typeof cellValue === 'string') ? cellValue.replace(/[^\d]/g, "") : "";
              a.href = `https://wa.me/55${phone}`;
              a.target = "_blank";
              a.innerHTML = `<i class="fab fa-whatsapp"></i> ${cellValue}`;
              td.appendChild(a);
            } else {
              // Para colunas padrão, formata a data se necessário
              td.textContent = formatData(cellValue, th.getAttribute('data-sort'));
            }
      
            // Se o th tiver o atributo hidden, oculta a TD
            if (th.hasAttribute('hidden')) {
              td.style.display = 'none';
            }
      
            // Se a TD deve ser formatada como número (classe "numVirg2c"), armazena o valor bruto
            if (td.classList.contains('numVirg2c')) {
              // Armazena o valor bruto antes de qualquer formatação
              td.setAttribute('data-raw-value', cellValue);
            }
      
            tr.appendChild(td);
          });
      
          tbody.appendChild(tr);
        });
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
        th.addEventListener('click', () => {
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
        const searchTerm = normalizeText(searchInput.value);
        const fieldsToSearch = Array.from(dataTableElement.querySelectorAll('thead th[data-field]'))
                                   .map(th => th.getAttribute('data-field'));
  
        // Filtragem dos dados usando activeConditions e o termo de busca.
        const filteredData = allData.filter(row => {
            // Aplica somente a busca do searchBox localmente
            return fieldsToSearch.some(field => {
                return row[field] && normalizeText(row[field].toString()).includes(searchTerm);
            });
        });
  
        // Ordenação dos dados filtrados.
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
  
        // Paginação dos dados.
        const totalRecords = filteredData.length;
        const startIdx = (currentPage - 1) * rowsPerPage;
        const paginatedData = filteredData.slice(startIdx, startIdx + rowsPerPage);
  
        displayRows(paginatedData);
        setupPagination(totalRecords);
        updateInfoRange(currentPage, rowsPerPage, totalRecords);

        formatBrazilianNumbers();  
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
            console.log('data-filtro alterado:', dataTableElement.getAttribute('data-filtro'));
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
  });







  //para formatar conforme as classas

  function formatBrazilianNumbers() {
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
  