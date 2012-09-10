<?php

use \Nette\Environment as Env;

class Todo
{

    public function deleteMy($id)
    {
        return dibi::delete('is_todo')
                        ->where('todo_id = %i', $id)
                        ->where('user_id = %i', Env::getUser()->getId())
                        ->execute();
    }

    public function save(array $data, $id)
    {
        $data['prevuser_id'] = Env::getUser()->getId();

        if ($data["fwto_id"] == "") {
            unset($data['fwto_id']);
            $data['user_id'] = $data['prevuser_id'];
        } else {
            $data['user_id'] = $data['fwto_id'];
            unset($data['fwto_id']);
        }
        // Remove rubbish before save / update        
        unset($data['fwto']);

        return dibi::update('is_todo', $data)
                        ->where('todo_id = %i', $id)
                        ->where('user_id = %i', Env::getUser()->getId())
                        ->execute();
    }

    public function done($id, $done = true)
    {
        $data = NULL;
        $data['done'] = $done;
        return dibi::update('is_todo', $data)
                        ->where('todo_id = %i', $id)
                        ->where('user_id = %i', Env::getUser()->getId())
                        ->execute();
    }

}