<?php

/* index.twig */
class __TwigTemplate_29f829e7468fdb8ddf67555f3625ffa6 extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        echo "
<html>
    <head>
        <title>";
        // line 5
        if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
        echo $_title_;
        echo "</title>
    </head>
    <body>
        Welcome, ";
        // line 8
        if (isset($context["username"])) { $_username_ = $context["username"]; } else { $_username_ = null; }
        echo $_username_;
        echo ".
    </body>
</html>";
    }

    public function getTemplateName()
    {
        return "index.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
