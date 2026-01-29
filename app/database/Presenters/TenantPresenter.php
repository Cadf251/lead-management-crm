<?php

namespace App\database\Presenters;

use App\adms\UI\Badge;
use App\adms\UI\Button;
use App\database\Models\Tenant;

class TenantPresenter
{
  /**
   * @param array<Tenant> $tenants
   */
  public static function present(array $tenants)
  {
    $final = [];
    foreach($tenants as $tenant) {
      $final[] = [
        "name" => $tenant->getName(),
        "contact" => $tenant->getContactEmail(),
        "api_token" => $tenant->getApiToken() ?? "null",
        "host" => $tenant->getDatabase()->getHost(),
        "db_name" => $tenant->getDatabase()->getName(),
        "db_user" => $tenant->getDatabase()->getUser(),
        "status_badge" => self::status($tenant),
        "buttons" => self::buttons($tenant)
      ];
    }

    return $final;
  }

  public static function status(Tenant $tenant)
  {
    $color = "green";
    if ($tenant->getStatusId() === 0) {
      $color = "red";
    }

    return Badge::create($tenant->getStatusName(), $color);
  }

  public static function buttons(Tenant $tenant)
  {
    $buttons = [
      "create_api_token" => Button::create("+ Api Token")
        ->color("black")
        ->link("criar-api/{$tenant->getId()}")
        ->render(),

      "activate" => Button::create("Ativar")
        ->color("green")
        ->link("ativar-servidor/{$tenant->getId()}")
        ->render(),
      
      "disable" => Button::create("Desativar")
        ->color("red")
        ->link("desativar-servidor/{$tenant->getId()}")
        ->render(),

      "install" => Button::create("Instalar")
        ->color("green")
        ->link("instalar-servidor/{$tenant->getId()}")
        ->render(),
    ];

    $btns = "";

    if ($tenant->getStatusId() === 0) {
      $btns .= $buttons["activate"];
    } else {
      $btns .= $buttons["disable"];
    }

    if ($tenant->getApiToken() === null) {
      $btns .= $buttons["create_api_token"];
    }

    if ($tenant->getDatabase()->getVersion() === null) {
      $btns .= $buttons["install"];
    }

    return $btns;
  }
}