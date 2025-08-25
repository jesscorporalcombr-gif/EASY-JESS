<?php 
$pag = 'personalizar_agenda';
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
<div class="conteiner-flex" style="padding-left: 60px;">
	<div class="row">
	 

		<div class="col-md-6" style=" overflow: hidden; position:static; border: 1px solid white; border-radius: 15px; margin-top: 20px; box-shadow: 5px 15px 20px rgba(59, 57, 128, 0.2);" >
			<form id="Form-envConfig">	
					<div class="row" style="height:55px; background-color: <?php echo $cor_principal?>; color: white; padding: 10px; font-size: 20px;">
					<h5 style="color: white; padding: 10px 0 10px 20px; font-size: 18px;">Personalização da Agenda:</h5>
					</div>

					<div class="col" style="padding:0px 0px 15px 0px; padding: 10px;">
						<div class="row" style="height:40px; position:relative; padding:10px; background-color: #AAABB0; ">
							<div class="col-md-12" style="text-align: center; padding-top: 0px; color: white;">
							Agenda:
							</div>
						</div>
						
						<div class="row" style="height:50px; background-color: white;">
								<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 15px;">
								Cor da Agenda :
								</div>
								<div class="col-md-1" style="padding-top:12px;">
									<div style="overflow: hidden; height: 25px; border-radius: 10px;">
										<input type="color" value="<?= $cor_fundo_agenda ?>" name="cor_fundo_agenda" style="cursor:pointer; width: 180px; height:80px; margin: -2px;" > 
									</div>
								</div>
								
								<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 15px;">
								Cor fonte horário :
								</div>
								<div class="col-md-1" style="padding-top:12px;">
									<div style="overflow: hidden; height: 25px; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_horario ?>" name="cor_fonte_horario" style="cursor:pointer; width: 180px; height:80px; margin: -2px;" > 
									</div>
								</div>

								<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 15px;">
								Cor fonte celula :
								</div>
								<div class="col-md-1" style="padding-top:12px;">
									<div style="overflow: hidden; height: 25px; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_celula ?>" name="cor_fonte_celula" style="cursor:pointer; width: 180px; height:80px; margin: -2px;" > 
									</div>
								</div>

								<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 15px;">
								Cor celula selecionada :
								</div>
								<div class="col-md-1" style="padding-top:12px;">
									<div style="overflow: hidden; height: 25px; border-radius: 10px;">
										<input type="color" value="<?= $cor_celula_selecionada ?>" name="cor_celula_selecionada" style="cursor:pointer; width: 180px; height:80px; margin: -2px;" > 
									</div>
								</div>
						
						</div>	
						<div class="row" style="height:50px; background-color: #F5F6FA;">
								<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 15px;">
								Cor linha horizontal:
								</div>
								<div class="col-md-1" style="padding-top:12px;">
									<div style="overflow: hidden; height: 25px; border-radius: 10px;">
										<input type="color" value="<?= $cor_linha_horizontal ?>" name="cor_linha_horizontal" style="cursor:pointer; width: 180px; height:80px; margin: -2px;" > 
									</div>
								</div>
								
								<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 15px;">
								Cor linha vertical :
								</div>
								<div class="col-md-1" style="padding-top:12px;">
									<div style="overflow: hidden; height: 25px; border-radius: 10px;">
										<input type="color" value="<?= $cor_linha_vertical ?>" name="cor_linha_vertical" style="cursor:pointer; width: 180px; height:80px; margin: -2px;" > 
									</div>
								</div>

	


								<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 15px;">
								Cor caixa fundo pesquisa :
								</div>
								<div class="col-md-1" style="padding-top:12px;">
									<div style="overflow: hidden; height: 25px; border-radius: 10px;">
										<input type="color" value="<?= $cor_fundo_caixa_pesquisa ?>" name="cor_fundo_caixa_pesquisa" style="cursor:pointer; width: 180px; height:80px; margin: -2px;" > 
									</div>
								</div>
						
						</div>						
						
						<div class="row" style="height:50px; background-color: #F5F6FA;">

								<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 15px;">
								Cor fonte profissional :
								</div>
								<div class="col-md-1" style="padding-top:12px;">
									<div style="overflow: hidden; height: 25px; border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_profissional ?>" name="cor_fonte_profissional" style="cursor:pointer; width: 180px; height:80px; margin: -2px;" > 
									</div>
								</div>

								<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 15px;">
								Cor Fundo Profissional:
								</div>
								<div class="col-md-1" style="padding-top:12px;">
									<div style="overflow: hidden; height: 25px; border-radius: 10px;">
										<input type="color" value="<?= $cor_fundo_profissional ?>" name="cor_fundo_profissional" style="cursor:pointer; width: 180px; height:80px; margin: -2px;" > 
									</div>
								</div>
						
						</div>	



					</div>							

					<div class="col" style="padding:0px 0px 15px 0px;">
						<div class="row" style="height:40px; position:relative; padding:10px; background-color: #AAABB0; ">
							<div class="col-md-12" style="text-align: center; padding-top: 0px; color: white;">
							Sombra da Agenda:
							</div>
						</div>
						<div class="row" style="height:50px; background-color: white;">
								<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 15px;">
								Cor da Sombra :
								</div>
								<div class="col-md-1" style="padding-top:12px;">
									<div style="overflow: hidden; height: 25px; border-radius: 10px;">
										<input type="color" value="<?= $cor_sombra ?>" name="cor_sombra" style="cursor:pointer; width: 180px; height:80px; margin: -2px;" > 
									</div>
								</div>
								
								<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 15px; text-align: right;">
								Opacidade:
								</div>
								<div class="col-md-1" style="padding-top:12px;">
									<div class="input-group mb-3" style="width:70px;">
										<input type="number"value="<?= $opacidade ?>" name="opacidade" class="form-control" min="0" max="1" step="0.1" style="font-size: 12px; padding:4px;" aria-describedby="basic-addon1">
										</div>
								</div>
								<div class="col-md-3" style="font-size: 12px; text-align: right; padding: 15px; text-align: right;">
								Efeito:
								</div>
								<div class="col-md-2" style="padding:12px;">
									<div class="input-group mb-3">
										<input type="number" value="<?= $efeito ?>"name="efeito" class="form-control" style="font-size: 12px; padding:5px;"aria-label="Username" aria-describedby="basic-addon1">
										<span class="input-group-text" style="font-size: 12px; padding: 5px;" id="basic-addon1">pixel</span>
									</div>
								</div>
						</div>								
						<div class="row" style="height:50px; background-color: #F5F6FA;">
								<div class="col-md-3" style="font-size: 12px; text-align: right; padding: 15px; text-align: right;">
								Deslocamento Horizontal:
								</div>
								<div class="col-md-2" style="padding:12px;">
									<div class="input-group mb-3">
										<input type="number" value="<?= $desloc_horizontal ?>"name="desloc_horizontal" class="form-control" style="font-size: 12px; padding:4px; text-align: center; width: 30px;"aria-label="Username" aria-describedby="basic-addon1">
										<span class="input-group-text" style="font-size: 12px; padding: 5px;" id="basic-addon1">pixel</span>
									</div>
								</div>
								<div class="col-md-3" style="font-size: 12px; text-align: right; padding: 15px; text-align: right;">
								Deslocamento Vertical:
								</div>
								<div class="col-md-2" style="padding:12px;">
									<div class="input-group mb-3">
										<input type="number" value="<?= $desloc_vertical ?>"name="desloc_vertical" class="form-control" style="font-size: 12px; padding:5px;"aria-label="Username" aria-describedby="basic-addon1">
										<span class="input-group-text" style="font-size: 12px; padding: 5px;" id="basic-addon1">pixel</span>
									</div>
								</div>
								


						</div>

						<div class="row" style="height:40px; position:relative; padding:10px; background-color: #AAABB0; ">
							<div class="col-md12" style="text-align: center; padding-top: 0px; color: white;">
							Cores dos Status da Agenda:
							</div>
						</div>
						
						<div class="row" style="height:40px; position:relative; background-color: #F5F6FA; padding-right: 25px;">
							<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 10px;">
							Agendado:
							</div>
							<div class="col-md-1" style="padding: 8px;">
								<div style="overflow: hidden; height: 25px;  position: relative;  border-radius: 10px;">
									<input type="color" value="<?= $corSAgendado ?>" name="cor_agendado" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
								</div>
							</div>

							<div class="col-md-2" style="font-size: 12px; padding: 10px; text-align: right;">
							Confirmado:
							</div>
							<div class="col-md-1" style="padding:8px;">
								<div style="overflow: hidden; height: 25px;  position: relative;  border-radius: 10px;">
									<input type="color" value="<?= $corSConfirmado ?>"name="cor_confirmado" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
								</div>
							</div>
							<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 10px;">
							Aguardando:
							</div>
							<div class="col-md-1" style="padding:8px;">
								<div style="overflow: hidden; height: 25px;  position: relative;  border-radius: 10px;">
									<input type="color"name="cor_aguardando" value="<?= $corSAguardando ?>" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
								</div>
							</div>
						</div>
						
						<div class="row"  style="height:40px; position:relative; background-color: #F5F6FA; padding-right: 25px;">

							<div class="col-md-2" style="font-size: 12px; padding: 10px; text-align: right;">
							Em Atendimento:
							</div>
							<div class="col-md-1" style="padding:8px;">
								<div style="overflow: hidden; height: 25px;  position: relative;  border-radius: 10px;">
									<input type="color" value="<?= $corSAtendimento ?>"name="cor_atendimento" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
								</div>
							</div>
							<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 10px;">
							Atendimento Concluido:
							</div>
							<div class="col-md-1" style="padding:8px;">
								<div style="overflow: hidden; height: 25px;  position: relative;  border-radius: 10px;">
									<input type="color" value="<?= $corSConcluido ?>"name="cor_concluido" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
								</div>
							</div>

							<div class="col-md-2" style="font-size: 12px; padding: 10px; text-align: right;">
							Finalizado:
							</div>
							<div class="col-md-1" style="padding:8px;">
								<div style="overflow: hidden; height: 25px;  position: relative;  border-radius: 10px;">
									<input type="color" value="<?= $corSFinalizado ?>" name="cor_finalizado" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
								</div>
							</div>

						</div>	

						<div class="row"  style="height:40px; position:relative; background-color: #F5F6FA; padding-right: 25px;">
							<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 10px;">
							Não Realizado:
							</div>
							<div class="col-md-1" style="padding:8px;">
								<div style="overflow: hidden; height: 25px;  position: relative;  border-radius: 10px;">
									<input type="color" value="<?= $corSNaoRealizado ?>"name="cor_nao_realizado" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
								</div>
							</div>

							<div class="col-md-2" style="font-size: 12px; text-align: right; padding: 10px;">
							Faltou:
							</div>
							<div class="col-md-1" style="padding:8px;">
								<div style="overflow: hidden; height: 25px;  position: relative;  border-radius: 10px;">
									<input type="color" value="<?= $corSFaltou ?>"name="cor_faltou" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
								</div>
							</div>

							<div class="col-md-2" style="font-size: 12px; padding: 10px; text-align: right;">
							Cancelado:
							</div>
							<div class="col-md-1" style="padding:8px;">
								<div style="overflow: hidden; height: 25px;  position: relative;  border-radius: 10px;">
									<input type="color" value="<?= $corSCancelado ?>" name="cor_cancelado" style="cursor:pointer; width: 80px; height:80px; margin: -2px;" >
								</div>
							</div>

						</div>


						<div class="row" style="height:40px; position:relative; padding:10px; background-color: #AAABB0; ">
							<div class="col-md12" style="text-align: center; padding-top: 0px; color: white;">
							Bloqueios:
							</div>
						</div>

						<div class="row" style="height:50px; background-color: #F5F6FA;">
								<div class="col-md-4" style="font-size: 12px; text-align: right; padding: 15px;">
								Cor quando não atende:
								</div>
								<div class="col-md-1" style="padding-top:12px;">
									<div style="overflow: hidden; height: 25px; width: 65px; position: relative;  border-radius: 10px;">
										<input type="color" value="<?= $cor_n_atende ?>"name="cor_n_atende" style="cursor:pointer; width: 180px; height:80px; margin: -2px;" > 
									</div>
								</div>
								
								<div class="col-md-3" style="font-size: 12px; text-align: right; padding: 15px; text-align: right;">
								Cor do Bloqueio:
								</div>
								<div class="col-md-1" style="padding:12px;">
									<div style="overflow: hidden; height: 25px; width: 65px; position: relative;  border-radius: 10px;">
										<input type="color" value="<?= $cor_bloqueio ?>"name="cor_bloqueio" style="cursor:pointer; width: 180px; height: 80px; margin: -2px;" > 
									</div>
								</div>

						</div>

						<div class="row" style="height:50px; background-color:white;">
								<div class="col-md-4" style="font-size: 12px; text-align: right; padding: 15px;">
								Largura da Borda de Sinalização:
								</div>
								<div class="col-md-2" style="padding-top:10px;">
									<div class="input-group mb-3">
										<input type="number" value="<?= $size_borda_bloqueio ?>"name="size_borda_bloqueio" class="form-control" style="font-size: 12px; width: 10px; padding-right: 3px;" aria-describedby="basic-addon1">
										<span class="input-group-text" style="font-size: 12px; padding: 5px;" id="basic-addon1">pixel</span>
									</div>
									
								</div>
								
								<div class="col-md-3" style="font-size: 12px; text-align: right; padding: 15px; text-align: right;">
								Cor da Borda de Sinalização:
								</div>
								<div class="col-md-1" style="padding:12px;">
									<div style="overflow: hidden; height: 25px; width: 65px; position: relative;  border-radius: 10px;">
										<input type="color" value="<?= $cor_borda_bloqueio ?>" name="cor_borda_bloqueio" style="cursor:pointer; width: 180px; height: 80px; margin: -2px;" > 
									</div>
								</div>

						</div>
						<div class="row" style="height:50px; background-color:white;">
								<div class="col-md-4" style="font-size: 12px; text-align: right; padding: 15px;">
								Transparência:
								</div>
								<div class="col-md-2" style="padding-top:10px;">
									<div class="input-group mb-3">
										<input type="number" value="<?= $opacicidade_bloqueio ?>"name="opacicidade_bloqueio" min="0" max="1" step="0.05" class="form-control" style="font-size: 12px; width: 10px; padding-right: 3px;" aria-describedby="basic-addon1">
									</div>
									
								</div>
								
								<div class="col-md-3" style="font-size: 12px; text-align: right; padding: 15px; text-align: right;">
								Cor da Fonte do Bloqueio
								</div>
								<div class="col-md-1" style="padding:12px;">
									<div style="overflow: hidden; height: 25px; width: 65px; position: relative;  border-radius: 10px;">
										<input type="color" value="<?= $cor_fonte_bloqueio ?>" name="cor_fonte_bloqueio" style="cursor:pointer; width: 180px; height: 80px; margin: -2px;" > 
									</div>
								</div>

						</div>
						
						
				</div>

					<div class="row" style="height:65px; background-color: #F5F6FA; border-top: 1px solid #AAABB0;">
						<div class="d-flex justify-content-center align-items-center">
							<button type="submit" class="btn btn-primary" >Aplicar</button>
						</div>
					</div>	

			</form>			
		</div>


		<div class="col-md-1" style="width: 35px;"></div>

<!---------------------------------------------------SEGUNDA TELA---------------------------------------------------->

		<div class="col-md-5" style=" overflow: hidden; border: 1px solid white; border-radius: 15px; margin-top: 20px; box-shadow: 5px 15px 20px rgba(59, 57, 128, 0.2); height: 410px;" >
			<form id="Form-envConfig2">	
					<div class="row" style="height:55px; background-color: <?php echo $cor_principal?>; color: white; padding: 10px; font-size: 20px;">
					<h5 style="color: white; padding: 10px 0 10px 20px; font-size: 18px;">Configurações da Agenda:</h5>
					</div>

					<div class="col" style="padding:0px 0px 15px 0px; padding: 10px;">


					<div class="row" style="height:40px; position:relative; padding:10px; background-color: #AAABB0; ">
							<div class="col-md12" style="text-align: center; padding-top: 0px; color: white;">
							HORÁRIOS DE FUNCIONAMENTO:
							</div>
					</div>
						<div class="row" style="height:50px; background-color: #F5F6FA;">
								<div class="col-md-3" style="font-size: 12px; text-align: right; padding: 15px;">
								Hora de Abertura:
								</div>
								<div class="col-md-2" style="padding-top:12px;">
									<div class="input-group" style="width: 70px;">
										<input type="time" value="<?= $abertura_agenda ?>"name="abertura_agenda" class="form-control" style="font-size: 12px; padding:5px; ">
										
									</div>
								</div>
								
								<div class="col-md-4" style="font-size: 12px; padding-top: 15px; text-align: right;">
								Hora de Fechamento:
								</div>
								<div class="col-md-2" style="marging-left:-10px; width:70px;padding-top:12px;">
									<div class="input-group mb-3" style="width:70px;">
										<input type="time" value="<?= $fechamento_agenda ?>" name="fechamento_agenda" class="form-control" style="font-size: 12px; padding:5px;">
										
									</div>
								</div>

						</div>	
					<div class="row" style="height:40px; position:relative; padding:10px; background-color: #AAABB0; ">
							<div class="col-md12" style="text-align: center; padding-top: 0px; color: white;">
							DIAS DA SEMANA:
							</div>
					</div>
						<div class="row" style="height:50px; background-color: #F5F6FA;">
						<table>
							<thead>
								<tr>
									<th>Seg</th>
									<th>Ter</th>
									<th>Qua</th>
									<th>Qui</th>
									<th>Sex</th>
									<th>Sáb</th>
									<th>Dom</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><input type="checkbox"></td>
									<td><input type="checkbox"></td>
									<td><input type="checkbox"></td>
									<td><input type="checkbox"></td>
									<td><input type="checkbox"></td>
									<td><input type="checkbox"></td>
									<td><input type="checkbox"></td>
								</tr>
							</tbody>
						</table>

						</div>			

						
						<div class="row" style="height:40px; position:relative; padding:10px; background-color: #AAABB0; ">
							<div class="col-md12" style="text-align: center; padding-top: 0px; color: white;">
							Configurações de visualização:
							</div>
						</div>
						<div class="row" style="height:50px; background-color: #F5F6FA;">
								<div class="col-md-4" style="font-size: 12px; text-align: right; padding: 15px;">
								Intervalo de Tempo da Agenda:
								</div>
								<div class="col-md-2" style="padding-top:12px;">
									<div class="input-group mb-3">
										<input type="number" value="<?= $intervalo_tempo_agenda ?>"name="intervalo_tempo_agenda" class="form-control" style="font-size: 12px; padding:5px;"aria-label="Username" aria-describedby="basic-addon1">
										<span class="input-group-text" style="font-size: 12px; padding: 5px;" id="basic-addon1">min</span>
									</div>
								</div>
								
								<div class="col-md-3" style="font-size: 12px; text-align: right; padding: 15px; text-align: right;">
								Altura das Linhas da Agenda:
								</div>
								<div class="col-md-2" style="padding:12px;">
									<div class="input-group mb-3">
										<input type="number" value="<?= $altura_linha_agenda ?>" name="altura_linha_agenda" class="form-control" style="font-size: 12px; padding:5px;"aria-label="Username" aria-describedby="basic-addon1">
										<span class="input-group-text"   style="font-size: 12px; padding: 5px;" id="basic-addon1">pixel</span>
									</div>
								</div>

						</div>		
						
				</div>

					<div class="row" style="height:65px; background-color: #F5F6FA; border-top: 1px solid #AAABB0;">
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

        $.ajax({
            url: 'personalizacoes/aplicar_agenda.php', // Caminho para o script PHP que vai processar os dados
            type: 'POST', 
            dataType: 'json', // Tipo de retorno esperado do servidor
            data: dados, // Dados do formulário serializados
            success: function(response) {
                // Se a resposta do servidor for bem-sucedida
                if(response.success) {
                    alert("Configuração salvo com sucesso!");
					window.location = "index.php?pagina="+pag;
                    // Aqui pode adicionar mais lógica, como fechar um modal, recarregar partes da página, etc.
                } else {
                    alert("Erro ao salvar a configuração: " + response.message);
                }
            },
            
        });
    });
});
    
    
</script>
<script>
    
	$(document).ready(function(){
    // Captura o evento de submit do formulário
    $('#Form-envConfig2').on('submit', function(e){
        // Previne o comportamento padrão do formulário (envio/reload da página)
        e.preventDefault();

        // Serializa os dados do formulário
        var dados = $(this).serialize();

        $.ajax({
            url: 'personalizacoes/aplicar_agenda2.php', // Caminho para o script PHP que vai processar os dados
            type: 'POST', 
            dataType: 'json', // Tipo de retorno esperado do servidor
            data: dados, // Dados do formulário serializados
            success: function(response) {
                // Se a resposta do servidor for bem-sucedida
                if(response.success) {
                    alert("Configuração salvo com sucesso!");
					window.location = "index.php?pagina="+pag;
                    // Aqui pode adicionar mais lógica, como fechar um modal, recarregar partes da página, etc.
                } else {
                    alert("Erro ao salvar a configuração: " + response.message);
                }
            },
            
        });
    });
});
    
    
</script>




</body>


