[![Bitbucket issues](https://img.shields.io/bitbucket/issues/idmarinas/lotgd-juego.svg?maxAge=2592000)]()
[![Installation](https://img.shields.io/badge/install-fail-red.svg?maxAge=2592000)]()
[![Upgrade](https://img.shields.io/badge/upgrade-fail-red.svg?maxAge=2592000)]()
[![Version](https://img.shields.io/badge/version-1.0.0--dev-red.svg?maxAge=2592000)]()

# Acerca de IDMarinas Edition #

La versión **IDMarinas Edition** esta basada en la versión **1.2.5 +nb Edition** de Oliver Brendel ([NB Core](http://nb-core.org)).


## Versión IDMarinas ##

La versión IDMarinas Edition está actualmente en la versión: 1.0.0-dev

La intención de hacer una rama nueva del Core, es poder actualizar ciertos aspectos, que se habian quedado desactualizados, como por ejemplo el script que se usa para acceder a la base de datos, a la vez que corregir ciertos errores, y agregar nuevas opciones de personalización.

Para ver la versión IDMarinas en acción puedes acceder a la siguiente web ([La Leyenda de Ignis](http://dragonverde.infommo.es)).

Se debe tener en cuenta que muchos de los módulos usados se pueden descargar de la web de ([DragonPrime](http://dragonprime.net)). Los módulos creados por mi **son privados**.

## Dependencias y requisitos LOTGD - IDMarinas Edition ##
* PHP >= 5.6
* Utiliza componentes de:
	* [Semantic UI Framework](http://semantic-ui.com/)
	* [Zend Framework](https://zendframework.github.io)
* Sistema de plantillas de [Twig](http://twig.sensiolabs.org)

## Cambios importantes ##
* Se actualiza la versión del juego a la 2.0.0 IDMarinas Edition y se agrega IDMarinas al Copyright para indicar que esta versión está modificada.
* Se usa Gulp para automatizar tareas a la hora de construir el proyecto y poder copiarlo al servidor
* Se modifica el comportamiento del showform() -> Ya no genera el Javascript por si mismo, sino que utiliza Semantic UI para generar las pestañas
	* Se renombra showform() por lotgd_showform()
* Se sustituyen todas las funciones relacionadas con la base de datos (Ejem: db_query...) por sus equivalentes DB::query...
	* Las funciones antiguas seguirán funcionando pero dan una advertencia de función obsoleta. Se borrarán en la versión 3.0.0
* Codificación por defecto UTF-8

## Añadidos ##
* *lib/showtabs.php* **lotgd_showtabs()** Permite mostrar contenido mediante pestañas. Se debe incluir el archivo cuando se necesite usar esta función.


# Compatiblidad #
* Esta versión es posible que no sea compatible con la mayor parte de los módulos disponibles para la versión 1.1.* DragonPrime Edition
	* Aunque, se ha modificado el script de conexión a la base de datos, las funciones antiguas relacionadas con la base de datos siguen funcionando.
	* La versión 3.0.0 no será compatible con muchos módulos que hacen uso de la base de datos. Ya que se eliminará las funciones antiguas de conexión a la base de datos.

## Advertencias ##
* Esta es una versión de desarrollo y por lo tanto inestable
* No usar en un servidor de producción ni actualizar desde una versión anterior

## Errores que se conocen ##
* No es posible hacer una instalación de actualización