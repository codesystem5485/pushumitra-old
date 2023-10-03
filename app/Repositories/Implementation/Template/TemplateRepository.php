<?php

namespace App\Repositories\Implementation\Template;

use App\Base\BaseRepository;
use App\Models\Template;
use App\Repositories\Interfaces\Template\TemplateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class TemplateRepository  extends BaseRepository implements TemplateRepositoryInterface
{
    /**
     * @var Template
     */
    protected $templateModel; 

    /**
     * UserRepository constructor.
     *
     * @param Template $templateModel
     */
    public function __construct(Template $templateModel)
    {
        parent::__construct($templateModel);
        $this->templateModelRepo = $templateModel;
    }



    
}
