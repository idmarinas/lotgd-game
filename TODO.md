* Sacar scripts inline a lotgd.js de los archivos
    * Usar una única función para codificar las contraseñas al enviar
        * home.php
        * create.php
    * lib/commentary.php
* Errores
    * s
* Añadir
    * s
* Cambiar
    * mail.php
        * en el inbox cambiar el select para informar de cuantos mensajes tiene cada uno de los remitentes
    * XAJAX
        * Eliminar el XAJAX del core y sustituirlo https://www.jaxon-php.org (es un fork)
* Cambiar el sistema de traducción por otro basado en Zend\Translator
* Cambiar como se guarda la configuración del juego
    * No abusar tanto del guardado en la base de datos
    * Archivos de configuración (como se hace en Zend Framework)
    * Otra opción es hacer uso de la cache para la configuración
* Temas
    * Revisar las plantillas, los menus no están divididos en varias plantillas, sino es una plantilla donde se organiza todo. Complicado, para una versión X (\*.X.\*)
    * Cambiar la oganización de las plantillas para que tenga más sentido y se más fácil de entender
        * Posibilidad
            * theme.html
            * fonts/
            * images/
            * templates/
                * popup.twig
                * content/
                    * content.twig
                    * adwraper.twig
                    * forgot.twig
                    * login.twig
                    * loginfull.twig
                    * register.twig
                * sidebar/
                    * navs/
                        * head.twig
                        * help.twig
                        * item.twig
                        * menu.twig
                    * character/
                        * statbuff.twig
                        * stathead.twig
                        * statrow.twig
                        * stats.twig
                * parts/
                    * petition.twig
                * pages/
                    * lib/
                    * lib/about/
                    * donator.twig (Ejemplo)
                    * Plantillas que son especificas para un archivo concreto
                * battle/
                    * forestcreaturebar.twig
