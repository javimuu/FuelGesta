<h2>Gestion de la configuration </h2>

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gg
 * Date: 24/07/13
 * Time: 15:16
 * To change this template use File | Settings | File Templates.
 */

echo Form::open(array('action'=>'administration/modif_config','class' => 'form-horizontal')); ?>



<fieldset>

    <legend></legend>

    <div class="control-group">
        <?php echo Form::label('Insertion Minimum Heure', 'mintime', array('class' => 'control-label')); ?>
        <div class="controls">
            <?php echo Form::input('mintime', Input::post('mintime', isset($config) ? $config->mintime : '00:30')); ?>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success">Sauver la configuration</button>
    </div>


</fieldset>

<?php echo render($partial_dir . '/_back.php', array('url' => Uri::create($view_dir . 'index'))); ?>