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
        echo "<h1>View: {$view->name()}</h1>";

//        $data = $view->getData();//TODO::Remove

        //$view->name();//TODO::Set default file name.
        $viewData = ufa()->getData();
        print_r($viewData);
        foreach($viewData as $key => $value) {
            $view->with($key, $value);
        }
//        if (! isset($data['title'])) {
//            $view->with('title', '');
//        }

    }
}