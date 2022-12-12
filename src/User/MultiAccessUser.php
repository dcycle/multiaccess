<?php

namespace Drupal\multiaccess\User;

use Drupal\user\UserInterface;

/**
 * A Drupal user.
 */
class MultiAccessUser implements MultiAccessUserInterface {

  /**
   * The user object.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $drupalUser;

  /**
   * {@inheritdoc}
   */
  public function getRoles() : array {
    return $this->drupalUser->getRoles();
  }

  /**
   * {@inheritdoc}
   */
  public function email() : string {
    return $this->drupalUser->getEmail();
  }

  /**
   * Make sure the user has these roles and save it.
   *
   * @param array $roles
   *   Array of roles.
   */
  public function addRolesAndSave(array $roles) {
    $existingRoles = $this->getRoles();
    $mustSave = FALSE;

    foreach ($roles as $role) {
      if (!in_array($role, $existingRoles)) {
        $mustSave = TRUE;
        $this->drupalUser->addRole($role);
      }
    }

    if ($mustSave) {
      $this->drupalUser->save();
    }
  }

  /**
   * Constructor.
   *
   * @param \Drupal\user\UserInterface $drupalUser
   *   The Drupal user object.
   * @param array $roles
   *   Roles this user must have.
   */
  public function __construct(UserInterface $drupalUser, array $roles = []) {
    $this->drupalUser = $drupalUser;
    $this->addRolesAndSave($roles);
  }

  /**
   * {@inheritdoc}
   */
  public function getLoginLink() : string {
    return user_pass_reset_url($this->drupalUser) . '/login';
  }

}
