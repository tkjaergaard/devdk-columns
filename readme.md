# Columns

To create custom coluns in Wordpress is quite a pain. We build a simple API around the wordpress functions to easily
create, delete, rename and sortable columns.

## Simple usage

    use Devdk\Columns;

    Columns::make($post_type, function($col)
    {
        $col->column("Column Title")->content(function($post, $meta){
            return $meta["key"][0];
        });
    });

## Advance usage

    use Devdk\Columns;

    Columns::make($post_type, function($col)
    {
        $col->column("Column Title")->content(function($post, $meta){
            return $meta["key"][0];
        })->before("date")->sortable("meta_key");
    });


    use Devdk\Columns;

    Columns::make($post_type, function($col)
    {
        $col->column("Column Title")->content(function($post, $meta){
            return $meta["key"][0];
        })->after("title")->sortable("meta_key");
    });