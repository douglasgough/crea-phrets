<?php

namespace Drupal\crearets\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for CREARets module.
 */
class CREARetsConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'crearets_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['crearets.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('crearets.settings');

    $form['crearets_username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#default_value' => $config->get('username'),
    ];

    $form['crearets_password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#default_value' => $config->get('password'),
    ];

    $form['crearets_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL'),
      '#default_value' => $config->get('url'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('crearets.settings');

    $config->set('username', $form_state->getValue('crearets_username'))
      ->set('password', $form_state->getValue('crearets_password'))
      ->set('url', $form_state->getValue('crearets_url'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
