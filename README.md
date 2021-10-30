# Mailcoach API Wrapper

This is an API wrapper for Spatie's [Mailcoach](https://mailcoach.app/). 
Use this package when you have installed Mailcoach on a standalone server - separate from your app.
I favored the standalone instance because it reduced dependencies in my apps.

## Installation

A prerequisite is that you have installed Mailcoach as a standalone app.

You can install this package via composer:

```bash
composer require rubenbuijs/mailcoach-api-wrapper
```

### Configuration

```php 
MAILCOACH_API_BASE_URL=https://yourdomain.com/api
MAILCOACH_API_TOKEN=1|XXXXXXXXXXXXXXXXXXXXXXX # Mailcoach installation: Config->API Tokens
MAILCOACH_LIST_ID=1
MAILCOACH_SSL=false
```

## Usage

After installation, the Newsletter class is available to interact with your Mailcoach server.

### Subscribe a new person
```php 
Newsletter::subscribe(string $email, string $name, array $tags = [])
```

### Update a subscriber
Enter NULL when you don't want to make changes to the email, name, or tags.
```php 
Newsletter::update(string $email, string $new_email = null, string $name = null, array $tags = null)
```

### Add tag(s)
```php 
Newsletter::addTags(string $email, array $tags_to_add)
```

### Delete tag(s)
```php 
Newsletter::deleteTags(string $email, array $tags_to_delete)
```

### Retrieve subscriber data
```php 
Newsletter::getSubscriberByEmail(string $email)
```

## Enjoyed this package?
Take a look at my products: 
- [Boei](https://www.boei.help?ref=github_mailcoach-api-wrapper): Website Lead, Communication & Social widget
- [ProductLift](https://www.productlift.dev?ref=github_mailcoach-api-wrapper): World’s most flexible prioritization, roadmap, and changelog tool.
