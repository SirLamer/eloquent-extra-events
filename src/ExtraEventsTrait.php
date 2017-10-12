<?php

namespace NeylsonGularte\EloquentExtraEvents;

// Trait for models
trait ExtraEventsTrait {

    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string  $relationName
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignPivotKey = null,
                                  $relatedPivotKey = null, $parentKey = null,
                                  $relatedKey = null, $relationName = null)
    {

        $belongsToMany = parent::belongsToMany($related, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey,
            $relatedKey, $relationName);

        $query = $belongsToMany->getQuery()->getModel()->newQuery();
        $parent = $belongsToMany->getParent();
        $table = $belongsToMany->getTable();

        $foreignPivotKey = explode('.', $belongsToMany->getQualifiedForeignPivotKeyName())[1];
        $relatedPivotKey = explode('.', $belongsToMany->getQualifiedRelatedPivotKeyName())[1];

        $relationName = $belongsToMany->getRelationName();


        return new BelongsToMany($query, $parent, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey,
            $relatedKey, $relationName);
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
}
