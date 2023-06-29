<?php

namespace Drupal\crearets;

use Drupal\Core\Config\ConfigFactoryInterface;

class RetsSession {

  protected $configFactory;

  //protected $creaPhrets;
  protected $rets;

  public function __construct(ConfigFactoryInterface $configFactory, CREAPhrets $CREAPhrets) {
    $this->configFactory = $configFactory;
    //$this->creaPhrets = $CREAPhrets;
  }

  public function connectRetsServer() {
    $this->rets = new CREAPhrets();
    $config = $this->configFactory->get('crearets.settings');
    $crea_rets_url = $config->get('url');
    $crea_rets_username = $config->get('username');
    $crea_rets_password = $config->get('password');
    $rets->Connect($crea_rets_url,$crea_rets_username,$crea_rets_password);
  }

  public function disconnectRetsServer() {
    $this->rets->Disconnect();
  }
}
