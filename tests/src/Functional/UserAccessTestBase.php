<?php

declare(strict_types=1);

namespace Drupal\Tests\ewp_institutions_user_access\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\field\Traits\EntityReferenceFieldCreationTrait;

/**
 * This is a base class for test setup.
 *
 * @group ewp_institutions_user_access
 */
abstract class UserAccessTestBase extends BrowserTestBase {

  use EntityReferenceFieldCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * An admin user with all permissions.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $adminUser;

  /**
   * An authenticated user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $authenticatedUser;

  /**
   * A user with the permission to bypass restrictions.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $bypassPermissionUser;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'test_page_test',
    'user',
    'field',
    'field_ui',
    'views',
    'ewp_core',
    'ewp_flexible_address',
    'ewp_phone_number',
    'ewp_contact',
    'ewp_institutions',
    'ewp_institutions_user',
    'ewp_institutions_user_access',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->createContentType(['type' => 'article']);
    $this->config('system.site')->set('page.front', '/test-page')->save();

    // Create the adminUser:
    $this->adminUser = $this->drupalCreateUser([]);
    $this->adminUser->addRole($this->createAdminRole('admin', 'admin'));
    $this->adminUser->save();

    // Create the bypassPermissionUser:
    $this->bypassPermissionUser = $this->createUser([
      'bypass user access restrictions',
    ]);

    // Create the authenticatedUser:
    $this->authenticatedUser = $this->createUser([]);
  }

}
