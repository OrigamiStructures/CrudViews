<?php
if (isset($flag)) { 
//	debug($this->Crud->CrudData->alias());//die;
}
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
        ?>
	        <tr class="record">
				<?php
				foreach ($this->Crud->whitelist() as $field) :
					echo "\t\t\t\t" . $this->Crud->output($field) . "\n";
				endforeach;
				?>
	            <td class="actions">
					<?php
					$tools = $this->Crud->useActionPattern('record', $this->Crud->alias('string'), 'index');
					foreach ($tools->content as $tool) {
						echo $this->Crud->RecordAction->output($tools, $tool, $entity) . '               ';
					}
					?>
	            </td>
	        </tr>

<?php endforeach; ?>
    </tbody>
</table>
