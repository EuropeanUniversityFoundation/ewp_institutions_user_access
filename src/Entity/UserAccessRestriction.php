<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\ewp_institutions_user_access\Entity\UserAccessRestrictionInterface;

/**
 * Defines the user access restriction entity type.
 *
 * @ConfigEntityType(
 *   id = "user_access_restriction",
 *   label = @Translation("User access restriction"),
 *   label_collection = @Translation("User access restrictions"),
 *   label_singular = @Translation("user access restriction"),
 *   label_plural = @Translation("user access restrictions"),
 *   label_count = @PluralTranslation(
 *     singular = "@count user access restriction",
 *     plural = "@count user access restrictions",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\ewp_institutions_user_access\Entity\UserAccessRestrictionListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ewp_institutions_user_access\Form\UserAccessRestrictionForm",
 *       "edit" = "Drupal\ewp_institutions_user_access\Form\UserAccessRestrictionForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *   },
 *   config_prefix = "user_access_restriction",
 *   admin_permission = "administer user_access_restriction",
 *   links = {
 *     "collection" = "/admin/structure/user-access-restriction",
 *     "add-form" = "/admin/structure/user-access-restriction/add",
 *     "edit-form" = "/admin/structure/user-access-restriction/{user_access_restriction}",
 *     "delete-form" = "/admin/structure/user-access-restriction/{user_access_restriction}/delete",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "restricted_type",
 *     "restricted_bundle",
 *     "reference_field",
 *     "restrict_view",
 *     "restrict_edit",
 *     "restrict_delete",
 *     "restrict_other",
 *     "strict_match",
 *   },
 * )
 */
final class UserAccessRestriction extends ConfigEntityBase implements UserAccessRestrictionInterface {

  /**
   * The restriction ID.
   */
  protected string $id;

  /**
   * The restriction label.
   */
  protected string $label;

  /**
   * The entity type to be restricted.
   */
  protected $restricted_type;

  /**
   * The entity bundle to be restricted.
   */
  protected $restricted_bundle;

  /**
   * The reference field used to calculate restrictions.
   */
  protected $reference_field;

  /**
   * Whether to restrict the 'view' operation.
   */
  protected $restrict_view;

  /**
   * Whether to restrict the 'edit' operation.
   */
  protected $restrict_edit;

  /**
   * Whether to restrict the 'delete' operation.
   */
  protected $restrict_delete;

  /**
   * Whether to restrict any other operation.
   */
  protected $restrict_other;

  /**
   * Whether all items must match as opposed to only one item.
   */
  protected $strict_match;

  /**
  * {@inheritdoc}
   */
  public function getRestrictedEntityTypeId(): ?string {
    return $this->restricted_type;
  }

  /**
  * {@inheritdoc}
   */
  public function getRestrictedEntityBundleId(): ?string {
    return $this->restricted_bundle;
  }

  /**
  * {@inheritdoc}
   */
  public function getReferenceFieldName(): ?string {
    return $this->reference_field;
  }

  /**
  * {@inheritdoc}
   */
  public function getRestrictView(): bool {
    return (bool) $this->restrict_view ?? FALSE;
  }

  /**
  * {@inheritdoc}
   */
  public function getRestrictEdit(): bool {
    return (bool) $this->restrict_edit ?? FALSE;
  }

  /**
  * {@inheritdoc}
   */
  public function getRestrictDelete(): bool {
    return (bool) $this->restrict_delete ?? FALSE;
  }

  /**
  * {@inheritdoc}
   */
  public function getRestrictOther(): bool {
    return (bool) $this->restrict_other ?? FALSE;
  }

  /**
  * {@inheritdoc}
   */
  public function getStrictMatch(): bool {
    return (bool) $this->strict_match ?? FALSE;
  }

}
