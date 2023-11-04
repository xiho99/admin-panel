<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Models\Ads;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\MenuItem;
use Illuminate\Http\Response;

class FrontEndController extends BaseController
{
    public function getConfigurations(): Response
    {
        $configurations = Configuration::orderBy('sort', 'asc')
            ->where('is_delete', 0)
            ->get();
        return $this->success($configurations);
    }
    public function getMenuList(): Response
    {
        $configurations = MenuItem::orderBy('sort', 'asc')
            ->where('is_delete', 0)
            ->get();
        return $this->success($configurations);
    }
    public function getAds(): Response
    {
        $configurations = Ads::orderBy('sort', 'asc')
            ->where('is_delete', 0)
            ->get();
        return $this->success($configurations);
    }
    public function getGroupList(): Response
    {
        $catGroupWithSub = Category::with('group')
            ->where('is_delete', 0)->get();
        return $this->success($catGroupWithSub);
    }
}