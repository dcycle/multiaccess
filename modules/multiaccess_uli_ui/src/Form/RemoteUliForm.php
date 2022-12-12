<?php

namespace Drupal\multiaccess_uli_ui\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\multiaccess\Utilities\DependencyInjectionTrait;
use Drupal\multiaccess\Integration\IntegrationDestinationInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;

/**
 * A form to get unique login links to remote accounts.
 */
class RemoteUliForm extends FormBase {

  use DependencyInjectionTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'multiaccess_uli_ui_remote_uli_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];

    foreach ($this->destinations() as $destination) {
      $form['actions'][$destination->getIntegrationUuid()] = [
        '#type' => 'submit',
        '#value' => $this->destinationSubmitButtonValue($destination),
        '#button_type' => 'primary',
      ];
    }

    return $form;
  }

  /**
   * Get a submit button value from a destination.
   *
   * Note that the as far as I can tell, the button label is the only way
   * to distinguish between submit buttons; that's why I'm putting the
   * UUID in there.
   *
   * @param \Drupal\multiaccess\Integration\IntegrationDestinationInterface $destination
   *   A destination.
   *
   * @return string
   *   A translated button value.
   */
  public function destinationSubmitButtonValue(IntegrationDestinationInterface $destination) : string {
    return $this->t('Go to @l (@u)', [
      '@l' => $destination->getLabel(),
      '@u' => $destination->getIntegrationUuid(),
    ]);
  }

  /**
   * Get all destinations available to the current user.
   *
   * @return array
   *   Destinations available to the current user.
   */
  public function destinations() {
    $roles = $this->currentUser()->getRoles();

    return $this->integrationDestinationFactory()
      ->destinationsAvailableToRoles($roles);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $input = $form_state->getUserInput();

    $desiredDestinationLabel = $input['op'];

    foreach ($this->destinations() as $destination) {
      if ($this->destinationSubmitButtonValue($destination) == $desiredDestinationLabel) {
        try {
          $uli = $destination->uli($this->currentUser()->getEmail());

          // https://drupal.stackexchange.com/a/209245/13414
          $response = new TrustedRedirectResponse(Url::fromUri($uli)->toString());

          $metadata = $response->getCacheableMetadata();
          $metadata->setCacheMaxAge(0);
          $form_state->setResponse($response);
          return;
        }
        catch (\Throwable $t) {
          $this->drupalSetMessage($this->t('Sorry, could not redirect to @site because @message.', [
            '@site' => $desiredDestinationLabel,
            '@message' => $t->getMessage(),
          ]));
          return;
        }
      }
    }

    $this->drupalSetMessage($this->t('We could not determine a valid destination site from the label @l', [
      '@l' => $desiredDestinationLabel,
    ]));
  }

}
