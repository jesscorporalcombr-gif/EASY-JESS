<?php
$query = $pdo->query("SELECT * from personalizacao_agenda WHERE configuracao_nome = 'Negocio' order by id desc");
$config_ag = $query->fetchAll(PDO::FETCH_ASSOC);



$cor_fundo_agenda = $config_ag[0]['cor_fundo_agenda'];
$cor_fonte_horario = $config_ag[0]['cor_fonte_horario'];
$cor_fonte_celula = $config_ag[0]['cor_fonte_celula'];
$cor_celula_selecionada = $config_ag[0]['cor_celula_selecionada'];
$cor_linha_horizontal = $config_ag[0]['cor_linha_horizontal'];
$cor_linha_vertical = $config_ag[0]['cor_linha_vertical'];
$cor_fonte_profissional = $config_ag[0]['cor_fonte_profissional'];
$cor_fundo_profissional = $config_ag[0]['cor_fundo_profissional'];
$cor_fundo_caixa_pesquisa = $config_ag[0]['cor_fundo_caixa_pesquisa'];




$cor_sombra = $config_ag[0]['cor_sombra'];
$opacidade = $config_ag[0]['opacidade'];


$efeito = $config_ag[0]['efeito'];
$desloc_horizontal = $config_ag[0]['desloc_horizontal'];
$desloc_vertical = $config_ag[0]['desloc_vertical'];

$corSAgendado = $config_ag[0]['cor_agendado'];
$corSConfirmado = $config_ag[0]['cor_confirmado'];
$corSAguardando = $config_ag[0]['cor_aguardando'];

$corSFaltou = $config_ag[0]['cor_faltou'];
$corSCancelado = $config_ag[0]['cor_cancelado']; // Corrigido de 'cor_calcelado' para 'cor_cancelado'


$corSAtendimento = $config_ag[0]['cor_atendimento'];
$corSNaoRealizado = $config_ag[0]['cor_nao_realizado'];
$corSConcluido = $config_ag[0]['cor_concluido'];

$corSFinalizado = $config_ag[0]['cor_finalizado'];






$cor_bloqueio = $config_ag[0]['cor_bloqueio'];
$cor_n_atende = $config_ag[0]['cor_n_atende'];

$cor_borda_bloqueio = $config_ag[0]['cor_borda_bloqueio'];
$size_borda_bloqueio= $config_ag[0]['size_borda_bloqueio']; // Ajustado para coletar o tamanho da borda
$opacicidade_bloqueio = $config_ag[0]['opacicidade_bloqueio'];
$cor_fonte_bloqueio = $config_ag[0]['cor_fonte_bloqueio'];




$intervalo_tempo_agenda= $config_ag[0]['intervalo_tempo_agenda'];
$altura_linha_agenda= $config_ag[0]['altura_linha_agenda'];

//$abertura_agenda=$config_ag[0]['abertura_agenda'];
//$fechamento_agenda=$config_ag[0]['fechamento_agenda'];



function hexToRgb($hex) {
    $hex = str_replace("#", "", $hex);
  
    if (strlen($hex) == 3) {
      $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
      $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
      $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
    } else {
      $r = hexdec(substr($hex, 0, 2));
      $g = hexdec(substr($hex, 2, 2));
      $b = hexdec(substr($hex, 4, 2));
    }
  
    return implode(', ', [$r, $g, $b]); // Retorna o resultado no formato 'r, g, b'
  }
  


?>