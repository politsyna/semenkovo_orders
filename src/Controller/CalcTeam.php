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
class CalcTeam extends ControllerBase {

  /**
   * Page Callback.
   */
  public static function view($node_order) {
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'exhour');
    $query->condition('field_exhour_ref_orders', $node_order->id());
    $ids = $query->execute();
    $output = "";
    if (!empty($ids)) {
      foreach (Node::loadMultiple($ids) as $nid => $node) {
        $team = $node->field_exhour_team->entity;
        $output .= $node->field_exhour_hours->value;
        $output .= " час.";
        $output .= "&#9;";
        $output .= " - ";
        $output .= $team->title->value;
        $output .= "<br>";
      }
    }
    return ['#markup' => $output];
  }

}
