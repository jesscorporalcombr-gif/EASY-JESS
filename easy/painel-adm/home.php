
<?php 

require_once('../conexao.php');
require_once('verificar-permissao.php');
gerarMenu($pag, $grupos);

$saldoMesF = 0;
$totalVendasMF = 0;
$receberMesF = 0;
$pagarMesF = 0;

$hoje = date('Y-m-d');
$mes_atual = Date('m');
$ano_atual = Date('Y');
$dataInicioMes = $ano_atual."-".$mes_atual."-01";

$query = $pdo->query("SELECT * from produtos");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$totalProdutos = @count($res);

	$query = $pdo->query("SELECT * from fornecedores");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$totalFornecedores = @count($res);

	$query = $pdo->query("SELECT * from produtos where estoque < estoque_min");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$totalEstoqueBaixo = @count($res);

	$query = $pdo->query("SELECT * from vendas where data = curDate()");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$totalVendasDia = @count($res);


	$query = $pdo->query("SELECT * from contas_receber where vencimento < curDate() and pago != 'Sim'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$contas_receber_vencidas = @count($res);


	$query = $pdo->query("SELECT * from contas_receber where vencimento = curDate() and pago != 'Sim'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$contas_receber_hoje = @count($res);


	$query = $pdo->query("SELECT * from contas_pagar where vencimento < curDate() and pago != 'Sim'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$contas_pagar_vencidas = @count($res);


	$query = $pdo->query("SELECT * from contas_pagar where vencimento = curDate() and pago != 'Sim'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$contas_pagar_hoje = @count($res);





	$entradasM = 0;
	$saidasM = 0;
	$saldoM = 0;
	$query = $pdo->query("SELECT * from movimentacoes where data >= '$dataInicioMes' and data <= curDate() ");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 

		for($i=0; $i < $total_reg; $i++){
			foreach ($res[$i] as $key => $value){	}


				if($res[$i]['tipo'] == 'Entrada'){

					$entradasM += $res[$i]['valor'];
				}else{

					$saidasM += $res[$i]['valor'];
				}

				$saldoMes = $entradasM - $saidasM;

				$entradasMF = number_format($entradasM, 2, ',', '.');
				$saidasMF = number_format($saidasM, 2, ',', '.');
				$saldoMesF = number_format($saldoMes, 2, ',', '.');

				if($saldoMesF < 0){
					$classeSaldoM = 'text-danger';
				}else{
					$classeSaldoM = 'text-success';
				}

			}

		}



		$totalPagarM = 0;
		$query = $pdo->query("SELECT * from contas_pagar where data >= '$dataInicioMes' and data <= curDate()");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$pagarMes = @count($res);
		$total_reg = @count($res);
		if($total_reg > 0){ 

			for($i=0; $i < $total_reg; $i++){
				foreach ($res[$i] as $key => $value){	}

					$totalPagarM += $res[$i]['valor'];
				$pagarMesF = number_format($totalPagarM, 2, ',', '.');

			}
		}


		$totalReceberM = 0;
		$query = $pdo->query("SELECT * from contas_receber where data >= '$dataInicioMes' and data <= curDate()");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$receberMes = @count($res);
		$total_reg = @count($res);
		if($total_reg > 0){ 

			for($i=0; $i < $total_reg; $i++){
				foreach ($res[$i] as $key => $value){	}

					$totalReceberM += $res[$i]['valor'];
				$receberMesF = number_format($totalReceberM, 2, ',', '.');

			}
		}





		$totalVendasM = 0;
		$query = $pdo->query("SELECT * from vendas where data >= '$dataInicioMes' and data <= curDate() and status = 'Concluída'");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$total_reg = @count($res);
		if($total_reg > 0){ 

			for($i=0; $i < $total_reg; $i++){
				foreach ($res[$i] as $key => $value){	}

					$totalVendasM += $res[$i]['valor'];
				$totalVendasMF = number_format($totalVendasM, 2, ',', '.');

			}
		}

		?>


		


	<div class="container" style= "background-color: grey;">
        
		<div class="container-fluid">
			<section id="minimal-statistics">
				<div class="row mb-2">
					<div class="col-12 mt-3 mb-1">
					</br>
						<h4 style="font-size: 13px;  font-style:normal;" class="text-uppercase">Estatísticas</h4>

					</div>
				</div>

				<div class="row mb-4">

					<div class="col-xl-3 col-sm-6 col-12"> 
						<div class="card">
							<div class="card-content">
								<div class="">
									<div class="row">
										<div class="align-self-center col-3">
											<img src="../img/icones/barra_verde.png" width="20px" height="20px">
										</div>
										<div class="col-9 text-end">
											<h3> <span class="text-success"><?php echo @$totalProdutos ?>&nbsp;</span></h3>
											<span style="font-size: 15px;  font-style:normal;">Total de Produtos &nbsp;</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					
					<div class="col-xl-3 col-sm-6 col-12"> 
						<a class="text-dark" href="index.php?pagina=estoque" style="text-decoration: none">
						<div class="card">
							<div class="card-content">
								<div class="">
									<div class="row">
										<div class="align-self-center col-3">
											<img src="../img/icones/barra_vermelha.png" width="20px" height="20px">
										</div>
										<div class="col-9 text-end">
											<h3> <span  class=""><?php echo @$totalEstoqueBaixo ?>&nbsp;</span></h3>
											<span style="font-size: 15px;  font-style:normal;">Estoque Baixo &nbsp;</span>
										</div>
									</div>
								</div>
							</div>
						</div>
						</a>
					</div>
					


					<div class="col-xl-3 col-sm-6 col-12"> 
						<div class="card">
							<div class="card-content">
								<div class="">
									<div class="row">
										<div class="align-self-center col-3">
											<img src="../img/icones/barra_preta.png" width="20px" height="20px">
										</div>
										<div class="col-9 text-end">
											<h3> <span class="<?php echo $classeSaldo ?> "&nbsp;> <?php echo @$totalFornecedores ?> &nbsp;</span></h3>
											<span style="font-size: 15px;  font-style:normal;">Total Fornecedores &nbsp;</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>


					<div class="col-xl-3 col-sm-6 col-12"> 
						<div class="card">
							<div class="card-content">
								<div class="">
									<div class="row">
										<div class="align-self-center col-3">
											<img src="../img/icones/dinheiro01.png" width="20px" height="20px">
										</div>
										<div class="col-9 text-end">
											<h3> <?php echo @$totalVendasDia ?> &nbsp;</h3>
											<span style="font-size: 15px;  font-style:normal;">Total Vendas Dia &nbsp;</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>






				<div class="row mb-4">

					<div class="col-xl-3 col-sm-6 col-12"> 
						<div class="card">
							<div class="card-content">
								<div class="">
									<div class="row">
										<div class="align-self-center col-3">
											<img src="../img/icones/pag03.png" width="20px" height="20px"></i>
										</div>
										<div class="col-9 text-end">
											<h3> <span class=""><?php echo @$contas_pagar_hoje ?> &nbsp;</span></h3>
											<span style="font-size: 15px;  font-style:normal;">Contas à Pagar (Hoje) &nbsp;</span>

										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-xl-3 col-sm-6 col-12"> 
						<div class="card">
							<div class="card-content">
								<div class="">
									<div class="row">
										<div class="align-self-center col-3">
											<img src="../img/icones/pag_01.png" width="20px" height="20px"></i>
										</div>
										<div class="col-9 text-end">
											<h3> <span class="">
												<?php echo @$contas_pagar_vencidas ?> &nbsp;</span></h3>
												<span style="font-size: 15px;  font-style:normal;">Contas à Pagar Vencidas &nbsp;</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>


						<div class="col-xl-3 col-sm-6 col-12"> 
							<div class="card">
								<div class="card-content">
									<div class="">
										<div class="row">
											<div class="align-self-center col-3">
												<img src="../img/icones/receber.png" width="20px" height="20px"></i>
											</div>
											<div class="col-9 text-end">
												<h3> <span class=""><?php echo @$contas_receber_hoje ?> &nbsp;</span></h3>
												<span style="font-size: 15px;  font-style:normal;">Contas Receber (Hoje) &nbsp;</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>


						<div class="col-xl-3 col-sm-6 col-12"> 
							<div class="card">
								<div class="card-content">
									<div class="">
										<div class="row">
											<div class="align-self-center col-3">
												<img src="../img/icones/pag02.png" width="20px" height="20px"></i>
											</div>
											<div class="col-9 text-end">
												<h3><?php echo @$contas_receber_vencidas ?> &nbsp;</h3>
												<span style="font-size: 15px;  font-style:normal;">Contas à Receber Vencidas &nbsp;</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>



				</section>

				<section id="stats-subtitle">
					<div class="row mb-2">
						<div class="col-12 mt-3 mb-1">
							<h6 style="font-size: 13px;  font-style:normal;" class="text-uppercase">Estatísticas Mensais &nbsp;</h6>

						</div>
					</div>

					<div class="row mb-4">

						<div class="col-xl-6 col-md-12">
							<div class="card overflow-hidden">
								<div class="card-content">
									<div class="">
										<div class="row media align-items-stretch">
											<div class="align-self-center col-1">
												<img src="../img/icones/totalmes03.png" width="20px" height="20px">
											</div>
											<div class="media-body col-6">
												<h4 style="font-size: 16px;  font-style:normal;">Saldo Total</h4>
												<span style="font-size: 15px;  font-style:normal;">Total Arrecado este Mês</span>
											</div>
											<div class="text-end col-5">
												<h2><span style="font-size: 22px;  font-style:normal;" class="<?php echo $classeSaldoM ?>" &nbsp;>R$ <?php echo $saldoMesF ?> &nbsp;</h2></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xl-6 col-md-12">
							<div class="card overflow-hidden">
								<div class="card-content">
									<div class="">
										<div class="row media align-items-stretch">
											<div class="align-self-center col-1">
												<img src="../img/icones/totcontmes.png" width="20px" height="20px">
											</div>
											<div class="media-body col-6">
												<h4 style="font-size: 16px;  font-style:normal;">Contas à Pagar</h4>
												<span style="font-size: 15px;  font-style:normal;">Total de <?php echo $pagarMes ?> Contas no Mês</span>
											</div>
											<div class="text-end col-5">
												<h2 style="font-size: 20px;  font-style:normal;">R$ <?php echo @$pagarMesF ?> &nbsp;</h2>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>


					<div class="row mb-4">

						<div class="col-xl-6 col-md-12">
							<div class="card overflow-hidden">
								<div class="card-content">
									<div class="">
										<div class="row media align-items-stretch">
											<div class="align-self-center col-1">
												<img src="../img/icones/totpagmes01.png" width="20px" height="20px">

											</div>
											<div class="media-body col-6">
												<h4 style="font-size: 16px;  font-style:normal;">Contas à Receber</h4>

												<span style="font-size: 15px;  font-style:normal;">Total de <?php echo $receberMes ?> Contas no Mês &nbsp;</span>
											</div>
											<div class="text-end col-5">
												<h2 style="font-size: 22px;  font-style:normal;">R$ <?php echo @$receberMesF ?> &nbsp;</h2>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xl-6 col-md-12">
							<div class="card overflow-hidden">
								<div class="card-content">
									<div class="">
										<div class="row media align-items-stretch">
											<div class="align-self-center col-1">

												<img src="../img/icones/totmes.png" width="20px" height="20px">

											</div>
											<div class="media-body col-6">
												<h4 style="font-size: 16px;  font-style:normal;">Total de Vendas</h4>
												<span style="font-size: 15px;  font-style:normal;">Vendas do Mês em R$</span>
											</div>
											<div class="text-end col-5">
												<h2 style="font-size: 20px;  font-style:normal;">R$ <?php echo $totalVendasMF ?> &nbsp;</h2>
											</div>
										</div>


									</div>
								</div>
							</div>
						</div>

					</div>


				</section>


				<section id="stats-subtitle">
					<div class="row mb-2">
						<div class="col-12 mt-3 mb-1">
							<h6 style="font-size: 13px;  font-style:normal;" class="text-uppercase">Gráficos Por Meses</h6>

						</div>
					</div>


<style type="text/css">
			#principal{
    width:100%;
    height: 100%;
    margin-left:10px;
    font-family:Verdana, Helvetica, sans-serif;
    font-size:12px;

}
#barra{
    margin: 0 2px;
    vertical-align: bottom;
    display: inline-block;
    padding:5px;
    text-align:center;

}
.cor1, .cor2, .cor3, .cor4, .cor5, .cor6, .cor7, .cor8, .cor9, .cor10, .cor11, .cor12{
    color:#FFF;
    padding: 5px;
}
.cor1{ background-color: #9b9797; }
.cor2{ background-color: #5f5b5b; }
.cor3{ background-color: #a4edb0; }
.cor4{ background-color: #7bca88; }
.cor5{ background-color: #90aaee; }
.cor6{ background-color: #6a82bf; }
.cor7{ background-color: #e3c574; }
.cor8{ background-color: #d9b042; }
.cor9{ background-color: #f167cc; }
.cor10{ background-color: #ce54ad; }
.cor11{ background-color: #ef6c7e; }
.cor12{ background-color: #fe3551; }
		</style>

<div id="principal">
    <p>Vendas no Ano de <?php echo $ano_atual ?></p>
<?php
// definindo porcentagem
//BUSCAR O TOTAL DE VENDAS POR MES NO ANO
$total  = 12; // total de barras
for($i=1; $i<13; $i++){
	

$dataMesInicio = $ano_atual."-".$i."-01";
$dataMesFinal = $ano_atual."-".$i."-31";
$totalVenM = 0;

		$query = $pdo->query("SELECT * from vendas where data >= '$dataMesInicio' and data <= '$dataMesFinal' and status = 'Concluída'");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$total_vendas_mes = @count($res);
		$totalValor = 0;
		$totalValorF = number_format($totalValor, 2, ',', '.');
		for($i2=0; $i2 < $total_vendas_mes; $i2++){
						foreach ($res[$i2] as $key => $value){	}

		
			$totalValor += $res[$i2]['valor'];
			$totalValorF = number_format($totalValor, 2, ',', '.');
			$altura_barra = $totalValor / 100;

		}


		if($i < 10){
			$texto = '0'.$i .'/'.$ano_atual;
		}else{
			$texto = $i .'/'.$ano_atual;
		}
		
			
		?>


     <div id="barra">
            <div class="cor<?php echo $i ?>" style="height:<?php echo $altura_barra + 25 ?>px"> <?php echo $totalValorF ?> </div>
            <div><?php echo $texto ?></div>
        </div>
		
<?php }?>

</div>



</section>

</div>
</div>


