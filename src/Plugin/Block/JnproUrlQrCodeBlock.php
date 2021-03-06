<?php

namespace Drupal\jn_pro\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use CodeItNow\BarcodeBundle\Utils\QrCode;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\Component\Utility\UrlHelper;
use Psr\Log\LoggerInterface;
use Drupal\Core\Utility\Error;

/**
 * Provides a jugaad patches product qr code block.
 *
 * @Block(
 *   id = "jn_pro_linkurl_qr_code_block",
 *   admin_label = @Translation("Jugaad Patches Product QR Code Block"),
 * )
 */
class JnproUrlQrCodeBlock extends BlockBase implements
    ContainerFactoryPluginInterface {

  /**
   * RouteMatch used to get parameter Node.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Describes a logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Construct Drupal\jugaad_products\Plugin\Block\ProductUrlQrCodeBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(
        array $configuration,
        $plugin_id,
        array $plugin_definition,
        RouteMatchInterface $route_match,
        LoggerInterface $logger
    ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->routeMatch = $route_match;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container,
        array $configuration,
  $plugin_id,
  $plugin_definition
    ) {
    return new static(
          $configuration,
          $plugin_id,
          $plugin_definition,
          $container->get('current_route_match'),
          $container->get('logger.factory')->get('jugaad_products'),
      );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->routeMatch->getParameter('node');

    if ($node instanceof NodeInterface) {
      // Show block only for product pages.
      if ($node->bundle() == 'jugad_products') {
        // Get product url from link.
        $product_url = $node
          ->get('field_purchase_link')
          ->getValue()[0]['value'];

        if (!empty($product_url)) {
          $qrCode_image = '';
          // Check added URL is external or not.
          if (!UrlHelper::isExternal($product_url)) {
            global $base_url;
            $product_url = $base_url . '' . Url::fromUri($product_url)
              ->toString();
          }

          // Create a QR code.
          try {
            $qrCode = new QrCode();

            $qrCode->setText($product_url)
              ->setSize(300)
              ->setPadding(10)
              ->setErrorCorrection('high')
              ->setForegroundColor(
                      ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]
                  )
              ->setBackgroundColor(
                      ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]
                  )
              ->setLabel('Scan Qr Code')
              ->setLabelFontSize(16)
              ->setImageType(QrCode::IMAGE_TYPE_PNG);

            $qrCode_image = Markup::create(
                  '<img src="data:' . $qrCode->getContentType()
                  . ';base64,' . $qrCode->generate() . '" />'
              );
          }
          catch (Exception $e) {
            // Log the exception to watchdog.
            $ex_vars = Error::decodeException($e);
            $this->logger
              ->error(
                      '%type:@message in %function (line %line of %file).',
                      $ex_vars
                  );
          }

          // Print the QR code in block.
          return [
            '#markup' => $qrCode_image,
          ];
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
