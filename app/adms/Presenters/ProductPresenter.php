<?php

namespace App\adms\Presenters;

use App\adms\Models\sales\Product;

class ProductPresenter
{
  public static function present(?array $products)
  {
    if ($products === null) return null;

    $final = [];

    /** @var Product $product */
    foreach ($products as $product) {
      $final[] = [
        "id" => $product->getId(),
        "name" => $product->getName(),
        "description" => $product->getDescription(),
      ];
    }

    return $final;
  }
}