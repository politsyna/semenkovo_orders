<?php

namespace Drupal\node_orders\Controller;

/**
 * @file
 * Contains \Drupal\node_orders\Controller\Page.
 */
use Drupal\Core\Controller\ControllerBase;
use Drupal\field_collection\Entity\FieldCollectionItem;

/**
 * Controller routines for page example routes.
 */
class Group extends ControllerBase {

  /**
   * Page Callback.
   */
  public static function groupCounter($node) {
    $collection = $node->field_orders_visitor;
    $data = self::collectionItems($collection);
    $group = 0;
    foreach ($data as $key => $value) {
      $group = $group + $value['visitor'];
    }
    return $group;
  }

  /**
   * Page Callback.
   */
  public static function ordersCalc($node) {
    $collection = $node->field_orders_visitor;
    $data = self::collectionItems($collection);
    $node_usluga = $node->field_orders_ref_activity->entity;
    dsm($node_usluga->title->value);
    $summa = 0;
    foreach ($data as $key => $value) {
      //$group = $group + $value['visitor'];
    }
    dsm($summa);
    $summa = 5;
    return $summa;
  }

  /**
   * Page Callback.
   */
  public static function collectionItems($c) {
    $array = [];
    foreach ($c as $key => $item) {
      $fc = $item->entity;
      $array[$key] = [
        'kategory' => $fc->field_orders_visitor_kategory->value,
        'visitor' => $fc->field_orders_visitor_group->value,
      ];
    }
    return $array;
  }

}
