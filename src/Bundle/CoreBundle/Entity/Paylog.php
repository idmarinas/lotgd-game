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

namespace Lotgd\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paylog.
 *
 * @ORM\Table(name="paylog",
 *     indexes={
 *         @ORM\Index(name="txnid", columns={"txnid"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Bundle\CoreBundle\Repository\PaylogRepository")
 */
class Paylog
{
    /**
     * @var int
     *
     * @ORM\Column(name="payid", type="integer", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $payid;

    /**
     * @var array
     *
     * @ORM\Column(name="info", type="array")
     */
    private $info = [];

    /**
     * @var string
     *
     * @ORM\Column(name="response", type="text", length=65535)
     */
    private $response = '';

    /**
     * @var string
     *
     * @ORM\Column(name="txnid", type="string", length=32)
     */
    private $txnid = '';

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", precision=9, scale=2)
     */
    private $amount = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name = '';

    /**
     * @var int
     *
     * @ORM\Column(name="acctid", type="integer", options={"unsigned": true})
     */
    private $acctid = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="processed", type="boolean", nullable=false)
     */
    private $processed = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="filed", type="boolean", nullable=false)
     */
    private $filed = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="txfee", type="float", precision=9, scale=2, nullable=false)
     */
    private $txfee = '0.00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="processdate", type="datetime", nullable=false)
     */
    private $processdate;

    public function __construct()
    {
        $this->processdate = new \DateTime();
    }

    public function __toString(): string
    {
        return $this->getTxnid();
    }

    /**
     * Set the value of Payid.
     *
     * @param int $payid
     *
     * @return self
     */
    public function setPayid($payid)
    {
        $this->payid = $payid;

        return $this;
    }

    /**
     * Get the value of Payid.
     */
    public function getPayid(): ?int
    {
        return $this->payid;
    }

    /**
     * Set the value of Info.
     *
     * @param string $info
     *
     * @return self
     */
    public function setInfo(array $info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get the value of Info.
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * Set the value of Response.
     *
     * @param string $response
     *
     * @return self
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get the value of Response.
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * Set the value of Txnid.
     *
     * @param string $txnid
     *
     * @return self
     */
    public function setTxnid($txnid)
    {
        $this->txnid = $txnid;

        return $this;
    }

    /**
     * Get the value of Txnid.
     */
    public function getTxnid(): string
    {
        return $this->txnid;
    }

    /**
     * Set the value of Amount.
     *
     * @param float $amount
     *
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of Amount.
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Set the value of Name.
     *
     * @param string $name
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
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of Acctid.
     *
     * @param int $acctid
     *
     * @return self
     */
    public function setAcctid($acctid)
    {
        $this->acctid = $acctid;

        return $this;
    }

    /**
     * Get the value of Acctid.
     */
    public function getAcctid(): int
    {
        return $this->acctid;
    }

    /**
     * Set the value of Processed.
     *
     * @param bool $processed
     *
     * @return self
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * Get the value of Processed.
     */
    public function getProcessed(): bool
    {
        return $this->processed;
    }

    /**
     * Set the value of Filed.
     *
     * @param bool $filed
     *
     * @return self
     */
    public function setFiled($filed)
    {
        $this->filed = $filed;

        return $this;
    }

    /**
     * Get the value of Filed.
     */
    public function getFiled(): bool
    {
        return $this->filed;
    }

    /**
     * Set the value of Txfee.
     *
     * @param float $txfee
     *
     * @return self
     */
    public function setTxfee($txfee)
    {
        $this->txfee = $txfee;

        return $this;
    }

    /**
     * Get the value of Txfee.
     */
    public function getTxfee(): float
    {
        return $this->txfee;
    }

    /**
     * Set the value of Processdate.
     *
     * @return self
     */
    public function setProcessdate(\DateTime $processdate)
    {
        $this->processdate = $processdate;

        return $this;
    }

    /**
     * Get the value of Processdate.
     */
    public function getProcessdate(): \DateTime
    {
        return $this->processdate;
    }
}
