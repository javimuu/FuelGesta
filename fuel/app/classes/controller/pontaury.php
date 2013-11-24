<?php

/**
 * Controller gérant la page d'accueil.
 */
class Controller_Pontaury extends Controller_Main
{
    /**
     * Affiche l'index.
     */
    public function action_index()
    {
        
        $this->template->title = 'Accueil';
        $this->template->content = View::forge('pontaury/index');
    }
}

?>
