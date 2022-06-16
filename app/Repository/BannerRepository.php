<?php

namespace App\Repository;

use App\Banner;

class BannerRepository
{
    private $model;

    public function __construct(Banner $model)
    {
        $this->model = $model;
    }

    public function getBanners()
    {
        return $this->model->paginate();
    }

    public function getBannersByStatus($data)
    {
        return $this->model->whereStatus($data)->paginate();
    }

    public function getBannersById($id)
    {
        return $this->model->whereId($id)->first();
    }

    public function getActiveBanners($shopName = '')
    {
        return $this->model
            ->whereShopName($shopName)
            ->whereStatus(1)
            ->take(5)
            ->get();
    }

}