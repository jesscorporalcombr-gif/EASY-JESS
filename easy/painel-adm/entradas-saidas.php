<?php 
$pag = 'entradas-saidas';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');
gerarMenu($pag, $grupos);



$data_inicial = date('Y-m-d', strtotime('-6 days'));
$data_final = date('Y-m-d', strtotime('+6 days'));
$condicoes = [
    "data_vencimento" => "$data_inicial<->$data_final"
];
  
$statusLabels = [
    ['st-pago-venc',  'Pago em atraso'],      // pago depois do vencimento
    ['st-pago',       'Pago / Recebido'],     // já liquidado
    ['st-a-receber',  'A Receber'],           // pagamento futuro (entrada)
    ['st-vencido',    'Vencido'],             // vencimento ultrapassado
    ['st-hoje',       'Vence hoje'],          // vence no dia corrente
    ['st-amanha',     'Vence amanhã'],        // vence amanhã
    ['st-vencendo',   'Vence em até 7 dias'], // prazo curto
    ['st-pendente',   'A Vencer'],            // prazo > 7 dias   ← conforme pedido
];
	
	//---======================CONEXÕES=================-->
	//--registros de lançamentos -->
	 
	$query = $pdo->query("SELECT * from financeiro_extrato order by id desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		 }else{
		echo '<p>Não existem dados para serem exibidos!!';
	} 
	
	
	//-contas bancárias -->
	
	$query = $pdo->query("SELECT * from contas_correntes WHERE ativa = 1 order by id asc ");
	$contas = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_contas = @count($contas);

	
	//--contas categorias contabeis -->
	
    $query = $pdo->query("
        SELECT * 
        FROM categorias_contabeis 
        WHERE (categoria = 1 OR categoria = 2) 
        AND subcategoria <> '' 
        ORDER BY id DESC
    ");
    $categorias = $query->fetchAll(PDO::FETCH_ASSOC);
    $total_categorias = count($categorias); // não precisa do @
	
	
    //-fornecedores -->
	
	$query = $pdo->query("SELECT * from fornecedores order by id desc");
	$fornecedores = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_fornecedores = @count($fornecedores);
 
       //-centro custo -->
	
	$query = $pdo->query("SELECT * from centro_custo order by id desc");
	$centro_custos = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_centro_custo= @count($centro_custos);

     //--Formas de pagamentito -->
	
	$query = $pdo->query("SELECT * from pagamentos_tipo order by id desc");
	$tipos_pagamentos = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_tipos_pagamentos= @count($tipos_pagamentos);
	
	   //--Formas forma_pagamentos -->
	
	$query = $pdo->query("SELECT * from pagamentos_forma order by id desc ");
	$formas_pagamentos = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_formas_pagamentos= @count($formas_pagamentos);
	

?>


<!--<script type="text/javascript" src="js/financeiro.js?v=0.74"></script>-->





	<!--=======================================================-->
	
<style>
    .btn-custom {
        margin-right: 25px; /* Espaçamento à direita de cada botão */
        font-size: 12px; /* Tamanho da fonte especificado */
    }
    .hover:hover {
    background-color: white; /* Cor de fundo quando o mouse estiver em cima */
    cursor: pointer; /* Cursor se transforma em uma mãozinha para indicar clique */
        border: none;
    }
    .btn-buscar {
            color: blue; /* Espaçamento à direita de cada botão */
            border: 1px solid blue; /* Tamanho da fonte especificado */
            width: 150px;
        }
    .btn-entrada {
        color: green;
        border: 1px solid green;
    }

    .btn-saida {
        color: red;
        border: 1px solid red;
    }

    .btn-transferencia {
        color: orange;
        border: 1px solid orange;
    }
    .calend-custom {
        margin-right: 15px; /* Espaçamento à direita de cada botão */
        font-size: 80px; /* Tamanho da fonte especificado */
    }
    
    
    .rd-cust {
         margin-right: 15px;/* Espaçamento à direita de cada botão */
        font-size: 12px; /* Tamanho da fonte especificado */
    }



.financeiroTable tr:last-of-type {
    border-bottom: 2px solid <?php echo $cor_head_tabelas?>;
}

.financeiroTable tr:nth-of-type(odd) {
    background-color: <?php echo $cor_linha_impar?>; /* Insira a cor de fundo desejada */
}

.financeiroTable tr:nth-of-type(even) {
    background-color: <?php echo $cor_linha_par?>;
}

.financeiroTable th {
    background-color:<?php echo $cor_head_tabelas?>;
    color: <?php echo $cor_fonte_head_tabelas?>;
}

.financeiroTable tr {
    color: <?php echo $cor_fonte_tabela?>;
}
.financeiroTable a {
    color: <?php echo $cor_fonte_tabela?>;
}
.financeiroTable tr:hover {
    background-color:<?php echo $cor_secundaria?>;
}

.btn{

    width:145px;
    font-size: 14px;
}

.btn-primary{
    width: 100px;

}

#custom-menu {
  display: none;
  position: absolute;
  background: #ffffff;
  border: 1px solid #cccccc;
  border-radius: 4px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  padding: 5px 0;
  z-index: 9999;
  opacity: 1;
  transition: opacity 0.3s ease;
  min-width: 160px;
  font-family: Arial, sans-serif;
}

#custom-menu .menu-item {
  padding: 8px 16px;
  cursor: pointer;
  color: #333;
  transition: background 0.2s, color 0.2s;
  border-radius: 4px;
}

#custom-menu .menu-item:hover {
  background-color:rgb(112, 113, 96);
  color: rgb(255, 255, 255);
}

#custom-menu .menu-item:not(:last-child) {
  border-bottom: 1px solid #eeeeee;
}



#custom-calend {
  display: none;
  position: absolute;
  background: #ffffff;
  border: 1px solid #cccccc;
  border-radius: 4px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  padding: 5px 0;
  z-index: 9999;
  opacity: 1;
  transition: opacity 0.3s ease;
  min-width: 160px;
  font-family: Arial, sans-serif;
}

#custom-calend .menu-item {
  padding: 8px 16px;
  cursor: pointer;
  color: #333;
  transition: background 0.2s, color 0.2s;
  border-radius: 4px;
}

#custom-calend .menu-item:hover {
  background-color:rgb(112, 113, 96);
  color: rgb(255, 255, 255);
}

#custom-calend .menu-item:not(:last-child) {
  border-bottom: 1px solid #eeeeee;
}





.linha-selecionada td{
background-color:rgb(29, 119, 197);
color: #ccc;    
}





   

.bootstrap-select .dropdown-toggle.btn.btn-outline-primary.btn-sm {
  border: 1px solid #D1D1D1 !important;
  font-size: 13px !important;
  height: 32px !important;
  /* padding: topo e base 2px, esquerda 10px, direita 0.7rem */
  /*padding: 2px 0.7rem 2px 10px !important;*/
  /* reposiciona o ícone de dropdown à direita */
  background-position: right 0.20rem center !important;
  /* permite o botão crescer se precisar */
  max-width: none !important;
  /* opcional: manter o mesmo line-height do seu design */
  line-height: 1.5 !important;
  border-radius: 6px;
  color:#333;
  background-color: #FFFFFF;
  width:auto;
  min-width: 180px;;
}

  .bootstrap-select .dropdown-toggle.btn.btn-outline-primary.btn-sm:hover{
    background-color: #FFFFFF;


  }

  .dropdown-toggle .btn .btn-outline-primary .btn-sm .show{
    background-color: #FFFFFF;

  }

  input.form-control.datepicker{
    width:120px;   
    cursor: pointer;
    padding-right: 15px;
    max-width: 180px;
  }

  .calendar-icon {
    position: absolute;
    right: 20px;
    top: 61%;

    transform: translateY(-50%);
    color: #6d6d6d;
    pointer-events: none;
  }

  .chktabela{
    width:18px;
    height:18px;
  }
</style>



<!-- ============================ ITENS DE FILTRAGEM =================================-->

<div id= "filtro-tabela" class="container-md mt-4" >
    <div class="row align-items-start">
        <!-- ================Filtro data inicio e data fim ==================-->
        <div class="col-md-9" >
            <div class="row">

                    <div class="col-auto mb-3 position-relative">
                        <label class="form-group" for="filt-dt-inicio">Data Início:</label>
                        <input id="dataInicial" type="text" data-type="date" class="form-control filtroExtrato datepicker" value="<?=$data_inicial?>" >
                        <i class="bi bi-calendar3 calendar-icon"></i>
                    </div>
                
                
                    <div class="col-auto mb-3 position-relative">
                        <label class="form-group" for="filt-dt-fim">Data Fim:</label>
                        <input id="dataFinal" type="text" data-type="date" class="form-control filtroExtrato datepicker" value="<?= $data_final?>" >
                        <i class="bi bi-calendar3 calendar-icon"></i>
                    </div>
                <div class="col-auto mb-3" style="min-width: 150px;" >
                    <div class="form-group" >
                        <label for="tp-data">Tipo de Data:</label>
                        <select id="tipoData" style = "font-size: 14px;"  class="form-select filtroExtrato">
                            
                            <option value="data_pagamento">Pagamento</option>
                            <option value="data_competencia">Competência</option>
                            <option selected value="data_vencimento">Vencimento</option>
                        </select>
                    </div>
                </div>
                <div class="col-auto mb-3">
                    <div class="form-group">
                        <label >Conta Bancária:</label>
                        <select id="filtroConta" style = "font-size: 14px;" class="form-select filtroExtrato">
                            <option value="Todas">Todas</option>
                            <?php foreach($contas as $conta_banc): ?>
                                <option value="<?php echo htmlspecialchars($conta_banc['id']); ?>">
                                    <?php echo htmlspecialchars($conta_banc['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <!--botão Buscar -->
                <div class="col-auto mb-3">
                    <div class="d-flex justify-content-start">
                        <button id="btn-buscar"  class="btn btn-primary">Buscar</button>
                    </div>
                </div>
            </div> 
            <!--fim da primeira linha de filtros -->

            <!-- ================Linha Quadros das Informações quando o tipo de data é pagamento ==================-->
            <div class="row mb-3" style="border-bottom: 1px solid grey;">
                <div class="container text-center " id="cont-saldos" hidden="true">
                    <div class="row row-cols-lg-3">
                        <div class="col p-3">
                            <div class="bloco-easy">
                                <label  class="form-group label-bl-fin">Saldo no Dia Anterior:</label>
                                <span  id="saldo-dia-anterior"> </span>
                            </div>
                        </div>
                        <div class="col p-3">
                            <div class="bloco-easy">
                                <label  class="form-group label-bl-fin">Resultado no Periodo:</label>
                                <span  id="saldo-periodo"> </span>
                            </div>
                        </div>
                        <div class="col p-3">
                            <div class="bloco-easy">
                                <label  class="form-group label-bl-fin">Saldo na Data:</label>
                                <span  id="saldo-atual"> </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- ================Linha dos filtros secundários ==================-->
            <div class="row">
                <div class="col-auto mb-3">
                    <div class="form-group">
                        <label for="filt-por">Filtrar por:</label>
                        <select id="filtrar-por" style = "font-size: 14px; min-width: 180px;" type="text" class="form-select">
                            <option></option>
                            <option value="categoria">Categoria</option>
                            <!--<option>Centro de Custos</option>-->
                            <!--<option>Fornecedor</option>-->
                            <option value="status">Status</option>
                        </select>
                    </div>
                </div>
                <div class="col-auto mb-3">
                    <div class="form-group" style ="min-width: 180px;">
                        <label for="filt-por">Selecione:</label>
                        <select id="filtro-selecionado" name="filt-por" data-live-search="true"  data-style="btn btn-outline-primary btn-sm" title=" " style = "font-size: 14px; " type="text" class="selectpicker">

                        </select>
                    </div>
                </div>
            </div>
            <div class="row mb-3" id="chks-mostrar">
                <div class="col-auto align-self-center">
                    <div class="form-check" style = "width: auto; ">
                        <input id="chk_liqui" name="chk_liqui" type="checkbox" class="form-check-input">
                        <label class="form-check-label lb-fin-most" for="chk_liqui">Valor Líquido</label>
                    </div>
                </div>
                <div class="col-auto align-self-center">
                    <div class="form-check" style = "width: auto; ">
                        <input id="chk_transf"  type="checkbox" checked class="form-check-input">
                        <label class="form-check-label lb-fin-most" for="chk_transf">Transferências:</label>
                    </div>
                </div>
                <div class="col-auto align-self-center">
                    <div class="form-check" style = "width: auto; ">
                        <input id="chk_entradas"  type="checkbox" checked class="form-check-input">
                        <label class="form-check-label lb-fin-most" for="chk_entradas">Entradas:</label>
                    </div>
                </div>

                <div class="col-auto align-self-center">
                    <div class="form-check" style = "width: auto; ">
                        <input id="chk_saidas"  type="checkbox" checked class="form-check-input">
                        <label class="form-check-label lb-fin-most" for="chk_saidas">Saídas:</label>
                    </div>
                </div>
            </div>

           

        </div>
        <div class="col-md-3 mb-3" >
            <div class="container-fuid text-center" >
                <div class="row mb-3 justify-content-center">
                    <a id="btnNovaEntrada" class="btn btn-outline-success">+ Entrada</a>
                </div>
                <div class="row mb-3 justify-content-center">
                    <a id="btnNovaSaida" class="btn btn-outline-danger">+ Saída</a>
                </div>
                <div class="row mb-3 justify-content-center">
                    <a id="btnNovaTransferencia" class="btn btn-outline-warning">+ Transferência</a> 
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="container text-center">
            <div class="row row-cols-2 row-cols-lg-5 g-2 g-lg-3">
                <div class="col p-3">
                    <div class="bloco-easy">
                        <label  class="form-group label-bl-fin">Entradas no Período</label>
                        <span id="entradas-periodo" class="num-positivo"> </span>
                    </div>
                </div>
                <div class="col p-3">
                    <div class="bloco-easy">
                        <label  class="form-group label-bl-fin">Saídas no Período:</label>
                        <span class="num-negativo" id="saidas-periodo"> </span>
                    </div>
                </div>
                <div class="col p-3">
                    <div class="bloco-easy">
                        <label  class="form-group label-bl-fin">Pagamentos Vencidos:</label>
                        <span  id="vencidos-periodo"> </span>
                    </div>
                </div>
                <div class="col p-3 invisible">
                    <div class="bloco-easy">
                    <label  class="form-group label-bl-fin"></label>
                    <span  id=""> </span>
                    </div>
                </div>
                <div class="col p-3">
                    <div class="bloco-easy">
                        <label  class="form-group label-bl-fin">Resultado Filtrado:</label>
                        <span  id="spResultadoFilt"> </span>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>       
        
        




<!-- ============= LISTA DE LANÇAMENTOS ======================================-->

<div class="container-md financeiro-container">

<div class="row mb-2">
    <div class="col-md-1">
        <select class ="form-select rowsPerPage" id="rowsPerPage" >
            <option value="10">10</option>
            <option value="25">50</option>
            <option value="100">100</option>
            <option value="500">500</option>
        </select>
    </div>

    <div class="col-md-7">
        <div class="col-auto mt-2" id="bl-result-sel">
                        <div class="bloco-easy" style="max-width: 220px;">
                            <label  class="form-group label-bl-fin">Resultado Selecionados:</label>
                            <button id="chk-no" class="bnt-select-th-financ"><i style = "color: red;" class="bi bi-x-circle"></i></button>
                            <span style="margin-left: 20px;" id="resultado-selecionados"> </span>
                            <button id="btn-excluir" class="bnt-select-th-financ"><i class="bi bi-trash3"></i></button>
                            <button id="btn-pagar" class="bnt-select-th-financ"><i class="bi bi-calendar2-check"></i></button>
                        </div>
                    </div>
        </div>
    <div class="col-md-4">
        <input type="text" class="form-control searchBox" id="searchBox" placeholder="Pesquisar...">
    </div>
</div>

<div class="row" >
    <div class="mb-4">
    

        <table id="tabelaExtrato" class="financeiroTable" data-table="financeiro_extrato" data-filtro='{"data_vencimento":"<?= $data_inicial?><-><?= $data_final?>"}'>
            <thead>
                <tr class="data-modal" data-modal="modalFinanceiro"  data-tipo="venda">  <!-- class="data-get" data-get= "index.php?pagina=entradas-saidas&funcao=editar&id=">-->
                    <th hidden   data-sort="num" data-field="id">ID</th>
                    <th class="checkbox" >
                        <button id="chk-all" class="bnt-select-th-financ" style="margin-top: -25px;"> <i style="color: green;" class="bi bi-check2-all"></i> </button>
                        
                    </th>
                    <th  data-classe-td="td-destacado td-vermelho" data-sort-init="DESC" data-classe-tr="tr-avisos"  data-sort="data" id="dataRef" data-field="data_vencimento">Data</th>
                    <th  data-sort="a-z"   data-field="descricao">Título</th>
                    <th  data-sort="a-z"   data-field="conta">Conta</th>
                    <th  data-sort="a-z" class="f-pagamento" data-field="forma_pagamento">Forma Pagamento</th>
                    <th  data-sort="a-z" data-field="categoria">Categoria</th>
                    <th  id="thBrut"  data-sort="num"  data-classe-td="numVirg2c tdBrut" data-field="valor_principal">Valor</th>
                    <th  id="thLiqu" hidden data-sort="num"  data-classe-td="numVirg2c tdLiqu" data-field="valor_liquido">Líquido</th>
                    <th data-sort="a-z" data-field="statusText" data-classe-td="status" class="status" >Situação</th>
                
                    <!-- Adicionar mais colunas conforme necessário -->
                </tr>
            </thead>

            <tbody>
                <!-- As linhas vao ser inseridas aqui dinamicamente -->
            </tbody>
        </table>
    </div>
</div>

<div class="row mb-8">
    
    <div class="col-auto pagination" id="pagination">
        <!-- Botões de navegação serão inseridos aqui -->
    </div>

    <div id="info-range" class="col-auto info-range" style="margin-bottom: 10px; text-align:right;">
        Exibindo de 0 a 0 de um total de 0 registros
    </div>
</div>
</div>

<!-- ==================================================================-->	







<div id="custom-menu" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; padding:10px; z-index:80000;">
  <div data-clique="editar" class="menu-item clk-editar">Editar</div>
  <hr>
  <div data-clique="pagar" class="menu-item status-aguardando">Efetuar Pagamento</div>
  <div data-clique="upload" class="menu-item status-atendimento">Upload de Arquivo</div>
  <div data-clique="excluir" id="btn-excluir-ind" class="menu-item status-finalizado">Excluir Lançamento</div>
</div>



<div id="custom-calend" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; padding:10px; z-index:80001; width:200px;">
  <label for="novoDataPagamento" class="form-label">Data de Pagamento:</label>
  <input type="date" id="novoDataPagamento" class="form-control mb-2">
  <div class="d-flex justify-content-end">
    <button id="btn-cancelar-data" class="btn btn-sm btn-secondary me-2">Cancelar</button>
    <button id="btn-salvar-data"    class="btn btn-sm btn-primary">Salvar</button>
  </div>
</div>








<script>



var activeConditions = <?php echo json_encode($condicoes); ?>;
//filtros
const dataInicial = document.getElementById('dataInicial');
const dataFinal = document.getElementById('dataFinal');

const tipoData = document.getElementById('tipoData');


var isModalOpen = false;


const filtroConta = document.getElementById('filtroConta');


const tabela = document.getElementById('tabelaExtrato');
const filtroExtrato = document.querySelectorAll('.filtroExtrato'); // inputs das datas


const thData = document.getElementById('dataRef');

const btnBuscar = document.getElementById('btn-buscar');
const chkLiqui = document.getElementById('chk_liqui');


const thLiqu = document.getElementById('thLiqu');
const thBrut = document.getElementById('thBrut');




function atualizarVisibilidadeColunas() {
    const tdBrut = document.querySelectorAll('.tdBrut');
    const tdLiqu = document.querySelectorAll('.tdLiqu');

    if (!chkLiqui.checked) {
        thBrut.removeAttribute('hidden');
        thLiqu.setAttribute('hidden', 'true');

        tdLiqu.forEach(td => td.setAttribute('hidden', 'true'));
        tdBrut.forEach(td => {
            td.removeAttribute('hidden');
            td.style.display = ''; // Remove o display:none
        });

    } else if (chk_liqui.checked) {
        thLiqu.removeAttribute('hidden');
        thBrut.setAttribute('hidden', 'true');

        
        tdBrut.forEach(td => td.setAttribute('hidden', 'true'));
        
        tdLiqu.forEach(td => {
        td.removeAttribute('hidden');
        td.style.display = ''; // Remove o display:none
        });

    }
}

chkLiqui.addEventListener('change', function() {
    atualizarVisibilidadeColunas();
        const blSoma = document.getElementById('spResultadoFilt');
        
        let somaValor = (chkLiqui.checked)? somaValorLiquido: somaValorPrincipal;
        blSoma.textContent = 'R$ ' + formatarNumeroParaVirgula(somaValor);
        exibirSaldos();
        

});

   
  
const contSaldos = document.getElementById('cont-saldos');

tipoData.addEventListener('change', function(e){ blocosSaldos('change')});
    filtroConta.addEventListener('change', function(e){ 
    blocosSaldos('change')
});

var mostrarTotais= false;
var tpData = tipoData.options[tipoData.selectedIndex].getAttribute('data-tipodata');




function blocosSaldos(evento){

    
    
    
    tpData = tipoData.value;
    if (evento=='change'){
        if (tpData!= 'data_pagamento' || filtroConta.value=='Todas'){
            
            mostrarTotais=false;
        } else if(filtroConta.value!='Todas' && tpData == 'data_pagamento'){
            mostrarTotais=true
        }
    } else if (evento=='carrega'){
        if (mostrarTotais){
            contSaldos.removeAttribute('hidden');
            chkLiqui.checked = true;
        }else{
            contSaldos.setAttribute('hidden', 'true');
            
        }
    }
}


    //filtroExtrato.forEach(function(input) {
   

const categoriasContabeis = <?php echo json_encode($categorias, JSON_UNESCAPED_UNICODE); ?>;

const statusLabels = <?php echo json_encode(
       array_map(fn($s) => ['cls'=>$s[0],'text'=>$s[1]], $statusLabels),
       JSON_UNESCAPED_UNICODE
); ?>;


  
document.getElementById('btnNovaEntrada').addEventListener('click', e => {
  if (isModalOpen) return;
  isModalOpen = true;
  abrirModal('modalFinanceiro','0','receita');
});
document.getElementById('btnNovaSaida').addEventListener('click', e => {
  if (isModalOpen) return;
  isModalOpen = true;
  abrirModal('modalFinanceiro','0','despesa');
});
document.getElementById('btnNovaTransferencia').addEventListener('click', e => {
  if (isModalOpen) return;
  console.log('nova transfenrencia');
  isModalOpen = true;
  abrirModal('modalFinanceiro','0','transferencia');
});

</script>






<script src="js/financeiro.js?v=0.100"></script>








<!--====================================MENU SUSPENSO ==================================//-->






















