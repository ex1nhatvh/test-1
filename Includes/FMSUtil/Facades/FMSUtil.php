<?php
namespace App\Plugins\FurnitureManagementSystem\Includes\FMSUtil\Facades;
use Illuminate\Support\Facades\Facade;
class FMSUtil extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Plugins\FurnitureManagementSystem\Includes\FMSUtil\FMSUtil::class;
    }
}