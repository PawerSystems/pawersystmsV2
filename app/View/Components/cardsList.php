<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\User;


class cardsList extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $cards;
    public $card = '';

    public function __construct($user,$card,$type)
    {
        $this->cards = User::find($user)->cards->where('is_active',1)->where('type',$type);

        if( $card != Null )
            $this->card = $card;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.cards-list');
    }
}
