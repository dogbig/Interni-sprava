<?php

use \Nette\Environment as Env;

class TodoManager
{

    // How to get all user todo
    public function getMy($limit, $offset, $userId)
    {
        $fluent = dibi::select('*')
                ->from('is_todo')
                ->orderBy('todo_id desc')
                ->where('user_id = %i', $userId);

        return $fluent->fetchAll($offset, $limit);
    }
    
    // This will only return one todo of user
    public function getMyTodo($id, $userId)
    {
        $fluent = dibi::select('*')
                ->from('is_todo')
                ->where('user_id = %i', $userId)
                ->where('todo_id = %i', $id);

        return $fluent->fetch();
    }
    
    // Delete done
    public function deleteDone($userId)
    {
        return dibi::delete('is_todo')
                        ->where('done = %i',true)
                        ->where('user_id = %i', $userId)
                        ->execute();
    }
    
    // Count user tasks OR count done tasks
    public function countMy($userId, $countOnlyDone = false)
    {
        $fluent = dibi::select('COUNT(todo_id)')
                ->from('is_todo')
                ->where('user_id = %i',$userId);
        if ($countOnlyDone == true) {
            $fluent->where('done = true');
        }
        
        return $fluent->fetchSingle();
    }

    public function createMy(array $todo)
    {
        $todo['prevuser_id']=Env::getUser()->getId();
        
        if ($todo["fwto_id"]=="") {
            unset($todo['fwto_id']);
            $todo['user_id'] = $todo['prevuser_id'];
        } else {
            $todo['user_id'] = $todo['fwto_id'];
            unset($todo['fwto_id']);
            
        }
        // Remove rubbish before save / update        
        unset($todo['fwto']);     
        
        return dibi::insert('is_todo', $todo)->execute();
    }

}