-   Cambiar
    -   mail.php
        -   en el inbox cambiar el select para informar de cuantos mensajes tiene cada uno de los remitentes

# A tener en cuenta desde la versión 5.0.0

-   El antiguo sistema de módulos está obsoleto desde la versión **5.0.0**
    -   Los módulos antiguos seguiran funcionando pero estan obsoletos.
    -   El nuevo sistema tipo Bundle remplaza al sistema de módulos antiguos.
    -   A partir de la versión **5.0.0** se empezará a usar un sistema de módulos tipo Bundle

# Módulos

-   Cambiar los sitios donde se usa battle.php
-   Adaptarlos para la version 6.0.0

# Actualmente haciendo esto (6.1.0)

-   Crear bundle:
    -   `Settings` para poder usarlo en sustitución de las `prefs` para usuario
    -   `Special Events`: usando el componente even-dispatcher crear uno para los eventos especiales:
        -   método que determina si se ejecuta el evento (según probabilidad)
        -   método que elige cual de los eventos adjuntos se ejecuta (teniendo en cuenta la probabilidad)
            -   Se filtran los eventos que cumplan con la probabilidad (chance) y se selecciona 1 teniendo en cuenta sus probabilidades
        -   Agregar opción para priorizar eventos.
            -   Agregar la prioridad a los eventos esto es útil para los eventos que no tienen menú de opciones y los que sí
                -   Se permitiria pasar antes los eventos que no tienen menús para así no entrar en conflicto con los que tienen menú
        -   Se agregará unas constantes con diferentes prioridades (ideas):
            -   `priority_needs_response` Para eventos que necesitan una respuesta por parte del usuario (tienen un menú de navegación)
                -   Este evento impide que se puedan ejecutar más eventos.
            -   `priority_interactive` Para eventos que no necesitan respuesta pero son interactivos, por ejemplo, se puede comentar (ejemplo es la pradera donde puede comer la montura y se puede escribir un comentario pero no hace falta una respuesta)
            -   `priority_info` Para los eventos que no necesitan una respuesta y no son interactivos, solo son informativos de que ha pasado algo
    -   `Mail` continuar con el que ya tengo iniciado `https://github.com/idmarinas/MessageBundle`
-   lotgd_core_paypal_currency para poner la moneda que se usa en el servidor para las donaciones por paypal (como en bundle core)
-   Migrar los cronjob a comandos de consola
-----
-   Sustituir la función lotgd_mail por Symfony mailer
    -   **lib/lotgd_mail.php** Function `lotgd_mail` is deprecated and removed in future versions.
        -   Use `Symfony mailer` instead.
-   **Correos** permitir usar una plantilla para así personalizar los mensajes
    -   Se usara el `Symfony\Bridge\Twig\Mime\TemplatedEmail` para todos los correos del core. 
    ```php
        use Symfony\Bridge\Twig\Mime\TemplatedEmail;

        $email = (new TemplatedEmail())
        // ...
            // html mail
            ->htmlTemplate('emails/signup.html.twig')
            //-- or only text mail
            ->textTemplate('emails/signup.txt.twig')
        // ...
        ;
    ```
    -   Habrá dos versiones de cada correo predeterminado version html y txt
    -   Desde la configuración se podrá decidir si se envian correos en html o txt
    -   Agregar opción para que el usuario pueda elegir.
---
-   Se intentará pasar todas las paginas al sistema de controlador igual que home.php y about.php
    -   Las páginas Grotto (las de configuración y administración) no se pasarán a un sistema de controlador.
        -   El panel de administración del juego se va a sustituir por **Sonata Admin**

# Futuras versiones

## Para la versión 7.0.0

-   Esta es la última versión que incluya compatibilidad con el antiguo sistema de módulos.
-   **BC** Sustituir Fomantic UI por https://tailwindcss.com 
    -   Tailwind ofrece más flexibilidad para crear la UI.
    -   npm install tailwindcss
    -   WebpackEncore (Se tiene que revisar como seria con Tailwind)
        -   Organizar mejor los archivos js/css
            -   El tema se crea en una configuración nueva para personalizar
            -   El js se crea en una entry comun para todo (app por ejemplo) ya que puede dar problemas
                -   webpack.encore.entry.js
                -   webpack.encore.theme.js
-   Revisar plantillas y traducciones (ver si se puede mejorar la estructura de las traducciones)
    -   Se aprobecha el cambio a TailWind para revisar y mejorar las plantillas
    -   Usar macros y blocks donde se pueda.

## Para la versión 8.0.0

-   **BC** Una versión intermedia en la que se eliminan los módulos, antes de pasar a una Symfony App
    -   Por lo que la versión 7.0.0 sería la última versión compatible con los módulos.
-   Con este paso intermedio se pretende hacer una transición un poquito más suave y limpiar el código de todo lo obsoleto.
-   Se revisará el código para hacer la transición más sencilla. 
-   Esta versión se centrará en hacer la transición a la versión 9.0.0 más sencilla.

## **BC** Para la versión 9.0.0

-   Se fusiona todos los installer a uno nuevo como clean version
    -   El installer de la versión 6.0.0 depende de laminas/laminas-serializer
        -   Es en el único sitio donde se utiliza este componente
-   Eliminar paquete laminas/laminas-serializer
-   Esta versión será ya una Symfony App
-   Se usará todo lo de Symfony (ruter incluido)

## Para la versión X.Y.Z

-   Migrar los cronjobs a cron/cron bundle mediante comandos de symfony console.
-   Copiar el sistema de petition (y adaptarlo) en el app bundle.
-   Motd, permitir la traducción, y que las encuestas tengan una configuración fuera de un campo serializado.
    -   Poner las opciones de la encuesta en una tabla separada. Permitiendo que las opciones también se puedan traducir.
-   Agregar sistema al core, para poder añadir términos y condiciones y politica de privacidad, sin necesidad de módulo.
-   **BC** Hacer el que sistema de combate sea mas personalizable, se pueda extender las clases para añadir más opciones.
-   **BC** Rehacer los personajes, para que sean mas sencillos de extender, tambien para que se complemente como el sistema de combate nuevo.
    -   Se simplifica la forma en la que se calcula las estadísticas del personaje, haciendo que tanto los personajes jugador como los creados por el servidor, tengan una forma de creación muy similar.
-   **BC** Habilidades y sus buffs. Usar la base de datos para guardar los buffs, y asi poder traducir ciertos campos.
    -   Estos buffs pueden servir para muchas cosas, las monturas por ejemplo.
-   Crear el bundle del inventario. Para sustituir el antiguo sistema de armadura y arma.
-   Crear el bundle de energia, que permita poner energia o un sistema por turnos.
-   Se actualiza el sistema de instalación para admitir la instalación por consola o via web.
    -   La instalación por consola ya se creo en la versión 5.0.0
        -   Se mirará incluir una versión de instalación por web
            -   Problematico la creación del usuario admin
    -   Para los admin que no dispongan de esta opción se agrega la opción de instalación via web.
-   Posible candidato a sustituir el petition system por https://github.com/hackzilla-project/TicketBundle
-   Para limitar los intentos de conexión https://github.com/anyx/LoginGateBundle

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
