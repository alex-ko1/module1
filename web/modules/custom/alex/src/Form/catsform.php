<?php

namespace Drupal\alex\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class catsform extends FormBase
{

    public function getFormId()
    {
        return 'cats_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $form['cat_name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Your cat\'s name:'),
          '#placeholder' => $this->t('min length - 2, max - 32 symbols'),
          '#required' => true,
        ];
        $form['email'] = [
          '#title' => 'Your email:',
          '#type' => 'email',
          '#required' => true,
          '#placeholder' => $this->t('A-Z, a-z, -, _.'),
          '#ajax' => [
            'callback' => '::emailMessage',
            'event' => 'keyup',
          ],
        ];
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Add cat'),
          '#button_type' => 'primary',
          '#ajax' => [
            'callback' => '::setMessage',
          ],
        ];
        return $form;
    }
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        if (strlen($form_state->getValue('cat_name')) < 2) {
            $form_state->setErrorByName('cat_name', $this->t('Please enter a longer name.'));
        } elseif (strlen($form_state->getValue('cat_name')) >32) {
            $form_state->setErrorByName('cat_name', $this->t('Please enter a shorter name.'));
        }
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
    }
    public function setMessage(array $form, FormStateInterface $form_state)
    {
        $response = new AjaxResponse();
        if ($form_state->hasAnyErrors()) {
            foreach ($form_state->getErrors() as $errors_array) {
                $response->addCommand(new MessageCommand($errors_array));
            }
        } else {
              $response->addCommand(new MessageCommand('You adedd a cat!'));
        }
        \Drupal::messenger()->deleteAll();
        return $response;
    }
}