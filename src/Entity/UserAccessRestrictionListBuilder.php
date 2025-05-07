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
    $header['match_mode'] = $this->t('Match mode');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ewp_institutions_user_access\UserAccessRestrictionInterface $entity */
    $restricted = [];

    if ($entity->getRestrictView()) {
      $restricted[] = 'view';
    }

    if ($entity->getRestrictEdit()) {
      $restricted[] = 'edit';
    }

    if ($entity->getRestrictDelete()) {
      $restricted[] = 'delete';
    }

    if ($entity->getRestrictOther()) {
      $restricted[] = 'other';
    }

    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['restricted'] = implode(', ', $restricted);
    $row['match_mode'] = ($entity->getStrictMatch())
      ? $this->t('Strict (match all)')
      : $this->t('Loose (match any)');
    $row['status'] = $entity->status()
      ? $this->t('Enabled')
      : $this->t('Disabled');

    return $row + parent::buildRow($entity);
  }

}
