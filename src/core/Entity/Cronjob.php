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
 * Cronjob.
 *
 * @ORM\Table(name="cronjob")
 * @ORM\Entity
 */
class Cronjob
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="command", type="text", length=65535, nullable=false)
     */
    private $command;

    /**
     * @var string
     *
     * @ORM\Column(name="schedule", type="string", length=255, nullable=false)
     */
    private $schedule;

    /**
     * @var string
     *
     * @ORM\Column(name="mailer", type="string", length=255, nullable=true)
     */
    private $mailer = 'sendmail';

    /**
     * @var int
     *
     * @ORM\Column(name="maxRuntime", type="integer", nullable=true, options={"unsigned": true})
     */
    private $maxruntime;

    /**
     * @var string
     *
     * @ORM\Column(name="smtpHost", type="string", length=255, nullable=true)
     */
    private $smtphost;

    /**
     * @var int
     *
     * @ORM\Column(name="smtpPort", type="smallint", nullable=true)
     */
    private $smtpport;

    /**
     * @var string
     *
     * @ORM\Column(name="smtpUsername", type="string", length=255, nullable=true)
     */
    private $smtpusername;

    /**
     * @var string
     *
     * @ORM\Column(name="smtpPassword", type="string", length=255, nullable=true)
     */
    private $smtppassword;

    /**
     * @var string
     *
     * @ORM\Column(name="smtpSender", type="string", length=255, nullable=true)
     */
    private $smtpsender = 'jobby@localhost';

    /**
     * @var string
     *
     * @ORM\Column(name="smtpSenderName", type="string", length=255, nullable=true)
     */
    private $smtpsendername = 'Jobby';

    /**
     * @var string
     *
     * @ORM\Column(name="smtpSecurity", type="string", length=255, nullable=true)
     */
    private $smtpsecurity;

    /**
     * @var string
     *
     * @ORM\Column(name="runAs", type="string", length=255, nullable=true)
     */
    private $runas;

    /**
     * @var string
     *
     * @ORM\Column(name="environment", type="text", length=65535, nullable=true)
     */
    private $environment;

    /**
     * @var string
     *
     * @ORM\Column(name="runOnHost", type="string", length=255, nullable=true)
     */
    private $runonhost;

    /**
     * @var string
     *
     * @ORM\Column(name="output", type="string", length=255, nullable=true)
     */
    private $output;

    /**
     * @var string
     *
     * @ORM\Column(name="dateFormat", type="string", length=100, nullable=true)
     */
    private $dateformat = 'Y-m-d H:i:s';

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="haltDir", type="string", length=255, nullable=true)
     */
    private $haltdir;

    /**
     * @var bool
     *
     * @ORM\Column(name="debug", type="boolean", nullable=true)
     */
    private $debug;

    /**
     * Set the value of Name.
     *
     * @param string name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of Name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of Command.
     *
     * @param string command
     *
     * @return self
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get the value of Command.
     *
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * Set the value of Schedule.
     *
     * @param string schedule
     *
     * @return self
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get the value of Schedule.
     *
     * @return string
     */
    public function getSchedule(): string
    {
        return $this->schedule;
    }

    /**
     * Set the value of Mailer.
     *
     * @param string mailer
     *
     * @return self
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;

        return $this;
    }

    /**
     * Get the value of Mailer.
     *
     * @return string
     */
    public function getMailer(): string
    {
        return $this->mailer;
    }

    /**
     * Set the value of Maxruntime.
     *
     * @param int maxruntime
     *
     * @return self
     */
    public function setMaxruntime($maxruntime)
    {
        $this->maxruntime = $maxruntime;

        return $this;
    }

    /**
     * Get the value of Maxruntime.
     *
     * @return int
     */
    public function getMaxruntime(): int
    {
        return $this->maxruntime;
    }

    /**
     * Set the value of Smtphost.
     *
     * @param string smtphost
     *
     * @return self
     */
    public function setSmtphost($smtphost)
    {
        $this->smtphost = $smtphost;

        return $this;
    }

    /**
     * Get the value of Smtphost.
     *
     * @return string
     */
    public function getSmtphost(): string
    {
        return $this->smtphost;
    }

    /**
     * Set the value of Smtpport.
     *
     * @param int smtpport
     *
     * @return self
     */
    public function setSmtpport($smtpport)
    {
        $this->smtpport = $smtpport;

        return $this;
    }

    /**
     * Get the value of Smtpport.
     *
     * @return int
     */
    public function getSmtpport(): int
    {
        return $this->smtpport;
    }

    /**
     * Set the value of Smtpusername.
     *
     * @param string smtpusername
     *
     * @return self
     */
    public function setSmtpusername($smtpusername)
    {
        $this->smtpusername = $smtpusername;

        return $this;
    }

    /**
     * Get the value of Smtpusername.
     *
     * @return string
     */
    public function getSmtpusername(): string
    {
        return $this->smtpusername;
    }

    /**
     * Set the value of Smtppassword.
     *
     * @param string smtppassword
     *
     * @return self
     */
    public function setSmtppassword($smtppassword)
    {
        $this->smtppassword = $smtppassword;

        return $this;
    }

    /**
     * Get the value of Smtppassword.
     *
     * @return string
     */
    public function getSmtppassword(): string
    {
        return $this->smtppassword;
    }

    /**
     * Set the value of Smtpsender.
     *
     * @param string smtpsender
     *
     * @return self
     */
    public function setSmtpsender($smtpsender)
    {
        $this->smtpsender = $smtpsender;

        return $this;
    }

    /**
     * Get the value of Smtpsender.
     *
     * @return string
     */
    public function getSmtpsender(): string
    {
        return $this->smtpsender;
    }

    /**
     * Set the value of Smtpsendername.
     *
     * @param string smtpsendername
     *
     * @return self
     */
    public function setSmtpsendername($smtpsendername)
    {
        $this->smtpsendername = $smtpsendername;

        return $this;
    }

    /**
     * Get the value of Smtpsendername.
     *
     * @return string
     */
    public function getSmtpsendername(): string
    {
        return $this->smtpsendername;
    }

    /**
     * Set the value of Smtpsecurity.
     *
     * @param string smtpsecurity
     *
     * @return self
     */
    public function setSmtpsecurity($smtpsecurity)
    {
        $this->smtpsecurity = $smtpsecurity;

        return $this;
    }

    /**
     * Get the value of Smtpsecurity.
     *
     * @return string
     */
    public function getSmtpsecurity(): string
    {
        return $this->smtpsecurity;
    }

    /**
     * Set the value of Runas.
     *
     * @param string runas
     *
     * @return self
     */
    public function setRunas($runas)
    {
        $this->runas = $runas;

        return $this;
    }

    /**
     * Get the value of Runas.
     *
     * @return string
     */
    public function getRunas(): string
    {
        return $this->runas;
    }

    /**
     * Set the value of Environment.
     *
     * @param string environment
     *
     * @return self
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Get the value of Environment.
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Set the value of Runonhost.
     *
     * @param string runonhost
     *
     * @return self
     */
    public function setRunonhost($runonhost)
    {
        $this->runonhost = $runonhost;

        return $this;
    }

    /**
     * Get the value of Runonhost.
     *
     * @return string
     */
    public function getRunonhost(): string
    {
        return $this->runonhost;
    }

    /**
     * Set the value of Output.
     *
     * @param string output
     *
     * @return self
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Get the value of Output.
     *
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * Set the value of Dateformat.
     *
     * @param string dateformat
     *
     * @return self
     */
    public function setDateformat($dateformat)
    {
        $this->dateformat = $dateformat;

        return $this;
    }

    /**
     * Get the value of Dateformat.
     *
     * @return string
     */
    public function getDateformat(): string
    {
        return $this->dateformat;
    }

    /**
     * Set the value of Enabled.
     *
     * @param bool enabled
     *
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get the value of Enabled.
     *
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Set the value of Haltdir.
     *
     * @param string haltdir
     *
     * @return self
     */
    public function setHaltdir($haltdir)
    {
        $this->haltdir = $haltdir;

        return $this;
    }

    /**
     * Get the value of Haltdir.
     *
     * @return string
     */
    public function getHaltdir(): string
    {
        return $this->haltdir;
    }

    /**
     * Set the value of Debug.
     *
     * @param bool debug
     *
     * @return self
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Get the value of Debug.
     *
     * @return bool
     */
    public function getDebug(): bool
    {
        return $this->debug;
    }
}
