<?php 
use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;



return function (ContainerBuilder $containerBuilder) {

  $containerBuilder->addDefinitions([

    Capsule::class => function () {
      $config = require __DIR__ . '/Config/ConfigDatabase.php';

      $capsule = new Capsule();
      foreach ($config['connect'] as $name => $connection) {
        $capsule->addConnection($connection, $name);
      }

      $capsule->setAsGlobal();
      $capsule->bootEloquent();

      return $capsule;
    },

    'db.agendapro' => fn(Capsule $capsule): Connection
      => $capsule->getConnection('db.agendapro'),


  ]);
};
