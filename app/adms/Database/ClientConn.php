<?php

namespace App\adms\Database;

use App\adms\Core\AppContainer;
use App\database\Models\Database;

class ClientConn
{
  use ConnBase;
  
  protected static function getDb(): ?Database
  {
    $array = AppContainer::getAuthUser()->getCredentials();

    return new Database(
      $array['db_name'],
      $array['host'],
      $array['user'],
      $array['pass'],
      true
    );
  }
}