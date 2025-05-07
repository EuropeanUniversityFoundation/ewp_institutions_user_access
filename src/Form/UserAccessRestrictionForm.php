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

    $options = $this->optionsProvider->getOptions();

    if (empty($options)) {
      $form['message'] = [
        '#type' => 'markup',
        '#markup' => $this->t('No options available.'),
      ];

      return $form;
    }

    $form['target'] = [
      '#type' => 'select',
      '#title' => $this->t('Reference field'),
      '#required' => TRUE,
      '#options' => ($this->entity->isNew())
        ? $options
        : [$this->entity->id() => $this->entity->label()],
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
      '#open' => $this->entity->isNew(),
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

    $form['summary']['restricted_type'] = [
      '#type' => 'item',
      '#default_value' => $this->entity->getRestrictedEntityTypeId(),
    ];

    $form['summary']['restricted_bundle'] = [
      '#type' => 'value',
      '#default_value' => $this->entity->getRestrictedEntityBundleId(),
    ];

    $form['summary']['reference_field'] = [
      '#type' => 'value',
      '#default_value' => $this->entity->getReferenceFieldName(),
    ];

    $form['operations'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Operations'),
    ];

    $form['operations']['description'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Check global permissions to be restricted.'),
    ];

    $title_arg = $this->t('%user field value', [
      '%user' => $this->t('User Institution'),
    ]);

    $form['operations']['restrict_view'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Restrict access to %operation based on @user_hei', [
        '%operation' => $this->t('View any ...'),
        '@user_hei' => $title_arg,
      ]),
      '#default_value' => $this->entity->getRestrictView(),
    ];

    $form['operations']['restrict_edit'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Restrict access to %operation based on @user_hei', [
        '%operation' => $this->t('Edit any ...'),
        '@user_hei' => $title_arg,
      ]),
      '#default_value' => $this->entity->getRestrictEdit(),
    ];

    $form['operations']['restrict_delete'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Restrict access to %operation based on @user_hei', [
        '%operation' => $this->t('Delete any ...'),
        '@user_hei' => $title_arg,
      ]),
      '#default_value' => $this->entity->getRestrictDelete(),
    ];

    $form['operations']['restrict_other'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Restrict access to @operation based on @user_hei', [
        '@operation' => $this->t('any other operation'),
        '@user_hei' => $title_arg,
      ]),
      '#default_value' => $this->entity->getRestrictOther(),
    ];

    $form['strict_match'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Strict match'),
      '#description' => $this->t('%user values must match ALL @field values.', [
        '%user' => $this->t('User Institution'),
        '@field' => $this->t('referenced Institution'),
      ]),
      '#default_value' => $this->entity->getStrictMatch(),
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $this->entity->status(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    if (!array_key_exists('message', $form)) {
      $actions = parent::actions($form, $form_state);
    }
    return $actions ?? [];
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

    // This should not happen but account for it anyway.
    if ($this->entity->isNew() && UserAccessRestriction::load($selected)) {
      $message = $this->t('Already restricted.');
      $form_state->setErrorByName('target', $message);
    }
    else {
      $form['summary']['label']['#disabled'] = FALSE;
      $form['summary']['id']['#disabled'] = FALSE;

      $id = $selected ?? $this->entity->id();
      $options = $this->optionsProvider->getOptions();
      $label = $options[$selected] ?? $this->entity->label();
      $form_state->setValue('label', $label);
      $form_state->setValue('id', $id);

      $components = explode(self::SEPARATOR, $id, 3);
      $form_state->setValue('restricted_type', $components[0]);
      $form_state->setValue('restricted_bundle', $components[1]);
      $form_state->setValue('reference_field', $components[2]);
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
