<?php



$pag = 'user';
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
$id_user = $_GET['id'];
$pasta = $_SESSION['x_url'];

$query = $pdo->query("SELECT nickname, nome, email, senha_sistema, foto_sistema, cpf, id from colaboradores_cadastros WHERE id = '$_SESSION[id_usuario]'");

$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nickname_user = $res[0]['nickname'];
$nome_user = $res[0]['nome'];
$email_user = $res[0]['email'];
$senha_user = $res[0]['senha_sistema'];
//$nivel_user = $res[0]['nivel'];
$foto_sistema_user = $res[0]['foto_sistema'];
$cpf_user = $res[0]['cpf'];
$id_user = $res[0]['id'];
                  

echo '
<div class="modal fade" tabindex="-1" style ="z-index: 95000;" id="modalPerfil" data-bs-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			
			<div class="modal-header">
				<h5 class="modal-title">Alterar Senha</h5>
				<a type="button"  class="btn-fecha-modal" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x-lg"></i></a>

			</div>
				
			
			<form method="POST" id="form-perfil">
				<div class="modal-body">

					<div class="row">
								<div class="col-md-3">
										<div class="form-group">
											<input type="file" hidden id="input-foto_sistema" name="input-foto_sistema" onChange="carregarImg();">
											<img style="cursor: pointer; border-radius:50%; margin-top: 10px; position:absolute; width: 100px; height:100px;"  id="img-foto_sistema" src="' . (!empty($foto_sistema_user) ? '../'.$pasta.'/img/users/' . $foto_sistema_user : '../img/sem-foto.jpg') . '">
										</div>
							
								</div>
						
					
								<div class="col-md-9">
								
										<div class="row">
											<div class="col-md-12">
												<div class="mb-4">
													<label for="exampleFormControlInput1" class="form-group">Nome</label>
													<input type="text" class="form-control" id="nome-perfil" name="nome-perfil" placeholder="Nome" required="" value="'. @$nome_user .'" readonly>
												</div> 
											</div>
										</div>

										<div class="row">
											<div class="col-md-12">
												<div class="mb-4">
													<label for="exampleFormControlInput1" class="form-group">Email</label>
													<input type="email" class="form-control" id="email-perfil" name="email-perfil" placeholder="Email" required="" value="' . @$email_user .' " readonly>
												</div> 
											</div>
										</div>
								</div>
					</div>

					<div style="align: center;" >
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label for="nickname" class="form-group">Nickname (apelido)</label>
										<input maxlength= "12" type="text" class="form-control" id="nickname" name="nickname" value="' . @$nickname_user .' " >
										</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-2"></div>
								<div class="col-md-8">
									<label for="senha-perfil-nova" class="form-group">Senha Atual:</label>
									<div class="mb-3 input-group">
										<input style="padding-right: 10px;" type="password" class="form-control" id="senha-perfil-atual" name="senha-perfil-atual" placeholder="digita a sua senha atual" required>
										
										<button class="btn btn-outline-secondary btn-span" type="button" onclick="toggleSenha(\'senha-perfil-atual\', \'toggleSenhaAtual\')" id="toggleSenhaAtual"><i class="bi bi-eye-slash" id="iconeSenha"></i></button>
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-md-2"></div>
								<div class="col-md-8">
									<label for="senha-perfil-nova" class="form-group">Nova Senha:</label>
										<div class="mb-3 input-group">
											
											<input style="padding-right: 10px;" type="password" class="form-control" id="senha-perfil-nova" name="senha-perfil-nova" placeholder="digite a nova senha">
											<button class="btn btn-outline-secondary btn-span" type="button" onclick="toggleSenha(\'senha-perfil-nova\', \'toggleSenhaNova\')"  id="toggleSenhaNova"><i class="bi bi-eye-slash" id="iconeSenha"></i></button>
										</div>
								</div>
							</div>

						</div>
					</div>

				<small><div align="center" class="mt-1" id="mensagem">


				<div class="modal-footer">
					<button type="button" id="btn-fechar-perfil" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
					<button name="btn-salvar-perfil" id="btn-salvar-perfil" type="submit" class="btn btn-primary">Salvar</button>

				</div>
			</form>

		</div>	
	</div>		
</div>';

?>



<script>

	document.getElementById("img-foto_sistema").addEventListener("click", function() {
	document.getElementById("input-foto_sistema").click();
	});

	function carregarImg() {

		var target = document.getElementById('img-foto_sistema');
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



<!--AJAX PARA INSERÇÃO E EDIÇÃO DOS DADOS COM IMAGEM -->
<script type="text/javascript">
	$("#form-perfil").submit(function () {
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
                   // $('#btn-fechar').click();
                    //window.location = "index.php?pagina="+pag;

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