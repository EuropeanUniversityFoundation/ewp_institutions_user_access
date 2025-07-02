<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

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
 *     "restrict_add",
 *     "restrict_add_match_all",
 *     "restrict_view",
 *     "restrict_view_match_all",
 *     "restrict_edit",
 *     "restrict_edit_match_all",
 *     "restrict_delete",
 *     "restrict_delete_match_all",
 *     "restrict_other",
 *     "restrict_other_match_all",
 *   },
 * )
 */
final class UserAccessRestriction extends ConfigEntityBase implements UserAccessRestrictionInterface {

  /**
   * The restriction ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The restriction label.
   *
   * @var string
   */
  protected $label;

  /**
   * The entity type to be restricted.
   *
   * @var string|null
   */
  protected $restricted_type;

  /**
   * The entity bundle to be restricted.
   *
   * @var string|null
   */
  protected $restricted_bundle;

  /**
   * The reference field used to calculate restrictions.
   *
   * @var string|null
   */
  protected $reference_field;

  /**
   * Whether to restrict the 'add' operation.
   *
   * @var bool
   */
  protected $restrict_add = FALSE;

  /**
   * Whether to restrict the 'add' operation (match all references).
   *
   * @var bool
   */
  protected $restrict_add_match_all = FALSE;

  /**
   * Whether to restrict the 'view' operation.
   *
   * @var bool
   */
  protected $restrict_view = FALSE;

  /**
   * Whether to restrict the 'view' operation (match all references).
   *
   * @var bool
   */
  protected $restrict_view_match_all = FALSE;

  /**
   * Whether to restrict the 'edit' operation.
   *
   * @var bool
   */
  protected $restrict_edit = FALSE;

  /**
   * Whether to restrict the 'edit' operation (match all references).
   *
   * @var bool
   */
  protected $restrict_edit_match_all = FALSE;

  /**
   * Whether to restrict the 'delete' operation.
   *
   * @var bool
   */
  protected $restrict_delete = FALSE;

  /**
   * Whether to restrict the 'delete' operation (match all references).
   *
   * @var bool
   */
  protected $restrict_delete_match_all = FALSE;

  /**
   * Whether to restrict any other operation.
   *
   * @var bool
   */
  protected $restrict_other = FALSE;

  /**
   * Whether to restrict any other operation (match all references).
   *
   * @var bool
   */
  protected $restrict_other_match_all = FALSE;

  /**
   * {@inheritdoc}
   */
  public function getRestrictedEntityTypeId(): ?string {
    return $this->restricted_type ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getRestrictedEntityBundleId(): ?string {
    return $this->restricted_bundle ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getReferenceFieldName(): ?string {
    return $this->reference_field ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getRestrictAdd(): bool {
    return (bool) $this->restrict_add;
  }

  /**
   * {@inheritdoc}
   */
  public function getRestrictAddMatchAll(): bool {
    return (bool) $this->restrict_add_match_all;
  }

  /**
   * {@inheritdoc}
   */
  public function getRestrictView(): bool {
    return (bool) $this->restrict_view;
  }

  /**
   * {@inheritdoc}
   */
  public function getRestrictViewMatchAll(): bool {
    return (bool) $this->restrict_view_match_all;
  }

  /**
   * {@inheritdoc}
   */
  public function getRestrictEdit(): bool {
    return (bool) $this->restrict_edit;
  }

  /**
   * {@inheritdoc}
   */
  public function getRestrictEditMatchAll(): bool {
    return (bool) $this->restrict_edit_match_all;
  }

  /**
   * {@inheritdoc}
   */
  public function getRestrictDelete(): bool {
    return (bool) $this->restrict_delete;
  }

  /**
   * {@inheritdoc}
   */
  public function getRestrictDeleteMatchAll(): bool {
    return (bool) $this->restrict_delete_match_all;
  }

  /**
   * {@inheritdoc}
   */
  public function getRestrictOther(): bool {
    return (bool) $this->restrict_other;
  }

  /**
   * {@inheritdoc}
   */
  public function getRestrictOtherMatchAll(): bool {
    return (bool) $this->restrict_other_match_all;
  }

}
