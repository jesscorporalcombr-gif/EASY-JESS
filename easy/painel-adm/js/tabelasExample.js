let tpPagination = "select";
let reordena=false;

document.addEventListener('DOMContentLoaded', function() {

    const tableExampleElement = document.getElementById('example');

    if (!tableExampleElement) {
    //console.error('Elemento "dataTable" não encontrado!');
    return;  // Interrompe a execução do restante do código na função
    }



    const table = document.getElementById('example');
    const h6Elementos = document.getElementsByTagName('h6');

    for (let h6 of h6Elementos) {
    h6.style.fontFamily=('Ubuntu-Regular');
    h6.style.fontSize=('14px');
    h6.style.color=('#333333');
    }

    if (!table) {
        //console.error('Table ' + table.id + ' not found');
        return;
    }

   
const tableCells = table.querySelectorAll('tr > td');
const labelsTroca = document.getElementsByClassName('form-label');



Array.from(labelsTroca).forEach(label => {
    label.classList.remove('form-label');
    label.classList.add('form-group');
});

// Iterar sobre cada célula para verificar a presença de uma imagem
tableCells.forEach(cell => {
    // Encontrar a primeira imagem (se houver) dentro da célula
    const imgElement = cell.querySelector('img');

    // Se uma imagem for encontrada, aplique os estilos necessários
    if (imgElement) {
        // Estilos para a célula `<td>`
        cell.style.padding = '1px';
        cell.style.textAlign = 'center';

        // Estilos para a imagem `<img>`
        imgElement.style.height = '60px';
        imgElement.style.width = 'auto';
        imgElement.style.padding = '1px';
    }
});

	table.classList.remove('my-4', 'table');


    const initialRowsPerPage = 10; // Define um número padrão de linhas por página
    setupTableControls(table);
	setupSortListeners(table.id);



});



function setupSortListeners(tableId) {
	//console.log('id da tabela: ' + tableId)

    let table = document.getElementById(tableId);
    let headers = table.getElementsByTagName('th');

    Array.from(headers).forEach((header, index) => {
        header.addEventListener('click', () => {
			
			const searchTerm = document.getElementById('searchInput').value
			reordena=true;
			filterTable(table, searchTerm, 1, index)
            
        });
    });
}


function sortTable(tableId, columnIndex, rowsPerPage){
    

    let table = document.getElementById(tableId);
    let tbody = table.getElementsByTagName('tbody')[0];
    let rows = Array.from(tbody.rows);
    let isAscending = table.getAttribute('data-order') === 'asc';
    table.setAttribute('data-order', isAscending ? 'desc' : 'asc');

    rows.sort((a, b) => {
        let valA = getNormalizedValue(a.cells[columnIndex].textContent);
        let valB = getNormalizedValue(b.cells[columnIndex].textContent);
        return compareValues(valA, valB, isAscending);
    });

    // Reattach sorted rows to the table body
    rows.forEach(row => tbody.appendChild(row));

	
	updatePagination(table, 1, rowsPerPage);
    // Reset to the first page after sorting
   // updatePagination(table, 1, parseInt(document.querySelector('#example select').value));
}





function getNormalizedValue(value) {
    value = value.trim();
    if (value.match(/^\d{1,2}\/\d{1,2}\/\d{2,4}$/)) {  // Estritamente uma data
        return new Date(normalizeDate(value));
    } else if (!isNaN(parseFloat(value.replace(/[R$\.,]/g, '')))) {  // Número
        return parseFloat(value.replace(/[R$\.,]/g, ''));
    }
    return value.toLowerCase();  // Texto, normalizado para minúsculas
}

function normalizeDate(dateStr) {
    let parts = dateStr.match(/(\d+)/g);
    if (!parts) return dateStr;
    if (dateStr.includes('/')) {
        return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
    }
    return dateStr;
}

function compareValues(valA, valB, isAscending) {
	//console.log('valorA: ' + valA + '  valorB: ' +  valB);
    if (typeof valA === "number" && typeof valB === "number") {
        return isAscending ? valA - valB : valB - valA;
    } else if (valA instanceof Date && valB instanceof Date) {
        return isAscending ? valA - valB : valB - valA;
    } else {
		
        return isAscending ? valA.localeCompare(valB) : valB.localeCompare(valA);
    }
}













function setupTableControls(table) {
    



	const encapsulatingDiv = document.createElement('div');
		encapsulatingDiv.classList.add('container-md');

	const container = table.parentElement;
    const controlsContainer = document.createElement('div');
	controlsContainer.classList.add('row', 'mb-2');
	//controlsContainer.style.marginLeft='15px';

	const colPesquisa = document.createElement('div');
	
	
	
	

	const colSelect = document.createElement('div');
	colSelect.classList.add('col-md-2');


	colPesquisa.classList.add('col-md-10', 'd-flex');
	


    // Criar e inserir o seletor de quantidade de registros
    const select = document.createElement('select');
	const searchInput = document.createElement('input');

    ['10', '25', '50', '100'].forEach(num => {
        const option = document.createElement('option');
        option.value = num;
        option.textContent = num;
        select.appendChild(option);
    });
    select.onchange = () => {

    
	if (searchInput.value){
		const searchTerm= searchInput.value;
		filterTable(table, searchTerm, 1);

	}else{
	    tpPagination="select";
		updatePagination(table, 1, parseInt(select.value));
	}

    };

	select.classList.add('form-select');
	select.style.maxWidth='50px';
    

// Criar e inserir o campo de pesquisa
    
    searchInput.type = 'text';
	searchInput.id = "searchInput";
    searchInput.placeholder = 'Pesquisar...';
    searchInput.oninput = () => {
		reordena=false;
		filterTable(table, searchInput.value, 1);
	}
	searchInput.classList.add('form-control', 'ms-auto');
	searchInput.style.maxWidth='250px';


	container.insertBefore(encapsulatingDiv, table);
	
	encapsulatingDiv.appendChild(controlsContainer);
	controlsContainer.appendChild(colSelect);
	controlsContainer.appendChild(colPesquisa);
	colSelect.appendChild(select);
    colPesquisa.appendChild(searchInput);

	encapsulatingDiv.appendChild(table);





    // Criar e inserir o texto informativo
    const infoText = document.createElement('div');
    infoText.className = 'info-text';
    encapsulatingDiv.appendChild(infoText);


    // Inicialização dos componentes de controle de paginação

	//console.log('setupTableControls concluido, chamando updataPagination')

    updatePagination(table, 1, parseInt(select.value));

    //updateInfoText(infoText, 1, parseInt(select.value), countVisibleRows(table));
}



 let paginacao= false;

function updatePagination(table, currentPage, rowsPerPage) {
		
		if(tpPagination == "pesquisa"){
				if (paginacao==true){	
					const searchInput = document.getElementById('searchInput');
					const searchTerm = searchInput.value;
					filterTable(table, searchTerm, currentPage);
				}else{
					updtPagPesquisa(table, currentPage, rowsPerPage);
				}


		} else{
				updtPagSelect(table, currentPage, rowsPerPage);
		}



}


function updtPagSelect(table, currentPage, rowsPerPage) {

   // console.log('updtPagSelect iniciado...');
	const tbody = table.getElementsByTagName('tbody')[0];
    const rows = Array.from(tbody.rows);
    const totalRecords = rows.length;
    const totalPages = Math.ceil(totalRecords / rowsPerPage);

    // Atualizar a exibição das linhas da tabela para a página atual
    rows.forEach((row, index) => {
        row.style.display = (index >= (currentPage - 1) * rowsPerPage && index < currentPage * rowsPerPage) ? '' : 'none';
    });

    // Verificar ou criar o container de paginação
    let paginationContainer = document.querySelector('.pagination-container');
    if (!paginationContainer) {
        paginationContainer = document.createElement('div');
        paginationContainer.className = 'pagination-container';
        table.parentElement.appendChild(paginationContainer);
    }
	separaPagina(table, totalPages, rowsPerPage, totalRecords, currentPage)
   
}


function updtPagPesquisa(table, currentPage, rowsPerPage) {


    const tbody = table.getElementsByTagName('tbody')[0];
    const allRows = Array.from(tbody.rows);
    const visibleRows = allRows.filter(row => row.style.display !== 'none');
    const totalRecords = visibleRows.length;
    const totalPages = Math.ceil(totalRecords / rowsPerPage);

    // Atualizar a exibição das linhas da tabela para a página atual
    visibleRows.forEach((row, index) => {
        row.style.display = (index >= (currentPage - 1) * rowsPerPage && index < currentPage * rowsPerPage) ? '' : 'none';
    });

    // Atualizar ou criar container de paginação
    let paginationContainer = document.querySelector('.pagination-container');
    if (!paginationContainer) {
        paginationContainer = document.createElement('div');
        paginationContainer.className = 'pagination-container';
        table.parentElement.appendChild(paginationContainer);
    }
	separaPagina(table, totalPages, rowsPerPage, totalRecords, currentPage)
}



function separaPagina(table, totalPages, rowsPerPage, totalRecords, currentPage){


let paginationContainer = document.querySelector('.pagination-container');

	paginationContainer.innerHTML = '';

    if (totalPages > 1) {
        const range = 2; // Define o intervalo de páginas ao redor da página atual
        let startPage = Math.max(1, currentPage - range);
        let endPage = Math.min(totalPages, currentPage + range);

        // Ajustar para garantir que sempre mostre o mesmo número de botões
        if (currentPage - startPage < range) {
            endPage = Math.min(totalPages, endPage + (range - (currentPage - startPage)));
        }
        if (endPage - currentPage < range) {
            startPage = Math.max(1, startPage - (range - (endPage - currentPage)));
        }

		['<'].forEach(button => {
            const btn = createPaginationButton(button, currentPage > 1, () => updatePagination(table, 1, rowsPerPage));
            paginationContainer.appendChild(btn);
        });

        ['<<'].forEach(button => {
            const btn = createPaginationButton(button, currentPage > 1, () => updatePagination(table, currentPage - 1, rowsPerPage));
            paginationContainer.appendChild(btn);
        });
		
        for (let i = startPage; i <= endPage; i++) {
            const btn = createPaginationButton(i.toString(), true, () => updatePagination(table, i, rowsPerPage));
            if (i === currentPage) btn.classList.add('active-page');
            paginationContainer.appendChild(btn);
        }

        ['>>'].forEach(button => {
            const btn = createPaginationButton(button, currentPage < totalPages, () => updatePagination(table, currentPage + 1, rowsPerPage));
            paginationContainer.appendChild(btn);
        });

		['>'].forEach(button => {
            const btn = createPaginationButton(button, currentPage < totalPages, () => updatePagination(table, totalPages, rowsPerPage));
            paginationContainer.appendChild(btn);
        });





    }

    // Referência ao infoText corrigida

	//console.log('updatePagination Concluido... chamando updateinfoText')
    const infoText = document.querySelector('.info-text');
    updateInfoText(infoText, currentPage, rowsPerPage, totalRecords);
}


function updateInfoText(info, currentPage, rowsPerPage, totalRows) {
//	console.log('updateInfoText iniciado')
	if (!info) {
        //console.error('Info text element not found');
        return;
    }
	info.textContent = `Exibindo ${Math.min((currentPage - 1) * rowsPerPage + 1, totalRows)} a ${Math.min(currentPage * rowsPerPage, totalRows)} de um total de ${totalRows} registros`;
	//console.log('updateInfoText Concluido...')
}


function filterTable(table, searchTerm, currentPage, columnIndex) {
    const rows = table.getElementsByTagName('tbody')[0].rows;
    const text = searchTerm.toLowerCase();

    // Filtro de linhas baseado no texto de pesquisa
    Array.from(rows).forEach(row => {
        const textContent = row.textContent.toLowerCase();
        row.style.display = textContent.includes(text) ? '' : 'none';
		
		
    });

    // Atualizar a paginação e o texto informativo após a filtragem
    const select = document.querySelector('select');
    const rowsPerPage = parseInt(select.value);
    const infoText = document.querySelector('.info-text');
    const visibleRows = countVisibleRows(table);
	
	tpPagination="pesquisa";
	paginacao=false;
    
	if (reordena==true){
		sortTable(table.id, columnIndex, rowsPerPage);
	}else{
	updatePagination(table, currentPage, rowsPerPage);  // Redefinir para a primeira página após filtragem
	}
}











function countVisibleRows(table) {
    const rows = table.getElementsByTagName('tbody')[0].rows;
    return Array.from(rows).filter(row => row.style.display !== 'none').length;
}

function createPaginationButton(label, enabled, action) {

	let labelContent = label.textContent;
    const button = document.createElement('button');
	
    button.textContent = label;
    button.disabled = !enabled;
	if (label === '<' || label === '<<' || label === '>' || label === '>>') {
		button.classList.add('page-nav-button');
	}else{
		button.classList.add('page-number-button');

	}

    button.onclick = () => {
		reordena=false;
		paginacao=true;
		action();
	}

    return button;
}



