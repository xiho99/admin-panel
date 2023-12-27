<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Models\Ads;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\MenuButton;
use App\Models\MenuIcon;
use Illuminate\Http\Response;

class FrontEndController extends BaseController
{
    public function getConfigurations(): Response
    {
        $configurations = Configuration::orderBy('sort', 'asc')
            ->where([
                'is_delete' => 0,
                'is_visible' => 1,
            ])
            ->get();
        return $this->success($configurations);
    }
    public function menuIconList(): Response
    {
        $menuIcons = MenuIcon::orderBy('sort', 'asc')
            ->where([
                'is_delete' => 0,
                'is_visible' => true,
            ])
            ->get();
        return $this->success($menuIcons);
    }
    public function menuButtonList(): Response
    {
        $menuButtons = MenuButton::orderBy('sort', 'asc')
            ->where([
                'is_delete' => 0,
                'is_visible' => true,
            ])
            ->get();
        return $this->success($menuButtons);
    }
    public function getAds(): Response
    {
        $configurations = Ads::orderBy('sort', 'asc')
            ->where([
                'is_delete' => 0,
                'is_visible' => true,
            ])
            ->get();
        return $this->success($configurations);
    }
    public function getGroupList(): Response
    {
        $catGroupWithSub = Category::with('group')
            ->where([
                'is_delete' => 0,
                'is_visible' => true,
            ])->get();
        return $this->success($catGroupWithSub);
    }
}