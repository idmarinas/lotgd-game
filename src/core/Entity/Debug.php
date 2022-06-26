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
 * Debug.
 *
 * @ORM\Table
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\DebugRepository")
 */
class Debug
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=100, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="category", type="string", length=100, nullable=true)
     */
    private $category;

    /**
     * @var string|null
     *
     * @ORM\Column(name="subcategory", type="string", length=100, nullable=true)
     */
    private $subcategory;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value", type="string", length=100, nullable=true)
     */
    private $value;

    /**
     * Set the value of Id.
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
     * Get the value of Id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of Type.
     *
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of Type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the value of Category.
     *
     * @param string $category
     *
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of Category.
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Set the value of Subcategory.
     *
     * @param string $subcategory
     *
     * @return self
     */
    public function setSubcategory($subcategory)
    {
        $this->subcategory = $subcategory;

        return $this;
    }

    /**
     * Get the value of Subcategory.
     */
    public function getSubcategory(): string
    {
        return $this->subcategory;
    }

    /**
     * Set the value of Value.
     *
     * @param string $value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of Value.
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
