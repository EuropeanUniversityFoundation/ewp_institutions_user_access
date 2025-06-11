<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access\Entity;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of user access restrictions.
 */
final class UserAccessRestrictionListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['restricted'] = $this->t('Restricted');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ewp_institutions_user_access\Entity\UserAccessRestrictionInterface $entity */
    $restricted = [];

    if ($entity->getRestrictView()) {
      $operation = 'view';
      if ($entity->getRestrictViewMatchAll()) {
        $operation .= ' (match all)';
      }
      $restricted[] = $operation;
    }

    if ($entity->getRestrictEdit()) {
      $operation = 'edit';
      if ($entity->getRestrictEditMatchAll()) {
        $operation .= ' (match all)';
      }
      $restricted[] = $operation;
    }

    if ($entity->getRestrictDelete()) {
      $operation = 'delete';
      if ($entity->getRestrictDeleteMatchAll()) {
        $operation .= ' (match all)';
      }
      $restricted[] = $operation;
    }

    if ($entity->getRestrictOther()) {
      $operation = 'other';
      if ($entity->getRestrictOtherMatchAll()) {
        $operation .= ' (match all)';
      }
      $restricted[] = $operation;
    }

    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['restricted'] = implode(', ', $restricted);
    $row['status'] = $entity->status()
      ? $this->t('Enabled')
      : $this->t('Disabled');

    return $row + parent::buildRow($entity);
  }

}
