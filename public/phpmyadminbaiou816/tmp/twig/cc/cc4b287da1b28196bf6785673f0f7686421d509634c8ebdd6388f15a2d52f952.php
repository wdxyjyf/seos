<?php

/* database/search/result_divs.twig */
class __TwigTemplate_3ef8d92271e212ecc2284d158303f9e52067c6c0088dc1e757eef2e5c1713f0b extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        echo "<div id=\"table-info\">
    <a id=\"table-link\" class=\"item\"></a>
</div>
";
        // line 6
        echo "<div id=\"browse-results\">
    ";
        // line 8
        echo "</div>
<div id=\"sqlqueryform\" class=\"clearfloat\">
    ";
        // line 11
        echo "</div>
";
        // line 13
        echo "<a id=\"togglequerybox\"></a>
";
    }

    public function getTemplateName()
    {
        return "database/search/result_divs.twig";
    }

    public function getDebugInfo()
    {
        return array (  34 => 13,  31 => 11,  27 => 8,  24 => 6,  19 => 2,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "database/search/result_divs.twig", "/www/seos/public/phpmyadminbaiou816/templates/database/search/result_divs.twig");
    }
}
