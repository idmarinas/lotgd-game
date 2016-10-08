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
	* [Uikit Framework](http://getuikit.com)
	* [Zend Framework](https://zendframework.github.io)
* Sistema de plantillas de [Twig](http://twig.sensiolabs.org)

## Cambios importantes ##
* Se actualiza la versión del juego a la 1.0.0 IDMarinas Edition y se agrega IDMarinas al Copyright para indicar que esta versión está modificada.
* Se usa Gulp para automatizar tareas a la hora de construir el proyecto y poder copiarlo al servidor
* Se modifica el comportamiento del showform() -> Ya no genera el Javascript por si mismo, sino que utiliza Uikit para generar las pestañas
	* Se renombra showform() por lotgd_showform()
* Se sustituyen todas las funciones relacionadas con la base de datos (Ejem: db_query...) por sus equivalentes DB::query...
	* Las funciones antiguas seguirán funcionando pero dan una advertencia de función obsoleta. Se borrarán en la versión 2.0.0

## Añadidos ##
* *lib/showtabs.php* **lotgd_showtabs()** Permite mostrar contenido mediante pestañas. Se debe incluir el archivo cuando se necesite usar esta función.


# Compatiblidad #
* Esta versión es posible que no sea compatible con la mayor parte de los módulos disponibles para la versión 1.1.* DragonPrime Edition
	* Aunque, se ha modificado el script de conexión a la base de datos, las funciones antiguas relacionadas con la base de datos siguen funcionando.

## Advertencias ##
* Esta es una versión de desarrollo y por lo tanto inestable
* No usar en un servidor de producción ni actualizar desde una versión anterior

## Errores que se conocen ##
* Es posible que no sea posible hacer una instalación limpia
* Realizar una instalación, actualizando desde una versión anterior, puede hacer que se pierdan datos, y se alteren las tablas orignales del juego (si se habian alterados por algunos módulos intalados)