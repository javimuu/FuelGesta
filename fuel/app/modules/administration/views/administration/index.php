<h2>Administration</h2>


<ul class="gesta-list">

    <li><?php echo Html::anchor($view_dir . 'liste_logins', 'Utilisateurs') ?></li>
    <li>
        <div>
            <a class="accordion-toggle" data-toggle="collapse" href="#collapseOne">
                Offre de Formation
            </a>
        </div>
        <div id="collapseOne" class="accordion-body collapse out">
            <div class="accordion-inner">
                <ul class="gesta-list">
                    <li><?php echo Html::anchor($view_dir . 'liste_centres', 'Centres') ?></li>
                    <li><?php echo Html::anchor($view_dir . 'liste_agrement', 'Agréments') ?></li>
                    <li><?php echo Html::anchor($view_dir . 'liste_filiere', 'Filières') ?></li>
                    <li><?php echo Html::anchor($view_dir . 'liste_localisation', 'Localisation') ?></li>
                    <li><?php echo Html::anchor($view_dir . 'liste_groupes', 'Groupes') ?></li>
                    <li><?php echo Html::anchor($view_dir . 'liste_types_contrat', 'Types de contrat') ?></li>
                    <li><?php echo Html::anchor($view_dir . 'liste_types_cedefop', 'Code Cedefop') ?></li>
                    <li><?php echo Html::anchor($view_dir . 'liste_subsides', 'Type de subsides') ?></li>
                    <li><?php echo Html::anchor($view_dir . 'prestations', 'Prestation sur l\'année') ?></li>
                    <li><?php echo Html::anchor($view_dir . 'liste_activites', 'Activités') ?></li>
                    <li><?php echo Html::anchor($view_dir . 'photogramme_xml', 'Photogramme') ?></li>
                </ul>
            </div>
    </li>
    <li>
        <div>
            <a class="accordion-toggle" data-toggle="collapse" href="#collapseTwo">
                Signalétique des participants
            </a>
        </div>
        <div id="collapseTwo" class="accordion-body collapse out">
            <div class="accordion-inner">
                <ul class="gesta-list">
                    <li><?php echo Html::anchor($view_dir . 'liste_pays_xml', 'Catégories de nationalité') ?></li>
                    <li><?php echo Html::anchor($view_dir . 'liste_types_statut', "Classement des statuts à l'entrée") ?></li>
                    <li><?php echo Html::anchor($view_dir . 'liste_statuts_entree', "Statuts à l'entrée") ?></li>
<!--                    <li>--><?php //echo Html::anchor($view_dir . 'liste_types_enseignement', "Gestion des types d'enseignement") ?><!--</li>-->
                    <li><?php echo Html::anchor($view_dir . 'liste_enseignements', "Niveau diplômes et types d'études") ?></li>
                    <li><?php echo Html::anchor($view_dir . 'liste_fins_formation', 'Classement des types et motifs de sortie') ?></li>
                    <li><?php echo Html::anchor($view_dir . 'liste_types_formation', 'Type et motif de sortie') ?></li>
                </ul>
            </div>
    </li>
    <?php //echo Html::anchor($view_dir.'typesContact', 'Administration des types de contact') ?><!--</li>-->
    <li><?php echo Html::anchor($view_dir . 'config', 'Configuration') ?></li>




</ul>