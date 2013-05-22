<?php namespace Devdk\Columns;

use Exception;
use Closure;

class Column {

    /**
     * Column propperties
     * @var array
     */
    public $column = array();

    /**
     * Sortable column?
     * @var boolean
     */
    public $sortable;

    /**
     * Content Colsure for column
     * @var Closure
     */
    public $callback;

    /**
     * Column slig
     * @var string
     */
    public $slug;

    /**
     * Insert column after
     * @var string
     */
    public $after;

    /**
     * Insert column before
     * @var string
     */
    public $before;

    /**
     * Register column
     * @param  string $name
     * @return self
     */
    public function column($name)
    {
        if( count($this->column) > 0 )
            throw new Exception("Column has already been named.");

        $this->slug = $slug = strtolower(
                                preg_replace("/[^a-zA-Z0-9_]/", "-", $name)
                             );

        $this->column[$slug] = array('name' => $name);

        return $this;
    }

    /**
     * Set column to be sortable
     * @return self
     */
    public function sortable($meta_key=null)
    {
        $this->sortable = true;

        if( $meta_key )
            $this->slug = $meta_key;

        return $this;
    }

    /**
     * Set column content
     * @param  Closure $callback
     * @return boolean
     */
    public function content(Closure $callback)
    {
        $this->callback = $callback;

        return true;
    }

    /**
     * Insert column after
     * @param  string $slug
     * @return self
     */
    public function after($slug)
    {
        if( $this->before )
            $this->before = NULL;

        $this->after = $slug;

        return $this;
    }

    /**
     * Insert column before
     * @param  string $slug
     * @return self
     */
    public function before($slug)
    {
        if( $this->after )
            $this->after = NULL;

        $this->before = $slug;

        return $this;
    }

}