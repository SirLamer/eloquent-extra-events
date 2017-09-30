<?php

namespace NeylsonGularte\EloquentExtraEvents;

use Illuminate\Database\Eloquent\Relations\BelongsToMany as BelongsToManyEloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


class BelongsToMany extends BelongsToManyEloquent {




    public function sync($ids, $detaching = true)
    {
        $eventData = $this->getBaseEventData($ids);

        $class = get_class($eventData['parent']);

        event('eloquent.syncing: ' . $class, str_contains(app()->VERSION(), ['5.2.', '5.3.']) ? [$eventData] : $eventData);

        $changes = parent::sync($ids, $detaching);

        $eventData['changes'] = $changes;
        ;
        event('eloquent.synced: ' . $class, str_contains(app()->VERSION(), ['5.2.', '5.3.']) ? [$eventData] : $eventData);

        return $changes;
    }

    public function attach($ids, array $attributes = [], $touch = true)
    {
        $eventData = $this->getBaseEventData($ids);

        $class = get_class($eventData['parent']);

        event('eloquent.attaching: ' . $class, str_contains(app()->VERSION(), ['5.2.', '5.3.']) ? [$eventData] : $eventData);

        parent::attach($ids, $attributes, $touch);

        event('eloquent.attached: ' . $class, str_contains(app()->VERSION(), ['5.2.', '5.3.']) ? [$eventData] : $eventData);
    }


    public function detach($ids = [], $touch = true)
    {
        $eventData = $this->getBaseEventData($ids);

        $class = get_class($eventData['parent']);

        event('eloquent.detaching: ' . $class, str_contains(app()->VERSION(), ['5.2.', '5.3.']) ? [$eventData] : $eventData);

        $results = parent::detach($ids, $touch);

        $eventData['results'] = $results;
        event('eloquent.detached: ' . $class, str_contains(app()->VERSION(), ['5.2.', '5.3.']) ? [$eventData] : $eventData);

        return $results;
    }

    protected function getBaseEventData($ids)
    {
        return [
            'parent' => $this->getParent(),
            'relationship' => $this->getRelationName(),
            'related' => get_class($this->getRelated()),
            'ids' => static::processIds($ids)
        ];
    }

    protected static function processIds($ids)
    {
        if ($ids instanceof Collection) {
            return $ids->modelKeys();
        }

        if ($ids instanceof Model) {
            return $ids->getKey();
        }

        return (array) $ids;
    }

}
