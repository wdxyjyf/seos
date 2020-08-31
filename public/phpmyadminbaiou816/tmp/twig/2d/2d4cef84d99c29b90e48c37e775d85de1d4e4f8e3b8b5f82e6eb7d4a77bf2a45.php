<?php

/* server/binlog/log_row.twig */
class __TwigTemplate_5c95f12f7c287c9a93266ce23ae743eeab5d93aa640f8eeecd486142d7ca9936 extends Twig_Template
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
        echo "<tr class=\"noclick\">
    <td>";
        // line 2
        echo twig_escape_filter($this->env, $this->getAttribute(($context["value"] ?? null), "Log_name", array(), "array"), "html", null, true);
        echo "</td>
    <td class=\"right\">";
        // line 3
        echo twig_escape_filter($this->env, $this->getAttribute(($context["value"] ?? null), "Pos", array(), "array"), "html", null, true);
        echo "</td>
    <td>";
        // line 4
        echo twig_escape_filter($this->env, $this->getAttribute(($context["value"] ?? null), "Event_type", array(), "array"), "html", null, true);
        echo "</td>
    <td class=\"right\">";
        // line 5
        echo twig_escape_filter($this->env, $this->getAttribute(($context["value"] ?? null), "Server_id", array(), "array"), "html", null, true);
        echo "</td>
    <td class=\"right\">";
        // line 7
        echo twig_escape_filter($this->env, (($this->getAttribute(($context["value"] ?? null), "Orig_log_pos", array(), "array", true, true)) ? ($this->getAttribute(($context["value"] ?? null), "Orig_log_pos", array(), "array")) : ($this->getAttribute(($context["value"] ?? null), "End_log_pos", array(), "array"))), "html", null, true);
        // line 8
        echo "</td>
    <td>";
        // line 9
        echo PhpMyAdmin\Util::formatSql($this->getAttribute(($context["value"] ?? null), "Info", array(), "array"),  !($context["dontlimitchars"] ?? null));
        echo "</td>
</tr>
";
    }

    public function getTemplateName()
    {
        return "server/binlog/log_row.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  43 => 9,  40 => 8,  38 => 7,  34 => 5,  30 => 4,  26 => 3,  22 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "server/binlog/log_row.twig", "/www/seos/public/phpmyadminbaiou816/templates/server/binlog/log_row.twig");
    }
}
