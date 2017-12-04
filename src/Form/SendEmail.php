<?php

namespace Drupal\node_orders\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\node_orders\Controller\Group;
use Drupal\user\Entity\User;

/**
 * SimpleForm.
 */
class SendEmail extends FormBase {

  /**
   * Отправка E-mail заказчику услуги (тур. фирме или лицу).
   */
  public function ajaxSendEmail(array &$form, &$form_state) {
    $response = new AjaxResponse();
    $uid = \Drupal::currentUser()->id();
    $user = User::load($uid);
    if ($user->hasPermission('orders-form')) {
      $node = $form_state->node_orders;
      $node_usluga = $node->field_orders_ref_activity->entity;
      $node_customer = $node->field_orders_customer->entity;
      $num = $node->id();
      $order = $node_usluga->title->value;
      $date = $node->field_orders_date->value;
      $type = $node->field_orders_type->value;
      $people = $node->field_orders_group->value;
      $fakt_cost = $node->field_orders_cost->value;
      $visitors = $node->field_orders_visitor;
      $vis = Group::collectionItems($visitors);
      $kategoria = [];
      foreach ($vis as $key => $value) {
        if ($value['kategory'] == 'adult') {
          $kategoria[] = "взрослые";
        }
        if ($value['kategory'] == 'student') {
          $kategoria[] = "студенты";
        }
        if ($value['kategory'] == 'school') {
          $kategoria[] = "школьники";
        }
        if ($value['kategory'] == 'baby') {
          $kategoria[] = "дошкольники";
        }
        if ($value['kategory'] == 'old') {
          $kategoria[] = "пенсионеры";
        }
        if ($value['kategory'] == 'military') {
          $kategoria[] = "военнослужащие";
        }
        if ($value['kategory'] == 'museum') {
          $kategoria[] = "музейные работники";
        }
        if ($value['kategory'] == 'lgotniki') {
          $kategoria[] = "льготники";
        }
        if ($value['kategory'] == 'guest') {
          $kategoria[] = "гости";
        }
      }
      $kategories = implode(", ", $kategoria);
      $email = FALSE;
      if ($type == 'turist') {
        if ($node_customer->field_customer_email->value) {
          $email = $node_customer->field_customer_email->value;
        }
        else {
          $email = $node->field_orders_email->value;
        }
      }
      else {
        $email = $node->field_orders_email->value;
      }
      $timestamp = strtotime($date);
      $date = format_date($timestamp, 'custom', 'j F Y (время - H:i)');
      $otvet = "";
      $otvet .= "E-mail отправлен на адрес: ";
      $otvet .= $email;
      // $otvet .= "<br />" . format_date(time(), 'custom', 'H:i:s');
      $to = 'zakaz@semenkovo.ru,' . $email;
      $subject = "Заявка на посещение Музея \"Семенково\"";
      $message = "Здравствуйте!

  Ваша заявка №$num на музейную услугу \"$order\" на $date создана.
  Численность группы - $people человек. Категория посетителей: $kategories.
  Стоимость услуги - " . number_format($fakt_cost, 0, ",", " ") . " руб.


  -----
  С уважением,
  экскурсионный отдел Музея \"Семёнково\".

  По всем возникающим вопросам звоните по телефону: (8172) 21-01-90.";
      $headers = "Content-type: text/plain; charset=UTF-8\r\n";
      $headers .= 'From: zakaz@semenkovo.ru' . "\r\n";
      $mail = mail($to, $subject, $message, $headers);
      if ($mail) {
        $response->addCommand(new HtmlCommand("#button-send-email-form .otvet", $otvet));
      }
      else {
        $response->addCommand(new HtmlCommand("#button-send-email-form .otvet", "E-mail отправить не удалось."));
      }
    }
    else {
      $response->addCommand(new HtmlCommand("#button-send-email-form .otvet", "Доступ запрещен"));
    }
    return $response;
  }

  /**
   * Build the simple form.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $extra = NULL) {
    $node = $extra;
    $form_state->node_orders = $node;
    $form_state->setCached(FALSE);
    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Отправить E-mail',
      '#attributes' => ['class' => ['btn', 'btn-xs', 'btn-success']],
      '#suffix' => '<div class="otvet"></div>',
      '#ajax' => [
        'callback' => '::ajaxSendEmail',
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
    return 'button_send_email_form';
  }

  /**
   * Implements a form submit handler.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
  }

}
