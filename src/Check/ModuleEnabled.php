<?php

namespace SiteAudit\Check;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class ModuleEnabled extends Check {
  public function check() {
    $response = new AuditResponse('module/enabled', $this);

    $response->test(function ($check) {

      $modules = $check->getOption('modules');
      $check->setToken('modules', implode(', ', $modules));

      $not_enabled = [];
      foreach ($modules as $module_name) {
        try {
          if (!$check->context->drush->moduleEnabled($module_name)) {
            throw new \Exception($module_name);
          }
        }
        catch (\Exception $e) {
          $not_enabled[] = $module_name;
        }
      }

      if (!empty($not_enabled)) {
        $this->setToken('not_enabled', implode(', ', $not_enabled));
        return FALSE;
      }

      return TRUE;
    });

    return $response;
  }
}
