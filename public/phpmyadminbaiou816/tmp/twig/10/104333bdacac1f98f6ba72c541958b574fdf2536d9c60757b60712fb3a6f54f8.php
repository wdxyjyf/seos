<?php

/* server/variables/session_variable_row.twig */
class __TwigTemplate_096025cf0454442e3e36a19dd253a75ceaadf88a317136d406c6c22693f1cfa6 extends Twig_Template
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
        echo "<tr class=\"var-row ";
        echo twig_escape_filter($this->env, ($context["row_class"] ?? null), "html", null, true);
        echo "\" data-filter-row=\"";
        echo twig_escape_filter($this->env, twig_upper_filter($this->env, ($context["name"] ?? null)), "html", null, true);
        echo "\">
    <td class=\"var-action\"></td>
    <td class=\"var-name session\">(";
        // line 3
        echo _gettext("Session value");
        echo ")</td>
    <td class=\"var-value value\">&nbsp;";
        // line 4
        echo twig_escape_filter($this->env, ($context["value"] ?? null), "html", null, true);
        echo "</td>
</tr>
";
    }

    public function getTemplateName()
    {
        return "server/variables/session_variable_row.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 4,  27 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "server/variables/session_variable_row.twig", "/www/seos/public/phpmyadminbaiou816/templates/server/variables/session_variable_row.twig");
    }
}
