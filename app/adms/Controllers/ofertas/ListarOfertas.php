<?php

namespace App\adms\Controllers\ofertas;

use App\adms\Controllers\ofertas\OfertaAbstract;
use App\adms\Core\LoadView;
use App\adms\Presenters\OfferPresenter;

/**
 * @todo O que essa VIEW deve cumprir?
 * 
 * - Ver ofertas
 * 
 * - Editar oferta
 * 
 * - Excluir produto
 * - Excluir oferta
 * 
 * - pausar oferta
 */
class ListarOfertas extends OfertaAbstract
{
  public function index($a)
  {
    $this->setData([
      "ofertas" => OfferPresenter::present($this->repository->list())
    ]);

    $this->render();
  }
}