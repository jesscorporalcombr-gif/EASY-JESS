<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
$id_cliente = $_GET['id_cliente'];
$id_foto    = $_GET['id_foto'] ?? null;

$foto = null;
if ($id_foto) {
    $stmt = $pdo->prepare("SELECT * FROM fotos WHERE id = ?");
    $stmt->execute([$id_foto]);
    $foto = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<div class="modal fade" tabindex="-1" style ="z-index: 95000;" id="modalPerfil" data-bs-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Alterar Senha</h5>
				<a type="button"  class="btn-fecha-modal" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x-lg"></i></a>
			</div>
			<form method="POST" id="form-perfil">
				<div class="modal-body">
					<div class="mb-3">
            <label for="fileFoto" class="form-label">Selecione a imagem</label>
            <input
              type="file"
              class="form-control"
              id="fileFoto"
              name="fileFoto"
              accept="image/*"
              <?= !$id_foto ? 'required' : '' ?>
            >
          </div>

          <!-- Descrição -->
          <div class="mb-3">
            <label for="descricaoFoto" class="form-label">Descrição</label>
            <input
              type="text"
              class="form-control"
              id="descricaoFoto"
              name="descricaoFoto"
              value="<?= $foto['descricao'] ?? '' ?>"
            >
          </div>

          <!-- Categoria (exemplo estático; adapte para buscar de BD se preferir) -->
          <div class="mb-3">
            <label for="categoriaFoto" class="form-label">Categoria</label>
            <select class="form-select" id="categoriaFoto" name="categoriaFoto">
              <option value="">— Selecione —</option>
              <?php
              $cats = ['Antes & Depois','Ambiente','Tratamento'];
              foreach ($cats as $cat) {
                  $sel = ($foto && $foto['categoria'] === $cat) ? 'selected' : '';
                  echo "<option value=\"{$cat}\" {$sel}>{$cat}</option>";
              }
              ?>
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" id="btn-fechar-perfil" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          <button name="btn-salvar-perfil" id="btn-salvar-perfil" type="submit" class="btn btn-primary">
            <?= $id_foto ? 'Atualizar' : 'Salvar' ?>
          </button>
        </div>
			</form>

		</div>	
	</div>		
</div>

