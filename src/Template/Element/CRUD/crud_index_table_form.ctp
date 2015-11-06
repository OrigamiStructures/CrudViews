<?php
use CrudViews\Lib\Uuid;
?>
<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<?php
			foreach ($this->Crud->whitelist() as $column_name) {
				echo '<th>' . $this->Paginator->sort($column_name) . '</th>';
			}
			?>
			<th class="actions"><?= __('Actions') ?></th>
		</tr>
	</thead>
    <tbody>
		<?php
		foreach (${$this->Crud->alias()->variableName} as $entity): 
			$this->Crud->entity = $entity;
			$uuid = new Uuid();
			$this->Crud->entity->_uuid = $uuid;
			?>
			<tr <?= $uuid->attr('id', 'row') ?> class="record">
				<td colspan="<?= count($this->Crud->whitelist()) ?>">
					<?= $this->Form->create($entity, ['id' => $uuid->uuid('form'), 'url' => ['action' => 'edit', $entity->id]]); ?>
					<?= $entity->table_tag ? $entity->table_tag : '<table>' ?>
		<tbody>
			<tr>
				<?php
				foreach ($this->Crud->whitelist() as $field) :
					echo "\t\t\t\t" . $this->Crud->output($field) . "\n";
				endforeach;
				?>
				<td> <?= $this->Form->submit('Save', ['form' => $uuid->uuid('form')]); ?> </td>
			</tr>
		</tbody>
	</table>
	<?= $this->Form->end(); ?>
	</td>
	<td class="actions">
		<?php
		$tools = $this->Crud->useActionPattern('record', $this->Crud->alias('string'), $this->request->action);
		foreach ($tools->content as $tool) {
			echo "\t\t\t\t" . $this->Crud->RecordAction->output($tools, $tool, $entity)  . "           \n";
		}
		?>
	</td>
	</tr>

<?php endforeach; ?>
</tbody>
</table>
