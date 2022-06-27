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

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * News.
 *
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(name="account_id", columns={"account_id"}),
 *         @ORM\Index(name="date", columns={"date"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\NewsRepository")
 */
class News
{
    /**
     *
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     *
     * @ORM\Column(type="date", options={"default"="0000-00-00"})
     */
    private ?\DateTimeInterface $date;

    /**
     *
     * @ORM\Column(type="text", length=65535)
     */
    private ?string $text = null;

    /**
     *
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private ?int $accountId = 0;

    /**
     *
     * @ORM\Column(type="array")
     */
    private array $arguments = [];

    /**
     *
     * @ORM\Column(type="boolean", options={"default"=1})
     */
    private ?bool $newFormat = true;

    /**
     *
     * @ORM\Column(type="string", length=255, options={"default"="partial_news"})
     */
    private ?string $textDomain = '';

    public function __construct()
    {
        $this->date = new DateTime('now');
    }

    /**
     * Set the value of id.
     *
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of date.
     *
     * @param \DateTime $date
     *
     * @return self
     */
    public function setDate(DateTimeInterface $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of date.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Set the value of text.
     *
     * @param string $text
     *
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the value of text.
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Set the value of AccountId.
     *
     * @param int $accountId
     *
     * @return self
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get the value of AccountId.
     */
    public function getAccountId(): int
    {
        return $this->accountId;
    }

    /**
     * Set the value of Arguments.
     *
     * @param array $arguments
     *
     * @return self
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Get the value of Arguments.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Set the value of TextDomain.
     *
     * @param string $textDomain
     *
     * @return self
     */
    public function setTextDomain($textDomain)
    {
        $this->textDomain = $textDomain;

        return $this;
    }

    /**
     * Get the value of TextDomain.
     */
    public function getTextDomain(): string
    {
        return $this->textDomain;
    }

    /**
     * Set the value of newFormat.
     *
     * @param bool $newFormat
     *
     * @return self
     */
    public function setNewFormat($newFormat)
    {
        $this->newFormat = $newFormat;

        return $this;
    }

    /**
     * Get the value of newFormat.
     */
    public function getNewFormat(): bool
    {
        return $this->newFormat;
    }
}
