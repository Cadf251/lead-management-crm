<?php

namespace App\adms\Models\leads;

use App\adms\Models\sales\Offer;
use App\adms\Models\traits\ComumId;

class Interaction
{
  use ComumId;

  /** @var ?string URL */
  private ?string $url;

  /** @var ?string $utm JSON */
  private ?string $utm;

  private ?string $type;

  private ?Offer $offer;

  public const TYPE_PAGE = "page";
  public const TYPE_MAILING = "mailing";
  public const TYPE_ARTICLE = "article";
  public const TYPE_VIDEO = "video";
  public const TYPE_SOCIAL = "social";
  public const TYPE_OTHERS = "others";

  public const CANONICAL_TYPES = [
    self::TYPE_PAGE,
    self::TYPE_VIDEO,
    self::TYPE_SOCIAL,
    self::TYPE_MAILING,
  ];

  public function setType(string $type): void
  {
    if (!in_array($type, self::CANONICAL_TYPES)) {
      $type = self::TYPE_OTHERS;
    }

    $this->type = $type;
  }

  public function setOffer(Offer $offer): void
  {
    $this->offer = $offer;
  }
}
