<?php
 
/*
 * O seguinte codigo abre uma conexao com o BD e adiciona um produto nele.
 * As informacoes de um produto sao recebidas atraves de uma requisicao POST.
 */

// conexão com bd
require_once('conexao_db_smartbell.php');

// array de resposta
$resposta = array();

$out = "";

	
// Primeiro, verifica-se se todos os parametros foram enviados pelo cliente.
// A criacao de um produto precisa dos seguintes parametros:
// nome - nome do produto
// preco - preco do produto
// descricao - descricao do produto
// img - imagem do produto
if (isset($_POST['filename']) && isset($_POST['mimetype']) && isset($_POST['data'])) {
	
	// Aqui sao obtidos os parametros
	$filename = $_POST['filename'];
	$mimetype = $_POST['mimetype'];
	$data = $_POST['data'];
	
	
	$client_id="1c36a4fcfc1c1a8";
	$pvars   = array('image' => $data);
	$timeout = 30;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
	curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
	$out = curl_exec($curl);
	curl_close ($curl);
	$pms = json_decode($out,true);
	$img_url=$pms['data']['link'];
	
	// A proxima linha insere um novo produto no BD.
	// A variavel consulta indica se a insercao foi feita corretamente ou nao.
	$consulta = $db_con->prepare("INSERT INTO produtos(img) VALUES('$data')");
	if ($consulta->execute()) {
		// Se o produto foi inserido corretamente no servidor, o cliente 
		// recebe a chave "sucesso" com valor 1
		$resposta["sucesso"] = 1;
	} else {
		// Se o produto nao foi inserido corretamente no servidor, o cliente 
		// recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
		// motivo da falha.
		$resposta["sucesso"] = 0;
		$resposta["erro"] = "Erro ao criar produto no BD: " . $consulta->error;
	}
	
	
	
} else {
	// Se a requisicao foi feita incorretamente, ou seja, os parametros 
	// nao foram enviados corretamente para o servidor, o cliente 
	// recebe a chave "sucesso" com valor 0. A chave "erro" indica o 00000
	// motivo da falha.
	$resposta["sucesso"] = 0;
	$resposta["erro"] = "Campo requerido nao preenchido";
	$out = json_encode($resposta);
}


// Fecha a conexao com o BD
$db_con = null;

// Converte a resposta para o formato JSON.
echo $out;
?>
