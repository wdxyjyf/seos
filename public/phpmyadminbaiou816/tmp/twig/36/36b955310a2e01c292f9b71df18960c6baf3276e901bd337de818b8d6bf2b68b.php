<?php

/* server/variables/link_template.twig */
class __TwigTemplate_91ee16eafba0708e9120d9e6a813e0dd95cde3dbd19e1a55b05254b5639a1d2a extends Twig_Template
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
        // line 1
        echo "<a href=\"";
        echo ($context["url"] ?? null);
        echo "\" class=\"ajax saveLink hide\">
    ";
        // line 2
        echo PhpMyAdmin\Util::getIcon("b_save", _gettext("Save"));
        echo "
</a>
<a href=\"#\" class=\"cancelLink hide\">
    ";
        // line 5
        echo PhpMyAdmin\Util::getIcon("b_close", _gettext("Cancel"));
        echo "
</a>
";
        // line 7
        echo PhpMyAdmin\Util::getImage("b_help", _gettext("Documentation"), array("class" => "hide", "id" => "docImage"));
        // line 10
        echo "
";
    }

    public function getTemplateName()
    {
        return "server/variables/link_template.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 10,  35 => 7,  30 => 5,  24 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "server/variables/link_template.twig", "/www/seos/public/phpmyadminbaiou816/templates/server/variables/link_template.twig");
    }
}
