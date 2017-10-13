<?php

namespace NeylsonGularte\EloquentExtraEvents;

// Trait for models
trait ExtraEventsTrait {

    /**
     * @var array The relationship event observables to be available through Eloquent's event dispatcher registration.
     */
    protected static $relationshipEventObservables = [
        'syncing',
        'synced',
        'attaching',
        'attached',
        'detaching',
        'detached',
    ];

    /**
     * {@inheritdoc} Returns a modified BelongsToMany object with relationship events included.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string  $relation
     * @return BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null,
                                  $parentKey = null, $relatedKey = null, $relation = null)
    {
        // Initialization copied from Illuminate\Database\Eloquent\Concerns\HasRelationships
        // From version 5.5.14

        // If no relationship name was passed, we will pull backtraces to get the
        // name of the calling function. We will use that function name as the
        // title of this relation since that is a great convention to apply.
        if (is_null($relation)) {
            $relation = $this->guessBelongsToManyRelation();
        }

        // First, we'll need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we'll make the query
        // instances as well as the relationship instances we need for this.
        $instance = $this->newRelatedInstance($related);

        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();

        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        // If no table name was provided, we can guess it by concatenating the two
        // models using underscores in alphabetical order. The two model names
        // are transformed to snake case from their default CamelCase also.
        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        return new BelongsToMany(
            $instance->newQuery(), $this, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey ?: $this->getKeyName(),
            $relatedKey ?: $instance->getKeyName(), $relation
        );
    }

    /**
     * Register a syncing model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function syncing($callback)
    {
        static::registerModelEvent('syncing', $callback);
    }

    /**
     * Register a synced model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function synced($callback)
    {
        static::registerModelEvent('synced', $callback);
    }

    /**
     * Register a attaching model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function attaching($callback)
    {
        static::registerModelEvent('attaching', $callback);
    }

    /**
     * Register a attached model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function attached($callback)
    {
        static::registerModelEvent('attached', $callback);
    }

    /**
     * Register a detaching model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function detaching($callback)
    {
        static::registerModelEvent('detaching', $callback);
    }

    /**
     * Register a detached model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function detached($callback)
    {
        static::registerModelEvent('detached', $callback);
    }

    /**
     * {@inheritdoc} Injects the necessary relationship events in to the default observable list.
     *
     * @return array
     */
    public function getObservableEvents()
    {
        return array_merge(
            parent::getObservableEvents(),
            static::$relationshipEventObservables
        );
    }
}
