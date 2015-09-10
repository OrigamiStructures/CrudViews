<?php

/* 
 * Copyright 2015 Origami Structures
 */

namespace CrudViews\Error;

use Cake\Error\BaseErrorHandler;

class CrudError extends BaseErrorHandler
{
    public function _displayError($error, $debug)
    {
        return 'There has been an error!';
    }
    public function _displayException($exception)
    {
        return 'There has been an exception!';
    }
}