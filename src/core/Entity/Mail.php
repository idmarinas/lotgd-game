<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Structure of table "mail" in data base.
 *
 * This table stores emails between users.
 *
 * @ORM\Table(name="mail",
 *     indexes={
 *         @ORM\Index(name="msgto", columns={"msgto"}),
 *         @ORM\Index(name="seen", columns={"seen"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Core\EntityRepository\MailRepository")
 */
class Mail
{
    /**
     * @var int
     *
     * @ORM\Column(name="messageid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $messageid;

    /**
     * @var int
     *
     * @ORM\Column(name="msgfrom", type="integer", nullable=false, options={"unsigned": true})
     */
    private $msgfrom = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="msgto", type="integer", nullable=false, options={"unsigned": true})
     */
    private $msgto = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=false)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", length=65535, nullable=false)
     */
    private $body;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $sent = '0000-00-00 00:00:00';

    /**
     * @var bool
     *
     * @ORM\Column(name="seen", type="boolean", nullable=false, options={"default": "0", "unsigned": true})
     */
    private $seen = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="originator", type="integer", nullable=false, options={"unsigned": true})
     */
    private $originator = '0';

    /**
     * Set the value of Messageid.
     *
     * @param int messageid
     *
     * @return self
     */
    public function setMessageid($messageid)
    {
        $this->messageid = $messageid;

        return $this;
    }

    /**
     * Get the value of Messageid.
     *
     * @return int
     */
    public function getMessageid(): int
    {
        return $this->messageid;
    }

    /**
     * Set the value of Msgfrom.
     *
     * @param int msgfrom
     *
     * @return self
     */
    public function setMsgfrom($msgfrom)
    {
        $this->msgfrom = $msgfrom;

        return $this;
    }

    /**
     * Get the value of Msgfrom.
     *
     * @return int
     */
    public function getMsgfrom(): int
    {
        return $this->msgfrom;
    }

    /**
     * Set the value of Msgto.
     *
     * @param int msgto
     *
     * @return self
     */
    public function setMsgto($msgto)
    {
        $this->msgto = $msgto;

        return $this;
    }

    /**
     * Get the value of Msgto.
     *
     * @return int
     */
    public function getMsgto(): int
    {
        return $this->msgto;
    }

    /**
     * Set the value of Subject.
     *
     * @param string subject
     *
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the value of Subject.
     *
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Set the value of Body.
     *
     * @param string body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the value of Body.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Set the value of Sent.
     *
     * @param \DateTime sent
     *
     * @return self
     */
    public function setSent(\DateTime $sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get the value of Sent.
     *
     * @return \DateTime
     */
    public function getSent(): \DateTime
    {
        return $this->sent;
    }

    /**
     * Set the value of Seen.
     *
     * @param bool seen
     *
     * @return self
     */
    public function setSeen($seen)
    {
        $this->seen = $seen;

        return $this;
    }

    /**
     * Get the value of Seen.
     *
     * @return bool
     */
    public function getSeen(): bool
    {
        return $this->seen;
    }

    /**
     * Set the value of Originator.
     *
     * @param int originator
     *
     * @return self
     */
    public function setOriginator($originator)
    {
        $this->originator = $originator;

        return $this;
    }

    /**
     * Get the value of Originator.
     *
     * @return int
     */
    public function getOriginator(): int
    {
        return $this->originator;
    }
}
