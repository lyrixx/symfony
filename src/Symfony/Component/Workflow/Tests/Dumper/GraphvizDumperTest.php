<?php

namespace Symfony\Component\Workflow\Tests\Dumper;


use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\Place;
use Symfony\Component\Workflow\Transition;

class GraphvizDumperTest extends \PHPUnit_Framework_TestCase
{

    private $dumper;

    public function setUp()
    {
        parent::setUp();
        $this->dumper = new GraphvizDumper();
    }

    /**
     * @dataProvider provideWorkflowDefinitionWithoutMarking
     */
    public function testGraphvizDumperWithoutMarking($definition, $expected)
    {
        $dump = $this->dumper->dump($definition);

        $this->assertEquals($expected, $dump);
    }
    
    /**
     * @dataProvider provideWorkflowDefinitionWithMarking
     */
    public function testWorkflowWithMarking($definition, $marking, $expected)
    {
        $dump = $this->dumper->dump($definition, $marking);
        
        $this->assertEquals($expected, $dump);
    }

    public function provideWorkflowDefinitionWithMarking()
    {
        return [
            [
                $this->provideComplexWorkflowDefinition(),
                new Marking(['b' => 1]),
                $this->provideComplexWorkflowDumpWithMarking()
            ],
            [
                $this->provideSimpleWorkflowDefinition(),
                Marking::fromPlaces(Place::fromNames(['c', 'd'])),
                $this->provideSimpleWorkflowDumpWithMarking()
            ],
        ];
    }
    
    public function provideWorkflowDefinitionWithoutMarking()
    {
        return [
            [$this->provideComplexWorkflowDefinition(), $this->provideComplexWorkflowDumpWithoutMarking()],
            [$this->provideSimpleWorkflowDefinition(), $this->provideSimpleWorkflowDumpWithoutMarking()],
        ];
    }
    
    public function provideComplexWorkflowDefinition()
    {
        $places = Place::fromNames(range('a', 'g'));
        $transitions = [
            new Transition('t1', new Place('a'), Place::fromNames(['b', 'c'])),
            new Transition('t2', new Place('c'), new Place('d')),
            new Transition('t3', new Place('b'), new Place('e')),
            new Transition('t4', Place::fromNames(['d', 'e']), new Place('f')),
            new Transition('t5', new Place('f'), new Place('g')),
        ];
        
        return new Definition($places, $transitions);
    }
    
    public function provideComplexWorkflowDumpWithMarking()
    {
        return 'digraph workflow {
  ratio="compress" rankdir="LR"
  node [fontsize="9" fontname="Arial" color="#333333" fillcolor="lightblue" fixedsize="1" width="1"];
  edge [fontsize="9" fontname="Arial" color="#333333" arrowhead="normal" arrowsize="0.5"];

  place_a [label="a", shape=circle, style="filled"];
  place_b [label="b", shape=circle, color="#FF0000", shape="doublecircle"];
  place_c [label="c", shape=circle];
  place_d [label="d", shape=circle];
  place_e [label="e", shape=circle];
  place_f [label="f", shape=circle];
  place_g [label="g", shape=circle];
  transition_t1 [label="t1", shape=box, shape="box", regular="1"];
  transition_t2 [label="t2", shape=box, shape="box", regular="1"];
  transition_t3 [label="t3", shape=box, shape="box", regular="1"];
  transition_t4 [label="t4", shape=box, shape="box", regular="1"];
  transition_t5 [label="t5", shape=box, shape="box", regular="1"];
  place_a -> transition_t1 [style="solid"];
  transition_t1 -> place_b [style="solid"];
  transition_t1 -> place_c [style="solid"];
  place_c -> transition_t2 [style="solid"];
  transition_t2 -> place_d [style="solid"];
  place_b -> transition_t3 [style="solid"];
  transition_t3 -> place_e [style="solid"];
  place_d -> transition_t4 [style="solid"];
  place_e -> transition_t4 [style="solid"];
  transition_t4 -> place_f [style="solid"];
  place_f -> transition_t5 [style="solid"];
  transition_t5 -> place_g [style="solid"];
}
';
    }
    
    public function provideSimpleWorkflowDumpWithMarking()
    {
        return 'digraph workflow {
  ratio="compress" rankdir="LR"
  node [fontsize="9" fontname="Arial" color="#333333" fillcolor="lightblue" fixedsize="1" width="1"];
  edge [fontsize="9" fontname="Arial" color="#333333" arrowhead="normal" arrowsize="0.5"];

  place_a [label="a", shape=circle, style="filled"];
  place_b [label="b", shape=circle];
  place_c [label="c", shape=circle, color="#FF0000", shape="doublecircle"];
  transition_t1 [label="t1", shape=box, shape="box", regular="1"];
  transition_t2 [label="t2", shape=box, shape="box", regular="1"];
  place_a -> transition_t1 [style="solid"];
  transition_t1 -> place_b [style="solid"];
  place_b -> transition_t2 [style="solid"];
  transition_t2 -> place_c [style="solid"];
}
';
    }
    
    public function provideComplexWorkflowDumpWithoutMarking()
    {
        return 'digraph workflow {
  ratio="compress" rankdir="LR"
  node [fontsize="9" fontname="Arial" color="#333333" fillcolor="lightblue" fixedsize="1" width="1"];
  edge [fontsize="9" fontname="Arial" color="#333333" arrowhead="normal" arrowsize="0.5"];

  place_a [label="a", shape=circle, style="filled"];
  place_b [label="b", shape=circle];
  place_c [label="c", shape=circle];
  place_d [label="d", shape=circle];
  place_e [label="e", shape=circle];
  place_f [label="f", shape=circle];
  place_g [label="g", shape=circle];
  transition_t1 [label="t1", shape=box, shape="box", regular="1"];
  transition_t2 [label="t2", shape=box, shape="box", regular="1"];
  transition_t3 [label="t3", shape=box, shape="box", regular="1"];
  transition_t4 [label="t4", shape=box, shape="box", regular="1"];
  transition_t5 [label="t5", shape=box, shape="box", regular="1"];
  place_a -> transition_t1 [style="solid"];
  transition_t1 -> place_b [style="solid"];
  transition_t1 -> place_c [style="solid"];
  place_c -> transition_t2 [style="solid"];
  transition_t2 -> place_d [style="solid"];
  place_b -> transition_t3 [style="solid"];
  transition_t3 -> place_e [style="solid"];
  place_d -> transition_t4 [style="solid"];
  place_e -> transition_t4 [style="solid"];
  transition_t4 -> place_f [style="solid"];
  place_f -> transition_t5 [style="solid"];
  transition_t5 -> place_g [style="solid"];
}
';
    }
    
    public function provideSimpleWorkflowDumpWithoutMarking()
    {
        return 'digraph workflow {
  ratio="compress" rankdir="LR"
  node [fontsize="9" fontname="Arial" color="#333333" fillcolor="lightblue" fixedsize="1" width="1"];
  edge [fontsize="9" fontname="Arial" color="#333333" arrowhead="normal" arrowsize="0.5"];

  place_a [label="a", shape=circle, style="filled"];
  place_b [label="b", shape=circle];
  place_c [label="c", shape=circle];
  transition_t1 [label="t1", shape=box, shape="box", regular="1"];
  transition_t2 [label="t2", shape=box, shape="box", regular="1"];
  place_a -> transition_t1 [style="solid"];
  transition_t1 -> place_b [style="solid"];
  place_b -> transition_t2 [style="solid"];
  transition_t2 -> place_c [style="solid"];
}
';
    }
    
    public function provideSimpleWorkflowDefinition()
    {
        $places = Place::fromNames(range('a', 'c'));
        $transitions = [
            new Transition('t1', new Place('a'), new Place('b')),
            new Transition('t2', new Place('b'), new Place('c')),
        ];
        return new Definition($places, $transitions);
    }
}
