<?php

//pagina inicial de cada grupo
$menu = [
    1 => "agenda_atendimentos",
    2 => "clientes",
    3 => "tecnico_atendimentos",
    4 => "entradas-saidas",
    5 => "marketing",
    6 => "produtos",
    7 => "colaboradores",
    8 => "vendas",
    9 => "informacoes_do_estabelecimento",
    10 => "personalizar_sistema"
  ];
  
  

  $grupos = [
      'Recepção' => [
            [
              'pagina' => 'agenda_atendimentos',
              'descricao' => 'Agenda' // Página principal do grupo
            ],
            [
              'pagina' => 'caixa',
              'descricao' => 'Caixa'
            ],            
            [
              'pagina' => 'clientes',
              'descricao' => 'Clientes'
            ],
            [
              'pagina' => 'agenda_disponibilidades',
              'descricao' => 'Profissionais'
            ],
                        [
              'pagina' => 'agenda_adm',
              'descricao' => 'Administração'
            ],
            [
              'pagina' => 'personalizar_agenda',
              'descricao' => 'Personalizar'
            ],

            [
              'pagina' => 'agenda_configuracoes',
              'descricao' => 'Configurações'
            ]
      ],
      'cliente' => [
          [
              'pagina' => 'ver_cliente',
              'descricao' => 'Ver Cliente'
          ],
          [
              'pagina' => 'clientes',
              'descricao' => 'Clientes' // Página principal do grupo
          ],
          [
              'pagina' => 'contratos_clientes',
              'descricao' => 'Contratos Clientes'
          ],
          [
              'pagina' => 'termos_contratos_assinaturas',
              'descricao' => 'Termos Contratos Assinaturas'
          ],
          [
              'pagina' => 'documentos_clientes',
              'descricao' => 'Documentos Clientes'
          ]
      ],
      
      'tecnico' => [
        [
            'pagina' => 'builder_full',
            'descricao' => 'Builder'
        ],
        [
            'pagina' => 'tecnico_atendimentos',
            'descricao' => 'Atendimentos'
        ],
        [
            'pagina' => 'tecnico/anamnese',
            'descricao' => 'Anamnese'
        ],
        [
            'pagina' => 'equipamentos',
            'descricao' => 'Equipamentos'
        ],
        [
            'pagina' => 'salas',
            'descricao' => 'Salas'
        ],
        [
            'pagina' => 'servicos',
            'descricao' => 'Servicos' // Página principal do grupo
        ]
      ],
      'financeiro' => [
                    [
              'pagina' => 'entradas-saidas',
              'descricao' => 'Movimentações'
          ],
          [
              'pagina' => 'caixas',
              'descricao' => 'Caixas'
          ],
          [
              'pagina' => 'forma_pgtos',
              'descricao' => 'Forma Pgtos'
          ],

          [
              'pagina' => 'contas_contabeis',
              'descricao' => 'Contas Contabeis'
          ]

      ],
      'marketing' => [
          [
              'pagina' => 'marketing',
              'descricao' => 'Marketing' // Página principal do grupo
          ]
      ],
      'produtos' => [
          [
              'pagina' => 'fornecedores',
              'descricao' => 'Fornecedores'
          ],
          [
              'pagina' => 'categorias',
              'descricao' => 'Categorias'
          ],
          [
              'pagina' => 'produtos',
              'descricao' => 'Produtos' // Página principal do grupo
          ],
          [
              'pagina' => 'compras',
              'descricao' => 'Compras'
          ],
          [
              'pagina' => 'estoque',
              'descricao' => 'Estoque'
          ],
          [
              'pagina' => 'lista_de_compras',
              'descricao' => 'Lista De Compras'
          ]
      ],
      'pessoal' => [
          [
            'pagina' => 'colaboradores',
            'descricao' => 'Pessoal' // Página principal do grupo
          ],
          [
              'pagina' => 'usuarios',
              'descricao' => 'Usuarios' // Página principal do grupo
          ],
          [
              'pagina' => 'documentos',
              'descricao' => 'Documentos'
          ],
          [
              'pagina' => 'contratos',
              'descricao' => 'Contratos'
          ],
          [
              'pagina' => 'img_usuario',
              'descricao' => 'Img Usuario'
          ],
          [
              'pagina' => 'gravar_curriculo',
              'descricao' => 'Gravar Curriculo'
          ]
      ],
      'comercial' => [
          [
              'pagina' => 'vendas_propostas',
              'descricao' => 'Proposta De Vendas'
          ],
          [
              'pagina' => 'pacotes',
              'descricao' => 'Pacotes'
          ],
          [
              'pagina' => 'promocoes',
              'descricao' => 'Promocoes'
          ],
          [
              'pagina' => 'interesse',
              'descricao' => 'Interesse' // Página principal do grupo
          ],
          [
              'pagina' => 'comissoes',
              'descricao' => 'Comissoes'
          ],
          [
              'pagina' => 'vendas',
              'descricao' => 'Vendas'
          ]
      ],
      'administracao' => [
          [
              'pagina' => 'lista_contratos_modelos',
              'descricao' => 'Lista Contratos Modelos'
          ],
          [
              'pagina' => 'avisos',
              'descricao' => 'Avisos'
          ],
          [
              'pagina' => 'ler_aviso',
              'descricao' => 'Ler Aviso'
          ],
          [
              'pagina' => 'informacoes_do_estabelecimento',
              'descricao' => 'Informacoes Do Estabelecimento' // Página principal do grupo
          ],
          [
              'pagina' => 'comanda_vale_presente',
              'descricao' => 'Comanda Vale Presente'
          ]
      ],
      'sistema' => [
          [
              'pagina' => 'tutoriais',
              'descricao' => 'Tutoriais'
          ],
          [
              'pagina' => 'personalizar_sistema',
              'descricao' => 'Personalizar Sistema' // Página principal do grupo
          ],
          [
              'pagina' => 'gravar_links',
              'descricao' => 'Gravar Links'
          ]
      ]
  ];
  
 
  
  
  $ultimoIndice = max(array_keys($menu));
  


  // Percorrendo cada grupo e seus submenus para adicionar ao array $menu
     foreach ($grupos as $grupo => $submenus) {

    
       foreach ($submenus as $submenu) {
       
        $ultimoIndice++; // Incrementa o índice para cada novo item
        $menu[$ultimoIndice] = $submenu['pagina']; // Adiciona a página no próximo índice disponível
   }
     }








?>