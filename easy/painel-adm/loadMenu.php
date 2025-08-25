<?php
include 'subMenu.php';

function gerarMenu($pagina, $grupos) {
    $grupo = null;

    // Identificar a qual grupo a página pertence
    foreach ($grupos as $nomeGrupo => $submenus) {
        foreach ($submenus as $submenu) {
            if ($submenu['pagina'] == $pagina) {
               
                $grupo = $nomeGrupo;
                break 2; // Sai dos dois loops
            }
        }
    }

    if ($grupo !== null) {
       echo '
       <nav class="navbar navbar-expand-lg navbar-light sub-menu mb-2">
            <ul">';
        foreach ($grupos[$grupo] as $submenu) {
            $paginaAtual = $submenu['pagina'];
            $descricao = $submenu['descricao'];
            $classeAtiva = ($paginaAtual == $pagina) ? 'ativo' : 'inativo';
            echo '<a href="index.php?pagina='. $paginaAtual .'" class="'. $classeAtiva . '">' . ucfirst($descricao) . '</a>
            &nbsp;&nbsp;&nbsp;&nbsp;';
        }
        echo '
            </ul>
        </nav>';
    } else {
        echo "<p>Submenu não encontrado para a página '{$pagina}'.</p>";
    }
}
?>