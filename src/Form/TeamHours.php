<?php

namespace Drupal\node_orders\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * SimpleForm.
 */
class TeamHours extends FormBase {

  /**
   * F ajaxModeDev.
   */
  public function ajaxTeamHours(array &$form, &$form_state) {
    $response = new AjaxResponse();
    $uid = \Drupal::currentUser()->id();
    $user = User::load($uid);
    if ($user->hasPermission('orders-form')) {
      $nid = $form_state->getValue('orders');
      $node = Node::load($nid);
      $get_values = $form_state->getValues();
      $num = $node->id();
      $people = $node->field_orders_team;
      foreach ($people as $key => $value) {
        $node_people = $value->entity;
        $nid = $node_people->id();
        $form_key = 'team-' . $nid;
        $current_value = str_replace(',', '.', $get_values[$form_key]);
        $source = [
          'type' => 'exhour',
          'title' => 'title',
          'field_exhour_ref_orders' => $num,
          'field_exhour_team' => $nid,
          'field_exhour_hours' => $current_value,
          'uid' => \Drupal::currentUser()->id(),
        ];
        $node_chas = Node::create($source);
        $node_chas->save();
      }
      $response->addCommand(new HtmlCommand("#button-team-hours-form .form-actions", "Часы работникам созданы"));
      $node->field_orders_status->setValue('done');
      $node->save();
    }
    else {
      $response->addCommand(new HtmlCommand("#button-team-hours-form .form-actions", "Доступ запрещен"));
    }
    return $response;
  }

  /**
   * Build the simple form.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $extra = NULL) {
    $node = $extra;
    $hours = $node->field_orders_ref_activity->entity->field_activity_long_time->value;
    $form_state->node_orders = $node;
    $form_state->setCached(FALSE);

    $form['orders'] = [
      '#type' => 'hidden',
      '#title' => 'номер заказа: ',
      "#default_value" => $node->id(),
    ];
    foreach ($node->field_orders_team as $key => $value) {
      $people = $value->entity->title->value;
      $form['team-' . $value->entity->id()] = [
        '#type' => 'textfield',
        '#title' => 'часы для работника: ' . $people,
        "#default_value" => $hours,
      ];
    }
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Создать часы сотрудникам',
      '#attributes' => ['class' => ['btn', 'btn-xs', 'btn-danger']],
      '#ajax' => [
        'callback' => '::ajaxTeamHours',
        'effect' => 'fade',
        'progress' => ['type' => 'throbber', 'message' => ""],
      ],
    ];
    return $form;
  }

  /**
   * Getter method for Form ID.
   */
  public function getFormId() {
    return 'button_team_hours_form';
  }

  /**
   * Implements a form submit handler.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
  }

}
