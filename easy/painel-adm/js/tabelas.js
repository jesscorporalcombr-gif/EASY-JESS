let allData = []; // Armazena todos os dados carregados




document.addEventListener('DOMContentLoaded', function() {

    

    const fields = Array.from(document.querySelectorAll('#dataTable th[data-field]'))
                        .map(th => th.getAttribute('data-field'))
                        .join(',');

    const dataTableElement = document.getElementById('dataTable');

    if (!dataTableElement) {
    //console.error('Elemento "dataTable" não encontrado!');
    return;  // Interrompe a execução do restante do código na função
    }

    const tableName = dataTableElement.getAttribute('data-table');
    const data_tipo = dataTableElement.getAttribute('data-tipo');
    
    const dataFiltroAttr = dataTableElement.getAttribute('data-filtro');
   
    
    
    
    //const initUrl = `search.php?table=${tableName}&fields=${fields}&all=true`;
     // console.log("URL de requisição:", initUrl);Verifique a URL gerada

     
     let initialFilters = {};
    if (dataFiltroAttr) {
        try {
            initialFilters = JSON.parse(dataFiltroAttr);  
        } catch (e) {
            console.error('Erro ao fazer parse do data-filtro: ', e);
        }
    }

    let initUrl = `search.php?table=${tableName}&fields=${fields}&all=true`;

    

    // 5. Incluir os filtros do data-filtro como parâmetros GET extras
    for (const [key, value] of Object.entries(initialFilters)) {
        initUrl += `&${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
    }




    fetch(initUrl)
        .then(response => response.json())
        .then(data => {
             // console.log("Dados recebidos:", data);Confirma que os dados são recebidos corretamente
            allData = data.rows;
            setupPagination(Math.ceil(allData.length / rowsPerPage), 1);
            fetchData(); // Use os dados carregados inicialmente
        }).catch(error => {
            console.error('Error loading initial data:', error);
        });


    const table = document.getElementById('dataTable');
    const searchInput = document.getElementById('searchBox');
    const rowsPerPageSelect = document.getElementById('rowsPerPage');
    const paginationDiv = document.getElementById('pagination');    
    const conditionButtons = document.querySelectorAll('button[data-field-cond]'); // Seleciona todos os botões com os atributos de condição

    let currentPage = 1;
    let rowsPerPage = parseInt(rowsPerPageSelect.value);
    let currentSort = { field: 'id', order: 'ASC' };


    const thWithInitSort = dataTableElement.querySelector('thead th[data-sort-init]');
    if (thWithInitSort) {
      // Pega o campo e a ordem
      const initField = thWithInitSort.getAttribute('data-field');
      const initOrder = thWithInitSort.getAttribute('data-sort-init'); // "ASC" ou "DESC"
    
      // Atualiza o currentSort
      currentSort.field = initField;
      currentSort.order = initOrder.toUpperCase(); // só para garantir que fique ASC ou DESC
    }

    let activeConditions = {}; // Armazena as condições ativas

    const filterButtons = document.querySelectorAll('.filter-button'); // Seleciona apenas botões com a classe 'filter-button'

    filterButtons.forEach(button => {

		if (button.classList.contains('active')) {
            const field = button.getAttribute('data-field-cond');
            const value = button.getAttribute('data-cond-search');
            activeConditions[field] = value;
        }





        button.addEventListener('click', function() {
            const field = this.getAttribute('data-field-cond');
            const value = this.getAttribute('data-cond-search');
            // Aqui você alterna a condição ativa baseado na presença da classe 'active'
            if (this.classList.contains('active')) {
                delete activeConditions[field]; // Remove a condição
                this.classList.remove('active');
            } else {
                activeConditions[field] = value; // Adiciona a condição
                this.classList.add('active');
            }
            fetchData(); // Atualiza os dados com as condições atuais
        });
    });



    function normalizeText(text) {
        return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    }


    

    function fetchData() {
        const search = normalizeText(searchInput.value);
        const fieldsToSearch = Array.from(document.querySelectorAll('#dataTable th[data-field]'))
                                    .map(th => th.getAttribute('data-field'));
    
        // Função de ordenação baseada no tipo
        function getOrderFunction(field, order) {
            const sortType = document.querySelector(`#dataTable th[data-field="${field}"]`).getAttribute('data-sort');
            
            if (sortType === 'num') {
                return (a, b) => compareNumbers(a[field], b[field], order);
            } else if (sortType === 'data') {
                return (a, b) => compareDates(a[field], b[field], order);
            } else {
                // A-z ou default
                return (a, b) => compareStrings(normalizeText(a[field]), normalizeText(b[field]), order);
            }
        }
    
        // Filtrar dados com base nas condições e termo de pesquisa
        const filteredData = allData.filter(row => {
            const meetsConditions = Object.entries(activeConditions).every(([field, value]) => {
                return row[field] && normalizeText(row[field].toString()).toLowerCase() === normalizeText(value).toLowerCase();
            });
    
            const meetsSearch = fieldsToSearch.some(field => {
                return row[field] && normalizeText(row[field].toString()).includes(search);
            });
    
            return meetsConditions && meetsSearch;
        });
    
        // Ordenação dos dados filtrados com base na coluna e ordem correntes
        const orderFunction = getOrderFunction(currentSort.field, currentSort.order);
        filteredData.sort(orderFunction);
    
        // Paginação dos dados ordenados e filtrados
        const startIdx = (currentPage - 1) * rowsPerPage;
        const paginatedData = filteredData.slice(startIdx, startIdx + rowsPerPage);
    
        displayRows(paginatedData);
        setupPagination(Math.ceil(filteredData.length / rowsPerPage), currentPage);
        updateInfoRange(currentPage, rowsPerPage, filteredData.length);
    }
    
    
    








// 1) função que baixa do servidor e redesenha
    async function loadDataFromServer() {
        try {
            const resp  = await fetch(initUrl);
            const json  = await resp.json();

            allData     = json.rows;  // substitui os dados em memória
            currentPage = 1;          // (opcional) volta para página 1
            fetchData();              // redesenha a tabela
        } catch (err) {
            console.error('Erro ao recarregar dados:', err);
        }
    }

    // 2) primeira carga (equivale ao fetch que você apagou)
    loadDataFromServer();

    // 3) expõe globalmente para poder chamar de fora (modal, botão, etc.)
    window.reloadDataTable = loadDataFromServer;





















	function updateInfoRange(currentPage, rowsPerPage, totalRows) {
    const startIndex = (currentPage - 1) * rowsPerPage + 1;
    const endIndex = Math.min(startIndex + rowsPerPage - 1, totalRows);
    const infoRangeElement = document.getElementById('info-range');
    infoRangeElement.textContent = `Exibindo de ${startIndex} a ${endIndex} de um total de ${totalRows} registros`;
	}




	function setupPagination(totalPages) {
    paginationDiv.innerHTML = ''; // Limpa os botões existentes antes de adicionar novos

    // Botões de navegação para a primeira e página anterior
    const navBackButtons = ['<', '<<'];
    const navForwardButtons = ['>>', '>'];

    // Criar e adicionar botões de navegação para trás
    navBackButtons.forEach(label => {
        const btn = createPaginationButton(label, totalPages);
        btn.classList.add('page-nav-button'); // Adiciona uma classe para estilização
        paginationDiv.appendChild(btn);
    });

    // Determinar o intervalo de páginas a mostrar
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    for (let i = startPage; i <= endPage; i++) {
        const btn = createPaginationButton(i.toString(), totalPages);
        btn.classList.add('page-number-button'); // Adiciona uma classe específica para os botões de número de página
        if (i === currentPage) {
            btn.classList.add('active-page'); // Uma classe adicional para a página atual
        }
        paginationDiv.appendChild(btn);
    }

    // Criar e adicionar botões de navegação para frente
    navForwardButtons.forEach(label => {
        const btn = createPaginationButton(label, totalPages);
        btn.classList.add('page-nav-button'); // Adiciona uma classe para estilização
        paginationDiv.appendChild(btn);
    });
}






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
                currentPage = parseInt(label);
                break;
        }
        fetchData();  // Chamar fetchData com a página atualizada
    };
    return btn;
}

const theadRow = dataTableElement.querySelector('thead tr'); // Pega o <tr> do THEAD
let rowGetAttr = null;
let rowModalAttr = null;

// Se o <tr> tiver data-get, guardamos esse valor
if (theadRow && theadRow.hasAttribute('data-get')) {
    rowGetAttr = theadRow.getAttribute('data-get');
}
// Se o <tr> tiver data-modal, guardamos esse valor
if (theadRow && theadRow.hasAttribute('data-modal')) {
    rowModalAttr = theadRow.getAttribute('data-modal');
}


function displayRows(rows) {
    const tbody = table.getElementsByTagName('tbody')[0];
    tbody.innerHTML = '';
    rows.forEach(row => {
        const tr = document.createElement('tr');
        if (rowGetAttr) {
            // Deixa o mouse em pointer para indicar clique
            tr.style.cursor = "pointer";

            tr.addEventListener('click', () => {
                // Se tiver rowGetAttr, vai redirecionar.
                // Ex.: editar.php?id=10
                window.location.href = rowGetAttr + row["id"];
            });
        }

        // Exemplo para data-modal:
        if (rowModalAttr) {
            // Também estilo de cursor
            tr.style.cursor = "pointer";

            tr.addEventListener('click', (event) => {
                event.preventDefault();
                // Aqui chamamos a sua função de abrir modal
                // Usando row["id"] e o data_tipo (pode ser lido também do thead, se precisar)
                abrirModal(rowModalAttr, row["id"], data_tipo);
            });
        }





        Array.from(table.querySelectorAll('th')).forEach((th) => {
            const field = th.getAttribute('data-field');
            const td = document.createElement('td');


            const tdClasses = th.getAttribute('data-classe-td');  // Classes extras para o TD
            const trClasses = th.getAttribute('data-classe-tr');  // Classes extras para o TR
            
            
            if (tdClasses) {
                tdClasses.split(" ").forEach(cls => td.classList.add(cls));
            }
            if (trClasses) {
                // Caso queira acumular essas classes na linha (TR) toda
                trClasses.split(" ").forEach(cls => tr.classList.add(cls));
            }
            
            
            
            
            // Se for uma coluna de imagem
            if (th.classList.contains('data-img')) {
                const img = document.createElement('img');
                const imgPath = row[field] || 'sem-foto.svg'; // Use 'sem-foto.svg' se o campo estiver vazio
                let basePath = `../img/`; // Valor padrão para basePath
            
                // Obter basePath de data-img somente se row[field] não estiver vazio e data-img estiver definido corretamente
                if (row[field] && th.hasAttribute('data-img')) {
                    basePath = th.getAttribute('data-img') || `../${pastaFiles}/img/`;
                }
            
                img.src = basePath + imgPath;
                img.style.width = '40px'; // Defina o tamanho da imagem conforme necessário
                img.style.borderRadius = '50%';
            
                td.appendChild(img);
            }

           // if (condInicial) {
             //   const parsedConditions = JSON.parse(condInicial);
               // for (let [field, value] of Object.entries(parsedConditions)) {
                 //   activeConditions[field] = value;
                //}
           // }


            // Se for uma coluna de input de cor
            else if (th.classList.contains('data-color')) {
                const input = document.createElement('input');
                input.type = 'color'; // Define o tipo do input como 'color'
                //input.className = 'color-circle'; // Aplica a classe ao input
                //input.style.borderColor = 'white !important';
            
                // Defina o valor do input com o conteúdo do campo, se existir, ou defina um valor padrão
                if (row[field] && th.hasAttribute('data-field')) {
                    input.value = row[field]; // Usa o valor do campo se disponível
                } else {
                    input.value = '#ffffff'; // Um valor padrão de cor (branco) se não houver valor
                }
            
                // Opcional: Adicionar estilos ao input se necessário
               // input.style.width = '40px'; // Definir largura do input
                //input.style.height = '40px'; // Definir altura do input
                //input.style.borderRadius = '50%'; // Aparência circular
            
                td.appendChild(input);
            }
            else if (th.classList.contains('data-color-circle')) {
                const input = document.createElement('input');
                input.type = 'color'; // Define o tipo do input como 'color'
                input.className = 'circle'; // Aplica a classe ao input
                //input.style.borderColor = 'white !important';
            
                // Defina o valor do input com o conteúdo do campo, se existir, ou defina um valor padrão
                if (row[field] && th.hasAttribute('data-field')) {
                    input.value = row[field]; // Usa o valor do campo se disponível
                } else {
                    input.value = '#ffffff'; // Um valor padrão de cor (branco) se não houver valor
                }
            
                // Opcional: Adicionar estilos ao input se necessário
               // input.style.width = '40px'; // Definir largura do input
                //input.style.height = '40px'; // Definir altura do input
                //input.style.borderRadius = '50%'; // Aparência circular
            
                td.appendChild(input);
            }
            
            
            // Se for uma coluna com link GET, para acionar o método get
            else if (th.classList.contains('data-get')) {
                const a = document.createElement('a');
                a.textContent = row[field];
                a.href = `${th.getAttribute('data-get')}${row['id']}`; // Use o atributo data-get e o ID da linha
                td.appendChild(a);
            }
            
            // pra chamar um modal.php fora da página
            else if (th.classList.contains('data-modal')) {
                const a = document.createElement('a');
                var mModal= `${th.getAttribute('data-modal')}`;
                a.href = "#"; // Usar "#" para indicar um link clicável
                a.style.cursor = "pointer"; // Opcional: define o cursor como pointer para indicar que é clicável
                a.textContent = row[field]; // Define o texto do link para o nome do cliente
                a.onclick = function(event) {
                    event.preventDefault(); // Previne a navegação padrão do link
                    abrirModal(mModal, row['id'], data_tipo);
                };
                td.appendChild(a); // Adiciona o link à célula da tabela
            }

            //gerar um link whatsapp clicavel com base no numero do lelefone e colocar o icone do whgatapp
			else if (th.classList.contains('data-whats')) {
                const a = document.createElement('a');
                const phone = row[field].replace(/[^\d]/g, ""); // Remove caracteres não numéricos
                a.href = `https://wa.me/55${phone}`;
                a.target = "_blank"; // Instrui o navegador a abrir o link em uma nova aba
                a.innerHTML = `<i class="fab fa-whatsapp"></i> ${row[field]}`;
                a.firstChild.style.fontSize = '18px'; // Define o tamanho da fonte do ícone
                td.appendChild(a);
            }
            


            // Tratamento padrão para os outros dados
            else {                
                td.textContent = formatData(row[field], th.getAttribute('data-sort'));
            }
            // se a head da coluna de indicação tiver o atributo hidden, toda a coluna fica oculta
            if (th.hasAttribute('hidden')) {
                td.style.display = 'none';
            }
            
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    });
}


function formatData(data, sortType) {
    if (sortType === 'data' && data) {
        // Verifica se a string está no formato "YYYY-MM-DD"
        if (/^\d{4}-\d{2}-\d{2}$/.test(data)) {
            // Divide a string para obter ano, mês e dia
            const [year, month, day] = data.split('-').map(Number);
            // Cria uma data local (lembre-se: mês é 0-indexado)
            const dateObj = new Date(year, month - 1, day);
            return (
                ('0' + dateObj.getDate()).slice(-2) + '/' +
                ('0' + (dateObj.getMonth() + 1)).slice(-2) + '/' +
                dateObj.getFullYear()
            );
        } else {
            // Se não for o formato esperado, tenta criar a data normalmente
            const dateObj = new Date(data);
            return (
                ('0' + dateObj.getDate()).slice(-2) + '/' +
                ('0' + (dateObj.getMonth() + 1)).slice(-2) + '/' +
                dateObj.getFullYear()
            );
        }
    }
    return data;
}

    searchInput.addEventListener('input', () => {
        currentPage = 1;
        fetchData();
    });

    rowsPerPageSelect.addEventListener('change', () => {
        rowsPerPage = parseInt(rowsPerPageSelect.value);
        currentPage = 1;
        fetchData();
    });



    
        // Função de comparação para números
        function compareNumbers(a, b, order) {
            const numA = parseFloat(a);
            const numB = parseFloat(b);
            return order === 'ASC' ? numA - numB : numB - numA;
        }

        // Função de comparação para strings (alfabética)
        function compareStrings(a, b, order) {
            if (order === 'ASC') {
                return a.localeCompare(b);
            } else {
                return b.localeCompare(a);
            }
        }

        // Função de comparação para datas
        function compareDates(a, b, order) {
            const dateA = new Date(a);
            const dateB = new Date(b);
            return order === 'ASC' ? dateA - dateB : dateB - dateA;
        }

        table.querySelectorAll('th').forEach(th => {
            th.addEventListener('click', () => {
                const field = th.getAttribute('data-field');
                const sortType = th.getAttribute('data-sort');
                
                // Inverte a ordem da ordenação se já está ordenando por esse campo
                currentSort.field = field;
                currentSort.order = currentSort.field === field && currentSort.order === 'ASC' ? 'DESC' : 'ASC';

                // Aplica a ordenação baseada no tipo de dado
                if (sortType === 'num') {
                    allData.sort((a, b) => compareNumbers(a[field], b[field], currentSort.order));
                } else if (sortType === 'a-z') {
                    allData.sort((a, b) => compareStrings(a[field], b[field], currentSort.order));
                } else if (sortType === 'data') {
                    allData.sort((a, b) => compareDates(a[field], b[field], currentSort.order));
                }
                
                fetchData(); // Atualiza a exibição com os dados ordenados
            });
        });


	
    fetchData(); // Carrega inicialmente os dados
	
});

