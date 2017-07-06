<?php

namespace spec\Vivid\Database\Schema
{
    use Vivid\Database\Schema\Column;
    use Vivid\Database\Schema\Type\Varchar;
    use PhpSpec\ObjectBehavior;
    use Prophecy\Argument;

    class ColumnSpec extends ObjectBehavior
    {
        function it_is_initializable()
        {
            $this->beConstructedWith(
                'name',
                new Varchar(20)
            );
            $this->shouldHaveType(Column::class);
        }
    }
}
