<?php $ent = strtolower(Cake\Utility\Inflector::singularize($this->request->controller)); //debug($$ent); debug($ent); ?>
<?php
$label = ucwords($this->request->action . ' ' . Cake\Utility\Inflector::singularize($this->request->controller));
     echo $this->Form->create(strtolower($label)); 
//	 debug($this->viewVars);
?>
    <fieldset>
        <legend><?= __("$label") ?></legend>
        <?php
			foreach ($this->Crud->columns() as $field => $type) :
				echo $this->Form->input($field, ['value' => (isset($$ent) ? $$ent->$field : '')]);
			endforeach;
        if (!empty($associations['BelongsToMany'])) {
            foreach ($associations['BelongsToMany'] as $assocName => $assocData) {
            echo $this->Form->input($assocData['property'] . '_ids', ['options' => ${$assocData['variable']}, 'multiple' => TRUE]);
            }
		}
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
