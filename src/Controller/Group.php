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
    if (is_object($node_usluga)) {
      $source = [
        'название услуги' => $node_usluga->title->value,
        'категория посетителей и количество людей каждой категории' => $data,
        'общее количество людей' => $node->field_orders_group->value,
        'минимальная численность группы' => $node_usluga->field_activity_group_min->value,
        'цена на одного (дошкольники)' => $node_usluga->field_activity_cost_baby->value,
        'цена на одного (льготная категория)' => $node_usluga->field_activity_cost_lgota->value,
        'цена на одного (общая категория)' => $node_usluga->field_activity_cost_adult->value,
        'цена на одного (школьники)' => $node_usluga->field_activity_cost_school->value,
        'продолжительность услуги' => $node_usluga->field_activity_long_time->value,
        'повышающий коэффициент' => $node_usluga->field_activity_koef->value,
        'способ форимрования цены' => $node_usluga->field_activit_cost_formir->value,
      ];
      dsm($source);
    }
    $summa = 0;
    foreach ($data as $key => $value) {
    // $group = $group + $value['visitor'];
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
