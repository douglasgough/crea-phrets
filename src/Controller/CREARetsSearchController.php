<?php

namespace Drupal\crearets\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PHRETS\Configuration;
use PHRETS\Session;

/**
 * Controller for the search page.
 */
class CREARetsSearchController extends ControllerBase {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new CustomRetsModuleController object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   *
   * This is a factory method that returns a new instance of this class.
   * The factory should pass any needed dependencies into the constructor of this class, but not the container itself.
   */
  public static function create(ContainerInterface $container) {
    $config_factory = $container->get('config.factory');
    return new static($config_factory);
  }

  /**
   * Retrieves records from the CREA RETS server using PhRETS.
   */
  public function searchPage() {
    $config = $this->configFactory->get('crearets.settings');
    $crea_rets_url = $config->get('url');
    $crea_rets_username = $config->get('username');
    $crea_rets_password = $config->get('password');

    // Configure PhRETS with the login information.
    $config = new Configuration;
    $config->setLoginUrl($crea_rets_url);
    $config->setUsername($crea_rets_username);
    $config->setPassword($crea_rets_password);
    $config->setRetsVersion('1.7.2');

    // Create a session and retrieve records.
    $rets = new Session($config);
    $rets->Login();

    // Example code to retrieve properties from the CREA RETS server.
    $resource = 'Property';
    $dbml = "(ID=*)";

    $results = $rets->Search(
      $resource,
      $resource,
      $dbml,
      [
        'QueryType' => 'DMQL2',
        'Count' => 1, // count and records
        'Format' => 'COMPACT-DECODED',
        'Limit' => 100,
        'StandardNames' => 0, // give system names
      ]
    );

    $all_ids = $results->lists('ListingKey');
    // $all_ids now contains an array of ListingKey values from the search results.

    // Close the session.
    $rets->Logout();

    // Return a render array for the Drupal page.
    $content = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#title' => 'My List',
      '#items' => $all_ids,
      '#attributes' => ['class' => 'mylist'],
      '#wrapper_attributes' => ['class' => 'container'],
    ];
    return $content;

  }

}
