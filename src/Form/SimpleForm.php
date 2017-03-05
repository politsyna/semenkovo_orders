<?php

namespace Drupal\node_orders\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\CssCommand;

/**
 * SimpleForm.
 */
class SimpleForm extends FormBase {

  /**
   * F ajaxModeDev.
   */
  public function ajaxBackupForever(array &$form, $form_state) {
    $response = new AjaxResponse();
    $show = ['display' => 'block'];
    $hide = ['display' => 'none'];
    if ($form_state->getValue('example_select') == "show") {
      $response->addCommand(new CssCommand('h1', $show));
      $response->addCommand(new CssCommand('.breadcrumb', ['border' => '1px solid red']));
      $response->addCommand(new CssCommand('.breadcrumb', ['font-size' => '35px']));
    }
    else {
      $response->addCommand(new CssCommand('h1', $hide));
      $response->addCommand(new CssCommand('.breadcrumb', ['border' => '1px solid red']));
      $response->addCommand(new CssCommand('.breadcrumb', ['font-size' => '5px']));
    }
    $response->addCommand(new HtmlCommand(".menu-item a", "sdgh"));
    return $response;
  }

  /**
   * Build the simple form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#suffix'] = '<div id="form-results">результаты</div>';

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => 'Title',
      '#description' => 'Title must be at least 5 characters in length.',
      '#required' => TRUE,
    ];
    $form['example_select'] = [
      '#type' => 'radios',
      '#title' => 'Заголовок',
      '#options' => [
        'show' => 'Показать',
        'hide' => 'Спрятать',
      ],
    ];
    $form['example_select']['#ajax'] = [
      'callback' => '::ajaxBackupForever',
      'effect' => 'fade',
      'progress' => ['type' => 'throbber', 'message' => ""],
      'event' => 'change',
    ];
    $form['dop'] = [
      '#type' => 'details',
      '#title' => 'Дополнительно',
    ];
    $form['backup'] = [
      '#type' => 'submit',
      '#value' => 'Моя ajax-кнопка',
      '#attributes' => ['class' => ['btn', 'btn-xs', 'btn-danger']],
      '#ajax' => [
        'callback' => 'node_orders_ajax',
        'effect' => 'fade',
        'progress' => ['type' => 'throbber', 'message' => ""],
      ],
    ];
    $form['dop']['colorit-pole'] = [
      '#type' => 'color',
      '#title' => 'Цвет',
      '#prefix' => 'Это префикс',
      '#suffix' => 'Это суфикс',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Отправить',
    ];
    $form['submit2'] = [
      '#type' => 'submit',
      '#value' => 'Не отправить',
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
    dsm($form_state->getValues());
  }

}
