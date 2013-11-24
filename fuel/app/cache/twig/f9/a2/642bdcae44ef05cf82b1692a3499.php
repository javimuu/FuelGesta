<?php

/* template.twig */
class __TwigTemplate_f9a2642bdcae44ef05cf82b1692a3499 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'head' => array($this, 'block_head'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
    <head>
\t";
        // line 4
        $this->displayBlock('head', $context, $blocks);
        // line 8
        echo "    </head>
\t
\t<body>
                <div id=\"deco\"></div>
                
\t\t<div id=\"header\">
\t\t\t<h1>Ateliers de Pontaury</h1>
                        <ul>
                            <li>Login |</li>
                            <li>Retour |</li>
                            <li>Quitter</li>
                        </ul>
\t\t</div> 
 
\t\t<div id=\"menu\">
                    <ul>
                        <li><a href=\"\" id=\"link_home\">Accueil</a></li>
                        <li><a href=\"\" id=\"link_participant\">Participant</a></li>
                        <li><a href=\"\" id=\"link_contrat\">Contrat</a></li>
                        <li><a href=\"\" id=\"link_heures\">Prestations</a></li>
                        <li><a href=\"\" id=\"link_admin\">Admin</a></li>
                    </ul>
\t\t</div>
 
\t\t<div id=\"content\">
\t\t
\t\t\t";
        // line 34
        $this->displayBlock('body', $context, $blocks);
        // line 36
        echo "\t\t\t
\t\t</div> 
 
\t\t<div id=\"footer\"> 
                    <p><a href=\"\">Gesta v3.0</a> &#169; Ateliers de Pontaury</p> 
\t\t</div>
\t</body>
</html>";
    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        echo "Ateliers de Pontaury";
    }

    // line 4
    public function block_head($context, array $blocks = array())
    {
        // line 5
        echo "\t\t<title>";
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
                ";
        // line 6
        if (isset($context["css"])) { $_css_ = $context["css"]; } else { $_css_ = null; }
        echo $_css_;
        echo "
\t";
    }

    // line 34
    public function block_body($context, array $blocks = array())
    {
        // line 35
        echo "\t\t\t";
    }

    public function getTemplateName()
    {
        return "template.twig";
    }

}
