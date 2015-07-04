<?php

namespace Angejia\Ufa\Composers;

use Illuminate\Contracts\View\View;
//use Illuminate\Users\Repository as UserRepository;

class UfaComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $users;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
//    public function __construct(UserRepository $users)
//    {
//        // Dependencies automatically resolved by service container...
//        $this->users = $users;
//    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        ufa()->setName($view->name());
        $viewData = ufa()->getData();
        foreach($viewData as $key => $value) {
            $view->with($key, $value);
        }
    }
}