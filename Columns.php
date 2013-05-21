<?php namespace Devdk;

use Closure;
use Devdk\Columns\Facade;

class Columns {

    /**
     * Post type to apply columns
     * @var string
     */
    protected $post_type;

    /**
     * Closure to call upon content
     * @var Closure
     */
    protected $callback;

    /**
     * Initialize class
     * @param string  $post_type
     * @param Closure $callback
     */
    public function __construct($post_type, Closure $callback)
    {
        $this->post_type = $post_type;
        $this->callback  = $callback;

        $this->register();
    }

    /**
     * Register the columns
     * @return void;
     */
    protected function register()
    {
        $this->provider = new Facade();

        call_user_func_array($this->callback, array($this->provider));

        if( $rename = $this->provider->rename )
            $this->renameColumns($rename);

        if( $columns = $this->provider->columns )
            $this->registerColumns($columns);

        if( $remove = $this->provider->remove )
            $this->removeColumns($remove);

        if( $sortable = $this->provider->sortable )
            $this->registerSortable($sortable);

        if( $content = $this->provider->content )
            $this->registerContent($content);
    }

    /**
     * Rename default columns
     * @param  array  $columns
     * @return void
     */
    protected function renameColumns(array $columns)
    {
        add_filter( "manage_edit-{$this->post_type}_columns", function($original) use($columns)
        {

            foreach( $columns as $before => $after )
            {
                if( !isset($original[$before]) ) continue;

                $original[$before] = __( $after );

            }

            return $original;

        }, 1);
    }

    /**
     * Remove columns
     * @param  array  $columns
     * @return void
     */
    protected function removeColumns(array $columns)
    {
        add_filter( "manage_edit-{$this->post_type}_columns", function($original) use($columns)
        {

            foreach( $columns as $column )
            {
                if( !isset($original[$column]) ) continue;

                unset( $original[$column] );
            }

            return $original;

        }, 3);
    }

    /**
     * Register custom columns
     * @param  array  $columns
     * @return void
     */
    protected function registerColumns(array $columns)
    {
        add_filter( "manage_edit-{$this->post_type}_columns", function($original) use($columns)
        {
            foreach( $columns as $slug => $attr )
            {
                if( isset($attr['after']) )
                {
                    $original = $this->array_after($attr['after'], $original, $slug, $attr["name"]);
                    continue;
                }

                if( isset($attr['before']) )
                {
                    $original = $this->array_before($attr['before'], $original, $slug, $attr["name"]);
                    continue;
                }

                $original[$slug] = __( $attr["name"] );
            }

            return $original;

        }, 2);
    }

    /**
     * Register sortable columns
     * @param  array  $sortable
     * @return void
     */
    protected function registerSortable(array $sortable)
    {
        $post_type = $this->post_type;

        add_filter( 'manage_edit-movie_sortable_columns',function( $columns ) use($sortable)
        {
            foreach( $sortable as $column )
            {
                $columns[$column] = $column;
            }

            return $columns;

        }, 1);

        add_action("load-edit.php", function() use($sortable, $post_type)
        {

            add_filter('request', function($vars) use($sortable, $post_type)
            {

                /* Check if we're viewing the 'movie' post type. */
                if ( isset( $vars['post_type'] ) && $post_type == $vars['post_type'] && isset($vars['orderby']) )
                {

                    foreach( $sortable as $slug )
                    {
                        if(  $slug == $vars['orderby'] )
                        {
                            $vars = array_merge($vars, array(
                              'meta_key' => $slug,
                              'orderby'  => 'meta_value'
                            ));
                        }
                    }

                }

                return $vars;

            }, 1);

        });
    }

    /**
     * Register content for columns
     * @param  array  $content
     * @return void
     */
    protected function registerContent(array $content)
    {
        $post_type = $this->post_type;

        add_action('manage_posts_custom_column', function($column) use($content, $post_type)
        {
            global $post;
            $meta = get_post_meta($post->ID);

            if( $post->post_type != $post_type ) return $column;

            if( isset($content[$column]) )
            {
                echo call_user_func_array($content[$column], array($post, $meta));
            }
        });
    }

    /**
     * Helper: Insert element in array before $key
     * @param  sting  $key
     * @param  array  $array
     * @param  string $new_key
     * @param  string $new_value
     * @return array
     */
    protected function array_before($key, array $array, $new_key, $new_value)
    {
        if( !array_key_exists($key, $array) )
        {
            $array[$new_key] = $new_value;

            return $array;
        }

        $new = array();

        foreach ($array as $k => $v)
        {
            if ($k === $key)
            {
                $new[$new_key] = $new_value;
            }

            $new[$k] = $v;
        }

        return $new;
    }

    /**
     * Helper: Insert element in array after $key
     * @param  string $key
     * @param  array  $array
     * @param  string $new_key
     * @param  string $new_value
     * @return void
     */
    protected function array_after($key, array $array, $new_key, $new_value)
    {
        if( !array_key_exists($key, $array) )
        {
            $array[$new_key] = $new_value;

            return $array;
        }

        $new = array();

        foreach ($array as $k => $value)
        {
            $new[$k] = $value;

            if ($k === $key) {
                $new[$new_key] = $new_value;
            }
        }

        return $new;
    }

    /**
     * Facade for simpler API.
     * Maps to __construct()
     * @param  string  $post_type
     * @param  Closure $callback
     * @return Devdk\Columns
     */
    public static function make($post_type, Closure $callback)
    {
        return new self($post_type, $callback);
    }

}