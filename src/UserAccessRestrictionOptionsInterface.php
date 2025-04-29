<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access;

/**
 * Provides an interface defining a user access restriction options provider.
 */
interface UserAccessRestrictionOptionsInterface {

  const SEPARATOR = '__';

  /**
   * Builds and returns a list of options.
   *
   * @return array
   */
  public function getOptions(): array;

}
