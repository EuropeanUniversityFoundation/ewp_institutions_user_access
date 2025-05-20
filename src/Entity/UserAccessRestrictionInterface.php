<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining an user access restriction entity type.
 */
interface UserAccessRestrictionInterface extends ConfigEntityInterface {

  /**
   * Gets the entity type to be restricted.
   *
   * @return string|null
   *   The restricted entity type ID.
   */
  public function getRestrictedEntityTypeId(): ?string;

  /**
   * Gets the entity bundle to be restricted.
   *
   * @return string|null
   *   The restricted entity bundle ID.
   */
  public function getRestrictedEntityBundleId(): ?string;

  /**
   * Gets entity reference field user to calculate restrictions.
   *
   * @return string|null
   *   The entity reference field name on the restricted entity type and bundle.
   */
  public function getReferenceFieldName(): ?string;

  /**
   * Whether to restrict the 'view' operation.
   *
   * @return bool
   *   TRUE if the 'view' operation should be restricted, FALSE otherwise.
   */
  public function getRestrictView(): bool;

  /**
   * Whether to restrict the 'view' operation (match all references).
   *
   * @return bool
   *   TRUE if the 'view' operation should be restricted, FALSE otherwise.
   */
  public function getRestrictViewMatchAll(): bool;

  /**
   * Whether to restrict the 'edit' operation.
   *
   * @return bool
   *   TRUE if the 'edit' operation should be restricted, FALSE otherwise.
   */
  public function getRestrictEdit(): bool;

  /**
   * Whether to restrict the 'edit' operation (match all references).
   *
   * @return bool
   *   TRUE if the 'edit' operation should be restricted, FALSE otherwise.
   */
  public function getRestrictEditMatchAll(): bool;

  /**
   * Whether to restrict the 'delete' operation.
   *
   * @return bool
   *   TRUE if the 'delete' operation should be restricted, FALSE otherwise.
   */
  public function getRestrictDelete(): bool;

  /**
   * Whether to restrict the 'delete' operation (match all references).
   *
   * @return bool
   *   TRUE if the 'delete' operation should be restricted, FALSE otherwise.
   */
  public function getRestrictDeleteMatchAll(): bool;

  /**
   * Whether to restrict any other operation.
   *
   * @return bool
   *   TRUE if any other operation should be restricted, FALSE otherwise.
   */
  public function getRestrictOther(): bool;

  /**
   * Whether to restrict any other operation (match all references).
   *
   * @return bool
   *   TRUE if any other operation should be restricted, FALSE otherwise.
   */
  public function getRestrictOtherMatchAll(): bool;

}
