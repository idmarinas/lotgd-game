-   Errores
    -   s
-   Añadir
    -   s
-   Cambiar
    -   mail.php
        -   en el inbox cambiar el select para informar de cuantos mensajes tiene cada uno de los remitentes

# Actualmente haciendo esto
-   ...

# Para la versión 4.7.0
-   CronJob -> revisar que no le fuerze la actualizacion/instalación al cronjob (cli)
-   Eliminar la función `renderTheme` de la clase Template.php
-   Eliminar la función `renderLayout` de la clase Template.php
-   LotgdForm
    -   Borrar el uso de la clase LotgdForm (se usará la factory en su lugar)
    -   Borrar la creación de la clase estática LotgdForm
-   Agregar un sistema de publicidad en el core que permita vender espacios de publicidad en el juego.
-   Revisar plantillas y traducciones (ver si se puede mejorar la estructura de las traducciones)
    -   Usar macros y blocks donde se pueda.
-   Revisar las funciones de Output/Collector para reemplazar todas las funciones.
    -   Se tiene que ir quitando estas clases, y mover las fuciones a otras zonas.
    -   Ejemplo: el colorize (appoencode) se moverá a otra clase

# Para la versión 4.8.0
-   Navegación: clase de php ArrayObject, puede servir para agregar la funcionalidad de agregar un link en una posición concreta
    -   No sirve, no se puede usar
    -   Intentar dividir el array en la posición deseada, agregar el item y luego fusionar los dos arrays.

# Para la versión 4.9.0
-   ???? Crear el nuevo sistema de módulos
    -   Un sistema compatible con el viejo

# Para la versión 5.0.0
-   Un nuevo sistema de instalación, mixto: por consola o por web. Creo que mejor seria por consola.
    -   Pero es posible que no todos los server admin tengan esta opción
-   Uso de Laminas DB, obsoleto. `DB::` class `Lotgd\Core\Db\Dbwrapper`
    -   Eliminar uso de DB:: class, se usará Doctrine en su lugar.
    -   En las actualizaciones (a la 4.0.0, por ejemplo) se hace uso de Laminas DB
        -   Esto se eliminará en esta versión, ya que se incluirá un nuevo instalador
-   Eliminar compatibilidad con el uso del viejo sistema de módulos
    -   Esto también elimina el uso de DB::
-   Eliminar compatibilidad con el viejo sistema de traducción.
    -   Esto también elimina el uso de DB::
-   Eliminar compatibilidad con el viejo sistema de creación de formularios.


## Para la versión X.0.0
-   Rehacer el sistema de combate, usando el principio del resto del juego y haciendo uso de una factoria.
    -   Hacer el que sistema de combate sea mas personalizable, se pueda extender las clases para añadir más opciones.
-   Rehacer los personajes, para que sean mas sencillos de extender, tambien para que se complemente cono el sistema de combate nuevo.
    -   Se simplifica la forma en la que se calcula las estadísticas del perosnaje, haciendo que tanto los personajes jugador como los creados por el servidor, tengan una forma de creación muy similar.
-   Habilidades y sus buffs. Usar la base de datos para guardar los buffs, y asi poder traducir ciertos campos.
    -   Estos buffs pueden servir para muchas cosas, las monturas por ejemplo.

# ¿? Para la versión X1.0.0
-   ¿? Esta actualización requiere de que se valla migrando ciertos paquetes de Laminas a Symfony
-   ¿? Usar un sistema parecido al de Laminas MVC para crear las páginas.
    -   Posibilidad 1 de una migración a Laminas MVC framework (ya que todo el core usa módulos de Laminas)
    -   Posibilidad 2 de una migración a Symfony Framework (Esto seria la opción más lógica en el sentido de que viene con un sitema de plantillas que permite sustituir las de un bundle por otras propias)
        -   Creo que se migrará todo el código a los módulos de Symfony Framework, para así poder aprobechar sus capacidades, que son muy útiles en el LoTGD
        -   Templates: permitir reemplazar una pantilla de un bundle con otra propia, es una forma fácil de personalizar y crear temas propios.

## Cosas pendientes
-   Añadir un check para comprobar si se han usado las funciones obligatorias (copyright(), game_version() .... )
-   Modificar el sistema de logeo, para usar una clase (principalmente por el tema de la contraseña)
    -   Login
    -   Cambiar la forma en la que se códifican las contraseñas y se hace el login.
    -   Esto obligaría a los usuarios a generar una nueva contraseña.
-   Códigos de color, cambiar y unir todos los códigos de color, (color, negrita, cursiva, etc) en un mismo lugar
    -   IDEA: usar la clase BBCode, ejemplo como se usa en foros y similar

## Cosas a mirar
-   https://symfony.com/doc/current/components/process.html
-   https://symfony.com/doc/current/components/config.html
-   https://symfony.com/doc/current/components/finder.html
-   https://symfony.com/doc/current/components/console.html Ya se agrego la consola, pero sirve como referencia.
-   https://symfony.com/doc/current/components/options_resolver.html
    -   Para configurar los componentes y opciones que se pueden usar 
    -   Esto permite que cada componente tenga las opciones que necesita y los tipos de valor correctos
        
        ```php
        In many cases you may need to define multiple configurations for each option. For example, suppose the InvoiceMailer class has an host option that isrequired and a transport option which can be one of sendmail, mail and smtp. You can improve the readability of the code avoiding to duplicate option namefor each configuration using the define() method:
        // ...
        class InvoiceMailer
        {
            // ...
            public function configureOptions(OptionsResolver $resolver)
            {
                // ...
                $resolver->define('host')
                    ->required()
                    ->default('smtp.example.org')
                    ->allowedTypes('string')
                    ->info('The IP address or hostname');

                $resolver->define('transport')
                    ->required()
                    ->default('transport')
                    ->allowedValues(['sendmail', 'mail', 'smtp']);
            }
        }
        ```

### Sólo falta los módulos.
-   Adaptar los formularios a zend-form (Módulos)
    -   Los formularios usan también el sistema de traducción y es necesario actualizarlo
        -   Se ha adaptado el formulario de configuración básica del juego (settings)
        -   Se irán adaptando el resto de formularios con cada actualización
    -   En algunas partes se usa Symfony Form


## Puede ser complicado
-   *

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

#### Comandos

`/vendor/bin/doctrine orm:schema-tool:update --dump-sql --force`
