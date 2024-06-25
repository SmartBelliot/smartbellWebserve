<?php
 
/*
 * O codigo seguinte retorna os dados detalhados de um produto.
 * Essa e uma requisicao do tipo GET. Um produto e identificado 
 * pelo campo id.
 */

// conexão com bd
require_once('conexao_db_smartbell.php');

// array de resposta
$resposta = array();

// Obtem do BD os detalhes do produto com id especificado na requisicao GET
$consulta = $db_con->prepare("SELECT * FROM visitas WHERE id = (SELECT MAX($id) FROM visitas)");

if ($consulta->execute()) {
  if ($consulta->rowCount() > 0) {

    // Se o produto existe, os dados completos do produto 
    // sao adicionados no array de resposta. A imagem nao 
    // e entregue agora pois ha um php exclusivo para obter 
    // a imagem do produto.
    $linha = $consulta->fetch(PDO::FETCH_ASSOC);

    $resposta["id"] = $linha["id"];
    $resposta["img"] = $linha["img"];
    $resposta["data_criacao"] = $linha["data_criacao"];
    
    // Caso o produto exista no BD, o cliente 
    // recebe a chave "sucesso" com valor 1.
    $resposta["sucesso"] = 1;
    
  } else {
    // Caso o produto nao exista no BD, o cliente 
    // recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
    // motivo da falha.
    $resposta["sucesso"] = 0;
    $resposta["erro"] = "Visita não encontrado";
  }
} else {
  // Caso ocorra falha no BD, o cliente 
  // recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
  // motivo da falha.
  $resposta["sucesso"] = 0;
  $resposta["erro"] = "Erro no BD: " . $consulta->error;
}
// Fecha a conexao com o BD
$db_con = null;

// Converte a resposta para o formato JSON.
echo json_encode($resposta);
?>
