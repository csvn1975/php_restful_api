<?php 

namespace App\Models;

use Core\BaseModel;

class ProductModel extends BaseModel {
    
    public function __construct()
    {   
        parent::__construct('products');
    }
    
    /**
     * get all data with queryParam
     *  'select' => '*',  
     *   'join' => '',
     *   'params' => '',
     *   'where' => '',
     *   'order by' => '', 
     *   'limit' => '',
     *   'field' => '',            
     *   'value' => [],
     */
    public function getAll($queryParam = [])
    {
        return $this->all($this -> table, $queryParam);         
    }

    public function getTotal()
    {
        return $this->count($this -> table);         
    }

    public function findById($id) {
       return ($this -> find($this->table, $id));
    }

    /**
     * get all products from a category_id
     */
    public function getByCategoryId($categoryId)
    {
        $queryParams = [
            'where' => "category_id = $categoryId "
        ];
        return $this->all($this->table, $queryParams);
    }

    /**
     * Create a New 
     * @ $data ist array
     *  colname => value
     *  [
     *     'name' => 'Iphone',
     *     'detail' => '128GB Black' 
     *  ]
     */

    public function store($data) {
        $this->create($this->table, $data);
    } 

    # save edit value
    public function save($id, $data) 
    {
        $this->update($this->table, $id, $data);
    } 

    # delete a item
    public function destroy($id) 
    {
        $this->delete($this->table, $id);
    } 

} 

?>