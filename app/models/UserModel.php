<?php 
namespace App\Models;

use Core\BaseModel;

class UserModel extends BaseModel {
    
    public function __construct()
    {   
        parent::__construct('users');
    }
    
    public function getAll($queryParam = [])
    {
        return $this->all($this -> table, $queryParam);         
    }

    public function getUser($queryParam = [])
    {
        return $this->getOnce($this -> table, $queryParam);         
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
    

    public function authUser() {
        $token = getCOOKIE('token');
        if (empty($token) ) {
            return null;
        }
        else {
            $user  =  $this->getOnce('users', [
                'select' => 'users.*',
                'join' => 'join login_tokens on (users.id = login_tokens.user_id)',
                'where' => ['token' => $token,
                ]
            ]); 
            return $user; 
        }     
    }

    /**
     * @ return error-message
     */
    public function getLoginUser($email, $password) {
        if ($email && $password) {
            $user = $this->getOnce($this->table,  [
                'where' => ['email' => $email]
            ]);

            if (!$user) 
                return ['error' => 'This email is not exists.'];  

            if ( !password_verify($password, $user['password']) )
                return ['error' => 'The password is not correct!'];
            return  $user;
        }
    }

    public function deleteLoginToken() {
        $token = getCOOKIE('token');
        if (!empty($token) )
            $this->deleteByWhere('login_tokens', ['where' => ['token' => $token ]]);
    }


    public function store($data) {
        $this->create($this->table, $data);
    } 

    /* save Token to database */
    public function saveLoginToken($id, $token) {
        $this->create('login_tokens', [
            'user_id' => $id,
            'token' => $token,
        ]);
        
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
