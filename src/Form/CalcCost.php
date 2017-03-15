<?php

namespace Drupal\node_orders\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\CssCommand;

/**
 * SimpleForm.
 */
class CalcCost extends FormBase {

  /**
   * F ajaxModeDev.
   */
  public function ajaxCost(array &$form, &$form_state) {
    $node = $form_state->node_orders;
    $fakt_cost = $node->field_orders_cost->value;
    $rasch_cost = $node->field_orders_cost_raschet->value;
    $fakt = $form_state->getValue('fakt');
    $node->field_orders_cost->setValue($fakt);
    $response = new AjaxResponse();
    $response->addCommand(new RedirectCommand('/node/' . $node->id()));
    $node->save();
    return $response;
  }

  /**
   * Page Callback.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $extra = NULL) {
    $node = $extra;
    $fakt_cost = $node->field_orders_cost->value;
    $rasch_cost = $node->field_orders_cost_raschet->value;
    $form_state->node_orders = $node;
    $form_state->setCached(FALSE);

    if ($fakt_cost == 0) {
      $form['fakt'] = [
        '#type' => 'textfield',
        '#title' => '<span class="cost">Фактическая стоимость услуги: </span>',
        "#default_value" => $rasch_cost,
      ];
      $form['actions'] = [
        '#type' => 'actions',
      ];
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => 'OK',
        '#attributes' => ['class' => ['btn', 'btn-xs', 'btn-success']],
        '#suffix' => '<div class="otvet"></div>',
        '#ajax' => [
          'callback' => '::ajaxCost',
          'effect' => 'fade',
          'progress' => ['type' => 'throbber', 'message' => ""],
        ],
      ];
    }
    else {
      $form['#prefix'] = '<br><span class="cost">Фактическая стоимость услуги: </span>'
      . number_format($fakt_cost, 0, ",", " ") . " руб.";
    }
    return $form;
  }

  /**
   * Getter method for Form ID.
   */
  public function getFormId() {
    return 'button_cost_form';
  }

  /**
   * Implements a form submit handler.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
  }

}
