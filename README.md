[![CircleCI](https://circleci.com/gh/dcycle/multiaccess.svg?style=svg)](https://circleci.com/gh/dcycle/multiaccess)

Drupal Multiaccess
=====

Encrypted communication between Drupal sites, for example to allow users logged into one Drupal site to access other Drupal sites' via a one-time login link.

Similar modules
-----

If you are looking for single-sign on, there are several more mature, more powerful and more configurable, single sign-on solutions out there for Drupal.

If you are looking for APIs and webhooks to ensure communication between websites, there are also a number of mature, powerful options out there.

So why this module?
-----

This module is meant to have no dependencies, works with multisite configurations, and meant to be installable in a few minutes with some coding skill. Its approach is to have one main (source) site which can access information on destination sites via server-to-server encrypted messages without building a full-fledged API.

Design approach
-----

This module does not provide an administrative graphical user interface, does not use Drupal Configuration Manager, and does not store anything in the database. Administrators interact with it using Drush.

The only way to configure this module is to have access to an unversioned local settings file and to add lines to it. It is crucial to never store settings related to this module in version control, as that could be a security hole.

This module can then be interacted with programmatically.

Definitions
-----

In the context of this module,

* The **source site** is the site on which a user has an account
* The **destination site** is the site you want to access

The source site has complete control of the destination site
-----

The source site can have get a one-time login link to the root user, in effect allowing the source site to control the destination site. The destination site cannot control the source site.

Typical workflow
-----

* User with email some_user@example.com exists on the source site.
* User some_user@example.com has role role_a
* role_a on source is mapped to role_some_role the destination
* While on the source site, when this is set up correctly, a unique login link on the destination can be obtained.
* If a user with some_user@example.com does not exist on Destination, it is created
* User some_user@example.com on destinations is given role role_some_role as per the mapping.

Role mapping
-----

There must be at least one role mapping between sites. The simplest possible role mapping is:

    authenticated => [authenticated]

Typical setup
-----

Install and enable this module on two Drupal sites:

* The source site
* The destination site

Now make note of:

* The destination's human-readable name.
* The publicly accessible URL. These cannot be on the same URL (even with a different port). See the Troubleshooting section for details. For example:
 * http://site-where-i-have-an-account.example.com
 * http://site-i-want-to-access.example.com
* The internally accessible URL (optional). If this is the same as the publicly accessible URL, you do not need this, but if you are developing locally on Docker or if you are using reverse proxies, you might need this.

Log on to the backend of the Site On Which I Have An Account and type:

    drush ev "multiaccess_new_integration(label: "My Destination Site", public: 'http://site-i-want-to-access.example.com', internal: 'http://site-i-want-to-access.example.com', role_mapping_array: ['authenticated' => ['authenticated']])"

This will print out instructions of what to place in your local **unversioned** settings files for each site (the site on which you have an account, and the site you want to access).

Update your local unversioned settings files for each site and then, on the source, run:

    drush ev "multiaccess_selftest()"

This will succeed if all goes well. Otherwise you will see an error and will need to brush off your debugging skills.

How to get a login link to the destination programmatically
-----

On the source site, you can list all integrations sites like this:

    drush ev "multiaccess_list()"

This will give you a result such as:

    Site has 1 destination(s):
    * 28f92726-275f-4ecd-a41d-cffc121616bf (Name): http://example.com

Your information will differ. multiaccess_list() is designed to show human-readable information; inspect the code of that function to understand how to fetch that information for consumption from your own code.

Once you have the UUID (in this example 28f92726-275f-4ecd-a41d-cffc121616bf) of your destination, you can fetch a unique login link like this:

    drush ev "print_r(multiaccess()->integrationDestinationFactory()->fromDestinationUuid('28f92726-275f-4ecd-a41d-cffc121616bf')->uli('user-that-exists-on-source-and-will-be-created-on-destination@example.com') . PHP_EOL)"

Use your own UUID, and, instead of user-that-exists-on-source-and-will-be-created-on-destination@example.com, use a username of an account that exists on the source site.

Loggin in when you are already logged in
-----

The issue [Friendly response to logged-in user landing on user/reset](https://www.drupal.org/project/drupal/issues/3316655) documents an issue where, if you are already logged in to the destination, you will see an 'Access denied' message.

If your source and destination are on the same URL with different ports
-----

Because of [this issue](https://www.drupal.org/project/drupal/issues/2933569), if the destination has the same URL or IP with a different port, logging into the destination one the same browser as you are using for the source can log you out of the source. The solution is to use different domains or different browsers.

Automated testing
-----

This module's main page is on [Drupal.org](http://drupal.org/project/multiaccess); a mirror is kept on [GitHub](http://github.com/dcycle/multiaccess).

Unit tests are performed on Drupal.org's infrastructure and in GitHub using CircleCI. Linting is performed on GitHub using CircleCI and Drupal.org. For details please see  [Start unit testing your Drupal and other PHP code today, October 16, 2019, Dcycle Blog](https://blog.dcycle.com/blog/2019-10-16/unit-testing/).

* [Test results on Drupal.org's testing infrastructure](https://www.drupal.org/node/3098822/qa)
* [Test results on CircleCI](https://circleci.com/gh/dcycle/multiaccess)

To run automated tests locally, install Docker and type:

    ./scripts/ci.sh
