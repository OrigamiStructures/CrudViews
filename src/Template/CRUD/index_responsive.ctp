<?php
$this->append('css');
    echo $this->Html->css('CrudViews.crudBase');
$this->end();
echo $this->element('CrudViews.CRUD/index_responsive');
