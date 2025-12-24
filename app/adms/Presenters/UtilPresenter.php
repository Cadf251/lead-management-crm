<?php

namespace App\adms\Presenters;

class UtilPresenter
{
  public static function getStatusClass(int $statusId)
  {
    $classes = [
      1 => "red",
      2 => "blue",
      3 => "green",
    ];

    return $classes[$statusId] ?? "gray";
  }
}