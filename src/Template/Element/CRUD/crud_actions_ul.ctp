<?php
	$modelActions = $this->Crud->useActionPattern('model', $this->Crud->alias()->modelName, $this->request->action);
	$entity = isset(${$this->Crud->alias()->variableName}) ? ${$this->Crud->alias()->variableName} : NULL;
?>
<ul class="side-nav">

<?php  
// Loop for the primary models actions 
foreach ($modelActions as $label => $tool) : 
?>
	<li> <?= $this->Crud->ModelAction->output($tool, $label, $this->Crud->alias(), $entity) ?> </li>
<?php 
endforeach;
// done with the primary model
?>
</ul>

<ul class="side-nav">
<?php
//debug($this->Crud->AssociationActions);
//debug($this->Crud->associations());die;
// loop for the associated models
foreach ($this->Crud->associations() as $key => $value) :
	$assocActions = $this->Crud->useActionPattern('association', $value['name'], $this->request->action);
	// now loop the actions for this model
	foreach ($this->Crud->AssociationActions as $label => $tool) :
	
?>
	<li> <?= $this->Crud->ModelAction->output($tool, $label, $value['name'], $entity) ?> </li>
<?php
	endforeach;
endforeach;
// done with the associated models and thier actions
?>
</ul>
