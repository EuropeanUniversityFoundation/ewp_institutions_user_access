<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access\Plugin\Validation\Constraint;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

/**
 * Provides an UserCreateWithSameInstitution constraint.
 */
#[Constraint(
  id: 'UserCreateWithSameInstitution',
  label: new TranslatableMarkup('User create with same Institution', [], ['context' => 'Validation'])
)]
final class UserCreateWithSameInstitutionConstraint extends SymfonyConstraint {

  /**
   * The error message.
   *
   * This message is displayed if the entity being created does not reference
   * the same Institution as the author does via the 'user_institution' field.
   *
   * @var string
   */
  public $message = 'Must reference the same Institution as the author.';

}
