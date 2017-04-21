* Sacar scripts inline a lotgd.js de los archivos
* * Usar una única función para codificar las contraseñas al enviar
* * * home.php
* * * create.php
* * lib/commentary.php
* Errores
*
* Añadir
*
* Cambiar
* * mail.php
* * * en el inbox cambiar el select para informar de cuantos mensajes tiene cada uno de los remitentes
* * XAJAX
* * * Eliminar el XAJAX del core y sustituirlo https://www.jaxon-php.org (es un fork)
* Cambiar el sistema de traducción por otro basado en Zend\Translator
* Cambiar como se guarda la configuración del juego
* * No abusar tanto del guardado en la base de datos
* * Archivos de configuración (como se hace en Zend Framework)
* * Otra opción es hacer uso de la cache para la configuración
