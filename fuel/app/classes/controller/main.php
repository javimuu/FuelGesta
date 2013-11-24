<?php

/**
 * Main controller dont héritent tous les autres. Permet d'y placer des functions
 * devant s'exécuter à toutes les pages.
 */
class Controller_Main extends Controller_Template {

    /**
     * Overrides la function before.
     * Permet ici de vérifier si le visiteur est authentifié. Si c'est le cas,
     * ses informations sont stockées dans $this->current_user.
     * Utilise le composant Auth de FuelPHP.
     */
    public function before() {
        parent::before();
        
        if(\Uri::segment(1) != 'users')
            \Session::set('direction', 'tableau');
        $this->current_user = Auth::check() ? Model_User::find(Arr::get(Auth::get_user_id(), 1)) : NULL;
        
        View::set_global('current_user', $this->current_user);
    }

}

?>
