<?php
	$modelActions = $this->Crud->useActionPattern('model', 'Menus', $this->request->action);
?>
<ul class="side-nav">

<?php  
// Loop for the primary models actions 
foreach ($modelActions->content as $tool) : 
?>
	<li> <?= $this->Crud->ModelAction->output($modelActions, $tool, $this->Crud->alias()) ?> </li>
<?php 
endforeach;
// done with the primary model
?>
</ul>

<ul class="side-nav">
<?php
// loop for the associated models
foreach ($this->Crud->associations() as $key => $value) :
	$assocActions = $this->Crud->useActionPattern('association', $value['name'], $this->request->action);
	// now loop the actions for this model
	foreach ($assocActions->content as $tool) :
	
?>
	<li> <?= $this->Crud->ModelAction->output($modelActions, $tool, $value['name']) ?> </li>
<?php
	endforeach;
endforeach;
// done with the associated models and thier actions
?>
</ul>
