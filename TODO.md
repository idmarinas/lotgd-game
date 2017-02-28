* Sacar scripts inline a lotgd.js de los archivos
* * Usar una única función para codificar las contraseñas al enviar
* * * home.php
* * * create.php
* * lib/commentary.php
* Errores
* Los atajos de teclado no funcionan
* En los formularios se interpreta los códigos de color del juego (no deberia ser asi) GameSettings y Usuario ejemplos
*
* Añadir
*
* Cambiar
* * Permitir que se pueda elegir entre usar el sistema de stamina o el de turnos (Se hace que se compruebe si esta activo el módulo staminasystem)
* * mail.php
* * * en el inbox cambiar el select para informar de cuantos mensajes tiene cada uno de los remitentes
* * XAJAX
* * * Eliminar el XAJAX del core y sustituirlo por apis ajax (usando jQuery para recuperar los datos)
* Cambiar el sistema de traducción por otro basado en Zend\Translator
* Cambiar como se guarda la configuración del juego, no usar la base de datos y pasarlo todo a archivos de configuración (como se hace en Zend Framework)