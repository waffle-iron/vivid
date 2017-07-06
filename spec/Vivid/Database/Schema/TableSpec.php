<?php

namespace spec\Vivid\Database\Schema
{
    use Vivid\Database\Schema\Table;
    use PhpSpec\ObjectBehavior;
    use Prophecy\Argument;

    class TableSpec extends ObjectBehavior
    {
        function it_is_initializable()
        {
            $this->beConstructedWith('Table');
            $this->shouldHaveType(Table::class);
        }
    }
}
