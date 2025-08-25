<?php
require_once 'config.php';
header("Content-Type: text/css; charset=UTF-8");
?>
<style>
@charset "UTF-8";
        .agenda-easy {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Faz com que todas as colunas tenham a mesma largura */
            /*margin-top: 50px;*/
            /*margin-left: 5px;*/
            box-shadow: 0 5px 35px rgba(76, 10, 138, 0.5);
            /*border-radius: 15px;*/
            border-spacing: 0px;
            background-color: white;
            /*overflow: hidden;*/
        }
      
        .agenda-contanier {
            float: right;
            table-layout: fixed;
        }

        .agenda-easy-thead {
            background-color: transparent;
        }
        .col-profissional{
            color: #ddd;
        }

        .agenda-easy-thead th.agenda-easy-th-horario,
        .agenda-easy-td:first-child {
            padding: 0 8px;
            width:auto;
        }
        


        .agenda-easy-thead, .agenda-easy-th{
                position:relative;
                top: 0;
                background-color: transparent; /* Fundo para não tornar o texto abaixo visível ao rolar */
                z-index: 5500; /* Garante que o cabeçalho fique acima do conteúdo da tabela */
            }
 
        .agenda-easy-th:first-child {
            /*background-color: #6495ED;*/
            color: rgb(235, 15, 15);
            text-align: center;
            padding: 9px;
            width:auto;
        }
       
        .agenda-easy-tr, .agenda-easy-td:not(:first-child) {
            text-align: center;
            border-bottom: 1px solid #EDEDED; /*linha horizontal dos horários*/
            border-radius: 7px;
            width:auto;
        }

        .agenda-easy-th {
            border-radius: 7px;
            /*overflow: visible; /* Isso permite que os elementos filhos extrapolem a célula */
            position: sticky;
        }

        .font-td-tab{
            font-size: 8px;
            padding: 2px;
            color: #fafafa;
            
        }

        .font-td-tab:hover{
            color: white;
            cursor: default;
        }
        .celula{
            border-left:1px solid #9DE1E3;
        }

        .celula:hover{
            background-color: #6bd3d1;
            border-radius: 3px;
        }    
            
        .celula-hover { /* somente para o js */
                background-color: #9DE1E3;
                box-shadow: -5px -5px 15px rgba(0, 0, 0, 0.5);
        }     

        
        .agendamento {
                color: white;
                border-radius: 5px;
                border: 1px solid rgb(255, 255, 255);
                cursor: pointer;
                opacity: 0.95;
                overflow: hidden;
                box-shadow: inset 0 0 15px 8px rgba(255, 255, 255, 0.2);
                transition: box-shadow 0.6s ease-in-out, opacity 0.6s ease-in-out;
        }

            .agendamento:hover {
            
               /* color: black;*/
                /*border: 1px solid green; /* Corrigido de 'margin' para 'border' */
                opacity: 1;
               /* z-index: 5000;*/
                /*width: 90px;*/
                box-shadow: -1px -1px 10px rgba(10, 53, 146, 0.5);
            }
                
            .agendamento-p {
                
                text-align: left;
                color: white;
                font-size: 10px;
                margin: 2px;
                position: static;
                text-align: left;
                margin: 5px;
            }
            

            #tooltip {
                position: absolute;
                z-index: 10000;
                background-color: #fff;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 5px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
                color: #333;
                font-size: 12px; /* ajuste o tamanho da fonte conforme necessário */
                max-width: 200px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                display: none; /* Inicialmente não exibido */
                opacity: 0; /* Totalmente transparente */
                transition: opacity 0.3s ease-in-out; /* Suaviza a transição da opacidade */
                pointer-events: none; /* Evita interação com o mouse */
            }
            
            #tooltip::after {
                content: '';
                position: absolute;
                bottom: 100%; /* Posiciona na parte de cima do tooltip */
                left: 50%;
                margin-left: -5px;
                border-width: 5px;
                border-style: solid;
                border-color: transparent transparent #fff transparent;
            }
            
            #tooltip p:first-child {
                font-weight: bold;
                color: #d9534f;
                margin-bottom: 10px;
            }
            
</style>
