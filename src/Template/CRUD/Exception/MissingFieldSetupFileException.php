<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @since         3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace CrudViews\Template\CRUD\Exception;

use Cake\Core\Exception\Exception;

/**
 * Missing Action exception - used when a controller action
 * cannot be found, or when the controller's isAction() method returns false.
 */
class MissingFieldSetupFileException extends Exception
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = 'FieldSetups could not be found, or is not accessible.';

    /**
     * {@inheritDoc}
     */
    public function __construct($message, $code = 404)
    {
        parent::__construct($message, $code);
    }
}
