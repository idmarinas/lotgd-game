-   Errores
    -   s
-   Añadir
    -   s
-   Cambiar
    -   mail.php
        -   en el inbox cambiar el select para informar de cuantos mensajes tiene cada uno de los remitentes

# Actualmente haciendo esto (5.0.0)

-   El antiguo sistema de módulos está obsoleto desde la versión **5.0.0**
    -   Los módulos antiguos seguiran funcionando pero estan obsoletos.
    -   El nuevo sistema tipo Bundle remplaza al sistema de módulos antiguos.
    -   A partir de la versión **5.0.0** se empezará a usar un sistema de módulos tipo Bundle
-   Migrar al nuevo sitema de plantillas
    -   https://github.com/Sylius/SyliusThemeBundle para crear temas en LoTGD
-   Revisar plantillas y traducciones (ver si se puede mejorar la estructura de las traducciones)
    -   Usar macros y blocks donde se pueda.

# Módulos

-   ...

# Advertising bundle
-   Permitir desactivarlo en tiempo de ejecución (un módulo que lo desactiva por ejemplo)
    -   Posiblemente esto ya se pueda hacer

## Para la versión X.Y.Z
-   **BC** Cambiar el sistema de login/user por el de Symfony
-   Motd, permitir la traducción, y que las encuestas tengan una configuración fuera de un campo serializado.
    -   Poner las opciones de la encuesta en una tabla separada. Permitiendo que las opciones también se puedan traducir.
-   Agregar sistema al core, para poder añadir términos y condiciones y politica de privacidad, sin necesidad de módulo.
-   **BC** Rehacer el sistema de combate, usando el principio del resto del juego y haciendo uso de una factoria.
    -   Hacer el que sistema de combate sea mas personalizable, se pueda extender las clases para añadir más opciones.
-   **BC** Rehacer los personajes, para que sean mas sencillos de extender, tambien para que se complemente como el sistema de combate nuevo.
    -   Se simplifica la forma en la que se calcula las estadísticas del personaje, haciendo que tanto los personajes jugador como los creados por el servidor, tengan una forma de creación muy similar.
-   **BC** Habilidades y sus buffs. Usar la base de datos para guardar los buffs, y asi poder traducir ciertos campos.
    -   Estos buffs pueden servir para muchas cosas, las monturas por ejemplo.
-   **BC** Sustituir Fomantic UI por https://tailwindcss.com 
    -   Tailwind ofrece más flexibilidad para crear la UI.
    -   npm install tailwindcss
    -   WebpackEncore (Se tiene que revisar como seria con Tailwind)
        -   Organizar mejor los archivos js/css
            -   El tema se crea en una configuración nueva para personalizar
            -   El js se crea en una entry comun para todo (app por ejemplo) ya que puede dar problemas
                -   webpack.encore.entry.js
                -   webpack.encore.theme.js

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
-   https://github.com/KnpLabs/KnpPaginatorBundle para la paginación
-   Panel de administración, https://github.com/sonata-project/SonataAdminBundle 
-   https://github.com/sonata-project/SonataPageBundle 
    -   Puede ser interesante para añadir páginas personalizadas, pero parece que no es compatible con la versión 5 de symfony
-   https://github.com/Sylius/SyliusThemeBundle para crear temas en LoTGD
-   SonataBlockBundle puede ser interesante para agregar bloques en lugares concretos
    -   https://sonata-project.org/bundles/block/master/doc/reference/events.html
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
