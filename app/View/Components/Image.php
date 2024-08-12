<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Image extends Component
{
    public $id;
    public $url;
    public $title;
    
    public function __construct($url, $title, $id = null)
    {
        $this->id = $id;
        $this->url = $url;
        $this->title = $title;
    }

    
    public function render(): View|Closure|string
    {
        return view('components.image');
    }
}
