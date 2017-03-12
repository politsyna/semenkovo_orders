<?php

namespace Drupal\node_orders\Controller;

/**
 * @file
 * Contains \Drupal\node_orders\Controller\Page.
 */
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Controller routines for page example routes.
 */
class Payment extends ControllerBase {

  /**
   * Page Callback.
   */
  public static function view($node_order) {
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'payment');
    $query->condition('field_payment_ref_orders', $node_order->id());
    $entity_ids = $query->execute();
    $oplata = "";
    $summa = 0;
    $oplata_summa = "";
    $itog_summa = 0;
    foreach ($entity_ids as $key => $value) {
      $node_payment = Node::load($value);
      $oplata = $node_payment->title->value;
      $summa = (int) $node_payment->field_payment_summa->value;
      $oplata_summa .= $oplata . " --- " . number_format($summa, 0, ",", " ") . " руб.<br />";
      $itog_summa = $itog_summa + $summa;
    }
    $fakt_summa = $node_order->field_orders_cost->value;
    $ostat_summa = $fakt_summa - $itog_summa;
    $output = [
      'paym' => ['#markup' => 'Оплаты:' . "<br />" . $oplata_summa],
      'summa_oplat' => [
        '#markup' => 'Сумма всех оплат: '
        . number_format($itog_summa, 0, ",", " ") . " руб. <br />",
      ],
      'summa_ostatok' => [
        '#markup' => 'Осталось оплатить: '
        . number_format($ostat_summa, 0, ",", " ") . " руб. <br />",
      ],
    ];
    $status = $node_order->field_orders_status->value;
    if ($status == 'active' || $status == 'control' || $status == 'done') {
      $output['paybutton'] = [
        '#markup' => '<a href="/node/add/payment?orders=' . $node_order->id()
        . '&destination=/node/' . $node_order->id() . '" class="btn btn-primary">Добавить оплату</a>',
      ];
    }
    $node_order->field_orders_oplacheno->setValue($itog_summa);
    return $output;
  }

}
