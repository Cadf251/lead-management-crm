<?php

namespace App\adms\Models\traits;

/**
 * Um objeto comum com id, nome e descricao.
 */
trait ComumObject
{
  use ComumId;
  use ComumName;
  use ComumDescription;
}