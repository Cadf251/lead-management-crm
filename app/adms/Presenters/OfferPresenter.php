<?php

namespace App\adms\Presenters;

use App\adms\Models\sales\Offer;

class OfferPresenter
{
  public static function present(?array $offers)
  {
    if ($offers == null) return null;

    $final = [];
    /** @var Offer $offer */
    foreach($offers as $offer) {
      $final[] = [
        "id" => $offer->getId(),
        "name" => $offer->getName(),
        "description" => $offer->getDescription(),
        "status_badge" => $offer->getStatusId(),
      ];
    }
  }
}