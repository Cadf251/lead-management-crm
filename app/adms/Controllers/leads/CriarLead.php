<?php

namespace App\adms\Controllers\leads;

use App\adms\Helpers\CelularFormatter;
use App\adms\Helpers\GenerateLog;
use App\adms\Helpers\NameFormatter;

class CriarLead extends LeadAbstract
{
  public function index()
  {

  }

  /**
   * Formata e seta os dados do lead.
   * 
   * @param string $nome Sem formatação
   * @param string $email
   * @param string $celular No formato placeholder
   * @param array $utm ["utm_source", "utm_medium", "utm_campaign", "utm_term", "utm_content", "palavra", "gclid", "fbclid]
   */
  public function setarDados(string $nome, string $email, string $celular, array $utm):void
  {
    $this->nome = NameFormatter::formatarNome($nome);
    $this->email = $email;
    $this->celular = CelularFormatter::paraInternaciona($celular);
    $this->utm = [
      "origem" => $utm["utm_source"] ?? null,
      "meio" => $utm["utm_medium"] ?? null,
      "campanha" => $utm["utm_campaign"] ?? null,
      "termo" => $utm["utm_term"] ?? null,
      "conteudo" => $utm["utm_content"] ?? null,
      "palavra" => $utm["palavra"] ?? null,
      "gclid" => $utm["gclid"] ?? null,
      "fbclid" => $utm["fbclid"] ?? null
    ];
  }

  /**
   * Cria um lead com dados setados no objeto
   * 
   * @param $this->nome
   * @param $this->email
   * @param $this->celular
   * @param $this->utm
   * 
   * @return int|false
   */
  public function criarLead():int|false
  {
    // Tenta inserir o novo lead com o repositório
    $result = $this->repo->criarLead($this->nome, $this->email, $this->celular);

    if($result !== false){
      $leadId = $result;
    } else {
      GenerateLog::generateLog("error", "Não foi possível criar um lead.", ["utm" => $this->utm]);
      return false;
    }

    if (!empty($this->utm)){
      // Filtra valores que NÃO são vazios e NÃO são null
      $validValues = array_filter($this->utm, function($value){
        return $value !== null && $value !== '';
      });

      // Se existe pelo menos 1 valor válido, insere
      if (!empty($validValues)) {
        $this->repo->insertUtm($leadId, $this->utm);
      }
    }
    return $leadId;
  }

  /**
   * Verifica se existe um lead com esses dados de contato.
   * 
   * @param $this->email
   * @param $this->celular
   * 
   * @return int|false
   */
  public function leadExiste():int|false
  {
    return $this->repo->leadExiste($this->email, $this->celular);
  }
}