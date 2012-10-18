<?php
class A
{
    function foo()
    {
        if (isset($this)) {
            echo '$this is defined (';
            echo get_class($this);
            echo ")<br>";
        } else {
            echo "\$this is not defined.<br>";
        }
    }
}

class B
{
    function bar()
    {
        // Note: the next line will issue a warning if E_STRICT is enabled.
        A::foo();
    }
}

$a = new A();
$a->foo();

// Note: the next line will issue a warning if E_STRICT is enabled.
A::foo();
$b = new B();
$b->bar();

// Note: the next line will issue a warning if E_STRICT is enabled.
B::bar();
?>