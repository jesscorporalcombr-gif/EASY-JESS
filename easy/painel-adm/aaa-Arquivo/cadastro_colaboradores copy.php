<?php 
$pag = 'cadastro_colaboradores';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php')

?>


<!--formularios EASY-->
<style> 
.form-group{
	font-size:10px;
	font-weight: 400;
	color: grey;
	margin-top: -10px;
	/*padding-top: -10px;*/
	padding-bottom: 0px;
}
.form-control, .form-select{
	font-size: 12px;
	height: 30px;
	padding-bottom: 10px;
	padding-top: 10px;
	padding: 2px;
	padding-left: 10px;
	border: 1px solid #CDC0F5;
	}
.header-easy{
	background-color:#716EF6;
	border-top-left-radius: 10px;
	border-top-right-radius: 10px;
}
.MdHeader-easy{
	border-top-left-radius: 50px;
	border-top-right-radius: 50px;
	border: 1px solid #716EF6;
}
.modal-title{
	color: white;
	font-size: 12px;
}
.footer-modal-easy{
	background-color: #E6E1FF;
	height: 80px; 
	border-radius: 0px 0px 8px 8px; 
	/*background-color: #D1D1D1; */
	display: flex; 
	justify-content: flex-end; 
	align-items: center;

}



/*EASYTABLE*/


table {
    width: 100%;
    border-collapse: collapse;
    margin: 25px 0;
    font-size: 0.9em;
    min-width: 400px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #dddddd;
}

th {
    background-color: #716EF6;
    color: #ffffff;
    font-weight: bold;
}

tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

tr:last-of-type {
    border-bottom: 2px solid #716EF6;
}

tr:hover {
    background-color: #f1f1f1;
}

/* Estilos para o botão de editar */
.edit-btn {
    padding: 8px 15px;
    background-color: #008CBA;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9em;
}

.edit-btn:hover {
    background-color: #005f73;
}

/* Estilo para o quadrinho da cor */
.easy-tb-colorbox {
    display: inline-block;
    width: 40px;
    height: 20px;
	border-radius: 4px;
    border: 1px solid #ddd;
}

/* Estilo para a imagem */
.easy-tb-img {
    width: 50px; /* ou 100% se quiser que seja responsivo */
    height: auto;
    border-radius: 50%; /* para torná-la circular */

	/* para as tabs*/
}
	.page-nav-button {
    background-color: #f0f0f0;
    color: #333;
    border: none;
    padding: 5px 10px;
    margin: 2px;
    cursor: pointer;
}

.page-number-button {
    background-color: #ffffff;
    color: #007bff;
    border: 1px solid #ddd;
    padding: 5px 8px;
    margin: 2px;
    cursor: pointer;
}

.active-page {
    background-color: #007bff;
    color: #ffffff;
}

.page-nav-button:disabled, .page-number-button:disabled {
    color: #ccc;
    cursor: not-allowed;
}


</style>


<div class="container-md">
	<div class="col-md-7 mb-2"> <!--  -->
		<div class="col-md-3">
			</br>

				<a style="font-size: 12px;  " href="index.php?pagina=<?php echo $pag ?>&funcao=novo" type="button" class="btn btn-outline-primary">Novo Colaborador</a>

		</div>
	</div>
<hr>	
</div>




<div class="container-md">
    <div class="row">
		<div class="col-md-1 mb-4">
			<select class ="form-select" id="rowsPerPage">
				<option value="10">10</option>
				<option value="25">50</option>
				<option value="100">100</option>
				<option value="500">500</option>
			</select>
		</div>
		<div class="col-md-6"></div>
		<div class="col-md-4 mb-4">
			<input type="text" class="form-control" id="searchBox" placeholder="Pesquisar...">
		</div>

		

    <table id="dataTable" data-table="cadastro_colaboradores">
        <thead>
            <tr>
			<th data-sort="num" data-field="id">ID</th>
			<!--<th data-sort="data" data-field="dt_cadastro">Data Cadastro</th>-->
            <th data-sort="a-z" data-field="nome">Nome</th>
            <th data-sort="a-z" data-field="email_pessoal">Email</th>
            <th data-sort="num" data-field="telefone">Telefone</th>
			<th data-sort="num" data-field="situacao">Situação</th>
			<th data-sort="data" data-field="data_criacao">Cadastro</th>
                <!-- Adicione mais colunas conforme necessário -->
            </tr>
        </thead>
        <tbody>
            <!-- As linhas serão inseridas aqui dinamicamente -->
        </tbody>
    </table>

    <div id="pagination">
        <!-- Botões de navegação serão inseridos aqui -->
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('dataTable');
    const searchInput = document.getElementById('searchBox');
    const rowsPerPageSelect = document.getElementById('rowsPerPage');
    const paginationDiv = document.getElementById('pagination');

    let currentPage = 1;
    let rowsPerPage = parseInt(rowsPerPageSelect.value);
    let currentSort = { field: 'id', order: 'ASC' };  // Guarda a coluna e ordem de ordenação atual

    function fetchData() {
        const search = searchInput.value;
        const sort = currentSort.field;
        const order = currentSort.order;
        const tableName = table.getAttribute('data-table');
        const fields = Array.from(table.querySelectorAll('th[data-field]')).map(th => th.getAttribute('data-field')).join(',');

        fetch(`search.php?search=${encodeURIComponent(search)}&fields=${fields}&limit=${rowsPerPage}&page=${currentPage}&sort=${sort}&order=${order}&table=${tableName}`)
            .then(response => response.json())
            .then(data => {
                displayRows(data.rows);
                setupPagination(data.totalPages, currentPage);
            }).catch(error => console.error('Error fetching data:', error));
    }

    function displayRows(rows) {
        const tbody = table.getElementsByTagName('tbody')[0];
        tbody.innerHTML = '';
        rows.forEach(row => {
            const tr = document.createElement('tr');
            Array.from(table.querySelectorAll('th[data-field]')).forEach((th, index) => {
                const field = th.getAttribute('data-field');
                const td = document.createElement('td');
                td.textContent = formatData(row[field], th.getAttribute('data-sort'));
                if (th.hasAttribute('hidden')) {
                    td.style.display = 'none';
                }
                tr.appendChild(td);
            });
            tbody.appendChild(tr);
        });
    }

    function formatData(data, sortType) {
        // Formatar datas se o tipo de ordenação for 'data'
        if (sortType === 'data' && data) {
            const dateObj = new Date(data);
            return `${('0' + dateObj.getDate()).slice(-2)}/${('0' + (dateObj.getMonth() + 1)).slice(-2)}/${dateObj.getFullYear()}`;
        }
        return data;
    }

    function setupPagination(totalPages, currentPage) {
    paginationDiv.innerHTML = '';

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
    btn.disabled = (label === currentPage.toString()) || (label === '<<' && currentPage === 1) || (label === '>>' && currentPage === totalPages);
    btn.onclick = () => {
        currentPage = (label === '<') ? 1 : (label === '<<') ? currentPage - 1 : (label === '>>') ? currentPage + 1 : (label === '>') ? totalPages : parseInt(label);
        fetchData();
    };
    return btn;
}

    searchInput.addEventListener('input', () => {
        currentPage = 1; // Reset to page 1 on new search
        fetchData();
    });

    rowsPerPageSelect.addEventListener('change', () => {
        rowsPerPage = parseInt(rowsPerPageSelect.value);
        currentPage = 1; // Reset to page 1 on rows per page change
        fetchData();
    });

    table.querySelectorAll('th').forEach(th => {
        th.addEventListener('click', () => {
            const field = th.getAttribute('data-field');
            if (currentSort.field === field) {
                currentSort.order = currentSort.order === 'ASC' ? 'DESC' : 'ASC';
            } else {
                currentSort.field = field;
                currentSort.order = 'ASC';  // Start with ascending
            }
            fetchData();
        });
    });

    fetchData();  // Initial fetch
});



</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchBox');
    const conditionButtons = document.querySelectorAll('[data-field-cond]'); // Seleciona todos os elementos com o atributo `data-field-cond`
    let conditions = {};

    conditionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const field = button.getAttribute('data-field-cond');
            const value = button.getAttribute('data-cond-search');
            const isActive = button.classList.contains('active'); // Toggle condition

            if (isActive) {
                delete conditions[field]; // Remove condição
                button.classList.remove('active');
            } else {
                conditions[field] = value; // Adiciona/atualiza condição
                button.classList.add('active');
            }

            fetchData();
        });
    });

    function fetchData() {
        const search = encodeURIComponent(searchInput.value);
        const conditionStrings = Object.entries(conditions).map(([field, value]) => `${field}=${value}`).join('&');

        fetch(`search.php?search=${search}&${conditionStrings}`)
            .then(response => response.json())
            .then(data => {
                console.log(data);  // Processar os dados recebidos
            }).catch(error => console.error('Error fetching data:', error));
    }
});


</script>






















<?php 
if(@$_GET['funcao'] == "editar"){
	$titulo_modal = 'Editar Registro';
	$query = $pdo->query("SELECT * from cadastro_colaboradores where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		$nome = $res[0]['nome'];
		$data_nascimento = $res[0]['data_nascimento'];
		$sexo = $res[0]['sexo'];
		$cpf = $res[0]['cpf'];
		$cnh = $res[0]['cnh'];
		$cnh_categoria = $res[0]['cnh_categoria'];
		$cnh_dt_validade = $res[0]['cnh_dt_validade'];
		$rg = $res[0]['rg'];
		$orgao = $res[0]['orgao'];	
		$data_exp = $res[0]['data_exp'];
		$e_social = $res[0]['e_social'];
		$data_chegada_brasil = $res[0]['data_chegada_brasil'];
		$etinia = $res[0]['etinia'];
		$pis_dt_cadastro = $res[0]['pis_dt_cadastro'];
		$conta_fgts = $res[0]['conta_fgts'];
		$fgts_dt_opcao = $res[0]['fgts_dt_opcao'];
		$cert_reservista= $res[0]['cert_reservista'];
		$est_civil = $res[0]['est_civil'];
		$nome_conj = $res[0]['nome_conj'];
		$dados_conj = $res[0]['dados_conj'];
		$ctps = $res[0]['ctps'];
		$serie = $res[0]['serie'];
		$pis = $res[0]['pis'];
		$titulo = $res[0]['titulo'];
		$zona = $res[0]['zona'];
		$sessao = $res[0]['sessao'];
		$cep = $res[0]['cep'];
		$endereco = $res[0]['endereco'];
		$numero = $res[0]['numero'];
		$complemento = $res[0]['complemento'];
		$bairro = $res[0]['bairro'];
		$cidade = $res[0]['cidade'];
		$uf_endereco = $res[0]['uf_endereco'];
		$nome_mae = $res[0]['nome_mae'];
		$nome_pai = $res[0]['nome_pai'];
		$telefone = $res[0]['telefone'];
		$telefone2 = $res[0]['telefone2'];
		$escolaridade = $res[0]['escolaridade'];
		$email_pessoal = $res[0]['email_pessoal'];
		$situacao = $res[0]['situacao'];
		$banco_if = $res[0]['banco_if'];
		$agencia = $res[0]['agencia'];
		$conta = $res[0]['conta'];
		$pix = $res[0]['pix'];
		$tipo_pix = $res[0]['tipo_pix'];
		$tp_sanguineo = $res[0]['tp_sanguineo'];
		$naturalidade = $res[0]['naturalidade'];
		$uf_naturalidade = $res[0]['uf_naturalidade'];
		$deficiente_sim_nao = $res[0]['deficiente_sim_nao'];
		$deficiencia = $res[0]['deficiencia'];
		$tp_deficiencia = $res[0]['tp_deficiencia'];
		$nacionalidade = $res[0]['nacionalidade'];
		$senha_sistema = $res[0]['senha_sistema'];
		$ativo_agenda = $res[0]['ativo_agenda'];
		$foto_cadastro= $res[0]['foto_cadastro'];

		

		
		
		//$banco_depositario_fgts = $res[0]['banco_depositario_fgts'];

		//$num_dependentes = $res[0]['num_dependentes'];
		//$dados_dependentes = $res[0]['dados_dependentes'];
		
				

	}
}else{
	$titulo_modal = 'Inserir Colaborador';
}
?>


<div class="modal fade" tabindex="-1" id="modalCadastrar" data-bs-backdrop="static">
	<div class="modal-dialog modal-xl ">
		<div class="modal-content MdHeader-easy">
			<div class="modal-header header-easy">
				<h5 class="modal-title"><?php echo $titulo_modal ?></h5>
				<button type="button" class="btn-close modal-title" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form method="POST" id="form">
				<div class="modal-body">

				<div class="row">
						<div class="col-md-2">
								<div class="row">
									
										<!--<label for="img-foto_cad" >Foto</label>-->
										<input type="file" hidden style="display:none;" class="form-control-file" id="img-foto_cadastro" name="img-foto_cadastro" onChange="carregarImg();">
										
															
								
								<div id="capdivImgConta" >
									<div id="divImgConta" >
										<?php if(@$foto_cadastro != ""){ ?>
											<img src="../img/<?php echo $pag ?>/<?php echo $foto_cadastro ?>"  width="150px" id="frm-foto_cadastro">
										<?php  }else{ ?>
											<img src="../img/<?php echo $pag ?>/sem-foto.jpg" width="150px" id="frm-foto_cadastro">
										<?php } ?>
									</div>
								</div>
							</div>

						</div>
						<div class="col-md-10">
								<div class="row">
										<div class="col-md-6">
											<div class="mb-3">
												<label for="frm-nome" class="form-group">Nome</label>
												<input type="text" class="form-control" id="frm-nome" name="frm-nome"  required value="<?php echo @$nome ?>">
											</div> 
										</div>

										

										<div class="col-md-3">
											<div class="mb-3">
												<label for="frm-data_nascimento" class="form-group">Data Nascimento </label>
												<input type="date" class="form-control" id="frm-data_nascimento" name="frm-data_nascimento"  value="<?php echo @$data_nascimento ?>">
										</div>					   						   
										</div>

										<div class="col-md-3">
											<div class="mb-3">
												<label class="form-group" for="frm-sexo">Gênero</label>
													<select class="form-select" aria-label="Default select example" id ="frm-sexo" name="frm-sexo">
														<option <?php if(@$sexo == '--'){ ?> selected <?php } ?>  value="--">--</option>
														<option <?php if(@$sexo == 'Feminino'){ ?> selected <?php } ?>  value="Feminino">Feminino</option>
														<option <?php if(@$sexo == 'Masculino'){ ?> selected <?php } ?>  value="Masculino">Masculino</option>
														<option <?php if(@$sexo == 'Outro'){ ?> selected <?php } ?>  value="Outro">Outro</option>
														<option <?php if(@$sexo == 'Não Declarar'){ ?> selected <?php } ?>  value="Não Declarar">Não Declarar</option>
													</select>
											</div> 
										</div>

								</div>


								<div class="row">
									
									<div class="col-md-3">
										<div class="mb-3">
											<label for="frm-rg" class="form-group">RG</label>
											<input type="text" class="form-control" id="frm-rg" name="frm-rg"   value="<?php echo @$rg ?>">
										</div> 
									</div>

									<div class="col-md-2">
										<div class="mb-3">
											<label for="frm-orgao" class="form-group">Orgão</label>
											<input type="text" class="form-control" id="frm-orgao" name="frm-orgao" value="<?php echo @$orgao ?>">
										</div>  
									</div>

									<div class="col-md-2">
										<div class="mb-3">
											<label for="frm-data_exp" class="form-group">Data Emissão</label>
											<input type="date" class="form-control" id="frm-data_exp" name="frm-data_exp" value="<?php echo @$data_exp ?>">
										</div> 
									</div>
									<div class="col-md-3">
											<div class="mb-3">
												<label for="frm-nacionalidade" class="form-group">Nacionalidade</label>
												<input type="text" class="form-control" id="frm-nacionalidade" name="frm-nacionalidade" value="<?php echo @$nacionalidade ?>">
											</div>  
										</div>
										<div class="col-md-2">
											<div class="mb-3">
												<label for="frm-data_chegada_brasil" class="form-group">Chegada ao Brasil</label>
												<input type="date" class="form-control" id="frm-data_chegada_brasil" name="frm-data_chegada_brasil" value="<?php echo @$data_chegada_brasil ?>">
											</div>  
										</div>
								</div>

								<div class="row">
										<div class="col-md-3">
											<div class="mb-3">
												<label for="frm-cpf" class="form-group">CPF</label>
													<input type="text" class="form-control" id="frm-cpf" name="frm-cpf"  required="" value="<?php echo @$cpf ?>">
											
											</div>
										</div>

										<div class="col-md-3">
											<div class="mb-3">
												<label for="frm-naturalidade" class="form-group">Naturalidade</label>
												<input type="text" class="form-control" id="frm-naturalidade" name="frm-naturalidade"   value="<?php echo @$naturalidade ?>">
											</div>  
										</div>

										<div class="col-md-1">
											<div class="mb-3">
												<label for="frm-uf_naturalidade" class="form-group">Estado</label>
												<input type="text" class="form-control" id="frm-uf_naturalidade" name="frm-uf_naturalidade" value="<?php echo @$uf_naturalidade ?>">
											</div> 
										</div>

										<div class="col-md-3">
											<div class="mb-3">
												<label for="frm-etinia" class="form-group">Etnia</label>
												<input type="text" class="form-control" id="frm-etinia" name="frm-etinia"  value="<?php echo @$etinia ?>">
											</div>  
										</div>

										<div class="col-md-2">
											<div class="mb-3">
												<label for="frm-tp_sanguineo" class="form-group">Sangue</label>
												<input type="text" class="form-control" id="frm-tp_sanguineo" name="frm-tp_sanguineo"  value="<?php echo @$tp_sanguineo ?>">
											</div>  
										</div>
										


								</div>
						</div>

				</div>

					<div class="row">
						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-deficiente_sim_nao" style="padding-bottom: 3px;" class="form-group">Deficiente</label>
								<span class="spanStatus">
									<input type="radio" style ="margin-right: 5px;" id="frm-deficiente_sim" name="frm-deficiente_sim_nao" value="sim" <?php echo $deficiente_sim_nao ? 'checked' : ''; ?>>Sim
								</span>
								<span class="spanStatus" style="margin-left: 10px;">
									<input type="radio" style ="margin-right: 5px;" id="frm-deficiente_nao" name="frm-deficiente_sim_nao" value="nao" <?php echo !$deficiente_sim_nao ? 'checked' : ''; ?>>Não
								</span>

							</div>  
						</div>

						
						<div class="col-md-3">
							<div class="mb-3">
								<label for="frm-tp_deficiencia" class="form-group">Tipo de Deficiência</label>
								<input type="text" class="form-control" id="frm-tp_deficiencia" name="frm-tp_deficiencia"   value="<?php echo @$tp_deficiencia ?>">
							</div> 
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="frm-deficiencia" class="form-group">Deficiência</label>
								<input type="text" class="form-control" id="frm-deficiencia" name="frm-deficiencia" value="<?php echo @$deficiencia ?>">
							</div> 
						</div>

						<div class="col-md-4">
							<div class="mb-3">
								
								<label for="frm-escolaridade" class="form-group">Escolaridade</label>
								<select class="form-select" aria-label="Default select example" id="frm-escolaridade" name="frm-escolaridade>
									<option <?php if(@$escolaridade == '--'){ ?> selected <?php } ?>  value="--">--</option>
									<option <?php if(@$escolaridade == 'Primário'){ ?> selected <?php } ?>  value="Primário">Primário</option>
									<option <?php if(@$escolaridade == 'Médio'){ ?> selected <?php } ?>  value="Médio">Médio</option>
									<option <?php if(@$escolaridade == 'Superior'){ ?> selected <?php } ?>  value="Superior">Superior</option>
									<option <?php if(@$escolaridade == 'Pôs graduação'){ ?> selected <?php } ?>  value="Pôs graduação">Pôs graduação</option>
									<option <?php if(@$escolaridade == 'Doutorado'){ ?> selected <?php } ?>  value="Doutorado">Doutorado</option>
									<option <?php if(@$escolaridade == 'Pôs Doutorado'){ ?> selected <?php } ?>  value="Pôs Doutorado">Pôs Doutorado</option>
								</select>

							</div> 
						</div>




						
						
					</div>

					<div class="row">
						<div class="col-md-5">
							<div class="mb-3">
								<label for="frm-nome_mae" class="form-group">Nome da Mãe</label>
								<input type="text" class="form-control" id="frm-nome_mae" name="frm-nome_mae"  value="<?php echo @$nome_mae ?>">
							</div> 
						</div>

						<div class="col-md-5">
							<div class="mb-3">
								<label for="frm-nome_pai" class="form-group">Nome do Pai</label>
								<input type="text" class="form-control" id="frm-nome_pai" name="frm-nome_pai"   value="<?php echo @$nome_pai ?>">
							</div>  
						</div>
					</div>
					<div class="row">
						
							<div class="col-md-3">
								<div class="mb-3">
									<label class="form-group" for="frm-est_civil">Estado Civil</label>
									
										<select class="form-select" id= "frm-est_civil" aria-label="Default select example" name="frm-est_civil">
											
											<option <?php if(@$est_civil == '--'){ ?> selected <?php } ?>  value="--">--</option>

											<option <?php if(@$est_civil == 'Solteiro'){ ?> selected <?php } ?>  value="Solteiro">Solteiro</option>

											<option <?php if(@$est_civil == 'Casado'){ ?> selected <?php } ?>  value="Casado">Casado</option>
											
											<option <?php if(@$est_civil == 'Separado'){ ?> selected <?php } ?>  value="Separado">Separado</option>

											<option <?php if(@$est_civil == 'Desquitado'){ ?> selected <?php } ?>  value="Desquitado">Desquitado</option>

											
										</select>
								</div>
							</div>

							<div class="col-md-4">
								<div class="mb-3">
									<label for="frm-nome_conj" class="form-group">Nome Cônjuge</label>
									<input type="text" class="form-control" id="frm-nome_conj" name="frm-nome_conj"  value="<?php echo @$nome_conj ?>">
								</div> 
							</div>

							<div class="col-md-5">
								<div class="mb-3">
									<label for="frm-dados_conj" class="form-group">Dados Cônjuge</label>
									<input type="text" class="form-control" id="frm-dados_conj" name="frm-dados_conj"   value="<?php echo @$dados_conj ?>">

								</div>  
							</div>

					</div>




					<div class="row">
						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-cnh" class="form-group">CNH</label>
								<input type="text" class="form-control" id="frm-cnh" name="frm-cnh"  value="<?php echo @$cnh ?>">
							</div> 
						</div>
						
						<div class="col-md-1">
							<div class="mb-3">
								<label for="frm-cnh_categoria" class="form-group">Cat.</label>
								<input type="text" class="form-control" id="frm-cnh_categoria" name="frm-cnh_categoria"  value="<?php echo @$cnh_categoria ?>">
							</div>  
						</div>

						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-cnh_dt_validade" class="form-group">Validade CNH</label>
								<input type="date" class="form-control" id="frm-cnh_dt_validade" name="frm-cnh_dt_validade"  value="<?php echo @$cnh_validade ?>">
							</div>  
						</div>
						
						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-ctps" class="form-group">CTPS</label>
								<input type="text" class="form-control" id="frm-ctps" name="frm-ctps"  value="<?php echo @$ctps ?>">
							</div> 
						</div>

						<div class="col-md-1">
							<div class="mb-3">
								<label for="frm-serie" class="form-group">Série</label>
								<input type="text" class="form-control" id="frm-serie" name="frm-serie" value="<?php echo @$serie ?>">
							</div>  
						</div>


						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-pis" class="form-group">PIS/NIS</label>
								<input type="text" class="form-control" id="frm-pis" name="frm-pis" p value="<?php echo @$pis ?>">
							</div> 
						</div>

						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-pis_dt_cadastro" class="form-group">Data Cad PIS</label>
								<input type="date" class="form-control" id="frm-pis_dt_cadastro" name="frm-pis_dt_cadastro"  value="<?php echo @$pis_dt_cadastro ?>">
							</div>  
						</div>
					</div>
					<div class="row">
					<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-e_social" class="form-group">E-social</label>
								<input type="text" class="form-control" id="frm-e_social" name="frm-e_social" value="<?php echo @$e_social ?>">
							</div>  
						</div>

						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-titulo" class="form-group">Título</label>
								<input type="text" class="form-control" id="frm-titulo" name="frm-titulo" value="<?php echo @$titulo ?>">
							</div>  
						</div>

						<div class="col-md-1">
							<div class="mb-3">
								<label for="frm-zona" class="form-group">Zona</label>
								<input type="text" class="form-control" id="frm-zona" name="frm-zona" value="<?php echo @$zona ?>">
							</div> 
						</div>

						<div class="col-md-1">
							<div class="mb-3">
								<label for="frm-sessao" class="form-group">Sessão</label>
								<input type="text" class="form-control" id="frm-sessao" name="frm-sessao" value="<?php echo @$sessao ?>">
							</div>  
						</div>
					
						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-conta_fgts" class="form-group">FGTS</label>
								<input type="text" class="form-control" id="frm-conta_fgts" name="frm-conta_fgts"  value="<?php echo @$conta_fgts ?>">
							</div>  
						</div>
						

						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-fgts_dt_opcao" class="form-group">Data de Opção</label>
								<input type="date" class="form-control" id="frm-fgts_dt_opcao" name="frm-fgts_dt_opcao"  value="<?php echo @$fgts_dt_opcao ?>">
							</div>  
						</div>
						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-cert_reservista" class="form-group">Cert. Reservista</label>
								<input type="text" class="form-control" id="frm-cert_reservista" name="frm-cert_reservista" value="<?php echo @$cert_reservista ?>">
							</div>  
						</div>
						
					</div>
					
<hr>
					<div class="row">
						
						<div class="col-md-3">
							<div class="mb-3">
									<label for="frm-telefone" class="form-group">Telefone</label>
									<input type="text" class="form-control" id="frm-telefone" name="frm-telefone"  value="<?php echo @$telefone ?>">
							</div> 
						</div>

						<div class="col-md-3">
							<div class="mb-3">
									<label for="frm-telefone2" class="form-group">Telefone 2</label>
									<input type="text" class="form-control" id="frm-telefone2" name="frm-telefone2" value="<?php echo @$telefone2 ?>">
								</div> 
							</div>
						<div class="col-md-6">
							<div class="mb-3">
									<label for="frm-email_pessoal" class="form-group">Email Pessoal</label>
									<input type="email" class="form-control" id="frm-email_pessoal" name="frm-email_pessoal" value="<?php echo @$email_pessoal ?>">
							</div>
						</div>
					
					</div>

				
					
						
					

					<div class="row">

						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-cep" class="form-group">CEP</label>
								<input type="text" class="form-control" id="frm-cep" name="frm-cep" value="<?php echo @$cep ?>">
							</div> 
						</div>

						<div class="col-md-8">
							<div class="mb-3">
								<label for="frm-endereco" class="form-group">Endereço</label>
								<input type="text" class="form-control" id="frm-endereco" name="frm-endereco" value="<?php echo @$endereco ?>">
							</div>  
						</div>

						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-numero" class="form-group">Número</label>
								<input type="text" class="form-control" id="frm-numero" name="frm-numero" value="<?php echo @$numero ?>">
							</div> 
						</div>

					</div>


					

					<div class="row">
						<div class="col-md-5">
							<div class="mb-3">
								<label for="frm-complemento" class="form-group">Complemento</label>
								<input type="text" class="form-control" id="frm-complemento" name="frm-complemento"  value="<?php echo @$complemento ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="frm-bairro" class="form-group">Bairro</label>
								<input type="text" class="form-control" id="frm-bairro" name="frm-bairro" value="<?php echo @$bairro ?>">
							</div> 
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="frm-cidade" class="form-group">Cidade</label>
								<input type="text" class="form-control" id="frm-cidade" name="frm-cidade" value="<?php echo @$cidade ?>">
							</div>  
						</div>
						<div class="col-md-1">
							<div class="mb-3">
								<label for="frm-uf_endereco" class="form-group">Estado</label>
								<input type="text" class="form-control" id="frm-uf_endereco" name="frm-uf_endereco"  value="<?php echo @$uf_endereco ?>">
							</div> 
						</div>

					</div>

<hr >
					<div class="row" style="padding-top: 15px;">
						<div class="col-md-3">
							<div class="mb-3">
								<label for="frm-banco_if" class="form-group">Banco</label>
								<input type="text" class="form-control" id="frm-banco_if" name="frm-banco_if"  value="<?php echo @$banco_if ?>">
							</div> 
						</div>

						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-agencia" class="form-group">Agência</label>
								<input type="text" class="form-control" id="frm-agencia" name="frm-agencia" value="<?php echo @$agencia ?>">
							</div>  
						</div>

						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-conta" class="form-group">Conta</label>
								<input type="text" class="form-control" id="frm-conta" name="frm-conta"  value="<?php echo @$conta ?>">
							</div> 
						</div>
	
					
						

						<div class="col-md-3">
							<div class="mb-3">
								<label for="frm-pix" class="form-group">Pix</label>
								<input type="text" class="form-control" id="frm-pix" name="frm-pix"  value="<?php echo @$pix ?>">
							</div>  
						</div>

						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-tipo_pix" class="form-group">Tipo Pix</label>
								
									<select class="form-select" id="frm-tipo_pix" aria-label="Default select example" name="frm-tipo_pix">
										
										<option <?php if(@$tipo_pix == '--'){ ?> selected <?php } ?>  value="--">--</option>

										<option <?php if(@$tipo_pix == 'CPF'){ ?> selected <?php } ?>  value="CPF">CPF</option>
										<option <?php if(@$tipo_pix == 'Telefone'){ ?> selected <?php } ?>  value="Telefone">Telefone</option>

										<option <?php if(@$tipo_pix == 'CNPJ'){ ?> selected <?php } ?>  value="CNPJ">CNPJ</option>
										
										<option <?php if(@$tipo_pix == 'Email'){ ?> selected <?php } ?>  value="Email">Email</option>

										<option <?php if(@$tipo_pix == 'Chave Aleatoria'){ ?> selected <?php } ?>  value="Chave Aleatoria">Chave Aleatoria</option>

										
									</select>
							</div>  
						</div>

					</div>


					

					<hr >												
						

					<div class="row">
						<div class="col-md-2">
							<div class="mb-3">
								<label for="frm-ativo_agenda" class="form-group">Ativo na Agenda</label>
									<select class="form-select" if="frm-ativo_agenda" aria-label="Default select example" name="frm-ativo_agenda">
										
										<option <?php if(@$ativo_agenda == ''){ ?> selected <?php } ?>  value="--">--</option>

										<option <?php if(@$ativo_agenda == true){ ?> selected <?php } ?>  value="Ativo">Ativo</option>

										<option <?php if(@$ativo_agenda == false){ ?> selected <?php } ?>  value="Inativo">Inativo</option>
																							
									</select>
								
							</div>  
						</div>


						
						<div class="col-md-2">
								<div class="mb-3">
									<label for="frm-situacao" class="form-group">Situação Empregatícia</label>
									<select class="form-select" aria-label="Default select example" id="frm-situacao" name="frm-situacao">
										
										<option <?php if(@$situacao == '--'){ ?> selected <?php } ?>  value="--">--</option>

										<option <?php if(@$situacao == 'Ativo'){ ?> selected <?php } ?>  value="Ativo">Ativo</option>

										<option <?php if(@$situacao == 'Inativo'){ ?> selected <?php } ?>  value="Inativo">Inativo</option>
																							
									</select>
								
								</div>  
						</div>
					

						<div class="col-md-3">
									<div class="mb-3">
										<label for="frm-situacao"  class="form-group">Email Sistema</label>
										<input type="email" name="frm-email" class="form-control">
									
									</div>  
							
						</div>

						<div class="col-md-2">
								<div class="mb-3">
									<label for="frm-situacao"  class="form-group">Senha Sistema</label>
									<input type="password" name="frm-senha_sistema" class="form-control">
								<input type="password" name="frm-passalt" hidden value="<?php echo @$senha_sistema ?>">
								</div>  
						</div>
					</div>


					<small><div align="center" class="mt-1" id="mensagem"></div> </small>
						
					
			</div>
					<div class="modal-footer footer-modal-easy">
						<button type="button" id="btn-fechar" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
						<button name="btn-salvar" id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>

						<input name="frm-id"  type="hidden" id="frm-id" value="<?php echo @$_GET['id'] ?>">
					</div>
			</form>
		</div>
	</div>
</div>







<?php 
if(@$_GET['funcao'] == "novo"){ ?>
	<script type="text/javascript">
		var myModal = new bootstrap.Modal(document.getElementById('modalCadastrar'), {
			backdrop: 'static'
		})

		myModal.show();
	</script>
<?php } ?>



<?php 
if(@$_GET['funcao'] == "editar"){ ?>
	<script type="text/javascript">
		var myModal = new bootstrap.Modal(document.getElementById('modalCadastrar'), {
			backdrop: 'static'
		})

		myModal.show();
	</script>
<?php } ?>



<!--AJAX PARA INSERÇÃO E EDIÇÃO DOS DADOS COM IMAGEM -->
<script type="text/javascript">
	$("#form").submit(function () {
		var pag = "<?=$pag?>";
		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: pag + "/inserir.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {

				$('#mensagem').removeClass()

				if (mensagem.trim() == "Salvo com Sucesso!") {

                    //$('#nome').val('');
                    //$('#cpf').val('');
                    $('#btn-fechar').click();
                    window.location = "index.php?pagina="+pag;

                } else {

                	$('#mensagem').addClass('text-danger')
                }

                $('#mensagem').text(mensagem)

            },

            cache: false,
            contentType: false,
            processData: false,
            xhr: function () {  // Custom XMLHttpRequest
            	var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) { // Avalia se tem suporte a propriedade upload
                	myXhr.upload.addEventListener('progress', function () {
                		/* faz alguma coisa durante o progresso do upload */
                	}, false);
                }
                return myXhr;
            }
        });
	});
</script>








<!--SCRIPT PARA CARREGAR IMAGEM -->
<script type="text/javascript">
document.getElementById("frm-foto_cadastro").addEventListener("click", function() {
  document.getElementById("img-foto_cadastro").click();
  
});


	function carregarImg() {

		var target = document.getElementById('frm-foto_cadastro');
		var file = document.querySelector("input[type=file]").files[0];
		var reader = new FileReader();

		reader.onloadend = function () {
			target.src = reader.result;
		};

		if (file) {
			reader.readAsDataURL(file);


		} else {
			target.src = "";
		}
	}

</script>

