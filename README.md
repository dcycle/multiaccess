[![CircleCI](https://circleci.com/gh/dcycle/multiaccess.svg?style=svg)](https://circleci.com/gh/dcycle/multiaccess)

Drupal Multiaccess
=====

Encrypted communication between Drupal sites, for example to allow users logged into one Drupal site to access other Drupal sites via a one-time login link.

Similar modules
-----

If you are looking for single-sign on, there are several more mature, more powerful and more configurable, single sign-on solutions out there for Drupal.

If you are looking for APIs and webhooks to ensure communication between websites, there are also a number of mature, powerful options out there.

So why this module?
-----

This module is meant to have no dependencies, works with multisite configurations, and meant to be installable in a few minutes with some coding skill by adding some lines to an unversioned settings.php file. Its approach is to have one main (source) site which can access information on destination sites via server-to-server encrypted messages without building a full-fledged API.

Design approach
-----

The base module does not provide an administrative graphical user interface, does not use Drupal Configuration Manager, and does not store anything in the database. Administrators interact with it using Drush.

The only way to configure this module is to have access to an unversioned local settings file and to add lines to it. It is crucial to never store settings related to this module in version control, as that could be a security hole.

The base module can then be interacted with programmatically. The included multiaccess_uli_ui module provides a tab on the account page which allows users to log in to remote sites.

Definitions
-----

In the context of this module,

* The **source site** is the site on which a user has an account
* The **destination site** is the site you want to access

The source site has complete control of the destination site
-----

The source site can get a one-time login link to the root user, in effect allowing the source site to control the destination site. The destination site cannot control the source site.

Typical workflow
-----

* User with email some_user@example.com exists on the source site.
* User some_user@example.com has role role_a
* role_a on source is mapped to role_some_role the destination
* While on the source site, when this is set up correctly, developers can programmatically obtain a unique login link on the destination.
* With the multiaccess_uli_ui module enabled, users see a new tab called "Remote sites" where they can access unique login links to remote sites.
* If a user with some_user@example.com does not exist on Destination, it is created
* User some_user@example.com on destinations is given role role_some_role as per the mapping.

Instead of seeing Access Denied on the destination, how to automatically log in
-----

When your users land on a page in your destination site, and they are not logged in, by default they will see "Access Denied".

If, instead of that, you want them to be

* directed to the source site (where they have an account)
* from there, automatically logged in to the destination site
* and finally be redirected to the page they want to access

Here is what you can do:

### Step one, install and enable [r4032login](https://www.drupal.org/project/r4032login) on the _destination_ site

    composer require drupal/r4032login
    drush en r4032login

### Step two, make sure you have the source site's public URL

For this example, we will say it is `http://source.example.com`.

### Step three, make sure you know the UUID of the destination site on the source site

For this example, let's say it is DESTINATION_SITE.

### Step four, on the _destination_ site, set some unversioned settings for r4032login

We need to tell r4032login on the destination what to do when a user does not have access to a page. Instead of going to the destination's login page, we want to go to the source site, and redirect to the destination. Here is how we would do this using the configuration management system (**I don't recommend doing it this way; read on for my recommendation**):

    drush cset r4032login.settings user_login_path http://source.example.com/multiaccess/redirect/DESTINATION_SITE
    drush cset r4032login.settings destination_parameter_override destination_cannot_be_named_destination

(The "destination" parameter cannot be named "destination" because if it is, and it contains a destination on an external site (in this case the destination site is external to the source site), then Drupal will not allow us to accss it, even by directly access the $_GET superglobal.)

So if you set the config using `drush cset` on the production, the next time you import configuration from code, it will be overwritten, which we don't want.

If you set the config using `drush cset` on the development site, the destination site gets committed to version control, which we don't want either, because the destination site will probably be different on stage, dev, or prod.

So instead of all this, we can set the configuration using [configuration override in settings.php (or another unversioned file on the target destination environment)](https://www.drupal.org/docs/drupal-apis/configuration-api/configuration-override-system):

    $config['r4032login.settings']['user_login_path'] = 'http://source.example.com/multiaccess/redirect/DESTINATION_SITE';
    $config['r4032login.settings']['destination_parameter_override'] = 'destination_cannot_be_named_destination';

### Step five, test if you are logged out of the destination and logged in to source

Log in to the source.

Log out of the destination.

On the destination, visit a page accessible only to logged in users, for example /admin/index.

The system should log you in automatically.

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

    drush ev "multiaccess_new_integration(label: 'My Destination Site', public: 'http://site-i-want-to-access.example.com', internal: 'http://site-i-want-to-access.example.com', role_mapping_array: ['authenticated' => ['authenticated']])"

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

Access tokens, and difference between branch 1.0.x and 2.x
-----

In branch 1.x, the included multiacecss_uli_ui module requires links to external sites to be generated using the current time and a security token (this is done in `.modules/multiaccess_uli_ui/src/Controller/RemoteUliController.php`). When such as linked is followed, the system will check if the access token is valid and has been recently generated before allowing one to access a remote site.

In branch 2.x, we have done away with such checks entirely. The following scenario differs in branches 1.0.x and 2.x:

### In branch 1.0.x

* An internal function must be used to generate a link to an external site ABC. The link will look like http://source.example.com/multiaccess/redirect/ABC/TIMESTAMP/TOKEN.
* When a user visits the link, if the token is invalid, the system will not allow the user to access the destination site.
* When a user visits the link, if the token is valid _and_ the destination site is properly configured _and_ the user currently logged in to the source site has a role which maps to a role or roles on the destination site, the system will allow the user to access the destination site and log the user in if necessary (although see the "Loggin in when you are already logged in" section below.)
* This means it requires custom coding to create links to destination sites (example: `.modules/multiaccess_uli_ui/src/Controller/RemoteUliController.php`).

### In branch 2.x

* The link http://source.example.com/multiaccess/redirect/ABC will always be considered valid. No security token is necessary.
* When a user visits the link, if the destination site is properly configured _and_ the user currently logged in to the source site has a role which maps to a role or roles on the destination site, the system will allow the user to access the destination site and log the user in if necessary (although see the "Loggin in when you are already logged in" section below.)

### The reasoning behind doing away with the token

In branch 1.0.x, we consider that providing a link on the source site leading to an external site (called the destination site) and logging in a user automatically to said destination site is a sensitive operation.

In branch 2.x, we consider that all websites in a group of sites are meant to work together as one, and the difference between sites should be transparent to the end user (that is, if a user is logged in the source site and that that user's role allows them to be logged into a destination site, then logging in to and accessing a path on the destination site should not be considered more sensitive than accessing a path on the source site, for which no token is necessary).

Specifying a destination (branch 2.x only)
-----

Here is a path on the source site which leads to page /admin/index on the destination site:

* /multiaccess/redirect/DESTINATION_UUID?destination=/admin/index

Loggin in when you are already logged in
-----

The core issue [Friendly response to logged-in user landing on user/reset](https://www.drupal.org/project/drupal/issues/3316655) documents an issue where, if you are already logged in to the destination, you will see an 'Access denied' message.

This can be fixed using the [ULI Custom Workflow](https://www.drupal.org/project/uli_custom_workflow) module, which will no longer show an 'Access denied' message if you are already logged in.

If your source and destination are on the same URL with different ports
-----

Because of [this issue](https://www.drupal.org/project/drupal/issues/2933569), if the destination has the same URL or IP with a different port, logging into the destination on the same browser as you are using for the source can log you out of the source. The solution is to use different domains or different browsers.

Automated testing
-----

This module's main page is on [Drupal.org](http://drupal.org/project/multiaccess); a mirror is kept on [GitHub](http://github.com/dcycle/multiaccess).

Unit tests are performed on Drupal.org's infrastructure and in GitHub using CircleCI. Linting is performed on GitHub using CircleCI and Drupal.org. For details please see  [Start unit testing your Drupal and other PHP code today, October 16, 2019, Dcycle Blog](https://blog.dcycle.com/blog/2019-10-16/unit-testing/).

* [Test results on Drupal.org's testing infrastructure](https://www.drupal.org/node/3098822/qa)
* [Test results on CircleCI](https://circleci.com/gh/dcycle/multiaccess)

To run automated tests locally, install Docker and type:

    ./scripts/ci.sh
