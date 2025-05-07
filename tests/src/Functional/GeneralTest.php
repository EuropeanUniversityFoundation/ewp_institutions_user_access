<?php

namespace Drupal\Tests\ewp_institutions_user_access\Functional;

/**
 * Test whether the module can be installed and uninstalled safely.
 *
 * @group ewp_institutions_user_access
 */
class GeneralTest extends UserAccessTestBase {

  /**
   * Tests if installing the module won't break the site.
   */
  public function testInstallation() {
    $session = $this->assertSession();
    $this->drupalGet('<front>');
    // Ensure the status code is success:
    $session->statusCodeEquals(200);
    // Ensure the correct test page is loaded as front page:
    $session->pageTextContains('Test page text.');
  }

  /**
   * Tests if uninstalling the module won't break the site.
   */
  public function testUninstallation() {
    $this->drupalLogin($this->adminUser);
    // Go to uninstallation page and uninstall ewp_institutions_user_access:
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('/admin/modules/uninstall');
    $session->statusCodeEquals(200);
    $page->checkField('edit-uninstall-ewp-institutions-user-access');
    $page->pressButton('edit-submit');
    $session->statusCodeEquals(200);
    // Confirm uninstall:
    $page->pressButton('edit-submit');
    $session->statusCodeEquals(200);
    $session->pageTextContains('The selected modules have been uninstalled.');
    // Retest the frontpage:
    $this->drupalGet('<front>');
    // Ensure the status code is success:
    $session->statusCodeEquals(200);
    // Ensure the correct test page is loaded as front page:
    $session->pageTextContains('Test page text.');
  }

}
