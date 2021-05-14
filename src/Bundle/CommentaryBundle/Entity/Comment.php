<?php

namespace Lotgd\Bundle\CommentaryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;
use FOS\CommentBundle\Model\RawCommentInterface;
use FOS\CommentBundle\Model\SignedCommentInterface;
use Lotgd\Bundle\CoreBundle\Entity\Common as CommonEntity;
use Lotgd\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="commentary_comment")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @ORM\AttributeOverrides({
 *     @ORM\AttributeOverride(name="body",
 *         column=@ORM\Column(
 *             type="text",
 *             length=65535
 *         )
 *     )
 * })
 */
class Comment extends BaseComment implements SignedCommentInterface, RawCommentInterface
{
    use Comment\Body;
    use CommonEntity\Avatar;
    use CommonEntity\Clan;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Thread of this comment.
     *
     * @var Thread
     * @ORM\ManyToOne(targetEntity="Lotgd\Bundle\CommentaryBundle\Entity\Thread")
     */
    protected $thread;

    /**
     * Author of the comment.
     *
     * @ORM\ManyToOne(targetEntity="Lotgd\Bundle\UserBundle\Entity\User")
     *
     * @var User
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $authorName = '';

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $command = '';

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", options={"default": 0, "unsigned": true})
     */
    private $clanRank = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $clanName = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     */
    private $clanNameShort = '';

    /**
     * Parameters for system messages.
     *
     * @ORM\Column(type="array")
     */
    private $params = [];

    public function setAuthor(?UserInterface $author)
    {
        $this->author = $author;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthorName(string $authorName): self
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setCommand(string $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function getClanRank(): int
    {
        return $this->clanRank;
    }

    public function setClanRank(int $clanRank): self
    {
        $this->clanRank = $clanRank;

        return $this;
    }

    public function getClanName(): string
    {
        return $this->clanName;
    }

    public function setClanName(string $clanName): self
    {
        $this->clanName = $clanName;

        return $this;
    }

    public function getClanNameShort(): string
    {
        return $this->clanNameShort;
    }

    public function setClanNameShort(string $clanNameShort): self
    {
        $this->clanNameShort = $clanNameShort;

        return $this;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getAuthorNameAlternative()
    {
        if ($this->isSystemComment())
        {
            return 'system';
        }

        if ( ! $this->getAuthor()->getAvatar())
        {
            return $this->getAuthor()->getUsername();
        }

        return $this->getAuthor()->getAvatar()->getName();
    }

    /**
     * Check if comment is a system message.
     */
    public function isSystemComment(): bool
    {
        //-- If command is system and/or no author is a system message.
        return 'system' == $this->getCommand() && ! $this->getAuthor() || ! $this->getAuthor();
    }

    /**
     * Check if comment is a game message.
     */
    public function isGameComment(): bool
    {
        return 'game' == $this->getCommand();
    }
}
