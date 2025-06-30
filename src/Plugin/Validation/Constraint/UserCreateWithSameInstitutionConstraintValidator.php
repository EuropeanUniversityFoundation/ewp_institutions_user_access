<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\ewp_institutions_user\InstitutionUserBridge;
use Drupal\ewp_institutions_user_access\UserAccessManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the UserCreateWithSameInstitution constraint.
 */
final class UserCreateWithSameInstitutionConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The user access manager.
   *
   * @var \Drupal\ewp_institutions_user_access\UserAccessManagerInterface
   */
  protected $accessManager;

  /**
   * Constructs the object.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A proxied implementation of AccountInterface.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\ewp_institutions_user_access\UserAccessManagerInterface $access_manager
   *   The user access manager.
   */
  public function __construct(
    AccountProxyInterface $current_user,
    EntityTypeManagerInterface $entity_type_manager,
    UserAccessManagerInterface $access_manager,
  ) {
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
    $this->accessManager = $access_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('ewp_institutions_user_access.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $entity, Constraint $constraint): void {
    if (!$entity instanceof EntityInterface) {
      throw new \InvalidArgumentException(
        sprintf('The validated value must be instance of \Drupal\Core\Entity\EntityInterface, %s was given.', get_debug_type($entity))
      );
    }

    $bypass = $this->currentUser
      ->hasPermission('bypass user access restrictions');

    // This only applies on entity creation.
    if ($entity->isNew() && !$bypass) {
      $restrictions = $this->accessManager->getEntityRestrictions($entity);

      foreach ($restrictions as $restriction) {
        if ($restriction->getRestrictAdd()) {
          $user = $this->entityTypeManager
            ->getStorage('user')
            ->load($this->currentUser->id());

          $user_ref = $this->accessManager
            ->getSortedTargetId($user, InstitutionUserBridge::BASE_FIELD);

          $reference_field = $restriction->getReferenceFieldName();

          $ref = $this->accessManager
            ->getSortedTargetId($entity, $reference_field);

          $match_all = $restriction->getRestrictAddMatchAll();

          $match = $this->accessManager
            ->referenceValuesMatch($user_ref, $ref, $match_all);

          if (!$match) {
            $this->context->buildViolation($constraint->message)
              ->atPath($reference_field)
              ->addViolation();
          }
        }
      }
    }
  }

}
