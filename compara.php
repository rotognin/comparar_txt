<?php
   // Programa que lê dois arquivos textos e faz comparações,
   // jogando em tela as alterações
   // OBS: é feita comparação linha-a-linha

   // Passar dois parâmetros, onde ambos são os arquivos a serem
   // comparados

   // Checa a passagem de parâmetros
   if (!isset($argv[1]) || ($argv[1] == '')) {
      echo 'Parametro 1 nao passado. Informar dois arquivos texto para ' .
           ' comparar.';
      exit;
   }

   if (!isset($argv[2]) || ($argv[2] == '')) {
      echo 'Parametro 2 nao passado. Informar dois arquivos texto para ' .
           ' comparar.';
      exit;
   }

   // Terceiro parâmetro: passar a letra "D" sem as aspas para que
   // sejam exibidas apenas as diferenças encontradas.
   $diferente = false;

   if (isset($argv[3]) && ($argv[3] == 'D')) {
      $diferente = true;
   }

   // Guarda os arquivos passados por parâmetro em variáveis
   $arquivo1 = (string)$argv[1];
   $arquivo2 = (string)$argv[2];

   // Guardar todo o conteúdo do arquivo 1 em um array
   $a_arq1 = array();
   $i_linhas1_total = 0; // Total de linhas do arquivo 1
   $i_linhas1_atual = 0; // Variável para ir "caminhando" pelo array 1
   $i_linhas1_anda = 0;  // Vai caminhando e volta pelo array 1

   if (!($h_arq1 = fopen($arquivo1,'r'))) {
      echo 'Arquivo 1 não pode ser aberto.';
      exit;
   }

   while (($linha = fgets($h_arq1)) != '') {
      $a_arq1[] = $linha;
   }

   $i_linhas1_total = count($a_arq1);

   fclose($h_arq1);

   // Guardar todo o conteúdo do arquivo 2 em um array
   $a_arq2 = array();
   $i_linhas2_total = 0; // Total de linhas do arquivo 2
   $i_linhas2_atual = 0; // Variável para is "caminhando" pelo array 2
   $i_linhas2_anda = 0;  // Vai caminhando e volta pelo array 2

   if (!($h_arq2 = fopen($arquivo2,'r'))) {
      echo 'Arquivo 2 não pode ser aberto.';
      exit;
   }

   while (($linha = fgets($h_arq2)) != '') {
      $a_arq2[] = $linha;
   }

   $i_linhas2_total = count($a_arq2);

   fclose($h_arq2);

   // Verificar se os dois arquivos são idênticos no seu comteúdo.
   if ($a_arq1 === $a_arq2) {
      echo 'Ambos os arquivos são idênticos.' . PHP_EOL;
      exit;
   }

   // Guardar o maior número de linhas
   $total_linhas = ($i_linhas1_total >= $i_linhas2_total) ?
                    $i_linhas1_total : $i_linhas2_total;

   if ($total_linhas == 1) {
      if ($a_arq1 !== $a_arq2) {
         echo 'Ambos os arquivos são diferentes, possuem uma linha apenas.' . PHP_EOL;
         exit;
      }
   }

   $a_dif = array();
   $terminou = false;
   // Essa variável guarda a posição atual no array $a_dif, necessário para
   // guardar o número de linha da diferença encontrada nos arquivos.
   $num_info = 0;

   while (!$terminou) {
      if (rtrim($a_arq1[$i_linhas1_atual]) == rtrim($a_arq2[$i_linhas2_atual])) {
         $num_info++;
         $a_dif[$num_info]['*'] = rtrim($a_arq1[$i_linhas1_atual]);

         $i_linhas1_atual++;
         $i_linhas2_atual++;
      } else {
         // Aqui começa a lógica de verdade, fazendo comparações do arquivo 1
         // com o arquivo 2, invertendo a ordem também para ver se só há
         // a informação em um deles, ou nos dois, mas em posições diferentes.
         $i_linhas2_anda = $i_linhas2_atual;
         $i_linhas2_anda++;
         $achou_no_2 = false;
         $acabou = false;

         while (!$acabou) {
            if (rtrim($a_arq1[$i_linhas1_atual]) == rtrim($a_arq2[$i_linhas2_anda])) {
               $achou_no_2 = true;
               $acabou = true;
            } else {
               $i_linhas2_anda++;
               if ($i_linhas2_anda == $i_linhas2_total) {
                  $acabou = true;
               }
            }
         }

         // Ao chegar aqui, o linhas2_anda estará posicionado onde achou
         // um igual, ou se estiver acabado e não achou igual

         // Ver do 2 para o 1
         $i_linhas1_anda = $i_linhas1_atual;
         $i_linhas1_anda++;
         $achou_no_1 = false;
         $acabou = false;

         while (!$acabou) {
            if (rtrim($a_arq2[$i_linhas2_atual]) == rtrim($a_arq1[$i_linhas1_anda])) {
               $achou_no_1 = true;
               $acabou = true;
            } else {
               $i_linhas1_anda++;
               if ($i_linhas1_anda == $i_linhas1_total) {
                  $acabou = true;
               }
            }
         }

         // Fazer as verificações se achou no 1 e no 2, qual andou mais...
         if (($achou_no_1) && ($achou_no_2)) {
            // Ver o deslocamento, qual andou mais
            $dif1 = $i_linhas1_anda - $i_linhas1_atual;
            $dif2 = $i_linhas2_anda - $i_linhas2_atual;

            // Quem andou menos será deslocado (guardado os valores
            // no array geral do atual até onde andou)
            if ($dif1 > $dif2) {
               // Desloca o 2
               while ($i_linhas2_atual < $i_linhas2_anda) {
                  $num_info++;
                  $a_dif[$num_info]['linha'] = $i_linhas2_atual;
                  $a_dif[$num_info]['2'] = rtrim($a_arq2[$i_linhas2_atual]);
                  $i_linhas2_atual++;
               }
            } else {
               // Desloca o 1
               while ($i_linhas1_atual < $i_linhas1_anda) {
                  $num_info++;
                  $a_dif[$num_info]['linha'] = $i_linhas1_atual;
                  $a_dif[$num_info]['1'] = rtrim($a_arq1[$i_linhas1_atual]);
                  $i_linhas1_atual++;
               }
            }

            $num_info++;
            $a_dif[$num_info]['*'] = rtrim($a_arq1[$i_linhas1_atual]);
            $i_linhas1_atual++;
            $i_linhas2_atual++;
         } else if ($achou_no_1) {
            // Se só achou no 1, grava a informação como "só no 1"
            $num_info++;
            $a_dif[$num_info]['linha'] = $i_linhas1_atual;
            $a_dif[$num_info]['1'] = rtrim($a_arq1[$i_linhas1_atual]);
            $i_linhas1_atual++;
         } else {
            // Se só achou no 2, grava a informação como "só no 2"
            $num_info++;
            $a_dif[$num_info]['linha'] = $i_linhas2_atual;
            $a_dif[$num_info]['2'] = rtrim($a_arq2[$i_linhas2_atual]);
            $i_linhas2_atual++;
         }
      }

      if (($i_linhas1_atual >= $i_linhas1_total) ||
          ($i_linhas2_atual >= $i_linhas2_total)) {
         $terminou = true;
      }
   }

   // Após um dos dois ter finalizado, ver qual ficou faltando para
   // acrescentar no array resultante
   if ($i_linhas1_atual >= $i_linhas1_total) {
      // Quer dizer que a primeira linha chegou primeiro até o fim
      // Deverá fazer com que a linha 2 chegue também, andando de
      // onde parou.
      while ($i_linhas2_atual < $i_linhas2_total) {
         $num_info++;
         $a_dif[$num_info]['linha'] = $i_linhas2_atual;
         $a_dif[$num_info]['2'] = rtrim($a_arq2[$i_linhas2_atual]);
         $i_linhas2_atual++;
      }
   }

   if ($i_linhas2_atual >= $i_linhas2_total) {
      // Quer dizer que a segunda linha chegou primeiro até o fim
      // Deverá fazer com que a linha 1 chegue também, andando de
      // onde parou.
      while ($i_linhas1_atual < $i_linhas1_total) {
         $num_info++;
         $a_dif[$num_info]['linha'] = $i_linhas1_atual;
         $a_dif[$num_info]['1'] = rtrim($a_arq1[$i_linhas1_atual]);
         $i_linhas1_atual++;
      }
   }

   foreach ($a_dif as $valor1) {
      foreach ($valor1 as $chave => $valor) {
         if (($chave == '*' && !$diferente) || ($chave != '*')) {
            if (($chave != '*') && ($diferente) && ($chave == 'linha')) {
               echo $valor . ' |';
            }

            if ($chave != 'linha') {
               echo $chave . ' ' . $valor . PHP_EOL;
            }
         }
      }
   }
?>