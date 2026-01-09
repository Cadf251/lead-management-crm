<?php

$token = "4e8cb894b8556eebed1283f2f194cd61";
$url   = "http://crm.local/api/novo-lead";

$payload = [
  "visitor" => [
    "nome"    => "Teste Direto",
    "email"   => "direto@teste.com",
    "celular" => "5511999999999"
  ],
  "interaction" => [
    "offer_id" => 1,
    "url"      => "https://site.com",
    "type"     => "page",
    "utm"      => [
      "source"   => "manual",
      "medium"   => "php",
      "campaign" => "teste"
    ]
  ]
];

$ch = curl_init($url);

curl_setopt_array($ch, [
  CURLOPT_POST           => true,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER     => [
    "Content-Type: application/json",
    "Authorization: Bearer {$token}"
  ],
  CURLOPT_POSTFIELDS     => json_encode($payload)
]);

$response = curl_exec($ch);

if ($response === false) {
  die("Erro CURL: " . curl_error($ch));
}

curl_close($ch);

echo "<pre>";
var_dump(json_decode($response, true));
echo "</pre>";