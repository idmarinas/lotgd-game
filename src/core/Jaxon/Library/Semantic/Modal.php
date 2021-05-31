<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * Plugin.php - Adapter for the Semantic library.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Jaxon\Library\Semantic;

use Jaxon\Dialogs\Contracts\Modal as JaxonModal;
use Jaxon\Dialogs\Libraries\Library;

class Modal extends Library implements JaxonModal
{
    public const PATH_TEMPLATE_JAXON = 'data/jaxon';

    /**
     * The constructor.
     */
    public function __construct()
    {
        parent::__construct('semantic', '0.1.0');
    }

    /**
     * Get the javascript code to be printed into the page.
     *
     * It is a function of the Jaxon\Dialogs\Interfaces\Plugin interface.
     *
     * @return string
     */
    public function getScript()
    {
        return $this->render('/semantic/modal.js');
    }

    /**
     * Show a modal dialog.
     *
     * It is a function of the Jaxon\Dialogs\Interfaces\Modal interface.
     *
     * @param string $title   The title of the dialog
     * @param string $content The content of the dialog
     * @param array  $buttons The buttons of the dialog
     * @param array  $options The options of the dialog
     */
    public function show($title, $content, array $buttons, array $options = [])
    {
        // Show the modal dialog
        $this->addCommand(['cmd' => 'semantic.show'], [
            'id'           => $content['id'] ?? null,
            'title'        => $title,
            'content'      => $content['content'],
            'isScrollable' => $content['isScrollable'] ?? false,
            'buttons'      => $buttons,
            'options'      => $options,
        ]);
    }

    /**
     * Hide the modal dialog.
     *
     * It is a function of the Jaxon\Dialogs\Interfaces\Modal interface.
     */
    public function hide()
    {
        // Hide the modal dialog
        $this->addCommand(['cmd' => 'semantic.hide'], []);
    }

    /**
     * {@inheritdoc}
     */
    public function render($sTemplate, array $aVars = [])
    {
        jaxon()->template()->addNamespace('jaxon::lotgd', self::PATH_TEMPLATE_JAXON);

        // Is the library the default for alert messages?
        $isDefaultForAlert = ($this->getName() == $this->xDialog->getOption('dialogs.default.alert'));
        // Is the library the default for confirm questions?
        $isDefaultForConfirm = ($this->getName() == $this->xDialog->getOption('dialogs.default.confirm'));
        $aLocalVars          = [
            'yes'               => $this->getYesButtonText(),
            'no'                => $this->getNoButtonText(),
            'defaultForAlert'   => $isDefaultForAlert,
            'defaultForConfirm' => $isDefaultForConfirm,
        ];

        return $this->xDialog->render("jaxon::lotgd::{$sTemplate}", \array_merge($aLocalVars, $aVars));
    }
}
