<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

header('Content-Type: application/json; charset=utf-8');

function jexit($ok, $msg = '', $data = null) {
  echo json_encode(['success'=>$ok, 'msg'=>$msg, 'data'=>$data], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

// "1.234,56" -> 1234.56
function br2dec($s){
  $s = trim((string)$s);
  if ($s==='') return null;
  $s = str_replace('.', '', $s);
  $s = str_replace(',', '.', $s);
  return is_numeric($s) ? (float)$s : null;
}

try {
  // -------- inputs do form --------
  $id            = isset($_POST['id']) ? (int)$_POST['id'] : 0;

  $servico       = trim($_POST['frm-nome'] ?? '');
  $id_categoria     = isset($_POST['frm-categoria']) ? (int)$_POST['frm-categoria'] : null;
  $categoria     = isset($_POST['frm-categoria_txt']) ? $_POST['frm-categoria_txt'] : null;
  $tempo         = isset($_POST['frm-tempo']) ? (int)$_POST['frm-tempo'] : null;

  $valor_venda   = br2dec($_POST['frm-preco'] ?? '');
  $valor_custo   = br2dec($_POST['frm-custo'] ?? ''); // CUSTO

  // ATENÇÃO: espere que o HTML envie name="frm-comissao".
  // Se não vier, NÃO vamos atualizar a comissão (evita pegar custo por engano).
  $comissao_in    = $_POST['frm-comissao'] ?? null;
  $comissao       = ($comissao_in !== null && $comissao_in !== '') ? br2dec($comissao_in) : null;

  $intervalo      = isset($_POST['frm-intervalo']) ? (int)$_POST['frm-intervalo'] : null; // se sua coluna for folga_necess, troque no SQL
  $retorno        = isset($_POST['frm-retorno']) ? (int)$_POST['frm-retorno'] : null;

  $ag_online      = (isset($_POST['frm-agendamento_online']) && $_POST['frm-agendamento_online']=='1') ? 1 : 0;
  $fidelidade     = (isset($_POST['frm-fidelidade']) && $_POST['frm-fidelidade']=='1') ? 1 : 0;
  $nivel_paralelo = isset($_POST['frm-paralelo']) ? (int)$_POST['frm-paralelo'] : 0;
  $excluido = isset($_POST['frm-excluido']) ? (int)$_POST['frm-excluido'] : 0;

  // Seus 2 textareas tinham name duplicado no HTML original;
  // aqui aceitamos nomes novos (recomendado) com fallback:
  $descricao           = trim($_POST['frm-desc_interna'] ?? ($_POST['frm-desc_interna'] ?? ''));
  $descricao_cliente   = trim($_POST['frm-desc_cliente'] ?? ($_POST['frm-desc_cliente'] ?? ''));

  $site          = trim($_POST['frm-site'] ?? '');
  $ref1          = trim($_POST['frm-ref1'] ?? '');
  $ref2          = trim($_POST['frm-ref2'] ?? '');

  if ($servico === '') jexit(false, 'Informe o título do serviço.');

  // -------- upload da foto (opcional) --------
  $pastaSess = $_SESSION['x_url'] ?? '';
  $destDir = $pastaSess ? "../../{$pastaSess}/img/servicos/" : "../../img/servicos/";
  if (!is_dir($destDir)) @mkdir($destDir, 0775, true);

  $fotoNovoNome = null;
  if (!empty($_FILES['input-foto_cadServico']['name'])) {
    if (!empty($_FILES['input-foto_cadServico']['error']) && $_FILES['input-foto_cadServico']['error'] !== UPLOAD_ERR_OK) {
      jexit(false, 'Erro ao enviar a foto (código '.$_FILES['input-foto_cadServico']['error'].')');
    }
    $fname = $_FILES['input-foto_cadServico']['name'];
    $tmp   = $_FILES['input-foto_cadServico']['tmp_name'];
    $ext   = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
    $permitidas = ['jpg','jpeg','png','webp'];
    if (!in_array($ext, $permitidas, true)) jexit(false, 'Extensão de foto não permitida.');
    $fotoNovoNome = uniqid('svc_', true).'.'.$ext;
    if (!move_uploaded_file($tmp, $destDir.$fotoNovoNome)) jexit(false, 'Falha ao salvar a foto.');
  }

  // Foto antiga (se update)
  $fotoAntiga = null;
  if ($id > 0) {
    $st = $pdo->prepare("SELECT foto FROM servicos WHERE id=:id");
    $st->execute([':id'=>$id]);
    if ($row = $st->fetch(PDO::FETCH_ASSOC)) {
      $fotoAntiga = $row['foto'] ?? null;
    }
  }

  // -------- UPDATE ou INSERT --------
  if ($id > 0) {
    // UPDATE (sem loops, tudo explícito)
    $sql = "UPDATE servicos SET
              servico = :servico,
              id_categoria = :id_categoria,
              categoria = :categoria,
              tempo = :tempo,
              valor_venda = :valor_venda,
              valor_custo = :valor_custo,
              agendamento_online = :agendamento_online,
              fidelidade = :fidelidade,
              nivel_paralelo = :nivel_paralelo,
              folga_necess = :intervalo,
              retorno = :retorno,
              descricao = :descricao,
              descricao_cliente = :descricao_cliente,
              excluido = :excluido,
              site = :site,
              ref1 = :ref1,
              ref2 = :ref2";

    // opcionalmente atualiza comissão só se veio do POST
    if ($comissao !== null) {
      $sql .= ", comissao = :comissao";
    }
    // atualiza foto só se subiu uma nova
    if ($fotoNovoNome) {
      $sql .= ", foto = :foto";
    }

    $sql .= " WHERE id = :id";

    $up = $pdo->prepare($sql);
    $up->bindValue(':servico',            $servico);
    $up->bindValue(':id_categoria',       $id_categoria);
    $up->bindValue(':categoria',          $categoria);
    $up->bindValue(':tempo',              $tempo);
    $up->bindValue(':valor_venda',        $valor_venda);
    $up->bindValue(':valor_custo',        $valor_custo);
    $up->bindValue(':agendamento_online', $ag_online);
    $up->bindValue(':fidelidade',         $fidelidade);
    $up->bindValue(':nivel_paralelo',     $nivel_paralelo);
    $up->bindValue(':intervalo',       $intervalo); // troque para ':folga_necess' se for o caso
    $up->bindValue(':retorno',            $retorno);
    $up->bindValue(':descricao',          $descricao);
    $up->bindValue(':descricao_cliente',  $descricao_cliente);
    $up->bindValue(':excluido',  $excluido);
    $up->bindValue(':site',               $site);
    $up->bindValue(':ref1',               $ref1);
    $up->bindValue(':ref2',               $ref2);
    if ($comissao !== null) $up->bindValue(':comissao', $comissao);
    if ($fotoNovoNome)      $up->bindValue(':foto',     $fotoNovoNome);
    $up->bindValue(':id',                $id, PDO::PARAM_INT);
    $up->execute();

    // apaga a foto antiga se substituiu
    if ($fotoNovoNome && $fotoAntiga && $fotoAntiga !== $fotoNovoNome) {
      @unlink($destDir.$fotoAntiga);
    }

    $idFinal = $id;

  } else {
    // INSERT (sem loops)
    $sql = "INSERT INTO servicos
              (servico, id_categoria, categoria, tempo, valor_venda, valor_custo, agendamento_online,
               fidelidade, nivel_paralelo, folga_necess, retorno,
               descricao, descricao_cliente, excluido, site, ref1, ref2, data_criacao"
             . ($comissao !== null ? ", comissao" : "")
             . ($fotoNovoNome ? ", foto" : "")
             . ")
            VALUES
              (:servico, :id_categoria, :categoria, :tempo, :valor_venda, :valor_custo, :agendamento_online,
               :fidelidade, :nivel_paralelo, :intervalo, :retorno,
               :descricao, :descricao_cliente, :excluido, :site, :ref1, :ref2, NOW()"
             . ($comissao !== null ? ", :comissao" : "")
             . ($fotoNovoNome ? ", :foto" : "")
             . ")";

    $ins = $pdo->prepare($sql);
    $ins->bindValue(':servico',            $servico);
    $ins->bindValue(':id_categoria',          $id_categoria);
    $ins->bindValue(':categoria',            $categoria);
    $ins->bindValue(':tempo',              $tempo);
    $ins->bindValue(':valor_venda',        $valor_venda);
    $ins->bindValue(':valor_custo',        $valor_custo);
    $ins->bindValue(':agendamento_online', $ag_online);
    $ins->bindValue(':fidelidade',         $fidelidade);
    $ins->bindValue(':nivel_paralelo',     $nivel_paralelo);
    $ins->bindValue(':intervalo',          $intervalo); // troque para ':folga_necess' se for o caso
    $ins->bindValue(':retorno',            $retorno);
    $ins->bindValue(':descricao',          $descricao);
    $ins->bindValue(':descricao_cliente',  $descricao_cliente);
    $ins->bindValue(':excluido',               $excluido);
    $ins->bindValue(':site',               $site);
    $ins->bindValue(':ref1',               $ref1);
    $ins->bindValue(':ref2',               $ref2);
    if ($comissao !== null) $ins->bindValue(':comissao', $comissao);
    if ($fotoNovoNome)      $ins->bindValue(':foto',     $fotoNovoNome);
    $ins->execute();
    $idFinal = (int)$pdo->lastInsertId();
  }

  // -------- retorno p/ atualizar header do modal --------
  $pasta = $_SESSION['x_url'] ?? '';
  $baseImg = ($pasta ? "../{$pasta}" : "..") . "/img/servicos/";
  $fotoHead = null;
  if ($fotoNovoNome) {
    $fotoHead = $baseImg.$fotoNovoNome;
  } else if ($fotoAntiga) {
    $fotoHead = $baseImg.$fotoAntiga;
  }

  jexit(true, '', [
    'id'        => $idFinal,
    'foto_head' => $fotoHead,
    'titulo'    => $servico
  ]);

} catch (Throwable $e) {
  jexit(false, 'Erro ao salvar: '.$e->getMessage());
}
