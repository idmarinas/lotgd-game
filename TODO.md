-   Errores
    -   s
-   Añadir
    -   s
-   Cambiar
    -   mail.php
        -   en el inbox cambiar el select para informar de cuantos mensajes tiene cada uno de los remitentes

# Actualmente haciendo esto
-   ...

# Módulos
-   ...

# Para la versión 4.12.0
-   Migrar Twig (el sistema de templates y theme) al kernel
-   Última versión de la serie 4.y.z

# Advertising bundle
-   Permitir desactivarlo en tiempo de ejecución (un módulo que lo desactiva por ejemplo)
    -   Posiblemente esto ya se pueda hacer

# Para la versión 5.0.0
-   Un nuevo sistema de instalación por consola.
-   Migración al uso de Symfony usando el http-kernel (completado en esta versión)
    -   https://symfony.com/doc/current/components/http_kernel.html
        -   Se migrará 
            -   La configuración
            -   El uso del service manager
-   Se usará un sistema similar al Symfony como transición
-   **BC** Se elimina Laminas DB. `DB::` class `Lotgd\Core\Db\Dbwrapper`
    -   Eliminar uso de DB:: class, se usará Doctrine en su lugar.
    -   En las actualizaciones (a la 4.0.0, por ejemplo) se hace uso de Laminas DB
        -   Esto se eliminará en esta versión, ya que se incluirá un nuevo instalador
-   **BC** Eliminar compatibilidad con Laminas Service Manager.
-   **BC** Eliminar compatibilidad con Laminas Cache.
-   **BD** Eliminar compatibilidad con Laminas Translation.
-   **BD** Eliminar compatibilidad con Laminas Form.
-   Los módulos antiguos seguiran funcionando pero estan obsoletos.
    -   El nuevo sistema tipo Bundle remplaza al sistema de módulos antiguos.
-   A partir de esta versión se empezará a usar un sistema de módulos tipo Bundle

# Para la versión 6.0.0
-   Seguir la transición hacia un sistema Symfony Framework
-   Revisar plantillas y traducciones (ver si se puede mejorar la estructura de las traducciones)
    -   Usar macros y blocks donde se pueda.
-   **BC** Rehacer el sistema de combate, usando el principio del resto del juego y haciendo uso de una factoria.
    -   Hacer el que sistema de combate sea mas personalizable, se pueda extender las clases para añadir más opciones.
-   **BC** Rehacer los personajes, para que sean mas sencillos de extender, tambien para que se complemente como el sistema de combate nuevo.
    -   Se simplifica la forma en la que se calcula las estadísticas del perosnaje, haciendo que tanto los personajes jugador como los creados por el servidor, tengan una forma de creación muy similar.
-   **BC** Habilidades y sus buffs. Usar la base de datos para guardar los buffs, y asi poder traducir ciertos campos.
    -   Estos buffs pueden servir para muchas cosas, las monturas por ejemplo.

# Para la versión 7.0.0  (LoTGD Core as Symfony APP)
-   Esta versión LoTGD Core será una app Symfony Framework.
    -   Migrar a la estructura propia de Symfony Framework
-   Se actualiza el sistema de instalación para admitir la instalación por consola o via web.
    -   Mejor por consola
    -   Para los admin que no dispongan de esta opción se agrega la opción de instalación via web.
-   Se usará un sistema de módulos tipo Bundle, igual que Symfony Framework. 
    -   Se reemplaza por completo el viejo sistema de módulos
-   **BC** Eliminar compatibilidad con el uso del viejo sistema de módulos

# Para la versión 8.0.0 (LoTGD CORE as Bundle)
-   Esta versión LoTGD Core se transforma en un Symfony Bundle.
-   Se usa un sistema de módulos tipo Bundle
    -   Pensado para módulos que se tengan intención de compartir (en en proyectos propios o con terceros)
    -   La configuración personal se hace como una web en  Symfony Framework

## Para la versión X.0.0
-   Motd, permitir la traducción, y que las encuestas tengan una configuración fuera de un campo serializado.
    -   Poner las opciones de la encuesta en una tabla separada. Permitiendo que las opciones también se puedan traducir.
-   Agregar sistema al core, para poder añadir términos y condiciones y politica de privacidad, sin necesidad de módulo.

# ¿? Para la versión X1.0.0
-   ¿? Esta actualización requiere de que se valla migrando ciertos paquetes de Laminas a Symfony
-   ¿? Usar un sistema parecido al de Laminas MVC para crear las páginas.
    -   Posibilidad 1 de una migración a Laminas MVC framework (ya que todo el core usa módulos de Laminas)
    -   Posibilidad 2 de una migración a Symfony Framework (Esto seria la opción más lógica en el sentido de que viene con un sitema de plantillas que permite sustituir las de un bundle por otras propias)
        -   Creo que se migrará todo el código a los módulos de Symfony Framework, para así poder aprobechar sus capacidades, que son muy útiles en el LoTGD
        -   Templates: permitir reemplazar una pantilla de un bundle con otra propia, es una forma fácil de personalizar y crear temas propios.

## Cosas pendientes
-   Añadir un check para comprobar si se han usado las funciones obligatorias (copyright(), game_version() .... )
-   Crear un sistema de publicidad interno (que permite comprar espacios publicitarios)
    -   Compatible con el sistema simple (los tipo Google AdSense)
-   Modificar el sistema de logeo, para usar una clase (principalmente por el tema de la contraseña)
    -   Login
    -   Cambiar la forma en la que se códifican las contraseñas y se hace el login.
    -   Esto obligaría a los usuarios a generar una nueva contraseña.
-   Códigos de color, cambiar y unir todos los códigos de color, (color, negrita, cursiva, etc) en un mismo lugar
    -   IDEA: usar la clase BBCode, ejemplo como se usa en foros y similar
-   ¿? Permitir que en los eventos, el chance pueda ser superior a 100, para priorizar que un evento pueda pasar con más frecuencia.
    -   Usar otra forma

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

## Puede ser complicado
-   *

## Complicado
-   *

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

php bin/console doctrine:schema:update --force --dump-sql

php phpDocumentor.phar

php bin/console debug:container

composer dump-env prod

./vendor/bin/phan -m csv -o phan.csv

composer install --no-dev --no-suggest --optimize-autoloader
