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
    $node_customer = $node->field_orders_customer->entity;
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
    if (is_object($node_customer)) {
      $array = [
        'скидка' => $node_customer->field_customer_discount->value,
      ];
    }
    $output = '';
    $cost_adult = 0;
    $cost_school = 0;
    $cost_baby = 0;
    $discount = 0;
    $cost_lgota_student = 0;
    $cost_lgota_old = 0;
    $cost_lgota_military = 0;
    $cost_lgota_museum = 0;
    $cost_lgotniki = 0;
    $cost_guest = 0;
    foreach ($data as $key => $value) {
      if ($value['kategory'] == 'adult') {
        $cost_adult = $value['visitor'] * $source['цена на одного (общая категория)'];
      }
      if ($value['kategory'] == 'student') {
        $cost_lgota_student = $value['visitor'] * $source['цена на одного (льготная категория)'];
      }
      if ($value['kategory'] == 'old') {
        $cost_lgota_old = $value['visitor'] * $source['цена на одного (льготная категория)'];
      }
      if ($value['kategory'] == 'military') {
        $cost_lgota_military = $value['visitor'] * $source['цена на одного (льготная категория)'];
      }
      if ($value['kategory'] == 'museum') {
        $cost_lgota_museum = $value['visitor'] * 0;
      }
      if ($value['kategory'] == 'school') {
        $cost_school = $value['visitor'] * $source['цена на одного (школьники)'];
      }
      if ($value['kategory'] == 'baby') {
        $cost_baby = $value['visitor'] * $source['цена на одного (дошкольники)'];
      }
      if ($value['kategory'] == 'lgotniki') {
        $cost_lgotniki = $value['visitor'] * 0;
      }
      if ($value['kategory'] == 'guest') {
        $cost_guest = $value['visitor'] * 0;
      }
    }
    $cost_lgota = $cost_lgota_student + $cost_lgota_old + $cost_lgota_military + $cost_lgota_museum;
    if ($source['способ формирования цены'] == 'line') {
      $output .= "линейный способ\n";
      if ($source['общее количество людей'] < $source['минимальная численность группы']) {
        $output .= "численность меньше минимальной\n";
        $output .= $source['общее количество людей'] . "<" . $source['минимальная численность группы'] . "\n";
        $summa = $source['минимальная численность группы'] * $source['цена на одного (общая категория)'];
      }
      else {
        $output .= "численность больше минимальной\n";
        $output .= $source['общее количество людей'] . ">" . $source['минимальная численность группы'] . "\n";
        $summa = $cost_adult + $cost_lgota + $cost_school + $cost_baby + $cost_guest + $cost_lgotniki;
        $output .= "стоимость взрослых: " . $cost_adult . "\n";
        $output .= "стоимость льготников: " . $cost_lgota . "\n";
        $output .= "стоимость школьников: " . $cost_school . "\n";
        $output .= "стоимость дошкольников: " . $cost_baby . "\n";
        $output .= "сумма: " . $summa . "\n";
      }
      if ($source['применить повыш. коэфф-т'] == 'koefficient') {
        $summa = $summa * $source['повышающий коэффициент'];
        $output .= "сумма с учетом коэф-та: " . $summa . "\n";
      }
      if (!empty($array['скидка'])) {
        $output .= "скидка: " . $array['скидка'] . "\n";
        $discount = $summa * $array['скидка'] / 100;
        $output .= "сумма скидки: " . $discount . "\n";
        $summa = $summa - $discount;
        $output .= "итоговая сумма: " . $summa . "\n";
      }
      else {
        $summa = $summa;
        $output .= "итоговая сумма: " . $summa . "\n";
      }
    }
    elseif ($source['способ формирования цены'] == 'unline') {
      $output .= "нелинейный способ\n";
      if ($source['общее количество людей'] < $source['минимальная численность группы']) {
        $output .= $source['общее количество людей'] . "<" . $source['минимальная численность группы'] . "\n";
        $summa = $source['базовая стоимость услуги'];
      }
      else {
        $output .= $source['общее количество людей'] . ">" . $source['минимальная численность группы'] . "\n";
        $people = $source['общее количество людей'] - $source['минимальная численность группы'];
        $output .= "количество людей сверх минимума: " . $people . "\n";
        $cost = $people * $source['цена на одного (общая категория)'];
        $output .= "сумма за людей сверх минимума: " . $cost . "\n";
        $summa = $source['базовая стоимость услуги'] + $cost;
        $output .= "базовая стоимость услуги: " . $source['базовая стоимость услуги'] . "\n";
        $output .= "итоговая сумма: " . $summa . "\n";
      }
      if ($source['применить повыш. коэфф-т'] == 'koefficient') {
        $summa = $summa * $source['повышающий коэффициент'];
      }
      if (!empty($array['скидка'])) {
        $output .= "скидка: " . $array['скидка'] . "\n";
        $discount = $summa * $array['скидка'] / 100;
        $output .= "сумма скидки: " . $discount . "\n";
        $summa = $summa - $discount;
        $output .= "итоговая сумма: " . $summa . "\n";
      }
    }
    else {
      $summa = $source['базовая стоимость услуги'];
      if ($source['применить повыш. коэфф-т'] == 'koefficient') {
        $summa = $summa * $source['повышающий коэффициент'];
      }
      if (!empty($array['скидка'])) {
        $output .= "скидка: " . $array['скидка'] . "\n";
        $discount = $summa * $array['скидка'] / 100;
        $output .= "сумма скидки: " . $discount . "\n";
        $summa = $summa - $discount;
        $output .= "итоговая сумма: " . $summa . "\n";
      }
      else {
        $summa = $summa;
        $output .= "итоговая сумма: " . $summa . "\n";
      }
    }
    // dsm($output);
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
        'region' => $fc->field_orders_visitor_region->value,
      ];
    }
    // dsm($array);
    return $array;
  }

}
