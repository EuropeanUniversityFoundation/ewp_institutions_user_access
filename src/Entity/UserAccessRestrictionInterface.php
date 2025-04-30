<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining an user access restriction entity type.
 */
interface UserAccessRestrictionInterface extends ConfigEntityInterface {

  /**
   * Gets the ID of the type of the target entity.
   *
   * @return string|null
   *   The target entity type ID.
   */
  public function getTargetEntityTypeId(): ?string;

  /**
   * Gets the ID of the bundle of the target entity.
   *
   * @return string|null
   *   The target entity bundle ID.
   */
  public function getTargetEntityBundleId(): ?string;

  /**
   * Gets the name of the reference field on the target entity.
   *
   * @return string|null
   *   The target entity reference field name.
   */
  public function getTargetEntityFieldName(): ?string;

}
