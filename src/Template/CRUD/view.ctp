<?php
use Cake\Utility\Inflector;
use \Cake\ORM\TableRegistry;
use CrudViews\Lib\CrudConfig;
?>
<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
	<?= $this->element('CrudViews.CRUD/crud_actions_ul'); ?>
</div>

<?php
// send the entity to the helper for output services
$entityName = $this->Crud->alias()->singularName;
$this->Crud->entity = $$entityName;

// Group the fields by type for the standard view page
foreach ($this->Crud->columns() as $field => $data) {
	$type = $this->Crud->columnType($field);
	if (isset($this->Crud->foreignKeys()[$type])) {
		$this->start('string');
		echo "<p>{$this->Crud->output($field)}</p>\n";
		$this->end();
		continue;
	}
	switch ($type) {
		case 'integer':
		case 'float':
		case 'decimal':
		case 'biginteger':
//			debug('number');
//			debug($this->Crud->output($field));
			$this->append('number');
			echo '<h6 class="subheader">'.__(Inflector::humanize($field))."</h6>\n";
			echo "<p>{$this->Crud->output($field)}</p>\n";
			$this->end();
			break;
		case 'date':
		case 'time':
		case 'datetime':
		case 'timestamp':
//			debug('date');
//			debug($this->Crud->output($field));
			$this->append('date');
			echo '<h6 class="subheader">'.__(Inflector::humanize($field))."</h6>\n";
			echo "<p>{$this->Crud->output($field)}</p>\n";
			$this->end();
			break;
		case 'text':
			$this->append('text');
			echo "<div class=\"columns large-9\">\n";
			echo '<h6 class="subheader">'.__(Inflector::humanize($field))."</h6>\n";
			echo "<p>{$this->Crud->output($field)}</p>\n";
			echo "</div>\n";
			$this->end();
			break;
		case 'boolean': 
			$this->append('boolean');
			echo '<h6 class="subheader">'.__(Inflector::humanize($field))."</h6>\n";
			echo "<p>{$this->Crud->output($field)}</p>\n";
			$this->end();
			break;
		default:
			$this->append('string');
			echo '<h6 class="subheader">'.__(Inflector::humanize($field))."</h6>\n";
			echo "<p>{$this->Crud->output($field)}</p>\n";
			$this->end();
			break;
	}
}

// Output the blocks that gather each type
?>
<div class="<?= $this->Crud->alias()->variableName; ?> view large-10 medium-9 columns">

    <h2><?= h($this->Crud->displayField()) ?></h2>
    <div class="row">
		<?php if ($this->fetch('string')) : ?>
			<div class="large-5 columns strings">
				<!--this will be watched by the decorator for association links-->
				<?= $this->fetch('string') ?>
			</div>
		<?php endif; ?>
		<?php if ($this->fetch('number')) : ?>
			<div class="large-2 columns numbers end">
				<?= $this->fetch('number'); ?>
			</div>
			<?php endif; ?>
		<?php if ($this->fetch('date')) : ?>
			<div class="large-2 columns dates end">
				<?= $this->fetch('date'); ?>
			</div>
			<?php endif; ?>
		<?php if ($this->fetch('boolean')) : ?>
			<div class="large-2 columns booleans end">
				<?= $this->fetch('boolean'); ?>
			</div>
		<?php endif; ?>
    </div>
	<?php if ($this->fetch('text')) : ?>
		<div class="row texts">
			<?= $this->fetch('text'); ?>
		</div>
	<?php endif; ?>
</div>
<?php
// Now show the related table data

$associations = collection($this->Crud->associations());
$associated = $associations->filter(function($association, $foreignKey) {
	return stristr($association['class'], 'HasMany') || stristr($association['class'], 'BelongsToMany');
});
foreach ($associated as $assoc) :
?>
	<div class="related row">
		<div class="column large-12">
			<h4 class="subheader"><?=  __('Related ' . Inflector::humanize($assoc['property'])); ?></h4>
		</div>
	</div>

<?php
	if ($$entityName->{$assoc['property']}) :
		// move the nested entity data for this association to its own variable
		${$assoc['property']} = $$entityName->{$assoc['property']};
		$this->set($assoc['property'], $$entityName->{$assoc['property']});
		
		// create the crud data element for this data
		// this should be done on the controller prior to arrival
		// or called for through a method... possibly a view cell? (new feature)
		// _CrudData was made public for this call. 
		// we could also make a CrudHelper method to do this stuff
		$this->Crud->configIndex($assoc['name']->modelName);
		$this->Crud->useCrudData($assoc['name']->modelName);
		
		// and make the table view
		// this is why we need to have an external method to do the setup
		// there are no action or field setups in place doing things from here
		echo $this->element('CrudViews.CRUD/crud_index_table');
	endif;
endforeach;
