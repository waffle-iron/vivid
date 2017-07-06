<?php

namespace spec\Vivid\Database\Vivid
{
    use Vivid\Database\Vivid\Collection;
    use PhpSpec\ObjectBehavior;
    use Prophecy\Argument;

    class CollectionSpec extends ObjectBehavior
    {
        function it_is_initializable()
        {
            $this->beConstructedWith('A\\Model\\Class');
            $this->shouldHaveType(Collection::class);
        }
    }
}
