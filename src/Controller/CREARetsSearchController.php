<?php

namespace Drupal\crearets\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\crearets\CreaPhrets;

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
   *
   * I'm passing the ContainerInterface into create() and returning the config.factory service. The service is passed into
   * the __construct method so that I have access to the config values inside the instantiated Controller. This is
   * dependency injection.
   */
  public static function create(ContainerInterface $container) {
    $config_factory = $container->get('config.factory');
    return new static($config_factory);
  }

  /**
   * Retrieves records from the CREA RETS server using PhRETS.
   *
   * configFactory is the injected dependency
   */

//  TODO Refactor to create a service that handles login, logout and session. Include error handling and firewall test.
  public function searchPage() {
    $rets = new CREAPhrets();
    $config = $this->configFactory->get('crearets.settings');
    $crea_rets_url = $config->get('url');
    $crea_rets_username = $config->get('username');
    $crea_rets_password = $config->get('password');

    // Configure PhRETS with the login information.
    /**
    $config = new Configuration;
    $config->setLoginUrl($crea_rets_url);
    $config->setUsername($crea_rets_username);
    $config->setPassword($crea_rets_password);
    $config->setRetsVersion('1.7.2');
    $config->setOption('compression_enabled', true);
    $config->setOption('disable_follow_location', true);
    $config->setOption('offset_support', true);
    $config->setOption('Accept','/');
    */
    // Create a session and retrieve records.
    //$rets = new Session($config);
    $rets->Connect($crea_rets_url,$crea_rets_username,$crea_rets_password);
    // Get the master list from the CREA RETS server.
//    TODO Create a configuration form for Search parameter
    $resource = 'Property';
    $dbml = "(ID=*)";
    $results = $rets->SearchQuery(
      $resource,
      $resource,
      $dbml,
      [
        'QueryType' => 'DMQL2',
        'Count' => 0, // count and records
        'Format' => 'STANDARD-XML',
        'Limit' => 1,
        //'StandardNames' => 0, // give system names
      ]
    );
// TODO Get all available listings with date and id number. Include error handling.
    //$all_ids = $results[''];
    // $all_ids now contains an array of ListingKey values from the search results.
    // $count is the number of listings available using the configured query
    //$count[] = $results->count();
    //$response[] = $rets->getLastResponse();

    // Close the session.
    $rets->Disconnect();
    kpr($results);
    // Return a render array for the Drupal page.
    $content = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#title' => 'My List',
      '#items' => $results,
      '#attributes' => ['class' => 'mylist'],
      '#wrapper_attributes' => ['class' => 'container'],
    ];
    return $content;

  }

}
