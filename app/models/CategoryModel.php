<?php 

namespace App\Models;

use Core\BaseModel;

class CategoryModel extends BaseModel {
    
    public function __construct()
    {   
        parent::__construct('categories');
    }
    
    public function getAll($queryParam = [])
    {
        return $this->all($this -> table, $queryParam);         
    }

    public function findById($id) {
       return ($this -> find ($this->table, $id));
    }

    /**
     * @ $data ist array
     *  colname => value
     * Create a New 
     */

    public function store($data) {
        $this->create($this->table, $data);
    } 

    public function save($id, $data) 
    {
        $this -> update($this->table, $id, $data);
    } 

    public function destroy($id) 
    {
        $this->delete($this->table, $id);
    } 

    public function getTotal()
    {
        return $this->count($this -> table);         
    }

    # html-select-option 
    # alle category, die zur $currentId gehört, nicht auflisten
    public function categorySelectOption($seletedId = '', $inactiveId = '-1'){

        function recursive ($source, $parent, $level, &$newArray, $inactiveId) {
            if (count($source) > 0) {
                if ($parent != $inactiveId) {
                    foreach ($source as $key => $value) { 
                        if ($value['parent'] == $parent && $value['id'] != $inactiveId) {
                            $value['level'] = $level;
                            $newArray[]     = $value;
                            unset($source[$key]);
                            $newParent = $value['id'];
                            recursive($source, $newParent, $level + 1, $newArray, $inactiveId);
                        }
                    }
                }
            }
        };

        $categories = $this->getAll(['select' => 'id, name, parent']);
        $output = '';
        $arrayMenu = [];

        recursive($categories, 0, 1, $arrayMenu, $inactiveId);

        foreach ($arrayMenu as $key => $value) { 
            $seleted =  (!!$seletedId && ($value['id'] ==  $seletedId))  ? 'selected ' : '' ;
            if ($value['level'] == 1) {
                $output .= '<option ' . $seleted . ' value="' . $value['id'] . '">' . $value['name'] . '</option>';
            } 
            else 
            {
                $name = str_repeat('&nbsp;', ($value['level'] - 1) * 5) . '-' . $value['name'];
                $output .= '<option ' . $seleted . ' value="' . $value['id'] . '">' . $name . '</option>';
            }
        }
        return $output;
    }
} 

?>