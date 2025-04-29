<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ewp_institutions_user_access\Entity\UserAccessRestriction;
use Drupal\ewp_institutions_user_access\UserAccessRestrictionOptionsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * User access restriction form.
 */
final class UserAccessRestrictionForm extends EntityForm {

  const SEPARATOR = UserAccessRestrictionOptionsInterface::SEPARATOR;

  /**
   * The user access restriction options provider.
   *
   * @var \Drupal\ewp_institutions_user_access\UserAccessRestrictionOptionsInterface
   */
  protected $optionsProvider;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->optionsProvider = $container->get('ewp_institutions_user_access.options');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    $form['target'] = [
      '#type' => 'select',
      '#title' => $this->t('Reference field'),
      '#required' => TRUE,
      '#options' => ($this->entity->isNew())
        ? $this->optionsProvider->getOptions()
        : [$this->entity->id()],
      '#default_value' => $this->entity->id(),
      '#ajax' => [
        'callback' => '::updateSummary',
        'disable-refocus' => TRUE,
        'event' => 'change',
        'wrapper' => 'form-summary',
      ],
      '#disabled' => !$this->entity->isNew(),
    ];

    $form['summary'] = [
      '#type' => 'details',
      '#title' => $this->t('Summary'),
      '#prefix' => '<div id="form-summary">',
      '#suffix' => '</div>',
      '#open' => TRUE,
    ];

    $form['summary']['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->label(),
      '#required' => TRUE,
      '#disabled' => TRUE,
    ];

    $form['summary']['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#machine_name' => [
        'exists' => [UserAccessRestriction::class, 'load'],
      ],
      '#disabled' => TRUE,
    ];

    $form['summary']['target_entity_type'] = [
      '#type' => 'item',
      '#default_value' => $this->entity->getTargetEntityTypeId(),
    ];

    $form['summary']['target_entity_bundle'] = [
      '#type' => 'value',
      '#default_value' => $this->entity->getTargetEntityBundleId(),
    ];

    $form['summary']['target_field_name'] = [
      '#type' => 'value',
      '#default_value' => $this->entity->getTargetEntityFieldName(),
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $this->entity->status(),
    ];

    return $form;
  }

  /**
   * Update the values in the summary fields.
   */
  public function updateSummary(array $form, FormStateInterface $form_state) {
    if ($this->entity->isNew()) {
      $options = $this->optionsProvider->getOptions();
      $selected = $form_state->getValue('target');

      $form['summary']['label']['#value'] = $options[$selected];
      $form['summary']['id']['#value'] = $selected;
    }

    return $form['summary'];
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $selected = $form_state->getValue('target');

    if ($this->entity->isNew() && UserAccessRestriction::load($selected)) {
      $form_state->setErrorByName('target', $this->t('Already restricted.'));
    }
    else {
      $form['summary']['label']['#disabled'] = FALSE;
      $form['summary']['id']['#disabled'] = FALSE;

      $options = $this->optionsProvider->getOptions();
      $form_state->setValue('label', $options[$selected]);
      $form_state->setValue('id', $selected);

      $components = explode(self::SEPARATOR, $selected, 3);
      $form_state->setValue('target_entity_type', $components[0]);
      $form_state->setValue('target_entity_bundle', $components[1]);
      $form_state->setValue('target_field_name', $components[2]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);
    $args = ['%label' => $this->entity->label()];
    $this->messenger()->addStatus(
      match($result) {
        \SAVED_NEW => $this->t('Created new restriction for %label.', $args),
        \SAVED_UPDATED => $this->t('Updated restriction for %label.', $args),
      }
    );
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    return $result;
  }

}
