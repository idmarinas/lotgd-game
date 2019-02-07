-   Sacar scripts inline a lotgd.js de los archivos
    -   Usar una única función para codificar las contraseñas al enviar
        -   home.php
        -   create.php
    -   lib/commentary.php
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

## Temas
Los hook admiten incluir plantillas de forma dinamica en los archivos twig.
La forma de hacerlo es incluir en los parametros una key con el nombre del hook en formate kamelCaseTpl donde se incluya la templates a incluir. Es un array para poder incluir más.
Ejemplo:
``` php
$params = modulehook('create-form', $params);
$params['createFormTpl'] = ['../modules/MyModule/templates/template.twig'];

//-- Alternativa para las plantilas de los módulos
$params['createFormTpl'] = ['../modules/templates/MyModule/template.twig'];

//-- Alternativa para las plantilas de los módulos
$params['createFormTpl'] = ['templates/modules/MyModule/template.twig'];
```

Esta plantilla hereda todas las variables de la plantilla padre.


## Actualmente haciendo esto

-   Sustituir el sistema de traducción por otro basado en Zend\\Translator

Estructura para los modulos y el nuevo sistema de traducción:

Estructura para los módulos:

<!-- For single module file translation -->

    -   `translations/[LOCALE]/modules/[MODULENAME].yaml`

<!-- For a multiple module files translation -->

    -   `translations/[LOCALE]/[MODULENAME]/[FILENAME].yaml

Explicar la estructura de los archivos yaml y que admiten el sistema de anidación. que se accede a esa clave como: `key.key2.key3`

## Menu de navegación

hacer que la traducción se haga en la template
Hacer que genere un array con todos los menus
Crear una clase para los menus
    Permitir añadir al principio o al final de una categoria, añadir una categoría al final o al inicio

-   Revisar las plantillas, los menus no están divididos en varias plantillas, sino es una plantilla donde se organiza todo. Complicado, para una versión X (\*.X.\*)

## Comentarios

Modificar el script de los comentarios, para hacerlos de una forma más sencilla y mejorar el sistema.


## Instalación
Instalación (cuando se implemente el sistema de plantillas en las páginas)

Mejorar la apariencia usando segmentos y el grid
archivos
stage 9 y stage 7

./vendor/bin/doctrine orm:convert:mapping annotation "src/core/entity" --namespace "Lotgd\\Core\\Entity\\" --no-ansi --from-database --force

vendor/bin/iniscan scan --format=html --output="D:\\Users\\idmar\\Documents\\Proyectos Web"

./vendor/bin/phpcs -p . --standard=PHPCompatibility --report-full

Analizar los archivos para buscar incompatibilidades
./vendor/bin/phan -p -m="checkstyle" -o="phan.xml"
./vendor/bin/phan -p -m="codeclimate" -o="phan.xml"
