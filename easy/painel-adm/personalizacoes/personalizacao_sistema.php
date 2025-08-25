<?php

$query = $pdo->query("SELECT * from personalizacao_sistema WHERE nome = 'Negocio' order by id desc");
$config_sis = $query->fetchAll(PDO::FETCH_ASSOC);

$cor_background = $config_sis[0]['cor_background'];
$cor_fonte_background = $config_sis[0]['cor_fonte_background'];
$cor_principal = $config_sis[0]['cor_principal'];
$cor_secundaria = $config_sis[0]['cor_secundaria'];
$cor_terciaria = $config_sis[0]['cor_terciaria'];
$cor_fonte_secundaria = $config_sis[0]['cor_fonte_secundaria'];
$cor_head_tabelas = $config_sis[0]['cor_head_tabelas'];
$cor_fonte_head_tabelas = $config_sis[0]['cor_fonte_head_tabelas'];
$cor_linha_impar = $config_sis[0]['cor_linha_impar'];
$cor_linha_par = $config_sis[0]['cor_linha_par'];
$cor_fonte_tabela = $config_sis[0]['cor_fonte_tabela'];
$cor_head_form = $config_sis[0]['cor_head_form'];
$cor_fonte_head_form = $config_sis[0]['cor_fonte_head_form'];
$cor_fundo_form = $config_sis[0]['cor_fundo_form'];
$cor_fonte_fundo_form = $config_sis[0]['cor_fonte_fundo_form'];
$cor_rodape_form = $config_sis[0]['cor_rodape_form'];
$cor_fonte_rodape_form = $config_sis[0]['cor_fonte_rodape_form'];
$cor_icons = $config_sis[0]['cor_icons'];
$cor_fonte_icons = $config_sis[0]['cor_fonte_icons'];
$cor_barra2 = $config_sis[0]['cor_barra2'];
$cor_fonte_barra2 = $config_sis[0]['cor_fonte_barra2'];
$size_icons = $config_sis[0]['size_icons'];
$espaco_entre_icons = $config_sis[0]['espaco_entre_icons'];


$align_icons = $config_sis[0]['align_icons'];
$cor_barra_topo = $config_sis[0]['cor_barra_topo'];
$cor_fonte_barra_topo = $config_sis[0]['cor_fonte_barra_topo'];
$cor_fonte_barra_topo2 = $config_sis[0]['cor_fonte_barra_topo2'];
$cor_linha_barra = $config_sis[0]['cor_linha_barra'];
$size_icons_barra_topo = $config_sis[0]['size_icons_barra_topo'];
$cor_barra3 = $config_sis[0]['cor_barra3'];
$cor_fonte_barra3 = $config_sis[0]['cor_fonte_barra3'];
$size_icons_barra3 = $config_sis[0]['size_icons_barra3'];
$cor_btn_add = $config_sis[0]['cor_btn_add'];
$cor_btn_enviar = $config_sis[0]['cor_btn_enviar'];
$cor_btn_fechar = $config_sis[0]['cor_btn_fechar'];
$cor_btn_padrao = $config_sis[0]['cor_btn_padrao'];
$cor_fonte_btn_add = $config_sis[0]['cor_fonte_btn_add'];
$cor_fonte_btn_enviar = $config_sis[0]['cor_fonte_btn_enviar'];
$cor_fonte_btn_fechar = $config_sis[0]['cor_fonte_btn_fechar'];
$cor_fonte_btn_padrao = $config_sis[0]['cor_fonte_btn_padrao'];







if($align_icons==0){
$align_center = "justify-content-center";
$align_right = "";
}elseif($align_icons==2){
    $align_center = "";
    $align_right = "ms-auto";
}else{
    $align_center = "";
    $align_right = "";
}




?>