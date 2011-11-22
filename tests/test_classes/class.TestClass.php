<?php  

class TestClass {
    const A_CONSTANT = 12;

    /**
     * Stay out ;)
     */
    private $property;

    /**
     * tagged docblock.
     *
     * var: string
     */
    public static $staying_here = 'yup';

    /**
     * This is a comment.
     *
     * Desc With another line.   
     * Desc line 2.
     *
     * Parameters:
     *   - $param, string. The class name.
     *   - $param2, string. The property name.
     */
    public function test($param, $param2 = 'default') {

    }

    private function test_private() {

    }

    function just_a_func() {

    }
}

