<?php
    $this->start('script');
        echo $this->Html->script('timekeep');
    $this->end();
    $recordZoneCols = isset($recordZoneCols) ? $recordZoneCols : 'large-10 medium-9';
    $entityCols = isset($entityCols) ? $entityCols : 'small-10';
    $actionCols = isset($actionCols) ? $actionCols : 'small-2';
?>
<div class="activities index columns <?=$recordZoneCols?>">
    <div class="row">
        <div class="columns <?=$entityCols?>">
            <div class="row">
                <?php
                    $this->Crud->strategy('responsivePaginatorHead');
                    foreach ($this->Crud->columns() as $column_name => $column_specs) {
                        echo $this->Crud->output($column_name);
                    }
                ?>
            </div>
        </div>
        <div class="columns <?=$actionCols?>"><?= __('Actions') ?></div>
    </div>
    <?php
        foreach (${$this->Crud->alias()->variableName} as $entity): 
            $this->Crud->entity = $entity;
    ?>
        <section class="records">
            <?php
                $this->Crud->strategy('responsiveRecordRows');
                foreach ($this->Crud->columns() as $field => $specs) :
                    echo "\t\t\t\t" . $this->Crud->output($field) . "\n";
                endforeach;
            ?>
            <div class="columns <?=$actionCols?>">
                <?php
                    $tools = $this->Crud->useActionPattern('record', $this->Crud->alias('string'), 'glphiconIndex');
                    foreach ($tools->content as $tool) {
                        echo $this->Crud->RecordAction->output($tools, $tool, $entity) . '               ';
                    }
                ?>
            </div>
        </section>
    <?php endforeach; ?>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
