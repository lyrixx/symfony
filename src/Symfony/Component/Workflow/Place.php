<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Workflow;

use Symfony\Component\Workflow\Exception\InvalidArgumentException;

class Place
{

    /**
     * @var string
     */
    private $name;

    public function __construct($name)
    {
        if (!preg_match('{^[\w\d_-]+$}', $name)) {
            throw new InvalidArgumentException(sprintf('The place "%s" contains invalid characters.', $name));
        }
        $this->name = $name;
    }

    public static function fromNames(array $names)
    {
        $places = [];
        foreach ($names as $name) {
            $places[] = new self($name);
        }

        return $places;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
