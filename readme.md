# Columns

To create custom coluns in Wordpress is quite a pain. We build a simple API around the wordpress functions to easily create, delete, rename and make columns sortable.

## installation

Columns is installable through Composer.

    require {
        "devdk/columns": "dev-master"
    }


## Simple usage

    use Devdk\Columns;

    Columns::make($post_type, function($col)
    {
        $col->column("Column Title")->content( function ($post, $meta) {
            return $meta["key"][0];
        });
    });

## Advanced usage

    use Devdk\Columns;

    Columns::make($post_type, function ($col) {
        $col->column("Column Title")->content(function($post, $meta){
            return $meta["key"][0];
        })->before("date")->sortable("meta_key");
    });


    use Devdk\Columns;

    Columns::make($post_type, function ($col) {
        $col->column("Column Title")->content(function($post, $meta){
            return $meta["key"][0];
        })->after("title")->sortable("meta_key");
    });