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
        'применить повыш. коэфф-т' => $node->field_orders_rab_time->value,
        'способ формирования цены' => $node_usluga->field_activit_cost_formir->value,
        'базовая стоимость услуги' => $node_usluga->field_activity_cost_min->value,
      ];
    }
    $output = '';
    $cost_adult = 0;
    $cost_lgota = 0;
    $cost_school = 0;
    $cost_baby = 0;
    foreach ($data as $key => $value) {
      if ($value['kategory'] == 'adult') {
        $cost_adult = $value['visitor'] * $source['цена на одного (общая категория)'];
      }
      if ($value['kategory'] == 'student' || $value['kategory'] == 'old' || $value['kategory'] == 'museum') {
        $cost_lgota = $value['visitor'] * $source['цена на одного (льготная категория)'];
      }
      if ($value['kategory'] == 'school') {
        $cost_school = $value['visitor'] * $source['цена на одного (школьники)'];
      }
      if ($value['kategory'] == 'baby') {
        $cost_baby = $value['visitor'] * $source['цена на одного (дошкольники)'];
      }
    }
    if ($source['способ формирования цены'] == 'line') {
      $output .= "линейный способ\n";
      if ($source['общее количество людей'] < $source['минимальная численность группы']) {
        $output .= "численность меньше минимальной\n";
        $output .= $source['общее количество людей'] . "<" . $source['минимальная численность группы'];
        $summa = $source['минимальная численность группы'] * $source['цена на одного (общая категория)'];
      }
      else {
        $output .= "численность больше минимальной\n";
        $output .= $source['общее количество людей'] . ">" . $source['минимальная численность группы'] . "\n";
        $summa = $cost_adult + $cost_lgota + $cost_school + $cost_baby;
        $output .= "сумма" . $summa . "\n";
      }
      if ($source['применить повыш. коэфф-т'] == 'koefficient') {
        $summa = $summa * $source['повышающий коэффициент'];
      }
    }
    elseif ($source['способ формирования цены'] == 'unline') {
      $output .= "нелинейный способ\n";
      if ($source['общее количество людей'] < $source['минимальная численность группы']) {
        $summa = $source['базовая стоимость услуги'];
      }
      else {
        $people = $source['общее количество людей'] - $source['минимальная численность группы'];
        $cost = $people * $source['цена на одного (общая категория)'];
        $summa = $source['базовая стоимость услуги'] + $cost;
      }
      if ($source['применить повыш. коэфф-т'] == 'koefficient') {
        $summa = $summa * $source['повышающий коэффициент'];
      }
    }
    else {
      $summa = $source['базовая стоимость услуги'];
      if ($source['применить повыш. коэфф-т'] == 'koefficient') {
        $summa = $summa * $source['повышающий коэффициент'];
      }
    }
    //dsm($output);
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
