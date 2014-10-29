<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Container\Container;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\Schema\Builder;

class SetupDatabase {

    /**
     * @var \Illuminate\Database\Connection
     */
    protected static $connection;

    protected static $pdo;

    public static function setupTestDb()
    {
        $schema = static::setupConnection();

        static::cleanDb($schema);
        static::setupDb($schema);
    }

    public static function getPdo()
    {
        if(is_null(static::$pdo))
        {
            static::setupConnection();
        }

        return static::$pdo;
    }

    public static function cleanDb(Builder $schema)
    {
        $schema->dropIfExists('imager_image');
        $schema->dropIfExists('albums');
    }

    protected static function setupDb(Builder $schema)
    {
        $schema->create('imager_image', function(Blueprint $table)
        {
            $table
                ->increments('id')
                ->unsigned()
            ;

            $table
                ->integer('imageable_id')
                ->unsigned()
                ->nullable()
            ;
            $table
                ->string('imageable_type')
                ->nullable()
            ;

            $table
                ->string('slot')
                ->nullable()
            ;

            $table
                ->integer('width')
                ->unsigned()
            ;

            $table
                ->integer('height')
                ->unsigned()
            ;

            $table->string('mime_type');

            $table->string('average_color', 6);

            $table->timestamps();

            //
            // Indexes
            //
            $table->unique(['imageable_id', 'imageable_type', 'slot'], 'U_imageable_slot');
        });

        $schema->create('albums', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    private static function setupConnection($dbName = 'tests/_data/test.sqlite')
    {
        $dbSettings = [
            'driver'   => 'sqlite',
            'database' => $dbName,
            'prefix'   => ''
        ];

        $container = new Container();

        $capsule = new Manager($container);

        $connectionFactory = new ConnectionFactory($container);
        static::$connection = $connectionFactory->make($dbSettings);

        $capsule->addConnection($dbSettings);
        $capsule->setAsGlobal();

        static::$pdo = $capsule->connection('default')->getPdo();

        $schema = Manager::schema();
        $schema->setConnection(static::$connection);

        $resolver = new ConnectionResolver();
        $resolver->addConnection('default', static::$connection);
        $resolver->setDefaultConnection('default');

        Model::setConnectionResolver($resolver);

        return $schema;
    }
}
