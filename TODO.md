-   Cambiar
    -   mail.php
        -   en el inbox cambiar el select para informar de cuantos mensajes tiene cada uno de los remitentes

# Módulos

-   ...

# Actualmente haciendo... versión 6.0.0

-   Esta versión LoTGD Core será una app Symfony Framework.
    -   Se hace como un Bundle y se crea un lotgd-skeleton para crear una versión personalizada.
-   Agregar la validación a las entidades (sólo algunas tienen validaciones de los campos)
-   Plantilla de registro, agregar algunos events para agregar más información.
-   * No hace falta Gulp (puede ser), copiar los archivos se puede hacer desde composer igual que hice con el skeleton al crear proyecto.
-   Panel de administración
    -   https://github.com/sonata-project/SonataAdminBundle 
-   Las diferentes partes del juego serán bundles internos:
    -   La aldea
    -   El bosque
    -   El cementerio
    -   Tienda
    -   La taberna/posada
    -   La batalla
    -   Diferentes zonas (interiores de un edificio por ejemplo)
    -   ...
    -   Esto permite poder personalizar cada parte, principalmente por el menu de navegación y la opción de poder crear diferentes versiones:
        -   Diferentes tiendas con la misma base.
        -   Diferentes ciudades con la misma base
-   SonataBlockBundle puede ser interesante para agregar bloques en lugares concretos
    -   Se usará para agregar contenido personalizado a las páginas. por ejemplo:
        - pre_content y post_content (antes del contenido de la propia página y después)
    -   https://sonata-project.org/bundles/block/master/doc/reference/events.html
-   **BC** Hacer el que sistema de combate sea mas personalizable, se pueda extender las clases para añadir más opciones.
    -   Eliminar partes antiguas si aun quedan.
-   **BC** Rehacer los personajes, para que sean mas sencillos de extender, tambien para que se complemente como el sistema de combate nuevo.
    -   Se simplifica la forma en la que se calcula las estadísticas del personaje, haciendo que tanto los personajes jugador como los creados por el servidor, tengan una forma de creación muy similar.
-   **BC** Habilidades y sus buffs. Usar la base de datos para guardar los buffs, y asi poder traducir ciertos campos.
    -   Estos buffs pueden servir para muchas cosas, las monturas por ejemplo.
-   **BC** Eliminar compatibilidad con el uso del viejo sistema de módulos.

# Para la versión 6.1.0

-   Sustituir Fomantic UI por https://tailwindcss.com 
    -   Tailwind ofrece más flexibilidad para crear la UI.
    -   npm install tailwindcss
-   WebpackEncore
    -   Organizar mejor los archivos js/css
        -   El tema se crea en una configuración nueva para personalizar
        -   El js se crea en una entry comun para todo (app por ejemplo) ya que puede dar problemas
            -   webpack.encore.entry.js
            -   webpack.encore.theme.js

# Para la versión 6.y.x

-   Crear el bundle del inventario. Para sustituir el antiguo sistema de armadura y arma.
-   Crear el bundle de energia, que permita poner energia o un sistema por turnos.
-   Integrar los componentes Laminas View en un bundle para crear algo similar a Sonata SEO
-   Se actualiza el sistema de instalación para admitir la instalación por consola o via web.
    -   La instalación por consola ya se creo en la versión 5.0.0
        -   Se mirará incluir una versión de instalación por web
            -   Problematico la creación del usuario admin
    -   Para los admin que no dispongan de esta opción se agrega la opción de instalación via web.
-   Posible candidato a sustituir el petition system por https://github.com/hackzilla-project/TicketBundle
-   Para limitar los intentos de conexión https://github.com/anyx/LoginGateBundle
-   Agregar sistema al core, para poder añadir términos y condiciones y politica de privacidad, sin necesidad de módulo.
    -    Cookie consent
        -   https://github.com/nucleos/NucleosGDPRBundle
        -   https://github.com/kiprotect/klaro
        -   https://github.com/osano/cookieconsent/
-   Motd, permitir la traducción, y que las encuestas tengan una configuración fuera de un campo serializado.
    -   Poner las opciones de la encuesta en una tabla separada. Permitiendo que las opciones también se puedan traducir.

## Cosas pendientes

-   Añadir un check para comprobar si se han usado las funciones obligatorias (copyright(), game_version() .... )
-   Crear un sistema de publicidad interno (que permite comprar espacios publicitarios)
    -   Compatible con el sistema simple (los tipo Google AdSense)
-   Códigos de color, cambiar y unir todos los códigos de color, (color, negrita, cursiva, etc) en un mismo lugar
    -   IDEA: usar la clase BBCode, ejemplo como se usa en foros y similar
-   ¿? Permitir que en los eventos, el chance pueda ser superior a 100, para priorizar que un evento pueda pasar con más frecuencia.
    -   Usar otra forma

## Cosas a mirar
-   https://github.com/pirasterize/sonata-form-builder
-   https://github.com/nelmio/NelmioSecurityBundle
-   https://github.com/nan-guo/Sonata-Menu-Bundle
-   https://github.com/sonata-project/SonataPageBundle 
    -   Puede ser interesante para añadir páginas personalizadas, pero parece que no es compatible con la versión 5 de symfony
-   https://symfony.com/doc/current/workflow.html
-   https://github.com/vimeo/psalm/blob/master/docs/running_psalm/installation.md
-   https://symfony.com/doc/current/components/process.html
-   https://symfony.com/doc/current/components/config.html
-   https://symfony.com/doc/current/components/finder.html
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
-   Creado, un bundle sencillo
    -   Mirar el uso de un bundle tipo settings:
        -   (Se crea uno propio sencillo) https://github.com/dmishh/SettingsBundle lastest on 28 Jun 2016
        -   O mejor crear uno propio que sustituya:
            -   Los settings de los modulos
            -   Los prefs-user de los modulos (guardar datos del modulo con respecto al modulo)
            -   Menos importante
                -   Puede solaparse con las caracteristicas de los bundles
                -   Los prefs-* (ejemplo prefs-city, prefs-mount) datos, del modulo con respecto a alguna caracteristica, como puede ser una ciudad, montura
