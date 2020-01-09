# Wordpress Maintenance Webhook

PHP script that enables maintenance webhooks in Wordpress.

These hooks are essentially useful when implementing **Continuous Deployment** (CD) or **Automated Deployments**.

## How it works?

The script provides two webhooks that can be accessed through query string; one to enable Wordpress maintenance mode and another one to disable it.

### Security

The script requires you to define a user and password, these credentials will be validated on each request, the script will only process webhooks if authentication has succeeded. All query string variables are sanitized.

The script is independent of Wordpress, meaning that it will not load any Wordpress library, file, database or configuration.

### Disclaimer

Even though we have taken into consideration many security concerns. This is not an official Wordpress script file, so use it at your own risk. The script requires at least **PHP 5.4**.

## Configuration

Copy the file `wp-maintenance-webhook.php` into the root folder of your Wordpress setup (where `wp-config.php` is found).

Edit the file, and find the following code lines:

```php
define( 'HTTP_USER', 'YOUR USER NAME HERE' );
define( 'HTTP_PASSWORD', 'YOUR PASSWORD HERE' );
```

Replace `YOUR USER NAME HERE` with your own user and `YOUR PASSWORD HERE` with your own password. Save the file.

## Webhooks

Use the following webhook sample to call your webhooks:

```php
http://{yourdomain.com}/wp-maintenance-webhook.php?auth={user}:{password}&webhook={webhook}
```

| Replace | With |
| --- | --- |
| `{yourdomain.com}` | With your website's domain url. |
| `{user}` | With your defined user. |
| `{password}` | With your defined password. |
| `{webhook}` | With the name of an available webhook. |

### Webhook names

| Name | Description |
| --- | --- |
| `enable_maintenance` | Enables Wordpress maintenance mode. |
| `disable_maintenance` | Disables Wordpress maintenance mode. |

Sample of `enable_maintenance`:

```php
http://yourdomain.com/wp-maintenance-webhook.php?auth=user:password&webhook=enable_maintenance
```

### Output

By default, the webhook will return a JSON response like this:

```json
{
    "error": false,
    "time": 1578542635,
    "message": "Maintenance mode enabled!",
    "maintenance":true
}
```

When there is an error, only the `error` and `message` attributes will be returned.

#### Formats

Change the format of the output by adding an additional query string variable called `output`, like:

```php
http://yourdomain.com/wp-maintenance-webhook.php?auth=user:password&webhook=enable_maintenance&output=json
```

| Output format | Description |
| --- | --- |
| `json` or *empty* | Returns a JSON response. |
| `html` | Returns a HTML response (data displayed as table). |

## Coding Guidelines

The coding is a mix between PSR-2 and Wordpress PHP guidelines.

## License

Free software distributed under the terms of the MIT license.