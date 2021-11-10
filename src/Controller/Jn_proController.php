<?php

namespace Drupal\jn_pro\Controller;


use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Url;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\shortcut\Entity\Shortcut;
use Drupal\system\MenuInterface;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

class Jn_proController
{

    public function pro_list()
    {
        global $base_url;
        $nids = \Drupal::entityQuery('node')->condition('type','jugad_products')->execute();
		$nodes =  \Drupal\node\Entity\Node::loadMultiple($nids); 
		foreach($nodes as $res)
         {
			 $title = $res->get('title')->getValue();
			 $des = $res->get('body')->getValue();
			 $img = $res->get('field_product_image')->getValue();
			
			 $nid= $res->get('nid')->getValue();
			
			$file = \Drupal\file\Entity\File::load($img[0]['target_id']);
          
			$url = \Drupal\image\Entity\ImageStyle::load('medium')->buildUrl($file->getFileUri());

            $variables['productlist']['jnprolist'][]=array(

                'title'=>$title[0]['value'],
                'des'=>$des[0]['value'],
                'image'=>$url,
                'nid'=>$nid[0]['value']
            );    
         }
		
         return array(
		 '#theme'=>'jnprolist_template',
		 '#title'=>'Jugaad Patches Product List',
         '#name'=>$variables['productlist']['jnprolist']
		 );
    }
}

