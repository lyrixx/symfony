<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Workflow\Dumper;

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\Place;
use Symfony\Component\Workflow\Transition;

/**
 * GraphvizDumper dumps a workflow as a graphviz file.
 *
 * You can convert the generated dot file with the dot utility (http://www.graphviz.org/):
 *
 *   dot -Tpng workflow.dot > workflow.png
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class GraphvizDumper implements DumperInterface
{
    private static $defaultOptions = array(
        'graph' => array('ratio' => 'compress', 'rankdir' => 'LR'),
        'node' => array('fontsize' => 9, 'fontname' => 'Arial', 'color' => '#333333', 'fillcolor' => 'lightblue', 'fixedsize' => true, 'width' => 1),
        'edge' => array('fontsize' => 9, 'fontname' => 'Arial', 'color' => '#333333', 'arrowhead' => 'normal', 'arrowsize' => 0.5),
    );

    /**
     * Dumps the workflow as a graphviz graph.
     *
     * Available options:
     *
     *  * graph: The default options for the whole graph
     *  * node: The default options for nodes (places + transitions)
     *  * edge: The default options for edges
     *
     * @param Definition $definition A Definition instance
     * @param Marking    $marking    A Marking instance
     * @param array      $options    An array of options
     *
     * @return string The dot representation of the workflow
     */
    public function dump(Definition $definition, Marking $marking = null, array $options = array())
    {
        $places = $this->findPlaces($definition, $marking);
        $transitions = $this->findTransitions($definition);
        $edges = $this->findEdges($definition);

        $options = array_replace_recursive(self::$defaultOptions, $options);

        return $this->startDot($options)
            .$this->addPlaces($places)
            .$this->addTransitions($transitions)
            .$this->addEdges($edges)
            .$this->endDot();
    }

    /**
     * Finds all places.
     *
     * @return array An array of all places
     */
    private function findPlaces(Definition $definition, Marking $marking = null)
    {
        $places = array();

        /** @var Place $place */
        foreach ($definition->getPlaces() as $place) {
            $attributes = [];
            if ($place === $definition->getInitialPlace()) {
                $attributes['style'] = 'filled';
            }
            if ($marking && $marking->has($place)) {
                $attributes['color'] = '#FF0000';
                $attributes['shape'] = 'doublecircle';
            }
            $places[$place->getName()] = array(
                'attributes' => $attributes,
            );
        }

        return $places;
    }

    /**
     * Finds all transitions.
     *
     * @return array An array of all transitions
     */
    private function findTransitions(Definition $definition)
    {
        $transitions = array();

        foreach ($definition->getTransitions() as $name => $transition) {
            $transitions[$name] = array(
                'attributes' => array('shape' => 'box', 'regular' => true),
            );
        }

        return $transitions;
    }

    /**
     * Returns all places.
     *
     * @return string A string representation of all places
     */
    private function addPlaces(array $places)
    {
        $code = '';

        foreach ($places as $id => $place) {
            $code .= sprintf("  place_%s [label=\"%s\", shape=circle%s];\n",
                $this->dotize($id),
                $id,
                $this->addAttributes($place['attributes'])
            );
        }

        return $code;
    }

    /**
     * Returns all transitions.
     *
     * @return string A string representation of all transitions
     */
    private function addTransitions(array $transitions)
    {
        $code = '';

        foreach ($transitions as $id => $place) {
            $code .= sprintf("  transition_%s [label=\"%s\", shape=box%s];\n",
                $this->dotize($id),
                $id,
                $this->addAttributes($place['attributes'])
            );
        }

        return $code;
    }

    private function findEdges(Definition $definition)
    {
        $dotEdges = array();

        /** @var Transition $transition */
        foreach ($definition->getTransitions() as $transition) {
            /** @var Place $from */
            foreach ($transition->getFroms() as $from) {
                $dotEdges[] = array(
                    'from' => $from->getName(),
                    'to' => $transition->getName(),
                    'direction' => 'from',
                );
            }
            /** @var Place $to */
            foreach ($transition->getTos() as $to) {
                $dotEdges[] = array(
                    'from' => $transition->getName(),
                    'to' => $to->getName(),
                    'direction' => 'to',
                );
            }
        }

        return $dotEdges;
    }

    /**
     * Returns all edges.
     *
     * @return string A string representation of all edges
     */
    private function addEdges($edges)
    {
        $code = '';

        foreach ($edges as$edge) {
            $code .= sprintf("  %s_%s -> %s_%s [style=\"solid\"];\n",
                $edge['direction'] === 'from' ? 'place' : 'transition',
                $this->dotize($edge['from']),
                $edge['direction'] === 'from' ? 'transition' : 'place',
                $this->dotize($edge['to'])
            );
        }

        return $code;
    }

    /**
     * Returns the start dot.
     *
     * @return string The string representation of a start dot
     */
    private function startDot(array $options)
    {
        return sprintf("digraph workflow {\n  %s\n  node [%s];\n  edge [%s];\n\n",
            $this->addOptions($options['graph']),
            $this->addOptions($options['node']),
            $this->addOptions($options['edge'])
        );
    }

    /**
     * Returns the end dot.
     *
     * @return string
     */
    private function endDot()
    {
        return "}\n";
    }

    /**
     * Adds attributes.
     *
     * @param array $attributes An array of attributes
     *
     * @return string A comma separated list of attributes
     */
    private function addAttributes($attributes)
    {
        $code = array();
        foreach ($attributes as $k => $v) {
            $code[] = sprintf('%s="%s"', $k, $v);
        }

        return $code ? ', '.implode(', ', $code) : '';
    }

    /**
     * Adds options.
     *
     * @param array $options An array of options
     *
     * @return string A space separated list of options
     */
    private function addOptions($options)
    {
        $code = array();
        foreach ($options as $k => $v) {
            $code[] = sprintf('%s="%s"', $k, $v);
        }

        return implode(' ', $code);
    }

    /**
     * Dotizes an identifier.
     *
     * @param string $id The identifier to dotize
     *
     * @return string A dotized string
     */
    private function dotize($id)
    {
        return strtolower(preg_replace('/[^\w]/i', '_', $id));
    }
}
