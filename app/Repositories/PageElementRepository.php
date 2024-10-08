<?php

namespace App\Repositories;

use App\Models\PageElement;
use App\Repositories\Contracts\Repository;

class PageElementRepository implements Repository
{
    public function getAddress(): ?PageElement
    {
        return PageElement::where([
            'page_type' => PageElement::PAGE_TYPE_ABOUT_US,
            'key' => 'address',
        ])->first();
    }
}
