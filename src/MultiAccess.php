<?php

namespace Drupal\multiaccess;

use Drupal\multiaccess\Integration\IntegrationInterface;
use Drupal\multiaccess\Integration\Integration;
use Drupal\multiaccess\Integration\IntegrationSource;
use Drupal\multiaccess\Integration\IntegrationDestination;
use Drupal\multiaccess\RoleMapping\RoleMapping;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\multiaccess\Utilities\DependencyInjectionTrait;
use Drupal\user\Entity\User;
use Drupal\Component\Utility\Crypt;
use Drupal\multiaccess\User\MultiAccessUserInterface;
use Drupal\multiaccess\User\MultiAccessUser;
use Drupal\multiaccess\User\MultiAccessUserDoesNotExistException;

/**
 * The MultiAccess app.
 */
class MultiAccess implements MultiAccessInterface {

  use StringTranslationTrait;
  use DependencyInjectionTrait;

  /**
   * {@inheritdoc}
   */
  public function newIntegration(string $label, string $public, string $internal, array $role_mapping_array) : IntegrationInterface {

    $uuid = $this->uuid()->generate();
    $destinationKeyPair = $this->keyPairFactory()->new();
    $sourceKeyPair = $this->keyPairFactory()->new();

    $source = new IntegrationSource(
      uuid: $uuid,
      roleMapping: new RoleMapping($role_mapping_array),
      remotePublicKey: $sourceKeyPair->publicKey(),
      localPrivateKey: $destinationKeyPair->privateKey(),
      publicUrl: $public,
    );
    $destination = new IntegrationDestination(
      uuid: $uuid,
      url: $internal,
      remotePublicKey: $destinationKeyPair->publicKey(),
      localPrivateKey: $sourceKeyPair->privateKey(),
      label: $label,
      accessibleToRoles: array_keys($role_mapping_array),
    );

    return new Integration(
      source: $source,
      destination: $destination,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getUser(string $email, array $roles) : MultiAccessUserInterface {
    $this->validateEmail($email);

    try {
      return $this->getExistingUser($email, $roles);
    }
    catch (MultiAccessUserDoesNotExistException $e) {
      $newUserEntity = User::create([
        'name' => $this->emailToNonexistentUsername($email),
        'mail' => $email,
        'pass' => $this->generatePassword(),
      ]);
      $newUserEntity->save();

      return $this->getExistingUser($email, $roles);
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getUserOneEmail() : string {
    return $this->entityTypeManager()->getStorage('user')->load(1)->getEmail();
  }

  /**
   * {@inheritdoc}
   */
  public function getExistingUser(string $email, array $roles = []) : MultiAccessUserInterface {
    $users = $this->entityTypeManager()
      ->getStorage('user')
      ->loadByProperties([
        'mail' => $email,
      ]);

    if ($users) {
      return new MultiAccessUser(reset($users), $roles);
    }

    throw new MultiAccessUserDoesNotExistException($email);
  }

  /**
   * Generate a relatively secure password.
   *
   * @return string
   *   A relatively secure password.
   */
  public function generatePassword() : string {
    return Crypt::hashBase64(random_bytes(128));
  }

  /**
   * {@inheritdoc}
   */
  public function httpPost(string $url, array $post) : string {
    // https://www.agileana.com/blog/how-to-perform-http-requests-in-drupal/
    $method = 'POST';
    $options = [
      'form_params' => $post,
    ];

    $client = $this->httpClient();

    $response = $client->request($method, $url, $options);

    $code = $response->getStatusCode();

    if ($code == 200) {
      return $response->getBody()->getContents();
    }

    throw new \Exception('Response from POST call is not 200, it is ' . $code);
  }

  /**
   * Given an email, generate a username which does not exist.
   *
   * @param string $email
   *   An email address.
   *
   * @return string
   *   A relatively secure password.
   */
  public function emailToNonexistentUsername(string $email) : string {
    $baseCandidate = $candidate = $this->extractNameFromEmail($email);
    $i = 0;

    do {
      if (!$this->usernameExists($candidate)) {
        return $candidate;
      }
      $candidate = $baseCandidate . ++$i;
    } while (TRUE);
  }

  /**
   * Throw an \Exception if email address is invalid.
   *
   * @param string $email
   *   An email.
   */
  public function validateEmail(string $email) {
    if (!$this->emailValidator()->isValid($email)) {
      throw new \Exception($email . ' does not seem to be valid.');
    }
  }

  /**
   * Check if a username exists.
   *
   * @param string $username
   *   A username.
   *
   * @return bool
   *   TRUE if the user exists.
   */
  public function usernameExists(string $username) : bool {
    if (!$username) {
      throw new \Exception('Usernames cannot be empty');
    }

    $ids = $this->entityQuery('user')
      ->condition('name', $username)
      ->accessCheck(FALSE)
      ->range(0, 1)
      ->execute();

    return !empty($ids);
  }

  /**
   * Extracts hello from hello@world.
   *
   * @param string $email
   *   An email address hello@world.
   *
   * @return string
   *   Only the first part, hello.
   */
  public function extractNameFromEmail(string $email) : string {
    $this->validateEmail($email);

    $parts = explode("@", $email);

    if (count($parts) != 2) {
      throw new \Exception('Email should have only occurrence of @');
    }

    return array_shift($parts);
  }

  /**
   * Extract a domain from a URL.
   *
   * @param string $url
   *   A URL such as http://whatever:1234/a/b/c.
   *
   * @return string
   *   A domain such as whatever.
   */
  public function extractDomainFromUrl(string $url) : string {
    $matches = [];

    if (preg_match('/^[^\/]*\/\/([^:\/]*).*$/', $url, $matches)) {
      return $matches[1];
    }

    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function selftest(callable $callback) : bool {
    $result = TRUE;
    try {
      $callback('ok', $this->t('starting test'));

      $integrations = $this->integrationDestinationFactory()->allFromUnversionedSettingsFile();

      // Because of https://www.drupal.org/project/drupal/issues/2273889 I do
      // not trust format_plural().
      $callback('ok', $this->t('there is (are) @count destination integration(s).', [
        '@count' => count($integrations),
      ]));

      foreach ($integrations as $integration) {
        $uuid = $integration->getIntegrationUuid();
        $url = $integration->getUrl();
        $label = $integration->getLabel();

        $callback('ok', $this->t('Testing integration @uuid', [
          '@uuid' => $uuid,
        ]));
        $callback('ok', $this->t('Its URL is @url', [
          '@url' => $url,
        ]));
        $callback('ok', $this->t('Its label is @label', [
          '@label' => $label,
        ]));
        $callback('ok', $this->t('Ping test.'));
        $integration->ping();
        $callback('ok', $this->t('Ping test ok.'));
      }
    }
    catch (\Throwable $t) {
      $callback('error', $this->t('An exception was thrown during the selftest process:'));
      $callback('error', $this->throwableToString($t));
      $result = FALSE;
    }
    return $result;
  }

  /**
   * Check if a URL is valid.
   *
   * @param string $url
   *   A URL.
   *
   * @return bool
   *   TRUE if URL is valid.
   */
  public function validUrl(string $url) : bool {
    return UrlHelper::isValid($url, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function hookRequirements(string $phase) : array {
    $requirements = [];
    if ($phase == 'runtime') {
      foreach ([
        'sources' => $this->integrationSourceFactory(),
        'destinations' => $this->integrationDestinationFactory(),
      ] as $key => $factory) {
        $all = $factory->allFromUnversionedSettingsFile();
        $requirements['multiaccess_' . $key] = [
          'title' => $this->t('MultiAccess @k', [
            '@k' => $key,
          ]),
          'value' => count($all),
          'severity' => REQUIREMENT_INFO,
        ];
      }
    }
    return $requirements;
  }

}
