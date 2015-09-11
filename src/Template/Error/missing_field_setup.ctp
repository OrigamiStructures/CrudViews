<?php
/**
 *
 * @copyright     Copyright (c) Don Drake
 * @link          http://OrigamiStructures.com
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Utility\Inflector;

//$pluginDot = empty($plugin) ? null : $plugin . '.';
//$namespace = Configure::read('App.namespace');
//$prefixNs = '';
//$prefixPath = '';
//
//if (!empty($prefix)) {
//    $prefix = Inflector::camelize($prefix);
//    $prefixNs = '\\' . $prefix;
//    $prefixPath = $prefix . DS;
//}

$this->layout = 'dev_error';

$this->assign('title', 'Missing Method in FieldSetups');
$this->assign(
    'subheading',
    sprintf('The action <em>%s</em> is not defined in <em>FieldSetups</em>', h($action))
);
$this->assign('templateName', 'missing_action.ctp');

?>

<?php $this->start('file') ?>
<p class="error">
    <strong>Error: </strong>
    <?= sprintf('Create <em>FieldSetups::%s()</em> in file: src/View/CrudViewResources/FieldSetups.php.', h($action)); ?>
</p>

<?php
$code = <<<PHP
<?php
namespace App\View\Helper\CrudViewResources;

use CrudViews\View\Helper\CRUD\Decorator;

class FieldSetups
{
    public function {$action}(\$helper)
    {

    }

}
<?php
PHP;
?>
<div class="code-dump"><?php highlight_string($code) ?></div>
<?php $this->end() ?>
