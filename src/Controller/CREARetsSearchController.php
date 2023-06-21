<?php

namespace Drupal\crearets\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use PHRETS\Configuration;
use PHRETS\Session;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the search page.
 */
class CREARetsSearchController extends ControllerBase {

  /**
   * The PHRETS session.
   *
   * @var \PHRETS\Session
   */
  protected $session;

  /**
   * The CREARets configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Constructs a CREARetsSearchController object.
   *
   * @param \PHRETS\Session $session
   *   The PHRETS session.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct(Session $session, ConfigFactoryInterface $config_factory) {
    $this->session = $session;
    $this->config = $config_factory->get('crearets.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $config = $container->get('config.factory')->get('crearets.settings');

    // Create a PHRETS session and establish the connection using configuration values.
    $retsConfig = new Configuration;
    $retsConfig->setLoginUrl($config->get('url'))
      ->setUsername($config->get('username'))
      ->setPassword($config->get('password'))
      ->setRetsVersion('RETS/1.7.2')
      ->setOption('compression_enabled', TRUE)
      ->setOption('use_digest_authentication', TRUE)
      ->setOption('disable_follow_location', TRUE)
      ->setOption('offset_support', TRUE)
      ->setOption('accept_header', '/');

    $session = new Session($retsConfig);
    $session->login();

    return new static($session, $container->get('config.factory'));
  }

  /**
   * Returns the search page.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The rendered search page.
   */
  public function searchPage() {
    // Perform RETS queries and process the response.
    $search = $this->session->Search('Property', 'ResidentialProperty', '(Status=A)');
    if ($search->isSuccessful()) {
      $results = $search->getResults();

      // Build a table of search results.
      $header = [
        'MLS Number',
        'Price',
      ];
      $rows = [];

      foreach ($results as $result) {
        $mlsNumber = $result->get('MLSNumber');
        $price = $result->get('ListPrice');

        $rows[] = [
          $mlsNumber,
          $price,
        ];
      }

      $table = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];

      // Return the rendered table.
      return new Response(\Drupal::service('renderer')->render($table));
    }
    else {
      // Handle search error.
      return new Response('Error: ' . $search->getError());
    }
  }

}
