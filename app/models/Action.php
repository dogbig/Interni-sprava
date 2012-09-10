<?php

class Action
{

    public function delete($id)
    {
        return dibi::delete('is_actions')
                        ->where('action_id = %i', $id)
                        ->execute();
    }

    public function save(array $data, $id)
    {
        return dibi::update('is_actions', $data)
                        ->where('action_id = %i', $id)
                        ->execute();
    }

}