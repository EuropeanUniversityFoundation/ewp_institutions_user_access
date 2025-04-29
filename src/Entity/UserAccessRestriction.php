<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\ewp_institutions_user_access\UserAccessRestrictionInterface;

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
 *     "list_builder" = "Drupal\ewp_institutions_user_access\UserAccessRestrictionListBuilder",
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
 *     "target_entity_type",
 *     "target_entity_bundle",
 *     "target_field_name",
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
   * The restriction target entity type.
   */
  protected $target_entity_type;

  /**
   * The restriction target entity bundle.
   */
  protected $target_entity_bundle;

  /**
   * The restriction target field name.
   */
  protected $target_field_name;

  /**
  * {@inheritdoc}
   */
  public function getTargetEntityTypeId(): ?string {
    return $this->target_entity_type;
  }

  /**
  * {@inheritdoc}
   */
  public function getTargetEntityBundleId(): ?string {
    return $this->target_entity_bundle;
  }

  /**
  * {@inheritdoc}
   */
  public function getTargetEntityFieldName(): ?string {
    return $this->target_field_name;
  }

}
