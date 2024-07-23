<?php

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class RemoveModelTest extends \Tests\BaseCase
{
    public function test_remove_model_simple()
    {
        // kinda tricky with anonymous classes
        //
        //$result = $this->getService()->removeModel('cat');

        // Exception ìì
        // $model = $this->newModel('Cat');
    }
}
