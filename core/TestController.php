<?php

require_once 'BaseController.php';

class TestController extends BaseController
{
    public function holaGet()
    {
        return "Hola desde GET";
    }
}
