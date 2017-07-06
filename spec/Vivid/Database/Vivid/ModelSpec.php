<?php

namespace spec\Vivid\Database\Vivid
{
    use Vivid\Database\Vivid\Model;
    use PhpSpec\ObjectBehavior;
    use Prophecy\Argument;

    class ModelSpec extends ObjectBehavior
    {
        function it_is_initializable()
        {
            $this->shouldHaveType(Model::class);
        }

        function it_adds_fields()
        {
            $this->a_field = 'a value';

            $this->a_field->shouldReturn('a value');
        }
    }
}
