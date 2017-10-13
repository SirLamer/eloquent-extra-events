# Eloquent Extra Events

> Support for events before and after modification to Eloquent belongs-to-many relationships.

[![Total Downloads](https://poser.pugx.org/neylsongularte/eloquent-extra-events/downloads)](https://packagist.org/packages/neylsongularte/eloquent-extra-events)
[![Monthly Downloads](https://poser.pugx.org/neylsongularte/eloquent-extra-events/d/monthly)](https://packagist.org/packages/neylsongularte/eloquent-extra-events)
[![Daily Downloads](https://poser.pugx.org/neylsongularte/eloquent-extra-events/d/daily)](https://packagist.org/packages/neylsongularte/eloquent-extra-events)
[![Latest Stable Version](https://poser.pugx.org/neylsongularte/eloquent-extra-events/v/stable)](https://packagist.org/packages/neylsongularte/eloquent-extra-events)
[![Latest Unstable Version](https://poser.pugx.org/neylsongularte/eloquent-extra-events/v/unstable)](https://packagist.org/packages/neylsongularte/eloquent-extra-events)
[![License](https://poser.pugx.org/neylsongularte/eloquent-extra-events/license)](https://packagist.org/packages/neylsongularte/eloquent-extra-events)

## Dependencies
- Laravel 5.5.*

## Installation
```bash
composer require neylsongularte/eloquent-extra-events
```

## Basic Usage
Implementation in Eloquent Model:
```php
<?php

namespace App;

use NeylsonGularte\EloquentExtraEvents\ExtraEventsTrait;
use Illuminate\Database\Eloquent\Model;
///

class User extends Model
{
    use ExtraEventsTrait;
    
    ///
}
```

Listen to event within ```EventServiceProvider```:
```php
<?php

///

class EventServiceProvider extends ServiceProvider
{
    ///
    
    public function boot()
    {
        parent::boot();
        
        Event::listen('eloquent.syncing:*', function ($modelName, array $eventData) {
                ///
            });
        
        ///
    }    
}
```

## Relationship Events
The following events are enabled by the package. Each fire before and after their respective event in the same manner as Laravel's built-in Eloquent model events.
- eloquent.syncing
- eloquent.synced
- eloquent.attaching
- eloquent.attached
- eloquent.detaching
- eloquent.detached

# Listening to events
Events may be listened for via any normal means provided through Laravel. Please refer Laravel's documentation for further direction. Event observers and other implementations as demonstrated for Eloquent's built-in events are supported on all relationship events herein.
- [Events (general)](https://laravel.com/docs/events)
- [Eloquent Model events](https://laravel.com/docs/5.5/eloquent#events)

## Belongs-to-many Relationship Events
| Event              | Payload                                                |
| ------------------ | ------------------------------------------------------ |
| eloquent.attaching | parent, relationship, related, ids                     |
| eloquent.attached  | parent, relationship, related, ids                     |
| eloquent.detaching | parent, relationship, related, ids                     |
| eloquent.detached  | parent, relationship, related, ids, count              |
| eloquent.syncing   | parent, relationship, related, ids, detaching          |
| eloquent.synced    | parent, relationship, related, ids, detaching, changes |

### Event payload

| Payload key | Type | Description |
|:---:|:---:| --- |
| parent | Model (object) | The Eloquent Model or extended model featuring the relationship being acted upon. |
| relationship | string | The name of the relationship being acted on. |
| related | string | The fully qualified class name of the child model(s) on the relationship. |
| ids | array | The list of child IDs which are scheduled to be or have been modified on the relationship. |
| count | int | ('detach' only) The quantity of child models which were actually removed from the relationship. |
| detaching | bool | ('sync' only) When true, IDs present on the relationship but not present in the 'ids' array will be or have been removed from the relationship. |
| changed | array | ('sync' only) An array of ID arrays with a record of the actions performed via the 'sync' action, with keys 'attached', 'detached' and 'updated'. |

Per Laravel's built-in events system, an event payload accessed through an event listener will be provided as a keyed array with the keys indicated below. When accessed through an event observer method, the payload will be provided as arguments of the function call in the order presented above.

## Recommended Usage
If you intend to make common use of this feature throughout your project, create a local "base" model for your project which includes this trait as well as any other common features, then extend your base model throughout your project.

##### Example BaseModel
```php
<?php

namespace App;

use NeylsonGularte\EloquentExtraEvents\ExtraEventsTrait;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use ExtraEventsTrait;
    
    ///
}
```

Example implementation of BaseModel:
```php
<?php

namespace App;

class User extends BaseModel {
    ///
}
```

Example of an [event observer](https://laravel.com/docs/eloquent#observers) within an Observer class, in this case with intent for event broadcasting via Laravel Echo using event broadcast classes external to the scope of this package:
```php
<?php

namespace App;

use Auth;
use App\Events\Broadcasts;

class UserObserver
{
    const BROADCAST_RELATIONSHIPS = ['groups', 'classes', 'projects'];

    ///

    public function attached(BaseModel $parent, $relationship, $related, $ids)
    {
        if (in_array($relationship, static::BROADCAST_RELATIONSHIPS)) {
            broadcast(new Broadcasts\User\Attached($parent, $relationship, $related, $ids))->toOthers();
        }
    }

    public function detached(BaseModel $parent, $relationship, $related, $ids, $count)
    {
        if (in_array($relationship, static::BROADCAST_RELATIONSHIPS)) {
            broadcast(new Broadcasts\User\Detached($parent, $relationship, $related, $ids))->toOthers();
        }
    }
}
```

Register your event observer [per Laravel documentation](https://laravel.com/docs/eloquent#observers).