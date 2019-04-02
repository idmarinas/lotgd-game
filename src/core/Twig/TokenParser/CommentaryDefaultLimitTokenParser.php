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

namespace Lotgd\Core\Twig\TokenParser;

use Lotgd\Core\Twig\Node\CommentaryDefaultLimitNode;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Token Parser for the 'commentary_limit_comments' tag.
 */
class CommentaryDefaultLimitTokenParser extends AbstractTokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @return Node
     */
    public function parse(Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new CommentaryDefaultLimitNode($expr, $token->getLine(), $this->getTag());
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'commentary_limit_comments';
    }
}
