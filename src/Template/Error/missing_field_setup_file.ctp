<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Utility\Inflector;

$pluginDot = empty($plugin) ? null : $plugin . '.';
$namespace = Configure::read('App.namespace');
$prefixNs = '';
$prefixPath = '';

if (!empty($prefix)) {
    $prefix = Inflector::camelize($prefix);
    $prefixNs = '\\' . $prefix;
    $prefixPath = $prefix . DS;
}

$this->layout = 'dev_error';

$this->assign('title', 'Missing FieldSetups File');
$this->assign('templateName', 'missing_field_setups_file.ctp');

$this->start('subheading');
?>
<strong>Error: </strong>
<em>FieldSetups</em> could not be found.
<?php $this->end() ?>

<?php $this->start('file') ?>
<p class="error">
    <strong>Error: </strong>
    Create the class <em>FieldSetups</em> below in file: src/View/CrudViewResources/FieldSetups.php
</p>

<?php
$code = <<<PHP
<?php
namespace App\View\Helper\CrudViewResources;

use CrudViews\View\Helper\CRUD\Decorator;

class FieldSetups
{

}
PHP;
?>
<div class="code-dump"><?php highlight_string($code) ?></div>
<?php $this->end() ?>
