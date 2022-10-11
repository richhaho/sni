<?php namespace App\Http\Composers;

use Illuminate\Contracts\View\View;
use App\Notification;
use Auth;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NavigationComposer
 *
 * @author carlos
 */
class NavigationComposer {
    //put your code here
    protected  $user;


    public function __construct() {
        $this->user =Auth::user();
    }
    
    public function compose(View $view) {
      
    }
    
    public function notifications (View $view) {

        $data = [
            'count_notification' => $this->user->notifications->count(),
            'notifications' =>  $this->user->notifications
        ];
        
        $view->with($data);
    }
}
