<?php namespace Devdk\Columns;

use Devdk\Columns\Column;

class Facade {

    /**
     * Array of Column instances
     * @var array
     */
    protected $instances = array();

    /**
     * Columns to rename
     * @var array
     */
    public $rename = array();

    /**
     * Columns to remove
     * @var array
     */
    public $remove = array();

    /**
     * Register a column to be renamed
     * @param  string $slug
     * @param  string $new  [description]
     * @return boolean
     */
    public function rename($slug, $new)
    {
        $this->rename[$slug] = $new;

        return true;
    }

    /**
     * Register a column to be removed
     * @param  string $column   column slug
     * @return boolean
     */
    public function remove($column)
    {
        $this->remove[] = $column;

        return true;
    }

    /**
     * Return columns to be registred
     * @return array
     */
    protected function _columns()
    {
        $columns = array();

        foreach( $this->instances as $instance )
        {
            if( !$column = $instance->column ) continue;

            if( $after = $instance->after )
            {
                $column[$instance->slug]['after'] = $after;
            }

            if( $before = $instance->before )
            {
                $column[$instance->slug]['before'] = $before;
            }

            $columns = array_merge($columns, $column);
        }

        return $columns;
    }

    /**
     * Return columns to be sortable
     * @return array
     */
    protected function _sortables()
    {
        $sortables = array();

        foreach( $this->instances as $instance )
        {
            if( !$instance->sortable ) continue;

            $sortables[] = $instance->slug;
        }

        return $sortables;
    }

    /**
     * Return columns content
     * @return array
     */
    protected function _contents()
    {
        $content = array();

        foreach( $this->instances as $instance )
        {

            if( !$callback = $instance->callback ) continue;

            $content[$instance->slug] = $callback;

        }

        return $content;
    }

    /**
     * Map all call to a new instance of Devdk\Columns\Column.
     * @param  string $method
     * @param  array  $args
     * @return Devdk\Columns\Column
     */
    public function __call($method, $args)
    {
        $this->instances[] = $instance = new Column;

        return call_user_func_array(array($instance, $method), $args);
    }

    /**
     * Map inaccessible properties to the correct functions
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        switch( $name )
        {
            case "columns":
                return $this->_columns();

            case "sortable":
                return $this->_sortables();

            case "content":
                return $this->_contents();
        }

        return false;
    }

}