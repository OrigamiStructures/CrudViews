<?php
echo $this->Form->create(null, [
    'url' => ['action' => "simpleSearch/{$this->request->action}"]
]);
echo $this->Form->input('search');
echo $this->Form->end();