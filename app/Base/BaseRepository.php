<?php

namespace App\Base;

use Illuminate\Database\Eloquent\Collection; 
use DB;
class BaseRepository
{
    /**
     * Construct  function to call model 
     * @param $model (Model name)
     * @return object  
     */
    protected $model = '';
    public function __construct($model){
        $this->model = $model;
    }

    public  function setCreateBy($id = ''){
        $this->model::setCreateBy($id);
    }   
    /**
     * To create a resource from request 
     * @param $input
     * @return object
     */
    public function create($input){
        $data = $this->model->create($input);
        return $data;
    }

    /**
     * To Get a resource from request
     * @param $input $with (relation ship data)
     * @return object
     */
    public function getbyId(int $id,$with=[]){
        $data = $this->model;
        if(!empty($with)){
           $data = $data->with($with);
        }
        return  $data->where($this->model->getKeyName(),$id)->first();
    }

    
    /**
     * To Get a resource from request
     * @param $input
     * @return object
     */
    public function getAllRecords($input = [],$with = [],$order = [],$limit = [],$groupBy = false,$select = ''){
        $records =  $this->model;
        if(!empty($select)){
            $records = $records->selectRaw($select);
        }
        $records = $records->where(function($query) use ($input){
            foreach($input as $key => $value){
                if($key == 'whereIn'){
                    foreach($value as $keyV => $valueD){
                        $query->whereIn($keyV,$valueD);
                    }
                }
                else if($key == 'like'){
                    foreach($value as $keyV => $valueD){
                        $query->where($keyV,'LIKE', '%' . $valueD . '%');
                    }
                }
                else{
                    $query->where($key,$value);
                }
            }
        });
        if(!empty($with)){
            $records = $records->with($with);  
        }
        if($groupBy){
            $records = $records->groupBy($groupBy)->distinct();
        }
        if(!empty($order)){
            $records = $records->orderBy($order[0], $order[1]);  
        }
        if(!empty($limit)){
            $records = $records->skip($limit['start'])->take($limit['limit']);
        }
        return $records->get();
    }

    /**
     * To update a resource from request
     * @param $id,$input
     * @return object
     */
    public function update(int $id,$input){
        $oUpdateData = $this->model->where($this->model->getKeyName(),$id)->first();
        $oUpdateData->update($input);
        //$this->model->find($id)->update($input);
        return $this->getbyId($id);
        
    }

    /**
     * To update a resource from request
     * @param $id,$input
     * @return object
     */
    public function delete(int $id){
        $oDeleteData = $this->model->where($this->model->getKeyName(),$id)->first();
        return $oDeleteData->delete();
    }

    public function deleteSelectedIds($id){
        return $this->model->whereIn($this->model->getKeyName(),$id)->delete();
    }

    public function getCustomData($where, $id,$all = false,$with = []){
        $data = $this->model;
        if(!empty($with)){
            $data = $data->with($with);
        }
        $data = $data->where($where,$id);
        if($all){
            return $data->get();
        }
        return $data->first();
    }


    public function getAllCustomData($where,$all = false,$with = []){
        $data = $this->model->where(function($query) use ($where){
            if(!empty($where)){
                foreach($where as $key => $value){
                    $query->where($key,$value);
                }
            }
        });
        if(!empty($with)){
            $data = $data->with($with);
        }
        if($all){
            return $data->get();
        }
        return $data->first();
    }

    public function getDataCount($where,$with = []){
        $data = $this->model->where(function($query) use ($where){
            if(!empty($where)){
                foreach($where as $key => $value){
                    $query->where($key,$value);
                }
            }
        });
        if(!empty($with)){
            $data = $data->with($with);
        }
        return $data->count();
    }

    public function getCustomDelete($where,int $id){
        return  $this->model->where($where,$id)->delete();
    }

    public function getSingleRecords(array $input = [],$select = [],$with = [])
    {
        $query = $this->model;
        if(!empty($select)) {
            $select = implode(',',$select);
            $query = $query->selectRaw($select);
        }
        if(!empty($with)){
            $query = $query->with($with);
        }
        $query = $query 
        ->where(function($query) use ($input){
            foreach($input as $key => $value){
                $query->where($key,$value);
            }
        })->first();
        // echo $query;
        return $query;
    }
}
