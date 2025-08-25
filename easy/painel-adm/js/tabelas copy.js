
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('dataTable');
    const searchInput = document.getElementById('searchBox');
    const rowsPerPageSelect = document.getElementById('rowsPerPage');
    const paginationDiv = document.getElementById('pagination');
    const conditionButtons = document.querySelectorAll('button[data-field-cond]'); // Seleciona todos os botões com os atributos de condição

    let currentPage = 1;
    let rowsPerPage = parseInt(rowsPerPageSelect.value);
    let currentSort = { field: 'id', order: 'ASC' };
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

    function fetchData() {
    const search = encodeURIComponent(searchInput.value);
    const sort = currentSort.field;
    const order = currentSort.order;
    const tableName = table.getAttribute('data-table');
    const fields = Array.from(table.querySelectorAll('th[data-field]')).map(th => th.getAttribute('data-field')).join(',');

    const conditionsString = Object.entries(activeConditions)
        .map(([field, value]) => `conditions[${field}]=${encodeURIComponent(value)}`)
        .join('&');

    // Inclua a string de condições na URL se existirem condições
    const url = `search.php?search=${search}&fields=${fields}&limit=${rowsPerPage}&page=${currentPage}&sort=${sort}&order=${order}&table=${tableName}${conditionsString ? '&' + conditionsString : ''}`;
    
    console.log("Fetching URL:", url); // Confirma a URL final

	
	fetch(url)
        .then(response => response.json())
        .then(data => {
            displayRows(data.rows);
            window.currentPage = currentPage; // Certifique-se de que está atualizando a variável global
            setupPagination(data.totalPages, window.currentPage);
            updateInfoRange(window.currentPage, rowsPerPage, data.totalRows);
        }).catch(error => console.error('Error fetching data:', error));
	}







    

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




function displayRows(rows) {
    const tbody = table.getElementsByTagName('tbody')[0];
    tbody.innerHTML = '';
    rows.forEach(row => {
        const tr = document.createElement('tr');
        Array.from(table.querySelectorAll('th')).forEach((th) => {
            const field = th.getAttribute('data-field');
            const td = document.createElement('td');
            
            // Se for uma coluna de imagem
            if (th.classList.contains('data-img')) {
                const img = document.createElement('img');
                const imgPath = row[field] || 'sem-foto.svg'; // Use 'sem-foto.svg' se o campo estiver vazio
                img.src = th.getAttribute('data-img') + imgPath;
                img.style.width = '40px'; // Defina o tamanho da imagem conforme necessário
				img.style.borderRadius = '50%';

                td.appendChild(img);
            }
            // Se for uma coluna com link GET
            else if (th.classList.contains('data-get')) {
                const a = document.createElement('a');
                a.href = `${th.getAttribute('data-get')}${row['id']}`; // Use o atributo data-get e o ID da linha
                a.textContent = row[field];
                td.appendChild(a);
            }

            else if (th.classList.contains('data-modal')) {
                const a = document.createElement('a');
                var mModal= `${th.getAttribute('data-modal')}`;
                a.href = "#"; // Usar "#" para indicar um link clicável
                a.style.cursor = "pointer"; // Opcional: define o cursor como pointer para indicar que é clicável
                a.textContent = row[field]; // Define o texto do link para o nome do cliente
                a.onclick = function(event) {
                    event.preventDefault(); // Previne a navegação padrão do link
                    abrirModal(mModal, row['id']);
                };
                td.appendChild(a); // Adiciona o link à célula da tabela
            }


			else if (th.classList.contains('data-whats')) {
                const a = document.createElement('a');
                const phone = row[field].replace(/[^\d]/g, ""); // Remove caracteres não numéricos
                a.href = `https://wa.me/55${phone}`;
                a.innerHTML = `<i class="fab fa-whatsapp"></i> ${row[field]}`;
                td.appendChild(a);
            }
            // Tratamento padrão para os outros dados
            else {
                td.textContent = formatData(row[field], th.getAttribute('data-sort'));
            }

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
            const dateObj = new Date(data);
            return `${('0' + dateObj.getDate()).slice(-2)}/${('0' + (dateObj.getMonth() + 1)).slice(-2)}/${dateObj.getFullYear()}`;
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

    table.querySelectorAll('th').forEach(th => {
        th.addEventListener('click', () => {
            const field = th.getAttribute('data-field');
            currentSort.field = field;
            currentSort.order = currentSort.order === 'ASC' ? 'DESC' : 'ASC';
            fetchData();
        });
    });
	
    fetchData(); // Carrega inicialmente os dados
	
});
