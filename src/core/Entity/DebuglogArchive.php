<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DebuglogArchive.
 *
 * @ORM\Table(name="debuglog_archive",
 *     indexes={
 *         @ORM\Index(name="date", columns={"date"}),
 *         @ORM\Index(name="target", columns={"target"}),
 *         @ORM\Index(name="field", columns={"actor", "field"})
 *     }
 * )
 * @ORM\Entity
 */
class DebuglogArchive extends Debuglog
{
}
