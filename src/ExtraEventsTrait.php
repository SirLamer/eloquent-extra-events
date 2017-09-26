<?php

namespace NeylsonGularte\EloquentExtraEvents;


// Trait for models
trait ExtraEventsTrait {

    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
    {

        $belongsToMany = parent::belongsToMany($related, $table, $foreignKey, $otherKey, $relation);

        $query = $belongsToMany->getQuery()->getModel()->newQuery();
        $parent = $belongsToMany->getParent();
        $table = $belongsToMany->getTable();

        if(str_contains(app()->VERSION(), ['5.2.', '5.3.'])) {
            $foreignKey = explode('.', $belongsToMany->getForeignKey())[1];
            $otherKey = explode('.', $belongsToMany->getOtherKey())[1];
        } else {
            $foreignKey = explode('.', $belongsToMany->getQualifiedForeignKeyName())[1];
            $otherKey = explode('.', $belongsToMany->getQualifiedRelatedKeyName())[1];
        }

        $relation = $belongsToMany->getRelationName();


        return new BelongsToMany($query, $parent, $table, $foreignKey, $otherKey, $relation);
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
