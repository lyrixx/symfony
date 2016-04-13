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

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class Transition
{
    private $name;
    private $froms;
    private $tos;

    public function __construct($name, $froms, $tos)
    {
        $this->name = $name;
        $this->froms = (array) $froms;
        $this->tos = (array) $tos;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFroms()
    {
        return $this->froms;
    }

    public function getTos()
    {
        return $this->tos;
    }
}
