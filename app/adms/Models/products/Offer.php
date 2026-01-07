<?php

namespace App\adms\Models\products;

use App\adms\Models\Product;
use DateTime;

class Offer
{
  private int $id;
  private string $name;
  private int $type;
  private bool $sale = false;
  private ?DateTime $date_start;
  private ?DateTime $date_end;
  private int $status;
  private ?Product $product;
  private $targetProfile;

}