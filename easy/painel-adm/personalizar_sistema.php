<?php 
$pag = 'personalizar_sistema';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php')
?>

<head>



<style>
    table {
        width: 50%; /* Ajuste a largura conforme necessário */
        /*border-collapse: collapse; /* Garante que as bordas das células se juntem */
    }
    th, td {
        border: none; /* Adiciona borda às células */
        text-align: center; /* Centraliza o texto nas células */
        padding: 2px; /* Espaçamento interno nas células */
		font-size:12px;
    }
    th {
        background-color: #f2f2f2; /* Cor de fundo para os cabeçalhos */
    }
</style>




</head>
<body>
<!--=============================== SUBMENU =======================================-->
<?php  gerarMenu($pag, $grupos); ?>
<!--================================================================================-->
<div class="container-md">
	<div style=" overflow: hidden; position:static; border: 1px solid white; border-radius: 15px; margin-top: 20px; box-shadow: 5px 15px 20px rgba(59, 57, 128, 0.2);" >
			<form id="Form-envConfig">	
					<div class="row" style="height:55px; background-color: <?=$cor_principal ?>; color: white; padding: 10px; font-size: 20px;">
						<h5 style="color: white; padding: 10px 0 10px 20px; font-size: 18px;">Personalização do Easy:</h5>
					</div>

					
						<div class="row" style="height:40px; position:relative; padding:10px; background-color: <?=$cor_secundaria ?>; ">
							<div class="col-md-12" style="text-align: center; padding-top: 0px; color: white;">
							Cores do sistema:
							</div>
						</div>



						<div class="row" style="height:40px; position:relative; background-color: white; padding-right:25px;">

							<!-----------1------------->
								<div class="col-md-1" style="font-size: 10px; text-align: right; padding: 10px;">
								Fundo:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px;  width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color"name="cor_background" value="<?= $cor_background ?>" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
								<!-----------2------------->
								<div class="col-md-1" style="font-size: 10px; text-align: right; padding: 10px;">
								Fonte:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px;  width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color"name="cor_fonte_background" value="<?= $cor_fonte_background ?>" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
								<!------------3------------->
								<div class="col-md-1" style="font-size: 10px; text-align: right; padding: 10px;">
								Principal:
								</div>
								<div class="col-md-1" style="padding: 8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_principal ?>" name="cor_principal" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
								<!------------4------------->

								<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
								Secundária:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_secundaria ?>"name="cor_secundaria" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
								<!------------5------------>
								<div class="col-md-1" style="font-size: 10px; text-align: right; padding: 10px;">
								Terciária:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px;  width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color"name="cor_terciaria" value="<?= $cor_terciaria ?>" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
								<div class="col-md-1" style="font-size: 10px; text-align: right; padding: 10px;">
								Fonte sec:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px;  width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" name="cor_fonte_secundaria" value="<?= $cor_fonte_secundaria ?>" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

						</div>

	<!--------------------------------nova linha --------------------------->
					<div class="row" style="height:40px; position:relative; padding:10px; background-color: <?=$cor_secundaria ?>; ">
							<div class="col-md-12" style="text-align: center; padding-top: 0px; color: white;">
							Cores das tabelas:
							</div>
					</div>		
						
						
						<div class="row" style="height:40px; position:relative; background-color: #F5F6FA; padding-right: 25px;">

							<!------------1------------>
								<div class="col-md-1" style="font-size: 10px; text-align: right; padding: 10px;">
								</div>


								<div class="col-md-1" style="font-size: 10px; text-align: right; padding: 10px;">
								Topo:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px;  width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color"name="cor_head_tabelas" value="<?= $cor_head_tabelas ?>" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
							<!-----------2-------------->
								<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
								Fonte topo:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_head_tabelas ?>" name="cor_fonte_head_tabelas" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
								<!-----------3-------------->
								<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
								Linha impar:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_linha_impar ?>" name="cor_linha_impar" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
								<!-----------4-------------->
								<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
								Linha par:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_linha_par ?>" name="cor_linha_par" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
						
							<!-----------2-------------->
								<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
								Fonte:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_tabela ?>" name="cor_fonte_tabela" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
								<!-----------2-------------->


						</div>		<!-----------4-------------->



						<div class="row" style="height:40px; position:relative; padding:10px; background-color: <?= $cor_secundaria ?>; ">
							<div class="col-md-12" style="text-align: center; padding-top: 0px; color: white;">
							Cores dos formulários:
							</div>
						</div>	

						<div class="row" style="height:40px; position:relative; background-color:white; padding-right: 25px;">
			
								<div class="col-md-1" style="font-size: 10px; text-align: right; padding: 10px;">
								Topo:
								</div>
								<div class="col-md-1" style="padding: 8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_head_form ?>" name="cor_head_form" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
								Fonte topo:
								</div>
								<div class="col-md-1" style="padding:8px;">

								
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_head_form ?>"name="cor_fonte_head_form" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
								Fundo:
								</div>
								<div class="col-md-1" style="padding:8px;">
										<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_fundo_form ?>"name="cor_fundo_form" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
								Fonte:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_fundo_form ?>"name="cor_fonte_fundo_form" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
								Rodapé:
								</div>
								<div class="col-md-1" style="padding:8px;">
										<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_rodape_form ?>"name="cor_rodape_form" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
								Fonte rodapé:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_rodape_form ?>"name="cor_fonte_rodape_form" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
							</div>
					
					<div class="row" style="height:40px; position:relative; padding:10px; background-color: <?=$cor_secundaria ?>; ">
							<div class="col-md-12" style="text-align: center; padding-top: 0px; color: white;">
							Barra dos Icones:
							</div>
					</div>		
						
						
						<div class="row" style="height:40px; position:relative; background-color: #F5F6FA; padding-right: 25px;">

								<div class="col-md-2" style="font-size: 10px; text-align: right; padding: 10px;">
								Icones:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px;  width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color"name="cor_icons" value="<?= $cor_icons ?>" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-2" style="font-size: 10px; padding: 10px; text-align: right;">
								Fonte dos icones:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_icons ?>"name="cor_fonte_icons" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-2" style="font-size: 10px; text-align: right; padding: 10px;">
								Fundo:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_barra2 ?>"name="cor_barra2" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
								
								<div class="col-md-2" style="font-size: 10px; padding: 10px; text-align: right;">
								Fonte:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_barra2 ?>" name="cor_fonte_barra2" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

						</div>
						
						<div class="row" style="height:50px; background-color:white;">
								<div class="col-md-2" style="font-size: 10px; text-align: right; padding: 15px;">
								Tamanho dos icones:
								</div>
								<div class="col-md-2" style="padding-top:10px;">
									<div class="input-group" style= "width: 100px;">
										<input type="number" value="<?= $size_icons ?>"name="size_icons" class="form-control" style="font-size: 12px; padding: 1px 2px 1px 10px;" aria-describedby="basic-addon1">
										<span class="input-group-text" style="font-size: 10px; padding: 5px;" id="basic-addon1">pixel</span>
									</div>
									
								</div>

								<div class="col-md-2" style="font-size: 10px; text-align: right; padding: 15px;">
								Espaço entre os icones:
								</div>
								<div class="col-md-2" style="padding-top:10px;">
									<div class="input-group" style= "width: 100px;">
										<input type="number" value="<?= $espaco_entre_icons ?>"name="espaco_entre_icons" class="form-control" style="font-size: 12px; padding: 1px 2px 1px 10px;" aria-describedby="basic-addon1">
										<span class="input-group-text" style="font-size: 10px; padding: 5px;" id="basic-addon1">pixel</span>
									</div>
									
								</div>

								<div class="col-md-2" style="font-size: 10px; text-align: right; padding: 15px;">
								Alinhamento dos icones:
								</div>
								<div class="col-md-2" style="padding-top:10px;">
									<div class="input-group" style="width: 100px;">
										<select name="align_icons" class="form-select" style="font-size: 12px; padding: 1px 2px 1px 10px;" aria-describedby="basic-addon1">
											<option value="0" <?= $align_icons == '0' ? 'selected' : ''; ?>>Centro</option>
											<option value="1" <?= $align_icons == '1' ? 'selected' : ''; ?>>Esquerda</option>
											<option value="2" <?= $align_icons == '2' ? 'selected' : ''; ?>>Direita</option>
										</select>
									</div>
								</div>

									

						</div>


						<div class="row" style="height:40px; position:relative; padding:10px; background-color: <?=$cor_secundaria ?>; ">
							<div class="col-md-12" style="text-align: center; padding-top: 0px; color: white;">
							Barra topo:
							</div>
						</div>		

						<div class="row mb-2" style="height:40px; position:relative; background-color: white; padding-right: 25px;">


							<div class="col-md-1" style="font-size: 10px; text-align: right; padding: 10px;">
							Fundo:
							</div>
							<div class="col-md-1" style="padding:8px;">
								<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
									<input type="color" value="<?= $cor_barra_topo ?>"name="cor_barra_topo" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
								</div>
							</div>

							<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
							Fonte 1:
							</div>
							<div class="col-md-1" style="padding:8px;">
								<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
									<input type="color" value="<?= $cor_fonte_barra_topo ?>" name="cor_fonte_barra_topo" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
								</div>
							</div>
							
							<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
							Fonte 2:
							</div>
							<div class="col-md-1" style="padding:8px;">
								<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
									<input type="color" value="<?= $cor_fonte_barra_topo2 ?>" name="cor_fonte_barra_topo2" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
								</div>
							</div>

							<div class="col-md-1" style="font-size: 10px; padding: 10px; text-align: right;">
								Linha:
							</div>
							<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_linha_barra ?>"name="cor_linha_barra" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
							</div>

							<div class="col-md-2" style="font-size: 10px; text-align: right; padding: 15px;">
								Tamanho dos icones:
								</div>
								<div class="col-md-2" style="padding-top:10px; padding-bottom: 10px;">
									<div class="input-group" style= "width: 100px;">
										<input type="number" value="<?= $size_icons_barra_topo ?>"name="size_icons_barra_topo" class="form-control" style="size_icons_barra_topo: 12px; padding: 1px 2px 1px 10px;" aria-describedby="basic-addon1">
										<span class="input-group-text" style="font-size: 10px; padding: 5px;" id="basic-addon1">pixel</span>
									</div>
									
								</div>
							


						</div>
						
						<div class="row" style="height:40px; position:relative; padding:10px; background-color: <?=$cor_secundaria ?>; ">
							<div class="col-md-12" style="text-align: center; padding-top: 0px; color: white;">
							Barra opções:
							</div>
						</div>		

						<div class="row  mb-2" style="height:40px; position:relative; background-color: #F5F6FA; padding-right: 25px;">
								<div class="col-md-2" style="font-size: 10px; text-align: right; padding: 10px;">
								Fundo:
								</div>
								<div class="col-md-1" style="padding: 8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_barra3 ?>" name="cor_barra3" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-2" style="font-size: 10px; padding: 10px; text-align: right;">
								Fonte:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_barra3 ?>"name="cor_fonte_barra3" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
								
								<div class="col-md-2" style="font-size: 10px; text-align: right; padding: 15px;">
								Tamanho dos icones:
								</div>
								<div class="col-md-2" style="padding-top:10px;">
									<div class="input-group" style= "width: 100px;">
										<input type="number" value="<?= $size_icons_barra3 ?>"name="size_icons_barra3" class="form-control" style="font-size: 12px; padding: 1px 2px 1px 10px;" aria-describedby="basic-addon1">
										<span class="input-group-text" style="font-size: 10px; padding: 5px;" id="basic-addon1">pixel</span>
									</div>
									
								</div>
								



						</div>	



					<div class="row" style="height:40px; position:relative; padding:10px; background-color: <?=$cor_secundaria ?>; ">
							<div class="col-md-12" style="text-align: center; padding-top: 0px; color: white;">
							Botões:
							</div>
					</div>	
						<div class="row" style="height:40px; position:relative; background-color: white; padding-right: 25px;">
								<div class="col-md-2" style="font-size: 10px; text-align: right; padding: 10px;">
								Adicionar:
								</div>
								<div class="col-md-1" style="padding: 8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_btn_add ?>" name="cor_btn_add" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-2" style="font-size: 10px; text-align: right; padding: 10px;">
								Fonte adicionar:
								</div>
								<div class="col-md-1" style="padding: 8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_btn_add ?>" name="cor_fonte_btn_add" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-2" style="font-size: 10px; padding: 10px; text-align: right;">
								Enviar/Salvar:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_btn_enviar ?>"name="cor_btn_enviar" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-2" style="font-size: 10px; padding: 10px; text-align: right;">
								Fonte Enviar/Salvar:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_btn_enviar ?>"name="cor_fonte_btn_enviar" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
							</div>	
						<div class="row" style="height:40px; position:relative; background-color: white; padding-right: 25px;">



								<div class="col-md-2" style="font-size: 10px; text-align: right; padding: 10px;">
								Fechar :
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px;  width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color"name="cor_btn_fechar" value="<?= $cor_btn_fechar ?>" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
								<div class="col-md-2" style="font-size: 10px; text-align: right; padding: 10px;">
								Fonte Fechar :
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px;  width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color"name="cor_fonte_btn_fechar" value="<?= $cor_fonte_btn_fechar ?>" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-2" style="font-size: 10px; padding: 10px; text-align: right;">
								Padrão:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_btn_padrao?>"name="cor_btn_padrao" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>

								<div class="col-md-2" style="font-size: 10px; padding: 10px; text-align: right;">
								Fonte botão padrão:
								</div>
								<div class="col-md-1" style="padding:8px;">
									<div style="overflow: hidden; height: 25px; width: 70px; position: relative; border:2px solid #f5f3f3; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_btn_padrao?>"name="cor_fonte_btn_padrao" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
									</div>
								</div>
						</div>	
					
						
						
				

					<div class="row" style="height:65px; background-color: <?=$cor_secundaria ?>; border-top: 1px solid #AAABB0;">
						<div class="d-flex justify-content-center align-items-center">
							<button type="submit" class="btn btn-primary" >Aplicar</button>
						</div>
					</div>	

			</form>			
		</div>




	
	</div>		

			
		

	

</div>


<!-- GRAVAR OU ALTERAR AGENDAMENTO -->
<script>
    
	$(document).ready(function(){
    // Captura o evento de submit do formulário
    $('#Form-envConfig').on('submit', function(e){
        // Previne o comportamento padrão do formulário (envio/reload da página)
        e.preventDefault();

        // Serializa os dados do formulário
        var dados = $(this).serialize();
		var pag = "<?=$pag?>";
       
		$.ajax({
            url: 'personalizacoes/aplicar_sistema.php', // Caminho para o script PHP que vai processar os dados
            type: 'POST', 
            dataType: 'json', // Tipo de retorno esperado do servidor
            data: dados, // Dados do formulário serializados
			
            success: function(response) {
                // Se a resposta do servidor for bem-sucedida
                if(response.success) {
                    
					window.location = "index.php?pagina="+pag;
                    //alert("Configuração salva com sucesso!");// Aqui pode adicionar mais lógica, como fechar um modal, recarregar partes da página, etc.
                } else {
                    alert("Erro ao salvar a configuração: " + response.message);
                }
            },
            
        });
    });
});
    
    
</script>



</body>


