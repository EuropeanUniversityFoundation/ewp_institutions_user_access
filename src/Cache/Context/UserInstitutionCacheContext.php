<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access\Cache\Context;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CalculatedCacheContextInterface;
use Drupal\Core\Cache\Context\UserCacheContextBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\ewp_institutions_user\InstitutionUserBridge;

/**
 * Defines the UserInstitutionCacheContext service, for "per user hei" caching.
 *
 * Cache context ID: 'user.hei'.
 */
final class UserInstitutionCacheContext extends UserCacheContextBase implements CalculatedCacheContextInterface {

  /**
   * The account object.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Institution User Bridge service.
   *
   * @var \Drupal\ewp_institutions_user\InstitutionUserBridge
   */
  protected $bridge;

  /**
   * Constructs a new UserCacheContextBase class.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\ewp_institutions_user\InstitutionUserBridge $bridge
   *   The Institution User Bridge service.
   */
  public function __construct(
    AccountInterface $user,
    EntityTypeManagerInterface $entityTypeManager,
    InstitutionUserBridge $bridge,
  ) {
    parent::__construct($user);
    $this->entityTypeManager = $entityTypeManager;
    $this->bridge = $bridge;
  }

  /**
   * {@inheritdoc}
   */
  public static function getLabel(): string {
    return (string) t('User Institution');
  }

  /**
   * {@inheritdoc}
   */
  public function getContext($hei = NULL): string {
    $user = $this->entityTypeManager
      ->getStorage('user')
      ->load($this->user->id());

    $hei_list = $this->bridge->getUserInstitution($user);
    ksort($hei_list);
    $hei_ids = array_map('strval', array_keys($hei_list));

    if ($hei === NULL) {
      return implode(',', $hei_ids);
    }
    else {
      return (in_array($hei, $hei_ids) ? 'true' : 'false');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata($hei = NULL): CacheableMetadata {
    $metadata = new CacheableMetadata();
    $metadata->addCacheTags(['user:' . $this->user->id()]);
    return $metadata;
  }

}
