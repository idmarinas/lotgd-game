-   Errores
    -   s
-   Añadir
    -   s
-   Cambiar
    -   mail.php
        -   en el inbox cambiar el select para informar de cuantos mensajes tiene cada uno de los remitentes
-   Cambiar como se guarda la configuración del juego
    -   Toda la configuración se guarda en la base de datos
    -   Se hace uso de la cache para reducir las consultas a la base de datos
    -   Aplicar este concepto a los módulos también

# Actualmente haciendo esto

-

# Para la versión 4.2.0

-   Usar la extensión de Doctrine para hacer traducibles campos de la base de datos, como en las criaturas, armas, armaduras, items..
    -   Esto parece que no funcionará con valores guardados en la base de datos como puede ser en settings
    -   Se debe buscar una forma de agregar a los fomularios la opción de que un campo es traducible

# Para la versión 5.0.0
-   Migrar los componentes Zend a Laminas
-   Versión mínima de PHP 7.2.0

## Cosas pendientes
-   Adaptar los formularios a zend-form
    -   Los formularios usan también el sistema de traducción y es necesario actualizarlo
        -   Se ha adaptado el formulario de configuración básica del juego (settings)
        -   Se irán adaptando el resto de formularios con cada actualización
-   Modificar el sistema de logeo, para usar una clase (principalmente por el tema de la contraseña)
    -   Login
    -   Cambiar la forma en la que se códifican las contraseñas y se hace el login.
    -   Esto obligaría a los usuarios a generar una nueva contraseña.
-   Códigos de color, cambiar y unir todos los códigos de color, (color, negrita, cursiva, etc) en un mismo lugar
    -   IDEA: usar la clase BBCode, ejemplo como se usa en foros y similar

-   Traducción
    -   Hacer que los nombres de las criaturas y sus armas y textos, se puedan traduccir.
    -   Se agrega un campo nuevo `text_domain` para determinar donde estan las traducciones `creature-core`
        -   Ejemplo:
            -   creature-35.name
            -   creature-35.weapon
            -   creature-35.text.lose
            -   creature-35.text.win
            -   creature-35.description
        -   Se podría modificar desde la edición d ela criatura hadiendo uso de que los arhivos yaml se pueden trasformar en un array y viceversa.
            -   Se tiene que tener encuenta los diferentes idiomas instalados.

    -   Falta ajustar como sería para la categoría
        -   La categoría funciona de una forma diferente, tiene siempre el `textDomain` como `creature-category` donde están todas las categorías
            -   No se puede cambiar, ya que se puede agregar la criatura
            -   Ejemplos
                -   human
                -   undead
        - La categoría tiene que ser obligatoriamente la key de traducción, esto permitira un mejor uso dentro del juego (algunas funciones usan la categoria para hacer un select)




## Para la versión X

-   Rehacer el sistema de combate, usando el principio del resto del juego y haciendo uso de una factoria.
    -   Hacer el que sistema de combate sea mas personalizable, se pueda extender las clases para añadir más opciones.
-   Rehacer los personajes, para que sean mas sencillos de extender, tambien para que se complemente cono el sistema de combate nuevo.
    -   Se simplifica la forma en la que se calcula las estadísticas del perosnaje, haciendo que tanto los personajes jugador como los creados por el servidor, tengan una forma de creación muy similar.
-   Pasar jaxon-php a una factoria, y eliminar el lib/jaxon.php

## Puede ser complicado
-   Usar el zend-http component, para generar la respuesta del servidor (sustituir page_header y page_footer) y revisar también popup_header y popup_footer

## Complicado
-   Módulos, usar un sistema similar al que usa zenframework con sus módulos. pasar los módulos actuales a un sistema similar al manejado por zenframework.

* * *


## Temas

Los hook admiten incluir plantillas de forma dinamica en los archivos twig.
La forma de hacerlo es incluir en los parametros una key con el nombre del hook en formate kamelCaseTpl donde se incluya la templates a incluir. Es un array para poder incluir más.
Ejemplo:

```php
$params = modulehook('create-form', $params);

//-- Alternativa para las plantilas de los módulos
$params['createFormTpl'] = ['MyModule/template.twig'];
```

Esta plantilla hereda todas las variables de la plantilla padre.

## Menu de navegación

Permitir añadir al principio o al final de una categoria, añadir una categoría al final o al inicio

## Doctrine

Hacer que se pueda personalizar la configuración de Doctrine añadiendo más opciones, ejemplo: DoctrineORMModule para Zend


### Comandos
./vendor/bin/doctrine orm:convert:mapping annotation "entity" --namespace "Lotgd\Core\Entity\" --no-ansi --from-database --force

./vendor/bin/doctrine orm:schema-tool:update --force --dump-sql


Analizar los archivos para buscar incompatibilidades
./vendor/bin/phan -p -m="checkstyle" -o="phan.xml"
./vendor/bin/phan -p -m="csv" -o="phan.csv"
./vendor/bin/phan -p -m="csv" -o="phan.csv" --debug

Add --debug/-D flag to generate verbose debug output.
This is useful when looking into poor performance or unexpected behavior (e.g. infinite loops or crashes).

<!-- Sin uso -->
./vendor/bin/phpcs -p . --standard=PHPCompatibility --report-full
vendor/bin/iniscan scan --format=html --output="D:\\Users\\idmar\\Documents\\Proyectos Web"


#### Otro

${extensionPath}\\php-cs-fixer.phar

##### Generar los docs
php phpDocumentor.phar -c phpdoc.dist.xml

