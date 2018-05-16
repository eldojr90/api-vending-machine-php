<?php

require_once '../config/init.php';

use App\Model\Transacao,
    App\Model\DAO\CartaoDAO,
    App\Model\DAO\TransacaoDAO,
    PDO;
    
header("Content-Type:application/json; charset=UTF-8",true);

$td = new TransacaoDAO();
$cd = new CartaoDAO();

$msg = null;

//vars para armazenar dados das requisições
$token = null;

//vars genericas
$idCartao = null;

if(isset($_GET["token"])){
    
    $token = $_GET["token"]; 
    $idCartao = $cd->findIdByToken($token);

    if($td->validaCreditoDiario($token)){
        $t = new Transacao(null,5.5,null,1,$idCartao);
        if(!$td->insert($t)){$msg = "Ocorreu um erro interno na inserção do crédito diário. Contatar admin.";}
    }

}else{
    $msg = "Requisição inválida! Verifique os parâmetros necessários para sua requisição em README.md";
}

$saldo = 0;

if($cd->validaDebitos($token)){
    $saldo = $cd->getSaldo($token);
}else{
    $saldo = $cd->getTotalEntradas($token);
}

if(!isset($saldo)){$msg = "Erro interno ao pesquisar saldo. Contatar o admin.";}