<?php

namespace App\Repositories\Implementation\User;

use App\Base\BaseRepository;
use App\Models\UserDetail;
use App\Repositories\Interfaces\User\UserDetailRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB; 
class UserDetailRepository  extends BaseRepository implements UserDetailRepositoryInterface
{
    /**
     * @var UserDetail
     */
    protected $userDetailModelRepo;  

    /**
     * UserRepository constructor.
     *
     * @param User $userModel
     */
    public function __construct(UserDetail $userDetailModel)
    {
        parent::__construct($userDetailModel);
        $this->userDetailModelRepo = $userDetailModel;
    }
}
?>