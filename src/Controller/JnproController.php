<?php

namespace Drupal\jn_pro\Controller;

use Drupal\image\Entity\ImageStyle;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

/**
 * Listing of Products.
 */
class JnproController {

  /**
   * Start listing Of Products.
   */
  public function proList() {
    global $base_url;
    $nids = \Drupal::entityQuery('node')->condition('type', 'jugad_products')->execute();
    $nodes = Node::loadMultiple($nids);
    if (!empty($nodes)) {
      foreach ($nodes as $res) {
        $title = $res->get('title')->getValue();
        $des = $res->get('body')->getValue();
        $img = $res->get('field_product_image')->getValue();
        $nid = $res->get('nid')->getValue();
        $file = File::load($img[0]['target_id']);
        $url = ImageStyle::load('medium')->buildUrl($file->getFileUri());
        $variables['productlist']['jnprolist'][] =
        [
          'title' => $title[0]['value'],
          'des' => substr($des[0]['value'], 0, 150) . '...',
          'image' => $url,
          'nid' => $nid[0]['value'],
        ];
      }
    }
    else {
      $variables['productlist']['jnprolist'] = [];
    }

    return [
      '#theme' => 'jnprolist_template',
      '#title' => 'Jugaad Patches Product List',
      '#name' => $variables['productlist']['jnprolist'],
    ];
  }

  // End listing Of Products.
}
